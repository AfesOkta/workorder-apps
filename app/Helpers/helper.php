<?php

use App\Lib\Notification;
use App\Models\BendaharaOtorisator;
use App\Models\Bkupenerimaan;
use App\Models\BludSkpd;
use App\Models\Configs;
use App\Models\Skpd;
use App\Models\SkpdBendahara;
use App\Models\SourceRekening;
use App\Models\Stsheader;
use App\Models\Stsrincian;
use App\Models\User;
use App\Models\Tbpheader;
use App\Models\Tbprincian;
use App\Repositories\Impl\BkuImplement;
use App\Repositories\Impl\SKPDBendaharaImplement;
use App\Repositories\Impl\StsHeaderImplement;
use App\Repositories\Impl\StsRincianImplement;
use App\Repositories\Impl\TbpHeaderImplement;
use App\Repositories\Impl\TbpRincianImplement;
use Carbon\Carbon;
use EAccountingApiSts\Sts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Str;

function jenis_bendahara($status)
{
    if ($status==0){
        $status = 'Bendahara Pembantu';
    }else{
        $status = 'Bendahara Utama';
    }
    return $status;
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function http_request($url){
    // persiapkan curl
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url);

    // set user agent
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    // return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // tutup curl
    curl_close($ch);

    // mengembalikan hasil curl
    return $output;
}

function doGenerateKode($skpd,$status)
{
    if($status == 1) // TBP
    {
        $tbpRepo = new TbpHeaderImplement(new Tbpheader(), new SKPDBendaharaImplement(new SkpdBendahara()));
        $maxKode = @$tbpRepo->doGetMaxKode('id_skpd',$skpd) ?? 0;
        return create_reg_number($maxKode+1,6);
    }

    if($status == 2) // STS
    {
        $stsRepo = new StsHeaderImplement(new Stsheader(), new SKPDBendaharaImplement(new SkpdBendahara()));
        $maxKode = @$stsRepo->doGetMaxKode('id_skpd',$skpd) ?? 0;
        return create_reg_number($maxKode+1,6);
    }

    if($status == 3)
    {
        $bkuRepo = new BkuImplement(new Bkupenerimaan());
        $maxKode = $bkuRepo->doGetMaxKode('id_skpd', $skpd) ?? 0;
        return create_reg_number($maxKode+1,6);
    }
}

function getMaxNourut($skpd,$kode,$status)
{
    if($status == 1) // TBP
    {
        $tbpRepo = new TbpRincianImplement(new Tbprincian());
        $maxKode = @$tbpRepo->doGetMaxNourut('skpd_id',$skpd,'header_id',$kode) ?? 0;
        return $maxKode+1;
    }

    if($status == 2) // TBP
    {
        $stsRepo = new StsRincianImplement(new Stsrincian());
        $maxKode = @$stsRepo->doGetMaxNourut('id_skpd',$skpd,'id_hdr',$kode) ?? 0;
        return $maxKode+1;
    }
}

function create_reg_number($number, $max_length) {
    $number_length = strlen($number);
    $zero_length = $max_length - $number_length;
    $zero = "";
    for ($i = 1; $i <= $zero_length; $i++) {
        $zero .= '0';
    }
    return $zero . $number;
}

function getAmount($amount, $length = 0)
{
    if (0 < $length) {
        return round($amount + 0, $length);
    }
    return $amount + 0;
}

function amountFormat($number, int $decimal = 4)
{
    return number_format(round($number, $decimal), $decimal);
}


    /**
     * @note method untuk konversi
     * @param $x
     * @return string
     */
function konversi($x){
        $x = abs($x);
        $angka = array ("","satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";

        if($x < 12){
            $temp = " ".$angka[$x];
        }else if($x<20){
            $temp = konversi($x - 10)." belas";
        }else if ($x<100){
            $temp = konversi($x/10)." puluh". konversi($x%10);
        }else if($x<200){
            $temp = " seratus".konversi($x-100);
        }else if($x<1000){
            $temp = konversi($x/100)." ratus".konversi($x%100);
        }else if($x<2000){
            $temp = " seribu".konversi($x-1000);
        }else if($x<1000000){
            $temp = konversi($x/1000)." ribu".konversi($x%1000);
        }else if($x<1000000000){
            $temp = konversi($x/1000000)." juta".konversi($x%1000000);
        }else if($x<1000000000000){
            $temp = konversi($x/1000000000)." milyar".konversi($x%1000000000);
        }

        return $temp;
    }

function tkoma($x){
        $str = stristr($x,"."); //  mencari nominal desimal
        $ex = explode('.',$x);

        if(($ex[1]/10) >= 1){ //04
            $a = abs($ex[1]);
        }else{
            $a=0;
        }

        $string = array("nol", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan","sepuluh", "sebelas");
        $temp = "";

        $a2 = $ex[1]/10;
        $pjg = strlen($str);
        $i =1;

        if($a>=1 && $a< 12){
            $temp .= " ".$string[$a];
        }else if($a>12 && $a < 20){
            $temp .= konversi($a - 10)." belas"; //bisa menghapus "BELAS" bila menggunakan EYD yang benar
        }else if ($a>20 && $a<100){
            $temp .= konversi($a / 10)." puluh". konversi($a % 10); //bisa menghapus "PULUH" bila menggunakan EYD yang benar
        }else{
            if($a2<1){
                while ($i<$pjg){
                    $char = substr($str,$i,1);
                    $i++;
                    $temp .= " ".$string[$char];
                }
            }
        }
        return $temp;
    }// end of tkoma

function terbilang($x){
    if($x<0){
        $hasil = "minus ".trim(konversi(x));
    }else{

        $ex = explode('.',$x); // karena pemisah desimal didatabase menggunakan titik bukan koma

        if(empty($ex[1])){ // cek bilangan bulat atau bilangan desimal
            $hasil = trim(konversi($x));
            $poin = null;
        }else{
            $poin = trim(tkoma($x));
            $hasil = trim(konversi($x));
        }
    }

    if($poin){
        $hasil = $hasil." koma ".$poin;
    }else{
        $hasil = $hasil;
    }

    return $hasil;
}

function fdate($date)
{
	setlocale(LC_ALL, 'id_ID.UTF8', 'id_ID.UTF-8', 'id_ID.8859-1', 'id_ID', 'IND.UTF8', 'IND.UTF-8', 'IND.8859-1', 'IND', 'Indonesian.UTF8', 'Indonesian.UTF-8', 'Indonesian.8859-1', 'Indonesian', 'Indonesia', 'id', 'ID', 'en_US.UTF8', 'en_US.UTF-8', 'en_US.8859-1', 'en_US', 'American', 'ENG', 'English');
	return strftime('%A %d %B %Y', strtotime($date));
}

function caraBayar($status)
{
    return @$status==1?'Transfer':'Tunai';
}


function createdHeaderTbpToSts($skpd, $user, $tgl, $uraian, $jnsBayar, $tgl_tbp)
{
    try {
        DB::beginTransaction();
        //cek Skpd & Bendahara
        $skpd_ = Skpd::find($skpd);
        $parent_ = Skpd::whereParent($skpd_->parent)->first();

        $bendahara = SkpdBendahara::where("id_skpd",$skpd)->where('id_bendahara',$user)->first();

        $kodeSts = doGenerateKode($skpd,2);

        if ($bendahara->status == 1) {
            $bendahara = SkpdBendahara::where("id_skpd",$skpd)->where("status",0)->where('actived',1)->first();
        }
        $skpdOtor = SkpdBendahara::where("id_skpd",$skpd)->where("status",1)->where('actived',1)->where("id",$bendahara->id_otorisator)->first();
        $kdPengantar = @$skpd_->kd_sipd ?? $skpd_->kd_skpd;
        $checkExistsBlud = BludSkpd::find($skpd_->kd_skpd);
        $lunas = @$jnsBayar == 0 ? 0 : 1;
        if($checkExistsBlud){
            $lunas = 1;
        }
        $bludSkpd = BludSkpd::where("kd_skpd","$skpd_->kd_skpd")->first();
        $data = [
            "skpd_kode"         => $skpd_->kd_skpd,
            "id_skpd"           => $skpd,
            "parent_id"         => $parent_->id,
            "sts_kode"          => $kodeSts,
            "sts_tgl"           => $tgl,
            "penerimaan_tgl"    => date("Y-m-d", strtotime($tgl_tbp)), // Tgl Synchronize
            "total"             => 0,
            "cr_bayar"          => $jnsBayar,
            "skpd_bend_id"      => @$bendahara->id ?? $bendahara->id ,
            "skpd_otor_id"      => $skpdOtor->id,
            "actived"           => 1,
            "jns_pendapatan"    => $skpd_->jenis_skpd,
            "nama_wp"           => $skpd_->nama_skpd,
            "alamat_wp"         => "",
            "uraian"            => $uraian,
            "lunas"             => $lunas,
            "no_sts_pengantar"  => $kodeSts .'/'.$kdPengantar.'/'.month(date('m')).'/'.date('Y'),
            "no_rekening"       => @$bludSkpd==null ? "0011007000":$bludSkpd->no_rekening,
        ];

        $stsRepo = new StsHeaderImplement(new Stsheader(),new SKPDBendaharaImplement(new SkpdBendahara()));
        $stsRepo->create($data);
        $sts = Stsheader::where('sts_kode',$kodeSts)->where("id_skpd",$skpd)->first();
        DB::commit();
        return $sts;

    } catch (\Throwable $th) {
        DB::rollBack();
        Notification::sendException($th);
        return null;
    }

}

function createdDetailTbpToSts($idSkpd, $idSys, $sts)
{
    $tbpRician = Tbprincian::where("skpd_id", $idSkpd)->whereIn("header_id", $idSys)
        ->select('header_id','kegiatan_id','rekening_id','subrekening_id','skode_kegiatan',DB::raw('SUM(nominal) AS total'))
        ->where('actived',1)
        ->groupBy(['kegiatan_id','rekening_id','subrekening_id','skode_kegiatan','header_id'])
        ->get();
    $i = 0;
    $skpd = Skpd::find($idSkpd);
    $tbpKode = [];
    foreach ($tbpRician as $value) {
        $tbp = Tbpheader::find($value->header_id);
        $stsRincian = Stsrincian::where('id_hdr',$sts->id)->where('id_skpd',$skpd->id)->where('subkegiatan_kode',$value->kegiatan_id)
            ->where('subkegiatan_id',$value->skode_kegiatan)->where('subrekening_kode',$value->subrekening_id)->first();
        if($stsRincian != null) {
            $stsRincian->sub_total +=$value->total;
            $stsRincian->tbp_kode = array_push($tbpKode, $stsRincian->tbp_kode,$tbp->tbp_kode);
            $stsRincian->save();
        }else{
            $data = [
                'id_hdr' => $sts->id,
                'skpd_kode' => $skpd->kd_skpd,
                'line_no' => ++$i ,
                'subkegiatan_kode' =>$value->kegiatan_id,
                'subkegiatan_id' => $value->skode_kegiatan,
                'subrekening_kode' => $value->subrekening_id,
                'sub_total' => $value->total,
                'tbp_kode' => $tbp->tbp_kode,
                'tbp_lineno' => NULL,
                'id_skpd' => $skpd->id,
            ];
            Stsrincian::create($data);
        }
    }
}

function month($month) {
    if ($month == 1) {
        $code = 'I';
    } else if ($month == 2) {
        $code  = 'II';
    } else if ($month == 3) {
        $code  = 'III';
    } else if ($month == 4) {
        $code  = 'IV';
    } else if ($month == 5) {
        $code  = 'V';
    } else if ($month == 6) {
        $code  = 'VI';
    } else if ($month == 7) {
        $code  = 'VII';
    } else if ($month == 8) {
        $code  = 'VIII';
    } else if ($month == 9) {
        $code  = 'IX';
    } else if ($month == 10) {
        $code  = 'X';
    } else if ($month == 11) {
        $code  = 'XI';
    } else if ($month == 12) {
        $code  = 'XII';
    }
    return $code;
}

function monthLabel($month) {
    if ($month == 1) {
        $code = 'Januari';
    } else if ($month == 2) {
        $code  = 'Febuari';
    } else if ($month == 3) {
        $code  = 'Maret';
    } else if ($month == 4) {
        $code  = 'April';
    } else if ($month == 5) {
        $code  = 'Mei';
    } else if ($month == 6) {
        $code  = 'Juni';
    } else if ($month == 7) {
        $code  = 'Juli';
    } else if ($month == 8) {
        $code  = 'Agustus';
    } else if ($month == 9) {
        $code  = 'September';
    } else if ($month == 10) {
        $code  = 'Oktober';
    } else if ($month == 11) {
        $code  = 'November';
    } else if ($month == 12) {
        $code  = 'Desember';
    }
    return $code;
}

// datediff function format
function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' ){
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);

    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($differenceFormat);
}

