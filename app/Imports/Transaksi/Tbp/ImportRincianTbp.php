<?php

namespace App\Imports\Transaksi\Tbp;

use App\Lib\Notification;
use App\Models\Kegiatan;
use App\Models\SourceRekening;
use App\Models\Tbpheader;
use App\Models\Tbprincian;
use App\Repositories\Impl\TbpRincianImplement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ImportRincianTbp implements ToCollection,SkipsEmptyRows
{
    protected $rincian;
    protected $errors;
    protected $success;
    protected $errorCount;
    protected $errorsData=[];
    protected $successData=[];
    protected $messageErrors=[];
    protected $headerId;

    public function __construct($headerId) {
        $this->headerId = $headerId;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $heading = [
            'KODE KEGIATAN',
            'NAMA KEGIATAN',
            'KODE REKENING',
            'NAMA REKENING',
            'NOMINAL REKENING',
        ];

        if ($heading != $rows[0]->toArray()) {
            $tempError = [
                'key'   => "error",
                'message' => "Records not same format, check the file header or download the sample file"
            ];
            array_push($this->messageErrors,$tempError );
            $this->data = [
                'countExcel'        => 0,
                'countSuccess'      => 0,
                'countError'        => 1,
                'errorData'         => $this->errorsData,
                'successData'       => $this->successData,
                'messageError'      => $this->messageErrors
            ];
            return back()->withErrors('Error,  Records not same format, check the file header or download the sample file')->with($this->data);
        }

        if (count($rows) == 1) {
            $tempError = [
                'key'   => "error",
                'message' => "Records is empty"
            ];
            array_push($this->messageErrors,$tempError );
            $this->data = [
                'countExcel'        => 0,
                'countSuccess'      => 0,
                'countError'        => 1,
                'errorData'         => $this->errorsData,
                'successData'       => $this->successData,
                'messageError'      => $this->messageErrors
            ];
            return back()->withErrors('Error, Records is empty')->with($this->data);
        }

        unset($rows[0]);
        set_time_limit(0);

        $this->errors = "";
        $this->success=0;
        $this->errorCount=0;
        $baris = 0;
        $tbpRincianRepo = new TbpRincianImplement(new Tbprincian());
        try {
            DB::beginTransaction();
            foreach ($rows as $key => $value) {
                $baris++;
                $validator = Validator::make($value->toArray(), rules:[
                    0 => ['required'],
                    1 => ['required'],
                    2 => ['required'],
                    3 => ['required'],
                    4 => ['required'],
                    4 => ['numeric']
                ], messages:[
                    '0.required' => 'Kode Kegiatan (Column A line no-'.$baris.') must not be empty!',
                    '1.required' => 'Nama Kegiatan (Column B line no-'.$baris.') must not be empty!',
                    '2.required' => 'Kode Rekening (Column C line no-'.$baris.') must not be empty!',
                    '3.required' => 'Nama Rekening (Column D line no-'.$baris.') must not be empty!',
                    '4.required' => 'Nominal (Column E line no-'.$baris.') must not be empty!',
                ]);
                if($validator->fails()) {
                    $validatorMessage = $validator->errors()->getMessages();
                    foreach ($validatorMessage as $msg){
                        Notification::sendMessageTbp('Error validation import rincian '.$msg);
                        $this->errorCount++;
                    }
                }
                $kegiatan = Kegiatan::whereSubkegiatanKode("$value[0]")->firstOrFail();
                $subrekening = SourceRekening::whereKodeRekening("$value[2]")->firstOrFail();
                $rekening = SourceRekening::where('kode_rekening',substr($value[2],0,12))->firstOrFail();
                $tbp  = Tbpheader::whereId($this->headerId)->firstOrFail();
                $rincians = [
                    'header_id'     => $tbp->id,
                    'skpd_id'       => $tbp->id_skpd,
                    'no_urut'       => getMaxNourut($tbp->id,$tbp->id_skpd,1),
                    'kegiatan_id'   => $kegiatan->id,
                    'rekening_id'   => $rekening->id,
                    'subrekening_id'=> $subrekening->id,
                    'skode_kegiatan'=> $kegiatan->kegiatan_skode,
                    'nominal'       => $value[4],
                    'actived'       => 1
                ];
                $tbpRincianRepo->create($rincians);
            }
            DB::commit();
            $this->rincian++;
            $this->success++;
        } catch (\Throwable $th) {
            $this->errorCount++;
            Notification::sendException($th);
            DB::rollBack();
        }

        $this->data = [
            'countExcel'            => $this->rincian,
            'countSuccess'          => $this->success,
            'countError'            => $this->errorCount,
            'errorData'             => $this->errorsData,
            'successData'           => $this->successData,
            'messageError'          => $this->messageErrors,
        ];
        return back()->withStatus('Success imported order details')->with($this->data);
    }
}