function realisasi($fromDate, $toDate, $id, $ppkd, $blud, $skode_) {
    // Jika yang melihat Admin / Penyelia
    if($id == "ALL") {
        $realisasi = DB::select(" SELECT
                            x.*,  ifnull((x.realisasi_bku/x.nominal)*100,0) AS procentase_bku, skpd.nama_skpd,tahapan.nama
                        FROM
                        (
                            SELECT
                                A.id,
                                A.skpd_id,
                                A.subkegiatan_id,
                                A.subkegiatan_skode,
                                A.subrekening_id,
                                A.tahapan_id,
                                A.nominal,
                                A.realisasi,
                                A.procentase,
                                A.ppkd,
                                A.blud,
                                A.actived,
                                ifnull((Select SUM(B.Total) FROM bkupenerimaan AS B where A.skpd_id = B.id_parent AND A.subkegiatan_id = B.kegiatan_id AND A.subkegiatan_skode = B.skegiatan_kode AND A.subrekening_id = B.rekening_id
                                    and B.bukti_tgl BETWEEN ? AND ? and B.bku_jenis = 1 and B.actived = 1 ),0) AS realisasi_bku,
                                B.subkegiatan_nama,
                                B.subkegiatan_kode,
                                C.kode_rekening,
                                C.uraian
                            FROM
                                anggaran AS A
                                INNER JOIN
                                kegiatan AS B
                                ON
                                    A.subkegiatan_id = B.id
                                INNER JOIN
                                source_rekening AS C
                                ON
                                    A.subrekening_id = C.id
                            WHERE
                                A.actived = 1
                                and A.ppkd = ? and A.blud = ?
                        ) as x
                        INNER JOIN
                            skpd
                        ON
                            x.skpd_id = skpd.id
                        INNER JOIN
                            tahapan
                        ON
                            x.tahapan_id = tahapan.id
                        INNER JOIN configs AS D on x.tahapan_id = D.tahapan_id
                        Order By x.skpd_id ASC" , [$fromDate, $toDate,  $ppkd, $blud]);
    }else{
        $skpd = Skpd::find($id);
        // Jika yang melihat dinas kesehatan (1 02 0100)
        if($skpd->kd_skpd == "1 02 0100" && $blud == 0) {
            $realisasi = DB::select(" SELECT
                            x.*,  ifnull((x.realisasi_bku/x.nominal)*100,0) AS procentase_bku, skpd.nama_skpd,tahapan.nama
                        FROM
                        (
                            SELECT
                                A.id,
                                A.skpd_id,
                                A.subkegiatan_id,
                                A.subkegiatan_skode,
                                A.subrekening_id,
                                A.tahapan_id,
                                A.nominal,
                                A.realisasi,
                                A.procentase,
                                A.ppkd,
                                A.blud,
                                A.actived,
                                ifnull((Select SUM(B.Total) FROM bkupenerimaan AS B where A.skpd_id = B.id_parent AND A.subkegiatan_id = B.kegiatan_id AND A.subkegiatan_skode = B.skegiatan_kode AND A.subrekening_id = B.rekening_id
                                    and B.bukti_tgl BETWEEN ? AND ? and B.bku_jenis = 1 and B.actived = 1 ),0) AS realisasi_bku,
                                B.subkegiatan_nama,
                                B.subkegiatan_kode,
                                C.kode_rekening,
                                C.uraian
                            FROM
                                anggaran AS A
                                INNER JOIN
                                kegiatan AS B
                                ON
                                    A.subkegiatan_id = B.id
                                INNER JOIN
                                source_rekening AS C
                                ON
                                    A.subrekening_id = C.id
                            WHERE
                                A.skpd_id = ? AND
                                A.actived = 1 AND
                                A.ppkd = 0
                        ) AS x
                        INNER JOIN
                            skpd
                        ON
                            x.skpd_id = skpd.id
                        INNER JOIN
                            tahapan
                        ON x.tahapan_id = tahapan.id
                        INNER JOIN configs AS D on x.tahapan_id = D.tahapan_id
                        Order By x.skpd_id ASC", [$fromDate, $toDate,  $id, $ppkd]);
        }else{
            $realisasi = DB::select(" SELECT
                            x.*,  ifnull((x.realisasi_bku/x.nominal)*100,0) AS procentase_bku, skpd.nama_skpd,tahapan.nama
                        FROM
                        (
                            SELECT
                                A.id,
                                A.skpd_id,
                                A.subkegiatan_id,
                                A.subkegiatan_skode,
                                A.subrekening_id,
                                A.tahapan_id,
                                A.nominal,
                                A.realisasi,
                                A.procentase,
                                A.ppkd,
                                A.blud,
                                A.actived,
                                ifnull((Select SUM(B.Total) FROM bkupenerimaan AS B where A.skpd_id = B.id_parent AND A.subkegiatan_id = B.kegiatan_id AND A.subkegiatan_skode = B.skegiatan_kode AND A.subrekening_id = B.rekening_id
                                    and B.bukti_tgl BETWEEN ? AND ? and B.bku_jenis = 1 and B.actived = 1 ),0) AS realisasi_bku,
                                B.subkegiatan_nama,
                                B.subkegiatan_kode,
                                C.kode_rekening,
                                C.uraian
                            FROM
                                anggaran AS A
                                INNER JOIN
                                kegiatan AS B
                                ON
                                    A.subkegiatan_id = B.id
                                INNER JOIN
                                source_rekening AS C
                                ON
                                    A.subrekening_id = C.id
                            WHERE
                                A.skpd_id = ?
                                and A.actived = 1
                                and A.ppkd = ? and A.blud = ? and A.subkegiatan_skode = ?
                        ) as x
                        INNER JOIN
                            skpd
                        ON
                            x.skpd_id = skpd.id
                        INNER JOIN
                            tahapan
                        ON
                            x.tahapan_id = tahapan.id
                            INNER JOIN configs AS D on x.tahapan_id = D.tahapan_id
                        Order By x.skpd_id ASC" , [$fromDate, $toDate,  $id, $ppkd, $blud, $skode_]);
        }
    }
    return $realisasi;
}

function reportBku($month, $id, $pembayaran)
{
    DB::statement("SET @row_number = 0");
    DB::statement("SET @prev_bku_no = NULL");
    DB::statement("SET @prev_id_skpd = NULL");
    DB::statement("SET @prev_bku_jenis = NULL");
    if($pembayaran == 99) {
        if( $id == "ALL") {
            $sql_bku = "SELECT
                                record_id,
                                xx.id_skpd,
                                bku_no,
                                bukti_tgl,
                                bukti_no,
                                rekening_kode,
                                uraian,
                                pembayaran,
                                xx.bku_jenis,
                                penerimaan,
                                pengeluaran,
                            CASE
                                WHEN xx.duplicate_row = 1 THEN
                                xx.penerimaan + xx.pengeluaran else 0
                            END AS AdjustedSaldo,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) AS total_penerimaan_bulan_lalu ,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) AS total_pengeluaran_bulan_lalu,
                            ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_bulan_berjalan,
                            ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_bulan_berjalan,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_saldo_berjalan,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_saldo_berjalan
                        FROM (
                            SELECT
                                *,
                                CASE
                                    WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                    ELSE @row_number := 0
                                END AS duplicate_row,
                                @prev_bku_no := bku_no AS bku_no_temp,
                                @prev_id_skpd := id_skpd AS id_skpd_temp,
                                @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                @row_number AS row_number
                            FROM (
                                SELECT
                                    bku_no,
                                    bukti_tgl,
                                    bukti_no,
                                    rekening_kode,
                                    uraian,
                                    pembayaran,
                                    (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                    (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                    id AS record_id,
                                    bku_jenis,
                                    id_skpd
                                FROM (
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month." and actived = 1 and status_jurnal > 0
                                UNION ALL
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 1 and month(bukti_tgl) = ".$month."  and actived = 1  and status_jurnal > 0
                                ) AS X
                                GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                ORDER BY bku_no ASC, bku_jenis ASC, id_skpd ASC
                            ) AS xx,
                            (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                        ) AS xx
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) < ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1  and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) = ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1  and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                        ORDER BY
                            bku_no ASC, bku_jenis ASC, xx.id_skpd ASC;
            ";

            $results = DB::select($sql_bku);
        }else{
            $sql_bku = "SELECT
                                record_id,
                                xx.id_skpd,
                                bku_no,
                                bukti_tgl,
                                bukti_no,
                                rekening_kode,
                                uraian,
                                pembayaran,
                                xx.bku_jenis,
                                penerimaan,
                                pengeluaran,
                            CASE
                                WHEN xx.duplicate_row = 1 THEN xx.penerimaan + xx.pengeluaran
                                ELSE 0
                            END AS AdjustedSaldo,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) AS total_penerimaan_bulan_lalu ,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) AS total_pengeluaran_bulan_lalu,
                            ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_bulan_berjalan,
                            ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_bulan_berjalan,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_saldo_berjalan,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_saldo_berjalan
                        FROM (
                            SELECT
                                *,
                                CASE
                                    WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                    ELSE @row_number := 0
                                END AS duplicate_row,
                                @prev_bku_no := bku_no AS bku_no_temp,
                                @prev_id_skpd := id_skpd AS id_skpd_temp,
                                @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                @row_number AS row_number
                            FROM (
                                SELECT
                                    bku_no,
                                    bukti_tgl,
                                    bukti_no,
                                    rekening_kode,
                                    uraian,
                                    pembayaran,
                                    (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                    (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                    id AS record_id,
                                    bku_jenis,
                                    id_skpd
                                FROM (
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month." and id_skpd = ".$id."  and actived = 1  and status_jurnal > 0
                                UNION ALL
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 1 and month(bukti_tgl)  = ".$month." and id_skpd = ".$id."  and actived = 1  and status_jurnal > 0
                                ) AS X
                                GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                ORDER BY bku_no ASC, bku_jenis ASC, id_skpd ASC
                            ) AS xx,
                            (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                        ) AS xx
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) < ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) = ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                        ORDER BY
                            bku_no ASC, bku_jenis ASC, xx.id_skpd ASC;
                    ";
            $results = DB::select($sql_bku);
        }
    }else{
        if( $id == "ALL") {
            $sql_bku = "SELECT
                                record_id,
                                xx.id_skpd,
                                bku_no,
                                bukti_tgl,
                                bukti_no,
                                rekening_kode,
                                uraian,
                                pembayaran,
                                xx.bku_jenis,
                                penerimaan,
                                pengeluaran,
                            CASE
                                WHEN xx.duplicate_row = 1 THEN
                                xx.penerimaan + xx.pengeluaran else 0
                            END AS AdjustedSaldo,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) AS total_penerimaan_bulan_lalu ,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) AS total_pengeluaran_bulan_lalu,
                            ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_bulan_berjalan,
                            ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_bulan_berjalan,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_saldo_berjalan,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_saldo_berjalan
                        FROM (
                            SELECT
                                *,
                                CASE
                                    WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                    ELSE @row_number := 0
                                END AS duplicate_row,
                                @prev_bku_no := bku_no AS bku_no_temp,
                                @prev_id_skpd := id_skpd AS id_skpd_temp,
                                @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                @row_number AS row_number
                            FROM (
                                SELECT
                                    bku_no,
                                    bukti_tgl,
                                    bukti_no,
                                    rekening_kode,
                                    uraian,
                                    pembayaran,
                                    (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                    (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                    id AS record_id,
                                    bku_jenis,
                                    id_skpd
                                FROM (
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month." and actived = 1 and pembayaran =".$pembayaran."  and status_jurnal > 0
                                UNION ALL
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 1 and month(bukti_tgl) = ".$month."  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                ) AS X
                                GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                ORDER BY bku_no ASC, bku_jenis ASC, id_skpd ASC
                            ) AS xx,
                            (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                        ) AS xx
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) < ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) = ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                        ORDER BY
                            bku_no ASC, bku_jenis ASC, xx.id_skpd ASC;
            ";

            $results = DB::select($sql_bku);
        }else{
            $sql_bku = "SELECT
                                record_id,
                                xx.id_skpd,
                                bku_no,
                                bukti_tgl,
                                bukti_no,
                                rekening_kode,
                                uraian,
                                pembayaran,
                                xx.bku_jenis,
                                penerimaan,
                                pengeluaran,
                            CASE
                                WHEN xx.duplicate_row = 1 THEN xx.penerimaan + xx.pengeluaran
                                ELSE 0
                            END AS AdjustedSaldo,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) AS total_penerimaan_bulan_lalu ,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) AS total_pengeluaran_bulan_lalu,
                            ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_bulan_berjalan,
                            ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_bulan_berjalan,
                            ifnull(saldo_bulan_lalu.total_penerimaan_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan,0) AS total_penerimaan_saldo_berjalan,
                            ifnull(saldo_bulan_lalu.total_pengeluaran_bulan_lalu,0) + ifnull(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan,0) AS total_pengeluaran_saldo_berjalan
                        FROM (
                            SELECT
                                *,
                                CASE
                                    WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                    ELSE @row_number := 0
                                END AS duplicate_row,
                                @prev_bku_no := bku_no AS bku_no_temp,
                                @prev_id_skpd := id_skpd AS id_skpd_temp,
                                @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                @row_number AS row_number
                            FROM (
                                SELECT
                                    bku_no,
                                    bukti_tgl,
                                    bukti_no,
                                    rekening_kode,
                                    uraian,
                                    pembayaran,
                                    (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                    (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                    id AS record_id,
                                    bku_jenis,
                                    id_skpd
                                FROM (
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month." and id_skpd = ".$id."  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                UNION ALL
                                    SELECT
                                        id,
                                        id_skpd,
                                        bku_no,
                                        bukti_tgl,
                                        bukti_no,
                                        rekening_kode,
                                        uraian,
                                        total,
                                        bku_jenis,
                                        pembayaran
                                    FROM bkupenerimaan
                                    WHERE bku_jenis = 1 and month(bukti_tgl)  = ".$month." and id_skpd = ".$id."  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                ) AS X
                                GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                ORDER BY bku_no ASC, bku_jenis ASC, id_skpd ASC
                            ) AS xx,
                            (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                        ) AS xx
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) < ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                        LEFT JOIN (
                            SELECT
                                id_skpd,
                                bku_jenis,
                                SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                            FROM bkupenerimaan
                            WHERE
                                MONTH(bukti_tgl) = ".$month."
                                AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                            GROUP BY id_skpd, bku_jenis
                        ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                        ORDER BY
                            bku_no ASC, bku_jenis ASC, xx.id_skpd ASC;
                    ";
            $results = DB::select($sql_bku);
        }
    }
    return $results;
}

function totalReportBku($month, $id, $pembayaran)
{
    if($pembayaran ==99) {
        if( $id == "ALL") {
            $total_sql_bku = "  SELECT sum(total_penerimaan_bulan_lalu) AS total_penerimaan_bulan_lalu,
                                    sum(total_pengeluaran_bulan_lalu) AS total_pengeluaran_bulan_lalu, sum(total_penerimaan_bulan_berjalan)  AS total_penerimaan_bulan_berjalan,
                                    sum(total_pengeluaran_bulan_berjalan) AS total_pengeluaran_bulan_berjalan, sum(total_penerimaan_saldo_berjalan) AS total_penerimaan_saldo_berjalan,
                                    sum(total_pengeluaran_saldo_berjalan) AS total_pengeluaran_saldo_berjalan
                                from (SELECT DISTINCT
                                        ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) AS total_penerimaan_bulan_lalu,
                                        ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) AS total_pengeluaran_bulan_lalu,
                                        ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_bulan_berjalan,
                                        ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_bulan_berjalan,
                                        ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_saldo_berjalan,
                                        ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_saldo_berjalan
                                    FROM (
                                        SELECT
                                            *,
                                            CASE
                                                WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                                ELSE @row_number := 0
                                            END AS duplicate_row,
                                            @prev_bku_no := bku_no AS bku_no_temp,
                                            @prev_id_skpd := id_skpd AS id_skpd_temp,
                                            @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                            @row_number AS row_number
                                        FROM (
                                            SELECT
                                                bku_no,
                                                bukti_tgl,
                                                bukti_no,
                                                rekening_kode,
                                                uraian,
                                                pembayaran,
                                                (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                                (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                                id AS record_id,
                                                bku_jenis,
                                                id_skpd
                                            FROM (
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month."  and actived = 1 and status_jurnal > 0
                                                UNION ALL
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 1 and month(bukti_tgl) = ".$month."  and actived = 1 and status_jurnal > 0
                                            ) AS X
                                            GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                            ORDER BY id ASC, id_skpd ASC, bku_no ASC, bku_jenis ASC
                                        ) AS xx,
                                        (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                                    ) AS xx
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) < ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) = ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                                ) AS xxx;
            ";

            $results = DB::select($total_sql_bku);
        }else{
            $total_sql_bku = "SELECT sum(total_penerimaan_bulan_lalu) AS total_penerimaan_bulan_lalu,
                                sum(total_pengeluaran_bulan_lalu) AS total_pengeluaran_bulan_lalu, sum(total_penerimaan_bulan_berjalan)  AS total_penerimaan_bulan_berjalan,
                                sum(total_pengeluaran_bulan_berjalan) AS total_pengeluaran_bulan_berjalan, sum(total_penerimaan_saldo_berjalan) AS total_penerimaan_saldo_berjalan,
                                sum(total_pengeluaran_saldo_berjalan) AS total_pengeluaran_saldo_berjalan
                                from (SELECT DISTINCT
                                            ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) AS total_penerimaan_bulan_lalu,
                                            ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) AS total_pengeluaran_bulan_lalu,
                                            ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_bulan_berjalan,
                                            ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_bulan_berjalan,
                                            ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_saldo_berjalan,
                                            ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_saldo_berjalan
                                    FROM (
                                        SELECT
                                            *,
                                            CASE
                                                WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                                ELSE @row_number := 0
                                            END AS duplicate_row,
                                            @prev_bku_no := bku_no AS bku_no_temp,
                                            @prev_id_skpd := id_skpd AS id_skpd_temp,
                                            @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                            @row_number AS row_number
                                        FROM (
                                            SELECT
                                                bku_no,
                                                bukti_tgl,
                                                bukti_no,
                                                rekening_kode,
                                                uraian,
                                                pembayaran,
                                                (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                                (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                                id AS record_id,
                                                bku_jenis,
                                                id_skpd
                                            FROM (
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month." and id_skpd = ".$id."  and actived = 1 and status_jurnal > 0
                                                UNION ALL
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 1 and month(bukti_tgl)  = ".$month." and id_skpd = ".$id."  and actived = 1 and status_jurnal > 0
                                            ) AS X
                                            GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                            ORDER BY id ASC, id_skpd ASC, bku_no ASC, bku_jenis ASC
                                        ) AS xx,
                                        (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                                    ) AS xx
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) < ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) = ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                                ) AS xxx ;
                    ";
            $results = DB::select($total_sql_bku);
        }
    }else{
        if( $id == "ALL") {
            $total_sql_bku = "  SELECT sum(total_penerimaan_bulan_lalu) AS total_penerimaan_bulan_lalu,
                                    sum(total_pengeluaran_bulan_lalu) AS total_pengeluaran_bulan_lalu, sum(total_penerimaan_bulan_berjalan)  AS total_penerimaan_bulan_berjalan,
                                    sum(total_pengeluaran_bulan_berjalan) AS total_pengeluaran_bulan_berjalan, sum(total_penerimaan_saldo_berjalan) AS total_penerimaan_saldo_berjalan,
                                    sum(total_pengeluaran_saldo_berjalan) AS total_pengeluaran_saldo_berjalan
                                from (SELECT DISTINCT
                                        ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) AS total_penerimaan_bulan_lalu,
                                        ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) AS total_pengeluaran_bulan_lalu,
                                        ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_bulan_berjalan,
                                        ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_bulan_berjalan,
                                        ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_saldo_berjalan,
                                        ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_saldo_berjalan
                                    FROM (
                                        SELECT
                                            *,
                                            CASE
                                                WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                                ELSE @row_number := 0
                                            END AS duplicate_row,
                                            @prev_bku_no := bku_no AS bku_no_temp,
                                            @prev_id_skpd := id_skpd AS id_skpd_temp,
                                            @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                            @row_number AS row_number
                                        FROM (
                                            SELECT
                                                bku_no,
                                                bukti_tgl,
                                                bukti_no,
                                                rekening_kode,
                                                uraian,
                                                pembayaran,
                                                (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                                (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                                id AS record_id,
                                                bku_jenis,
                                                id_skpd
                                            FROM (
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month."  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                                UNION ALL
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 1 and month(bukti_tgl) = ".$month."  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                            ) AS X
                                            GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                            ORDER BY id ASC, id_skpd ASC, bku_no ASC, bku_jenis ASC
                                        ) AS xx,
                                        (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                                    ) AS xx
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) < ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE())  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) = ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE()) and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                                ) AS xxx;
            ";

            $results = DB::select($total_sql_bku);
        }else{
            $total_sql_bku = "SELECT sum(total_penerimaan_bulan_lalu) AS total_penerimaan_bulan_lalu,
                                sum(total_pengeluaran_bulan_lalu) AS total_pengeluaran_bulan_lalu, sum(total_penerimaan_bulan_berjalan)  AS total_penerimaan_bulan_berjalan,
                                sum(total_pengeluaran_bulan_berjalan) AS total_pengeluaran_bulan_berjalan, sum(total_penerimaan_saldo_berjalan) AS total_penerimaan_saldo_berjalan,
                                sum(total_pengeluaran_saldo_berjalan) AS total_pengeluaran_saldo_berjalan
                                from (SELECT DISTINCT
                                            ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) AS total_penerimaan_bulan_lalu,
                                            ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) AS total_pengeluaran_bulan_lalu,
                                            ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_bulan_berjalan,
                                            ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_bulan_berjalan,
                                            ifnull( saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0 ) AS total_penerimaan_saldo_berjalan,
                                            ifnull( saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0 ) + ifnull( saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0 ) AS total_pengeluaran_saldo_berjalan
                                    FROM (
                                        SELECT
                                            *,
                                            CASE
                                                WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd THEN @row_number := 1
                                                ELSE @row_number := 0
                                            END AS duplicate_row,
                                            @prev_bku_no := bku_no AS bku_no_temp,
                                            @prev_id_skpd := id_skpd AS id_skpd_temp,
                                            @prev_bku_jenis := bku_jenis AS bku_jenis_temp,
                                            @row_number AS row_number
                                        FROM (
                                            SELECT
                                                bku_no,
                                                bukti_tgl,
                                                bukti_no,
                                                rekening_kode,
                                                uraian,
                                                pembayaran,
                                                (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
                                                (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
                                                id AS record_id,
                                                bku_jenis,
                                                id_skpd
                                            FROM (
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 0 and month(bukti_tgl) = ".$month." and id_skpd = ".$id."  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                                UNION ALL
                                                SELECT
                                                    id,
                                                    id_skpd,
                                                    bku_no,
                                                    bukti_tgl,
                                                    bukti_no,
                                                    rekening_kode,
                                                    uraian,
                                                    total,
                                                    bku_jenis,
                                                    pembayaran
                                                FROM bkupenerimaan
                                                WHERE bku_jenis = 1 and month(bukti_tgl)  = ".$month." and id_skpd = ".$id."  and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                            ) AS X
                                            GROUP BY id, id_skpd, bku_no, bukti_tgl, bku_jenis, bukti_no, rekening_kode, uraian, total, pembayaran
                                            ORDER BY id ASC, id_skpd ASC, bku_no ASC, bku_jenis ASC
                                        ) AS xx,
                                        (SELECT @row_number := 0, @prev_bku_no := NULL, @prev_id_skpd := NULL, @prev_bku_jenis := NULL) AS temp
                                    ) AS xx
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) < ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE()) and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_lalu ON saldo_bulan_lalu.id_skpd = xx.id_skpd AND saldo_bulan_lalu.bku_jenis = xx.bku_jenis
                                    LEFT JOIN (
                                        SELECT
                                            id_skpd,
                                            bku_jenis,
                                            SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
                                            SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan
                                        FROM bkupenerimaan
                                        WHERE
                                            MONTH(bukti_tgl) = ".$month."
                                            AND YEAR(bukti_tgl) = YEAR(CURRENT_DATE()) and actived = 1 and pembayaran =".$pembayaran." and status_jurnal > 0
                                        GROUP BY id_skpd, bku_jenis
                                    ) AS saldo_bulan_berjalan ON saldo_bulan_berjalan.id_skpd = xx.id_skpd AND saldo_bulan_berjalan.bku_jenis = xx.bku_jenis
                                ) AS xxx ;
                    ";
            $results = DB::select($total_sql_bku);
        }
    }
    return $results;
}

function checkMonthLabel($month)
{
    $bulan = 0;
    if(Str::upper($month) == "JANUARI") {
        $bulan = 1;
    }else if(Str::upper($month) == "FEBRUARI") {
        $bulan = 2;
    }else if(Str::upper($month) == "MARET") {
        $bulan = 3;
    }else if(Str::upper($month) == "APRIL") {
        $bulan = 4;
    }else if(Str::upper($month) == "MEI") {
        $bulan = 5;
    }else if(Str::upper($month) == "JUNI") {
        $bulan = 6;
    }else if(Str::upper($month) == "JULI") {
        $bulan = 7;
    }else if(Str::upper($month) == "AGUSTUS") {
        $bulan = 8;
    }else if(Str::upper($month) == "SEPTEMBER") {
        $bulan = 9;
    }else if(Str::upper($month) == "OKTOBER") {
        $bulan = 10;
    }else if(Str::upper($month) == "NOVEMBER") {
        $bulan = 11;
    }else if(Str::upper($month) == "DESEMBER") {
        $bulan = 12;
    }

    return $bulan;
}

/**
     * doCalculationBku
     *
     * @param  mixed $tahun
     * @param  mixed $bulan
     * @param  mixed $opd_kode
     * @return void
     */
function doCalculationBku($tahun, $bulan, $opd_id)
{
    $data_saldo_bln_lalu_pengeluaran = BkuPenerimaan::where('id_skpd',$opd_id)
        ->where('tahun',$tahun)
        ->where('bku_jenis',1)
        ->where('status_jurnal','>',0)
        ->where(DB::raw('month(bukti_tgl)'),'<',$bulan)
        ->where('actived',1)
        ->sum('total');

    $data_saldo_bln_ini_pengeluaran = BkuPenerimaan::where('id_skpd',$opd_id)
        ->where('tahun',$tahun)
        ->where('bku_jenis',1)
        ->where('status_jurnal','>',0)
        ->where(DB::raw('month(bukti_tgl)'),'=',$bulan)
        ->where('actived',1)
        ->sum('total');

    $data = [
            'saldo_bln_lalu' => $data_saldo_bln_lalu_pengeluaran,
            'saldo_bln_ini'  => $data_saldo_bln_ini_pengeluaran
    ];
    return $data;
}

function getSynchronizeStsByDate($date)
{
    $sql = " SELECT
                    A.id, A.sts_kode, A.no_sts, A.sts_tgl,
                    D.kd_skpd AS opd_kode, E.kd_skpd AS parent_skpd, A.uraian, A.cr_bayar,
                    C.subkegiatan_kode, C.subkegiatan_nama, C.kegiatan_skode, F.kode_rekening,
                    SUM( B.sub_total ) AS sub_total, A.actived
                FROM
                    stsheader AS A
                    INNER JOIN stsrincian AS B ON A.id = B.id_hdr
                    AND A.skpd_kode = B.skpd_kode
                    INNER JOIN kegiatan AS C ON B.subkegiatan_kode = C.id
                    INNER JOIN skpd AS D ON A.id_skpd = D.id
                    INNER JOIN skpd AS E ON A.parent_id = E.id
                    INNER JOIN source_rekening AS F ON B.subrekening_kode = F.id
                WHERE
                    A.sts_tgl <= '".$date."'
                GROUP BY
                    A.id, A.sts_kode, A.no_sts, A.sts_tgl,
                    D.kd_skpd, E.kd_skpd, A.uraian, A.cr_bayar,
                    C.subkegiatan_kode, C.subkegiatan_nama, C.kegiatan_skode, F.kode_rekening, A.actived
                ORDER BY
                    A.sts_tgl, A.sts_kode, A.id_skpd";
    $results = DB::select($sql);
    return $results;
}

function getSynchronizeBkuManualByDate($date)
{
    $sql = "SELECT
                A.bku_no, A.bukti_no, A.bukti_tgl, A.rekening_kode, A.kegiatan_kode,
                A.skegiatan_kode, A.id_parent, A.id_skpd, A.tahun,
                A.uraian, A.total, B.subkegiatan_nama, D.nama_skpd AS opd_nama,
                C.nama_skpd AS parent_nama, C.kd_skpd AS parent_kode, D.kd_skpd AS opd_kode, A.actived
            FROM
                bkupenerimaan AS A
                INNER JOIN kegiatan AS B ON A.kegiatan_id = B.id
                INNER JOIN skpd AS C ON A.id_parent = C.id
                INNER JOIN skpd AS D ON A.id_skpd = D.id
            Where source = 2 and bku_jenis = 1 and bukti_tgl <= '".$date."'";
    $results = DB::select($sql);
    return $results;
}

function getSynchronizeBkuTertundaByDate($date)
{
    $sql = "SELECT
                A.bku_no, A.bukti_no, A.bukti_tgl, A.rekening_kode, A.kegiatan_kode,
                A.skegiatan_kode, A.id_parent, A.id_skpd, A.tahun,
                A.uraian, A.total, B.subkegiatan_nama, D.nama_skpd AS opd_nama,
                C.nama_skpd AS parent_nama, C.kd_skpd AS parent_kode, D.kd_skpd AS opd_kode, A.actived
            FROM
                bkupenerimaan AS A
                INNER JOIN kegiatan AS B ON A.kegiatan_id = B.id
                INNER JOIN skpd AS C ON A.id_parent = C.id
                INNER JOIN skpd AS D ON A.id_skpd = D.id
            Where source = 1 and bku_jenis = 0 and bukti_tgl <= '".$date."'";
    $results = DB::select($sql);
    return $results;
}

function getFunctional($id, $ppkd, $blud, $skode_, $month, $_skpd, $pak) {
    // if($pak == 1) {
    $results = doFunctionalPak($id, $ppkd, $blud, $skode_,$month,$_skpd,$pak);
    // }else{
    //     if($id == "ALL") {
    //         $sql = "SELECT x.*, skpd.nama_skpd, tahapan.nama
    //                 FROM
    //                     (
    //                     SELECT
    //                         A.id,
    //                         A.skpd_id,
    //                         A.subkegiatan_id,
    //                         A.subkegiatan_skode,
    //                         A.subrekening_id,
    //                         A.tahapan_id,
    //                         A.nominal,
    //                         A.realisasi,
    //                         A.procentase,
    //                         A.ppkd,
    //                         A.blud,
    //                         A.actived,
    //                         B.subkegiatan_nama,
    //                         B.subkegiatan_kode,
    //                         C.kode_rekening,
    //                         C.uraian,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_sd_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_sd_now
    //                     FROM
    //                         anggaran AS A
    //                         INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
    //                         INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
    //                         -- INNER JOIN configs AS D on A.tahapan_id = D.tahapan_id
    //                     WHERE
    //                         A.actived = 1
    //                         AND A.ppkd = $ppkd
    //                         AND A.blud = $blud
    //                         AND A.skpd_id = $id
    //                         AND A.tahapan_id = 4
    //                     ) AS x
    //                     INNER JOIN skpd ON x.skpd_id = skpd.id
    //                     INNER JOIN tahapan ON x.tahapan_id = tahapan.id
    //                 ORDER BY
    //                     x.skpd_id ASC";
    //         $results = DB::select($sql);
    //     }else{
    //         $skpd = Skpd::find($id);
    //         // Jika yang melihat dinas kesehatan (1 02 0100)
    //         if($skpd->kd_skpd == "1 02 0100" && $blud == 0) {
    //             $sql = "SELECT x.*, skpd.nama_skpd, tahapan.nama
    //                 FROM
    //                     (
    //                     SELECT
    //                         A.id,
    //                         A.skpd_id,
    //                         A.subkegiatan_id,
    //                         A.subkegiatan_skode,
    //                         A.subrekening_id,
    //                         A.tahapan_id,
    //                         A.nominal,
    //                         A.realisasi,
    //                         A.procentase,
    //                         A.ppkd,
    //                         A.blud,
    //                         A.actived,
    //                         B.subkegiatan_nama,
    //                         B.subkegiatan_kode,
    //                         C.kode_rekening,
    //                         C.uraian,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_sd_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_sd_now
    //                     FROM
    //                         anggaran AS A
    //                         INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
    //                         INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
    //                         -- INNER JOIN configs AS D on A.tahapan_id = D.tahapan_id
    //                     WHERE
    //                         A.actived = 1
    //                         AND A.ppkd = $ppkd
    //                         AND A.blud = $blud
    //                         AND A.skpd_id = $id
    //                         AND A.subkegiatan_skode = $skode_
    //                         AND A.tahapan_id = 4
    //                     ) AS x
    //                     INNER JOIN skpd ON x.skpd_id = skpd.id
    //                     INNER JOIN tahapan ON x.tahapan_id = tahapan.id
    //                 ORDER BY
    //                     x.skpd_id ASC";

    //         }else{
    //             if($_skpd->kd_skpd == "1 07 0101") {
    //                 $sql = "SELECT x.*, skpd.nama_skpd, tahapan.nama
    //                 FROM
    //                     (
    //                     SELECT
    //                         A.id,
    //                         A.skpd_id,
    //                         A.subkegiatan_id,
    //                         A.subkegiatan_skode,
    //                         A.subrekening_id,
    //                         A.tahapan_id,
    //                         A.nominal,
    //                         A.realisasi,
    //                         A.procentase,
    //                         A.ppkd,
    //                         A.blud,
    //                         A.actived,
    //                         B.subkegiatan_nama,
    //                         B.subkegiatan_kode,
    //                         C.kode_rekening,
    //                         C.uraian,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_sd_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_sd_now
    //                     FROM
    //                         anggaran AS A
    //                         INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
    //                         INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
    //                         -- INNER JOIN configs AS D on A.tahapan_id = D.tahapan_id
    //                     WHERE
    //                         A.actived = 1
    //                         AND A.ppkd = $ppkd
    //                         AND A.blud = $blud
    //                         AND A.skpd_id = $id
    //                         AND A.subkegiatan_skode = $skode_
    //                         AND C.kode_rekening = '4.1.04.16.02.0001'
    //                         AND A.tahapan_id = 4
    //                     ) AS x
    //                     INNER JOIN skpd ON x.skpd_id = skpd.id
    //                     INNER JOIN tahapan ON x.tahapan_id = tahapan.id
    //                 ORDER BY
    //                     x.skpd_id ASC";
    //             }else if($_skpd->kd_skpd == "1 07 0102") {
    //                 $sql = "SELECT x.*, skpd.nama_skpd, tahapan.nama
    //                 FROM
    //                     (
    //                     SELECT
    //                         A.id,
    //                         A.skpd_id,
    //                         A.subkegiatan_id,
    //                         A.subkegiatan_skode,
    //                         A.subrekening_id,
    //                         A.tahapan_id,
    //                         A.nominal,
    //                         A.realisasi,
    //                         A.procentase,
    //                         A.ppkd,
    //                         A.blud,
    //                         A.actived,
    //                         B.subkegiatan_nama,
    //                         B.subkegiatan_kode,
    //                         C.kode_rekening,
    //                         C.uraian,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_sd_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_sd_now
    //                     FROM
    //                         anggaran AS A
    //                         INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
    //                         INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
    //                         -- INNER JOIN configs AS D on A.tahapan_id = D.tahapan_id
    //                     WHERE
    //                         A.actived = 1
    //                         AND A.ppkd = $ppkd
    //                         AND A.blud = $blud
    //                         AND A.skpd_id = $id
    //                         AND A.subkegiatan_skode = $skode_
    //                         AND C.kode_rekening in ('4.1.04.16.01.0001','4.1.02.01.04.0001','4.1.02.02.05.0001')
    //                         AND A.tahapan_id = 4
    //                     ) AS x
    //                     INNER JOIN skpd ON x.skpd_id = skpd.id
    //                     INNER JOIN tahapan ON x.tahapan_id = tahapan.id
    //                 ORDER BY
    //                     x.skpd_id ASC";
    //             }else if($_skpd->kd_skpd == "1 07 0100"){
    //                 $sql = "SELECT x.*, skpd.nama_skpd, tahapan.nama
    //                 FROM
    //                     (
    //                     SELECT
    //                         A.id,
    //                         A.skpd_id,
    //                         A.subkegiatan_id,
    //                         A.subkegiatan_skode,
    //                         A.subrekening_id,
    //                         A.tahapan_id,
    //                         A.nominal,
    //                         A.realisasi,
    //                         A.procentase,
    //                         A.ppkd,
    //                         A.blud,
    //                         A.actived,
    //                         B.subkegiatan_nama,
    //                         B.subkegiatan_kode,
    //                         C.kode_rekening,
    //                         C.uraian,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_skpd
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_skpd
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_skpd
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_skpd
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_skpd
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_sd_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_skpd
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_sd_now
    //                     FROM
    //                         anggaran AS A
    //                         INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
    //                         INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
    //                         -- INNER JOIN configs AS D on A.tahapan_id = D.tahapan_id
    //                     WHERE
    //                         A.actived = 1
    //                         AND A.ppkd = $ppkd
    //                         AND A.blud = $blud
    //                         AND A.skpd_id = $id
    //                         AND A.subkegiatan_skode = $skode_
    //                         AND A.tahapan_id = 4
    //                     ) AS x
    //                     INNER JOIN skpd ON x.skpd_id = skpd.id
    //                     INNER JOIN tahapan ON x.tahapan_id = tahapan.id
    //                 UNION
    //                     SELECT
    //                         x.*,
    //                         skpd.nama_skpd,
    //                         tahapan.nama
    //                     FROM
    //                         (
    //                         SELECT
    //                             A.id,
    //                             A.skpd_id,
    //                             A.subkegiatan_id,
    //                             A.subkegiatan_skode,
    //                             A.subrekening_id,
    //                             A.tahapan_id,
    //                             A.nominal,
    //                             A.realisasi,
    //                             A.procentase,
    //                             A.ppkd,
    //                             A.blud,
    //                             A.actived,
    //                             B.subkegiatan_nama,
    //                             B.subkegiatan_kode,
    //                             C.kode_rekening,
    //                             C.uraian,
    //                             ifnull((
    //                                 SELECT
    //                                     SUM( B.Total )
    //                                 FROM
    //                                     bkupenerimaan AS B
    //                                 WHERE
    //                                     A.skpd_id = B.id_skpd
    //                                     AND A.subkegiatan_id = B.kegiatan_id
    //                                     AND A.subkegiatan_skode = B.skegiatan_kode
    //                                     AND A.subrekening_id = B.rekening_id
    //                                     AND MONTH ( B.bukti_tgl ) < $month AND B.bku_jenis = 0 AND B.actived = 1 AND tahun = 2025 AND B.status_jurnal >= 1
    //                                     ),
    //                                 0
    //                             ) AS penerimaan_bku_lalu,
    //                             ifnull((
    //                                 SELECT
    //                                     SUM( B.Total )
    //                                 FROM
    //                                     bkupenerimaan AS B
    //                                 WHERE
    //                                     A.skpd_id = B.id_skpd
    //                                     AND A.subkegiatan_id = B.kegiatan_id
    //                                     AND A.subkegiatan_skode = B.skegiatan_kode
    //                                     AND A.subrekening_id = B.rekening_id
    //                                     AND MONTH ( B.bukti_tgl ) < $month AND B.bku_jenis = 1 AND B.actived = 1 AND tahun = 2025 AND B.status_jurnal >= 1
    //                                     ),
    //                                 0
    //                             ) AS pengeluaran_bku_lalu,
    //                             ifnull((
    //                                 SELECT CASE WHEN  B.bukti_tgl  > '2024-09-02' THEN
    //                                     0
    //                                     ELSE
    //                                         SUM( B.Total )
    //                                     END
    //                                 FROM
    //                                     bkupenerimaan AS B
    //                                 WHERE
    //                                     A.skpd_id = B.id_skpd
    //                                     AND A.subkegiatan_id = B.kegiatan_id
    //                                     AND A.subkegiatan_skode = B.skegiatan_kode
    //                                     AND A.subrekening_id = B.rekening_id
    //                                     AND MONTH ( B.bukti_tgl ) = $month
    //                                     AND B.bku_jenis = 0
    //                                     AND B.actived = 1
    //                                     AND tahun = 2025
    //                                     AND B.status_jurnal >= 1
    //                                     ),
    //                                 0
    //                             ) AS penerimaan_bku_now,
    //                             ifnull((
    //                                 SELECT CASE WHEN  B.bukti_tgl  > '2024-09-02' THEN
    //                                     0
    //                                     ELSE
    //                                         SUM( B.Total )
    //                                     END
    //                                 FROM
    //                                     bkupenerimaan AS B
    //                                 WHERE
    //                                     A.skpd_id = B.id_skpd
    //                                     AND A.subkegiatan_id = B.kegiatan_id
    //                                     AND A.subkegiatan_skode = B.skegiatan_kode
    //                                     AND A.subrekening_id = B.rekening_id
    //                                     AND MONTH ( B.bukti_tgl ) = $month
    //                                     AND B.bku_jenis = 1
    //                                     AND B.actived = 1
    //                                     AND tahun = 2025
    //                                     AND B.status_jurnal >= 1
    //                                     ),
    //                                 0
    //                             ) AS pengeluaran_bku_now,
    //                             ifnull((
    //                                 SELECT
    //                                     SUM( B.Total )
    //                                 FROM
    //                                     bkupenerimaan AS B
    //                                 WHERE
    //                                     A.skpd_id = B.id_skpd
    //                                     AND A.subkegiatan_id = B.kegiatan_id
    //                                     AND A.subkegiatan_skode = B.skegiatan_kode
    //                                     AND A.subrekening_id = B.rekening_id
    //                                     AND MONTH ( B.bukti_tgl ) <= $month
    //                                     AND B.bku_jenis = 0 AND B.actived = 1 AND tahun = 2025 AND B.status_jurnal >= 1
    //                                     ),
    //                                 0
    //                             ) AS penerimaan_bku_sd_now,
    //                             ifnull((
    //                                 SELECT
    //                                     SUM( B.Total )
    //                                 FROM
    //                                     bkupenerimaan AS B
    //                                 WHERE
    //                                     A.skpd_id = B.id_skpd
    //                                     AND A.subkegiatan_id = B.kegiatan_id
    //                                     AND A.subkegiatan_skode = B.skegiatan_kode
    //                                     AND A.subrekening_id = B.rekening_id
    //                                     AND MONTH ( B.bukti_tgl ) <= $month
    //                                     AND B.bku_jenis = 1 AND B.actived = 1 AND tahun = 2025 AND B.status_jurnal >= 1
    //                                     ),
    //                                 0
    //                             ) AS pengeluaran_bku_sd_now
    //                         FROM
    //                             anggaran AS A
    //                             INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
    //                             INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
    //                             INNER JOIN configs AS D ON A.tahapan_id = D.tahapan_id
    //                         WHERE
    //                             A.actived = 1
    //                             AND A.ppkd = 0
    //                             AND A.blud = 1
    //                             AND A.skpd_id = $id
    //                             AND A.subkegiatan_skode = $skode_
    //                             AND C.kode_rekening IN ( '4.1.02.01.04.0001', '4.1.02.02.05.0001' )
    //                             AND A.tahapan_id = 4
    //                         ) AS x
    //                         INNER JOIN skpd ON x.skpd_id = skpd.id
    //                         INNER JOIN tahapan ON x.tahapan_id = tahapan.id";

    //             }else{
    //             $sql = "SELECT x.*, skpd.nama_skpd, tahapan.nama
    //                 FROM
    //                     (
    //                     SELECT
    //                         A.id,
    //                         A.skpd_id,
    //                         A.subkegiatan_id,
    //                         A.subkegiatan_skode,
    //                         A.subrekening_id,
    //                         A.tahapan_id,
    //                         A.nominal,
    //                         A.realisasi,
    //                         A.procentase,
    //                         A.ppkd,
    //                         A.blud,
    //                         A.actived,
    //                         B.subkegiatan_nama,
    //                         B.subkegiatan_kode,
    //                         C.kode_rekening,
    //                         C.uraian,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) < $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_lalu,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) = $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 0
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS penerimaan_bku_sd_now,
    //                         ifnull((
    //                             SELECT
    //                                 SUM( B.Total )
    //                             FROM
    //                                 bkupenerimaan AS B
    //                             WHERE
    //                                 A.skpd_id = B.id_parent
    //                                 AND A.subkegiatan_id = B.kegiatan_id
    //                                 AND A.subkegiatan_skode = B.skegiatan_kode
    //                                 AND A.subrekening_id = B.rekening_id
    //                                 AND MONTH ( B.bukti_tgl ) <= $month
    //                                 AND B.bku_jenis = 1
    //                                 AND B.actived = 1
    //                                 AND tahun = 2025
    //                                 AND B.status_jurnal >= 1
    //                                 ),
    //                             0
    //                         ) AS pengeluaran_bku_sd_now
    //                     FROM
    //                         anggaran AS A
    //                         INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
    //                         INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
    //                         -- INNER JOIN configs AS D on A.tahapan_id = D.tahapan_id
    //                     WHERE
    //                         A.actived = 1
    //                         AND A.ppkd = $ppkd
    //                         AND A.blud = $blud
    //                         AND A.skpd_id = $id
    //                         AND A.subkegiatan_skode = $skode_
    //                         AND A.tahapan_id = 4
    //                     ) AS x
    //                     INNER JOIN skpd ON x.skpd_id = skpd.id
    //                     INNER JOIN tahapan ON x.tahapan_id = tahapan.id
    //                 ORDER BY
    //                     x.skpd_id ASC";
    //             }
    //         }
    //         // dd($sql);
    //         $results = DB::select($sql);
    //     }
    // }
    $allfunctionals = array_map(function ($value) {
            return (array)$value;
        }, $results);
        $allfunctionals = collect($allfunctionals);

    return $allfunctionals->sortBy('anggaran.kode_rekening');
    // return $results;
}

function statusUser($user) {
    $cekUserActived = SkpdBendahara::where("id_bendahara",$user->id)->where('actived',1)->first();
    if($user->hasRole("administrator")) {
        return "ADMINISTARTOR";
    }else if($user->hasRole("penyelia")) {
        return "PENYELIA";
    }else if($user->hasRole("sub_koor_perben_kas") || $user->hasRole("kuasa_bud")) {
        return "";
    }else{
        $statusUser = BendaharaOtorisator::where("id_skpd", $cekUserActived->id_skpd)->where("nip",$user->nip)->first();
        return @$statusUser!=null ? $statusUser->jabatan :"";
    }

}

function doGetBkuWithJurnalNotSuccess(Request $request) {
    return Bkupenerimaan::getAllBkuWithJurnalNotSuccess();
}

function doGetBkuWithJurnalNotSuccessWithOpdId($skpd, $buktiNo) {
    $bkuPenerimaans =  Bkupenerimaan::whereIn('status_jurnal', [1, 2])
    ->where('bku_jenis', 1)
    ->where('actived', 1)
    ->where('bud', 0)
    // ->whereIn('send', [0, 1])
    ->where('source', 3)
    ->where('id_skpd', $skpd)
    ->where('bukti_no', $buktiNo)
    ->orderByRaw('id_skpd asc, bukti_no asc, bukti_tgl asc')
    ->select(DB::raw("DISTINCT id_parent, id_skpd, bku_no, bku_jenis, bukti_no, bukti_tgl, uraian, kegiatan_kode, skegiatan_kode, tahun"))
    ->first(); // Thi
    return $bkuPenerimaans;
}

function doGetRincianBkuWithJurnalNotSuccess($idskpd,$buktiNo) {
    $result = Bkupenerimaan::where('status_jurnal', 1)
            ->where('bku_jenis',1)
            ->where('actived',1)
            ->where('bud',0)
            ->where('send',0)
            ->where('source',3)
            ->where('id_skpd',$idskpd)
            ->where('actived',1)
            ->where('bukti_no',$buktiNo)
            ->SELECT(DB::raw("DISTINCT rekening_id, total "));
        return $result;
}

function doGetRincianBkuWithJurnalNotSuccess2($idskpd,$buktiNo) {
    $result = Bkupenerimaan::whereIn('status_jurnal', [1,2])
            ->where('bku_jenis',1)
            ->where('actived',1)
            ->where('bud',0)
            //->whereIn('send',[0,1])
            ->where('source',3)
            ->where('id_skpd',$idskpd)
            ->where('actived',1)
            ->where('bukti_no',$buktiNo)
            ->SELECT("rekening_id",DB::raw("sum(total) AS total "))
            ->groupBy("rekening_id");
        return $result;
}
function doGetRincianBkuNoSyncAkt($idskpd,$buktiNo) {
    $result = Bkupenerimaan::where('status_jurnal', 0)
            ->where('bku_jenis',1)
            ->where('actived',1)
            ->where('bud',0)
            ->where('send',0)
            ->where('source',3)
            ->where('id_skpd',$idskpd)
            ->where('actived',1)
            ->where('bukti_no',$buktiNo)
            ->SELECT(DB::raw("DISTINCT rekening_id, total "));
        return $result;
}
function doGetBkuWithAllJurnal(Request $request) {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', '0');
    $bkuRepo = new BkuImplement(new Bkupenerimaan());
    $bkus =  Bkupenerimaan::getAllBkuWithJurnalNotSuccess();
    $bkus = $bkus->where('id_skpd',$request->skpd_id_sync)->get();
    $setting = Configs::find(1);
    $_jnskpd = SKPD::find($request->skpd_id_sync);
    foreach ($bkus as $value) {
        $inirekening = [];
        $_rekening = SourceRekening::find($value->rekening_id);
        $rekening = ['kode'  => $_rekening->kode_rekening,'nominal'=> $value->total];

        $existsBlud = BludSkpd::whereKdSkpd($_jnskpd->kd_skpd)->first();
        if($existsBlud == null) {
            array_push($inirekening, $rekening);
            try {
                /**
                 * @since 06-09-2021
                 * @see update user blud transaction bku dishub
                 * @author Afes Oktavianus
                 */
                if ($_jnskpd->jenis_skpd == 2)  {
                    $source = 'STS_BUD';
                }else{
                    $source = 'TBP';
                }

                try {
                    $sendSts = Sts::new()
                    ->setTahun($value->tahun)
                    ->setOpdKode(@$_jnskpd->kd_skpd=="1 07 0101"?$_jnskpd->parent:$_jnskpd->kd_skpd)
                    ->setStsKode($value->bukti_no)
                    ->setStsJenis(0)
                    ->setStsTgl($value->bukti_tgl)
                    ->setUraian($value->uraian)
                    ->setReferensiKode('')
                    ->setIsian('')
                    ->setSource($source)
                    ->setKegiatanKode($value->kegiatan_kode)
                    ->setIdBapeko($value->skegiatan_kode)
                    ->setRekening(
                        $inirekening
                    )
                    ->setPotongan([])
                    ->post($setting->url_sync_api_ecounting,[]);
                }catch(\Exception $ex){
                    $error = 1;
                    $message = $ex->getMessage();
                    Notification::sendException($ex);
                    break;
                }

                $response = $sendSts->getResponse()['status'];
                if ($response == "0" || $response == false) {

                    Notification::sendMessageBku(json_encode($sendSts->getResponse()));
                }else{
                    Notification::sendMessageBku("Berhasil Synchronize BKU ".$_jnskpd->kd_skpd." STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :".Auth::user()->id);
                }
            }catch(Exception $ex) {
                Notification::sendException($ex);
            }
        }else{
            Notification::sendMessageBku("Berhasil Synchronize BKU ".$_jnskpd->kd_skpd." STS ke eaccounting dengan STS nomer ".$value->bukti_no." pada tanggal ".Carbon::now()." Oleh user :".Auth::user()->id);
        }
        DB::update("update bkupenerimaan set send = 1 where bku_no=".$value->bku_no);
    }
}


/**
 * doCalculationBku
 *
 * @param  mixed $tahun
 * @param  mixed $bulan
 * @param  mixed $opd_kode
 * @return void
 */
function doCalculationBkuWithRekening($tahun, $bulan, $opd_id)
{
    if($opd_id == 3) {
        $data_rekening = DB::select("
        SELECT `rekening_kode`
        FROM `bkupenerimaan`
        WHERE
           `id_skpd` = ?
            AND `tahun` = ?
            AND `bku_jenis` in (0,1)
            AND `status_jurnal` > 0
            AND MONTH(bukti_tgl) = ?
            AND `actived` = 1
        GROUP BY `rekening_kode`
        UNION
        SELECT '4.1.04.16.01.0001' AS rekening_kode
    ",[$opd_id,$tahun,$bulan]);
    }else {
        $data_rekening = DB::select("
        SELECT `rekening_kode`
        FROM `bkupenerimaan`
        WHERE
           `id_skpd` = ?
            AND `tahun` = ?
            AND `bku_jenis` in (0,1)
            AND `status_jurnal` > 0
            AND MONTH(bukti_tgl) = ?
            AND `actived` = 1
        GROUP BY `rekening_kode`
    ",[$opd_id,$tahun,$bulan]);
    }


    $records = [];
    foreach ($data_rekening as $value) {
      $data_saldo_bln_ini_pengeluaran=   BkuPenerimaan::where('id_skpd',$opd_id)
        ->where('tahun',$tahun)
        ->where('bku_jenis',1)
        ->where('status_jurnal','>',0)
        ->where(DB::raw('month(bukti_tgl)'),$bulan)
        ->where('actived',1)
        ->where('rekening_kode',$value->rekening_kode)
        ->sum('total');
        $data_saldo_bln_lalu_pengeluaran = BkuPenerimaan::where('id_skpd',$opd_id)
        ->where('tahun',$tahun)
        ->where('bku_jenis',1)
        ->where('status_jurnal','>',0)
        ->where(DB::raw('month(bukti_tgl)'),'<',$bulan)
        ->where('actived',1)
        // ->where('source',3)
        ->where('rekening_kode',$value->rekening_kode)
        ->sum('total');
        $data_saldo_bln_ini_penerimaan=   BkuPenerimaan::where('id_skpd',$opd_id)
        ->where('tahun',$tahun)
        ->where('bku_jenis',0)
        ->where('status_jurnal','>',0)
        ->where(DB::raw('month(bukti_tgl)'),$bulan)
        ->where('actived',1)
        ->where('source',1)
        ->where('rekening_kode',$value->rekening_kode)
        ->sum('total');
        // $data_saldo_bln_ini_penerimaan_asset=   BkuPenerimaan::where('id_skpd',$opd_id)
        // ->where('tahun',$tahun)
        // ->where('bku_jenis',0)
        // ->where('status_jurnal','>',0)
        // ->where(DB::raw('month(bukti_tgl)'),$bulan)
        // ->where('actived',1)
        // ->where('source',2)
        // ->where('rekening_kode','1.1.01.04.01.0001')
        // ->sum('total');
        $data_saldo_bln_lalu_penerimaan = BkuPenerimaan::where('id_skpd',$opd_id)
        ->where('tahun',$tahun)
        ->where('bku_jenis',0)
        ->where('status_jurnal','>',0)
        ->where(DB::raw('month(bukti_tgl)'),'<',$bulan)
        ->where('actived',1)
        ->where('source',3)
        ->where('rekening_kode',$value->rekening_kode)
        ->sum('total');
        $data = [
            'kode_rekening' => $value->rekening_kode,
            'saldo_bln_ini' => $data_saldo_bln_ini_pengeluaran+$data_saldo_bln_ini_penerimaan,
            'saldo_bln_lalu' => $data_saldo_bln_lalu_pengeluaran+$data_saldo_bln_lalu_penerimaan
        ];
        array_push($records,$data);
    }

    return $records;
}

function clearAllCache()
{
    Cache::flush();
    return response()->json(['message' => 'All cache cleared successfully.']);
}

function exportBkuTipeBank($month, $id) {
    $currentYear = 2025;
    // Main Query
    $data = DB::table(DB::raw('(SELECT
    id_skpd,
    bku_no,
    bukti_tgl,
    bukti_no,
    rekening_kode,
    uraian,
    pembayaran,
    (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
    (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
    id AS record_id,
    bku_jenis,
    CASE
        WHEN @prev_bku_no = bku_no AND @prev_id_skpd = id_skpd
        THEN @row_number := 1
        ELSE @row_number := 0
    END AS duplicate_row,
    @prev_bku_no := bku_no,
    @prev_id_skpd := id_skpd,
    @prev_bku_jenis := bku_jenis
    FROM (
    SELECT id, id_skpd, bku_no, bukti_tgl, bukti_no, rekening_kode, uraian, total, bku_jenis, pembayaran
    FROM bkupenerimaan
    WHERE (bku_jenis = 0 OR bku_jenis = 1)
        AND MONTH(bukti_tgl) = '.$month.'
        AND id_skpd = '.$id.'
        AND actived = 1
        AND pembayaran = 1
        AND status_jurnal > 0
    ) AS X
    ORDER BY bku_no ASC, bku_jenis ASC, id_skpd ASC
    ) AS xx'))
    ->mergeBindings(DB::table('bkupenerimaan')) // Bind parameters safely
    ->addSelect([
    'xx.record_id', 'xx.id_skpd', 'xx.bku_no', 'xx.bukti_tgl', 'xx.bukti_no',
    'xx.rekening_kode', 'xx.uraian', 'xx.pembayaran', 'xx.bku_jenis',
    'xx.penerimaan', 'xx.pengeluaran',
    DB::raw('CASE WHEN xx.duplicate_row = 1 THEN xx.penerimaan + xx.pengeluaran ELSE 0 END AS AdjustedSaldo'),
    DB::raw('IFNULL(saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0) AS total_penerimaan_bulan_lalu'),
    DB::raw('IFNULL(saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0) AS total_pengeluaran_bulan_lalu'),
    DB::raw('IFNULL(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0) AS total_penerimaan_bulan_berjalan'),
    DB::raw('IFNULL(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0) AS total_pengeluaran_bulan_berjalan'),
    DB::raw('(IFNULL(saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0) + IFNULL(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0)) AS total_penerimaan_saldo_berjalan'),
    DB::raw('(IFNULL(saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0) + IFNULL(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0)) AS total_pengeluaran_saldo_berjalan')
    ])
    ->leftJoinSub(
    DB::table('bkupenerimaan')
    ->selectRaw('id_skpd, bku_jenis,
        SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_lalu,
        SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_lalu')
    ->whereRaw('MONTH(bukti_tgl) < ?', [$month])
    ->whereRaw('YEAR(bukti_tgl) = ?', [$currentYear])
    ->where('actived', 1)
    ->where('status_jurnal', '>', 0)
    ->groupBy('id_skpd', 'bku_jenis'),
    'saldo_bulan_lalu',
    function ($join) {
    $join->on('saldo_bulan_lalu.id_skpd', '=', 'xx.id_skpd')
        ->on('saldo_bulan_lalu.bku_jenis', '=', 'xx.bku_jenis');
    }
    )
    ->leftJoinSub(
    DB::table('bkupenerimaan')
    ->selectRaw('id_skpd, bku_jenis,
        SUM(CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS total_penerimaan_bulan_berjalan,
        SUM(CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS total_pengeluaran_bulan_berjalan')
    ->whereRaw('MONTH(bukti_tgl) = ?', [$month])
    ->whereRaw('YEAR(bukti_tgl) = ?', [$currentYear])
    ->where('actived', 1)
    ->where('status_jurnal', '>', 0)
    ->groupBy('id_skpd', 'bku_jenis'),
    'saldo_bulan_berjalan',
    function ($join) {
    $join->on('saldo_bulan_berjalan.id_skpd', '=', 'xx.id_skpd')
        ->on('saldo_bulan_berjalan.bku_jenis', '=', 'xx.bku_jenis');
    }
    )
    ->orderBy('bku_no')
    ->orderBy('bku_jenis')
    ->orderBy('xx.id_skpd')
    ->get();
    return $data;
}

function exportTotalBkuTipeBank($month, $id) {
    $currentYear = 2025;
    // Main Query
    $data = DB::table(DB::raw('(WITH numbered_rows AS (
        SELECT
            id_skpd,
            bku_no,
            bukti_tgl,
            bukti_no,
            rekening_kode,
            uraian,
            pembayaran,
            (CASE WHEN bku_jenis = 0 THEN total ELSE 0 END) AS penerimaan,
            (CASE WHEN bku_jenis = 1 THEN total ELSE 0 END) AS pengeluaran,
            id AS record_id,
            bku_jenis,
            ROW_NUMBER() OVER (PARTITION BY id_skpd, bku_no ORDER BY bku_jenis) AS row_number
        FROM bkupenerimaan
        WHERE (bku_jenis = 0 OR bku_jenis = 1)
            AND MONTH(bukti_tgl) = ?
            AND id_skpd = ?
            AND YEAR(bukti_tgl) = ?
            AND actived = 1
            AND pembayaran = 1
            AND status_jurnal > 0
    )
    SELECT nr.*,
        IFNULL(saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0) AS total_penerimaan_bulan_lalu,
        IFNULL(saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0) AS total_pengeluaran_bulan_lalu,
        IFNULL(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0) AS total_penerimaan_bulan_berjalan,
        IFNULL(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0) AS total_pengeluaran_bulan_berjalan,
        (IFNULL(saldo_bulan_lalu.total_penerimaan_bulan_lalu, 0) + IFNULL(saldo_bulan_berjalan.total_penerimaan_bulan_berjalan, 0)) AS total_penerimaan_saldo_berjalan,
        (IFNULL(saldo_bulan_lalu.total_pengeluaran_bulan_lalu, 0) + IFNULL(saldo_bulan_berjalan.total_pengeluaran_bulan_berjalan, 0)) AS total_pengeluaran_saldo_berjalan
    FROM numbered_rows nr
    ORDER BY nr.bku_no, nr.bku_jenis, nr.id_skpd
    ) AS xx'))
    ->mergeBindings(DB::table('bkupenerimaan')) // Bind parameters safely
    ->setBindings([$month, $id, $currentYear])
    ->get();
    return $data;
}

function doFunctionalPak($id,$ppkd,$blud,$skode_,$month,$_skpd,$pak) {
    if($id == "ALL") {
        $results= doFunctionalPakAdmin($pak,$month,$ppkd,$blud,$skode_,$id);
    }else{
        $skpd = Skpd::find($id);
        // Jika yang melihat dinas kesehatan (1 02 0100)
        if($skpd->kd_skpd == "1 02 0100" && $blud == 0) {
            $results= doFunctionalPakDinkes($pak,$month,$ppkd,$blud,$skode_,$id);
        }else{
            if($_skpd->kd_skpd == "1 07 0101") {
                $results= doFunctionalPakBludDishub($pak,$month,$ppkd,$blud,$skode_,$id);
            }else if($_skpd->kd_skpd == "1 07 0102") {
                $results=doFunctionalPakBludDishubParkir($pak,$month,$ppkd,$blud,$skode_,$id);
            }else if($_skpd->kd_skpd == "1 07 0100"){
                $results=doFunctionalPakDishub($pak,$month,$ppkd,$blud,$skode_,$id);
            }else{
                $results=doFunctionalPenghasil($pak,$month,$ppkd,$blud,$skode_,$id);
            }
        }
    }
    return $results;
}

function doFunctionalPakAdmin($pak,$month,$ppkd,$blud,$skode_,$id) {
    $sql = "
        SELECT
            A.id,
            A.skpd_id,
            A.subkegiatan_id,
            A.subkegiatan_skode,
            A.subrekening_id,
            A.tahapan_id,
            A.nominal,
            A.realisasi,
            A.procentase,
            A.ppkd,
            A.blud,
            A.actived,
            B.subkegiatan_nama,
            B.subkegiatan_kode,
            C.kode_rekening,
            C.uraian,
            skpd.nama_skpd,
            " . ($pak == 1 ? "D.nama AS nama_tahapan," : "") . "

            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_sd_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_sd_now

        FROM anggaran AS A
        INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
        INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
        INNER JOIN skpd ON A.skpd_id = skpd.id
        " . ($pak == 1 ? "INNER JOIN tahapan AS D ON A.tahapan_id = D.id" : "") . "

        LEFT JOIN bkupenerimaan AS BK
            ON A.skpd_id = BK.id_parent
            AND A.subkegiatan_id = BK.kegiatan_id
            AND A.subkegiatan_skode = BK.skegiatan_kode
            AND A.subrekening_id = BK.rekening_id
            AND BK.tahun = 2025
            AND BK.actived = 1
            AND BK.status_jurnal >= 1
            AND MONTH(BK.bukti_tgl) <= $month

        WHERE A.actived = 1
        AND A.ppkd = $ppkd
        AND A.blud = $blud
        AND A.skpd_id = $id
        AND A.subkegiatan_skode = $skode_
        " . ($pak == 1 ? "AND D.nama LIKE '%PAK%'" : "") . "

        GROUP BY A.id, A.skpd_id, A.subkegiatan_id, A.subkegiatan_skode, A.subrekening_id,
                A.tahapan_id, A.nominal, A.realisasi, A.procentase, A.ppkd, A.blud, A.actived,
                B.subkegiatan_nama, B.subkegiatan_kode, C.kode_rekening, C.uraian,
                skpd.nama_skpd" . ($pak == 1 ? ", D.nama" : "") . "

        ORDER BY C.kode_rekening ASC;";


    return DB::select($sql);
}

function doFunctionalPakBludDishub($pak,$month,$ppkd,$blud,$skode_,$id){
    $sql = "
        SELECT
            A.id,
            A.skpd_id,
            A.subkegiatan_id,
            A.subkegiatan_skode,
            A.subrekening_id,
            A.tahapan_id,
            A.nominal,
            A.realisasi,
            A.procentase,
            A.ppkd,
            A.blud,
            A.actived,
            B.subkegiatan_nama,
            B.subkegiatan_kode,
            C.kode_rekening,
            C.uraian,
            skpd.nama_skpd,
            " . ($pak == 1 ? "D.nama AS nama_tahapan," : "") . "

            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 0 AND A.blud = 1 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 1 AND A.blud = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 0 AND A.blud = 1 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 1 AND A.blud = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 0 AND A.blud = 1 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_sd_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 1 AND A.blud = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_sd_now

        FROM anggaran AS A
        INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
        INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
        INNER JOIN skpd ON A.skpd_id = skpd.id
         " . ($pak == 1 ? "INNER JOIN tahapan AS D ON A.tahapan_id = D.id" : "") . "

        LEFT JOIN bkupenerimaan AS BK
            ON A.skpd_id = BK.id_parent
            AND BK.id_skpd = 78
            AND A.subkegiatan_id = BK.kegiatan_id
            AND A.subkegiatan_skode = BK.skegiatan_kode
            AND A.subrekening_id = BK.rekening_id
            AND BK.tahun = 2025
            AND BK.actived = 1
            AND BK.status_jurnal >= 1

        WHERE A.actived = 1
        AND A.ppkd = 0
        AND A.blud = 1
        AND A.skpd_id = $id
        AND A.subkegiatan_skode = $skode_
        AND (
        (C.kode_rekening = '4.1.04.16.06.0001' AND A.nominal = 33016246)  -- Hanya rekening ini yang ditampilkan dengan nominal 15.000.000
        OR
        (C.kode_rekening IN ( '4.1.02.02.20.0001', '4.1.04.16.04.0001', '4.1.04.16.06.0002' )))
         " . ($pak == 1 ? "AND D.nama LIKE '%PAK%'" : "") . "

         GROUP BY A.id, A.skpd_id, A.subkegiatan_id, A.subkegiatan_skode, A.subrekening_id,
                A.tahapan_id, A.nominal, A.realisasi, A.procentase, A.ppkd, A.blud, A.actived,
                B.subkegiatan_nama, B.subkegiatan_kode, C.kode_rekening, C.uraian,
                skpd.nama_skpd " . ($pak == 1 ? ", D.nama " : "") . "

        ORDER BY C.kode_rekening ASC;";

    return DB::select($sql);
}

function doFunctionalPakBludDishubParkir($pak,$month,$ppkd,$blud,$skode_,$id){
    $sql = "
        SELECT
            A.id,
            A.skpd_id,
            A.subkegiatan_id,
            A.subkegiatan_skode,
            A.subrekening_id,
            A.tahapan_id,
            A.nominal,
            A.realisasi,
            A.procentase,
            A.ppkd,
            A.blud,
            A.actived,
            B.subkegiatan_nama,
            B.subkegiatan_kode,
            C.kode_rekening,
            C.uraian,
            skpd.nama_skpd,
            " . ($pak == 1 ? "D.nama AS nama_tahapan," : "") . "

            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 0 AND A.bLUD= 1 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 1 AND A.bLUD= 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 0 AND A.bLUD= 1 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 1 AND A.bLUD= 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 0 AND A.bLUD= 1 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_sd_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 1 AND A.bLUD= 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_sd_now

        FROM anggaran AS A
        INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
        INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
        INNER JOIN skpd ON A.skpd_id = skpd.id
        " . ($pak == 1 ? "INNER JOIN tahapan AS D ON A.tahapan_id = D.id" : "") . "

        LEFT JOIN bkupenerimaan AS BK
            ON A.skpd_id = BK.id_parent
            AND BK.id_skpd = 126
            AND A.subkegiatan_id = BK.kegiatan_id
            AND A.subkegiatan_skode = BK.skegiatan_kode
            AND A.subrekening_id = BK.rekening_id
            AND BK.tahun = 2025
            AND BK.actived = 1
            AND BK.status_jurnal >= 1

        WHERE A.actived = 1
        AND A.ppkd = $ppkd
        AND A.blud = $blud
        AND A.skpd_id = $id
        AND A.subkegiatan_skode = $skode_
        AND (
        (C.kode_rekening = '4.1.04.16.06.0001' AND A.nominal = 15000000)  -- Hanya rekening ini yang ditampilkan dengan nominal 15.000.000
        OR
        (C.kode_rekening IN ( '4.1.02.01.04.0001', '4.1.02.02.05.0001', '1.1.01.04.01.0001'))  -- Rekening lainnya tetap ditampilkan
    )

        " . ($pak == 1 ? "AND D.nama LIKE '%PAK%'" : "") . "

        GROUP BY A.id, A.skpd_id, A.subkegiatan_id, A.subkegiatan_skode, A.subrekening_id,
                A.tahapan_id, A.nominal, A.realisasi, A.procentase, A.ppkd, A.blud, A.actived,
                B.subkegiatan_nama, B.subkegiatan_kode, C.kode_rekening, C.uraian,
                skpd.nama_skpd " . ($pak == 1 ? ", D.nama" : "") . "

        ORDER BY C.kode_rekening ASC;";

    return DB::select($sql);;
}

function doFunctionalPakDishub($pak,$month,$ppkd,$blud,$skode_,$id) {
    $sql = "
        SELECT
            A.id,
            A.skpd_id,
            A.subkegiatan_id,
            A.subkegiatan_skode,
            A.subrekening_id,
            A.tahapan_id,
            A.nominal,
            A.realisasi,
            A.procentase,
            A.ppkd,
            A.blud,
            A.actived,
            B.subkegiatan_nama,
            B.subkegiatan_kode,
            C.kode_rekening,
            C.uraian,
            skpd.nama_skpd,
            " . ($pak == 1 ? "D.nama AS nama_tahapan," : "") . "

            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 0 AND A.blud = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 1 AND A.blud = 0 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 0 AND A.blud = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 1 AND A.blud = 0 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 0 AND A.blud = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_sd_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 1 AND A.blud = 0 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_sd_now

        FROM anggaran AS A
        INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
        INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
        INNER JOIN skpd ON A.skpd_id = skpd.id
        " . ($pak == 1 ? "INNER JOIN tahapan AS D ON A.tahapan_id = D.id" : "") . "

        LEFT JOIN bkupenerimaan AS BK
            ON A.skpd_id = BK.id_skpd
            AND A.subkegiatan_id = BK.kegiatan_id
            AND A.subkegiatan_skode = BK.skegiatan_kode
            AND A.subrekening_id = BK.rekening_id
            AND BK.tahun = 2025
            AND BK.actived = 1
            AND BK.status_jurnal >= 1

        WHERE A.actived = 1
        AND A.ppkd = 0
        AND A.blud = 0
        AND A.skpd_id = $id
        AND A.subkegiatan_skode = $skode_
        " . ($pak == 1 ? "AND D.nama LIKE '%PAK%'" : "") . "

        GROUP BY A.id, A.skpd_id, A.subkegiatan_id, A.subkegiatan_skode, A.subrekening_id,
                A.tahapan_id, A.nominal, A.realisasi, A.procentase, A.ppkd, A.blud, A.actived,
                B.subkegiatan_nama, B.subkegiatan_kode, C.kode_rekening, C.uraian,
                skpd.nama_skpd" . ($pak == 1 ? ", D.nama" : "") . "

        ORDER BY C.kode_rekening ASC;";
    return DB::select($sql);
}

function doFunctionalPakDinkes($pak,$month,$ppkd,$blud,$skode_,$id) {
    $sql = "
        SELECT
            A.id,
            A.skpd_id,
            A.subkegiatan_id,
            A.subkegiatan_skode,
            A.subrekening_id,
            A.tahapan_id,
            A.nominal,
            A.realisasi,
            A.procentase,
            A.ppkd,
            A.blud,
            A.actived,
            B.subkegiatan_nama,
            B.subkegiatan_kode,
            C.kode_rekening,
            concat(C.uraian,' ',SUBString_index(B.subkegiatan_nama,' ',-1)) as uraian,
            skpd.nama_skpd,
            " . ($pak == 1 ? "D.nama AS nama_tahapan," : "") . "
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_sd_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_sd_now

        FROM anggaran AS A
        INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
        INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
        INNER JOIN skpd ON A.skpd_id = skpd.id
        " . ($pak == 1 ? "INNER JOIN tahapan AS D ON A.tahapan_id = D.id" : "") . "
        LEFT JOIN bkupenerimaan AS BK
            ON A.skpd_id = BK.id_parent
            AND A.subkegiatan_id = BK.kegiatan_id
            AND A.subkegiatan_skode = BK.skegiatan_kode
            AND A.subrekening_id = BK.rekening_id
            AND BK.tahun = 2025
            AND BK.actived = 1
            AND BK.status_jurnal >= 1

        WHERE A.actived = 1
        AND A.ppkd = $ppkd
        AND A.blud = 1
        AND A.skpd_id = $id
        AND A.subkegiatan_skode = $skode_
        " . ($pak == 1 ? "AND D.nama LIKE '%PAK%'" : "") . "

        GROUP BY A.id, A.skpd_id, A.subkegiatan_id, A.subkegiatan_skode, A.subrekening_id,
                A.tahapan_id, A.nominal, A.realisasi, A.procentase, A.ppkd, A.blud, A.actived,
                B.subkegiatan_nama, B.subkegiatan_kode, C.kode_rekening, C.uraian,
                skpd.nama_skpd" . ($pak == 1 ? ", D.nama" : "") . "

        ORDER BY C.kode_rekening ASC;";


    return DB::select($sql);
}

function doFunctionalPenghasil($pak,$month,$ppkd,$blud,$skode_,$id) {
    $sql = "
        SELECT
            A.id,
            A.skpd_id,
            A.subkegiatan_id,
            A.subkegiatan_skode,
            A.subrekening_id,
            A.tahapan_id,
            A.nominal,
            A.realisasi,
            A.procentase,
            A.ppkd,
            A.blud,
            A.actived,
            B.subkegiatan_nama,
            B.subkegiatan_kode,
            C.kode_rekening,
            C.uraian,
            skpd.nama_skpd,
            " . ($pak == 1 ? "D.nama AS nama_tahapan," : "") . "

            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) < $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_lalu,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) = $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 0 THEN BK.Total ELSE 0 END), 0) AS penerimaan_bku_sd_now,
            COALESCE(SUM(CASE WHEN MONTH(BK.bukti_tgl) <= $month AND BK.bku_jenis = 1 THEN BK.Total ELSE 0 END), 0) AS pengeluaran_bku_sd_now

        FROM anggaran AS A
        INNER JOIN kegiatan AS B ON A.subkegiatan_id = B.id
        INNER JOIN source_rekening AS C ON A.subrekening_id = C.id
        INNER JOIN skpd ON A.skpd_id = skpd.id
        " . ($pak == 1 ? "INNER JOIN tahapan AS D ON A.tahapan_id = D.id" : "") . "

        LEFT JOIN bkupenerimaan AS BK
            ON A.skpd_id = BK.id_parent
            AND A.subkegiatan_id = BK.kegiatan_id
            AND A.subkegiatan_skode = BK.skegiatan_kode
            AND A.subrekening_id = BK.rekening_id
            AND BK.tahun = 2025
            AND BK.actived = 1
            AND BK.status_jurnal >= 1

        WHERE A.actived = 1
        AND A.ppkd = $ppkd
        AND A.blud = $blud
        AND A.skpd_id = $id
        AND A.subkegiatan_skode = $skode_
        " . ($pak == 1 ? "AND D.nama LIKE '%PAK%'" : "") . "

        GROUP BY A.id, A.skpd_id, A.subkegiatan_id, A.subkegiatan_skode, A.subrekening_id,
                A.tahapan_id, A.nominal, A.realisasi, A.procentase, A.ppkd, A.blud, A.actived,
                B.subkegiatan_nama, B.subkegiatan_kode, C.kode_rekening, C.uraian,
                skpd.nama_skpd" . ($pak == 1 ? ", D.nama" : "") . "

        ORDER BY C.kode_rekening ASC;";
    return DB::select($sql);;
}
