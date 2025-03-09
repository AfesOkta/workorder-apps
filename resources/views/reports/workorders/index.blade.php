<?php
    $content = "";
    $rincian_tbp= DB::table('tbprincian')
        ->select('tbprincian.*','r.uraian','r.kode_rekening')
        ->join('source_rekening AS r','r.id','=','tbprincian.subrekening_id')
        ->where('tbprincian.header_id', $tbp->id)
        ->where('tbprincian.skpd_id', $tbp->id_skpd)
        ->where('actived',1)->get();
    $total = DB::table('tbprincian')
        ->where('tbprincian.header_id', "$tbp->id")
        ->where('tbprincian.skpd_id', $tbp->id_skpd)
        ->where('actived',1)->sum('nominal');

    // custom total for PDF Format
    $total = str_replace(",",".", $total);

    $url = url('/siap/print/tbp/'.$tbp->id);

?>
@extends('reports.layouts.pdf')
@push('style')
    <style type="text/css">

        .tg-label {
            border-collapse: collapse;
            border-spacing: 0;
        }
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tg td {
            border-color: rgb(186, 186, 186);
            border-style: solid;
            border-width: 0px;
            font-family: Arial, sans-serif;
            font-size: 16px;
            overflow: hidden;
            padding: 5px 5px;
            word-break: normal;
            vertical-align:top;
        }

        .tg-label td {
            border-color: rgb(186, 186, 186);
            border-style: solid;
            border-width: 0px;
            font-family: Arial, sans-serif;
            font-size: 16px;
            overflow: hidden;
            padding: 5px 5px;
            word-break: normal;
            vertical-align:top;
        }

        .tg th {
            border-color: rgb(186, 186, 186);
            border-style: solid;
            border-width: 0px;
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: normal;
            overflow: hidden;
            padding: 10px 5px;
            word-break: normal;
        }

        .tg .tg-0lax {
            text-align: left;
            vertical-align: middle
        }


        .footer {
            padding-left: 1em;
            padding-right: 1em;
            padding-bottom: 4em;
            padding-top: 1em;
            border: 1px solid black
        }

        .footer-content {
            height: 32px; /* height + top/bottom paddding + top/bottom border must add up to footer height */
            padding: 8px;
        }

        .wrapper {
            background-color: #e3f2fd;
            min-height: 100%;
            height: auto !important;
            margin-bottom: -50px; /* the bottom margin is the negative value of the footer's total height */
        }

        .wrapper:after {
            content: "";
            display: block;
            height: 50px; /* the footer's total height */
        }

        .content {
            height: 100%;
        }
    </style>
@endpush
@section('content')
<div class="bg">
    <div class="wrapper">
        <div class="content">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tg">
                <tr>
                    <td colspan="3">
                        <table cellpadding="1" cellspacing="15" width="100%">
                            <tr style="border: none;">
                                <th colspan="4">
                                    <img src="{{asset('assets/images/logo.png')}}" width="64" height="64" /><br />
                                </th>
                                <th colspan="8" style="width:100%; text-align: left;vertical-align:top">
                                    <span style="font-size: 22px;vertical-align:top">PEMERINTAH KOTA SURABAYA</span><br />
                                    <span style="font-size: 20px;vertical-align:top">TANDA BUKTI PEMBAYARAN</span><br />
                                    <span style="font-size: 20px;vertical-align:top">NOMER BUKTI &nbsp; &nbsp;{!! $tbp['tbp_kode'] !!}</span><br />
                                </th>
                            </tr>
                            <tr style="border: none;">

                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <hr style="5px solid black !important">
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <table cellpadding="5" cellspacing="20" width="100%" class="tg-label">
                            <tr>
                                <td style="width:1%;">a)</td>
                                <td colspan="3" style="font-size: 16px;vertical-align:top">Bendahara Penerimaan/Bendahara Penerima Pembantu &nbsp; {{@$bendahara->name ?? ""}} </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="3" style="font-size: 16px;vertical-align:top">Telah menerima uang sebesar Rp. {{number_format($tbp->total_pembayaran,2,",",".")}}
                                </td>
                            </tr>
                            <tr>
                                <td>b)</td>
                                <td colspan="3" style="font-size: 16px;vertical-align:top">(dengan huruf : {{terbilang($tbp->total_pembayaran)}} Rupiah)</td>
                            </tr>
                            <tr>
                                <td>c)</td>
                                <td width="25%" style="font-size: 16px;vertical-align:top">Nama</td>
                                <td>:</td>
                                <td style="font-size: 16px;vertical-align:top">{{$tbp->atas_nama}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="font-size: 16px;vertical-align:top">Alamat</td>
                                <td>:</td>
                                <td style="font-size: 16px;vertical-align:top">{{$tbp->alamat}}</td>
                            </tr>
                            <tr>
                                <td>d)</td>
                                <td width="25%" style="font-size: 16px;vertical-align:top">Sebagai Pembayaran</td>
                                <td>:</td>
                                <td style="text-align:left;font-size: 18px;max-width: 600px; word-wrap: break-word;">{{$tbp->uraian}}</td>
                            </tr>

                            <tr>
                                <td>e)</td>
                                <td width="25%" style="font-size: 16px;vertical-align:top">Bank</td>
                                <td>:</td>
                                <td style="text-align:left;font-size: 18px;max-width: 600px; word-wrap: break-word;">Bank Jatim</td>
                            </tr>

                            <tr>
                                <td>f)</td>
                                <td width="25%" style="font-size: 16px;vertical-align:top">No. Rekening</td>
                                <td>:</td>
                                <td style="text-align:left;font-size: 18px;max-width: 600px; word-wrap: break-word;">
                                    {{-- @if($tbp->skpd->jenis_skpd < 3)
                                        0011007000
                                    @else
                                        {{ @$bludSkpd != null ? $bludSkpd->no_rekening :""}}
                                    @endif --}}
                                    {{ $skpd->skpd_rekening_bank[0]->rekening_bank }}
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td colspan="3"></td>
                            </tr>

                            <tr>
                                <td width="5%"></td>
                                <td width="95%" colspan="3"  style="font-size: 16px;vertical-align:top">Dengan rincian penerimaan sebagai berikut:</td>
                            </tr>
                        </table>
                        <br><br>
                        <table border="1em solid !important" cellpadding="1" cellspacing="5" width="100%">
                            <tr>
                                <td style="text-align: center; font-size: 20px"></td>
                                <td style="text-align: center; font-size: 20px">KODE REKENING</td>
                                <td style="text-align: center;font-size: 20px">URAIAN</td>
                                <td style="text-align: right;font-size: 20px">JUMLAH (Rp.)</td>
                            </tr>

                            <?php
                                $i = 1;
                            ?>
                            @foreach($rincian_tbp as $row)
                                <tr>
                                    <td style="text-align: center;font-size: 18px">{{$i}}</td>
                                    <td style="text-align: center;font-size: 18px;">{{$row->kode_rekening}}</td>
                                    <td style="font-size: 18px; padding-left:5px">{{$row->uraian}}</td>
                                    <td style="text-align: right;font-size: 18px; padding-right:5px">{{number_format($row->nominal,2,",",".")}}</span></td>
                                </tr>
                            <?php
                                $i++;
                            ?>
                            @endforeach

                            <tr>
                                <td colspan="3" style="text-align: right;font-size: 18px; padding-right:5px">JUMLAH (Rp.)</td>
                                <td style="text-align: right;font-size: 18px;padding-right:5px">{{number_format($total,2,",",".")}}</td>
                            </tr>
                        </table>
                        <br><br>
                        <table cellpadding="0" cellspacing="10" width="100%">
                            <tr>
                                <td>e)</td>
                                <td colspan="2">Tanggal diterima uang:</td>
                                <td colspan="3">{{date('d-m-Y', strtotime($tbp->tbp_tgl))}}</td>
                            </tr>
                        </table>
                        <table cellpadding="5" cellspacing="10" width="100%">
                            <tr>
                                <td align="right" colspan="6">Surabaya, {{date('d-m-Y', strtotime($tbp->tbp_tgl))}}</td>
                            </tr>
                            <tr>
                                <td align="center" colspan="6">Mengetahui</td>
                            </tr>
                            @for ( $i=0; $i<=5;$i++ )
                            <tr>
                                <td colspan="6"></td>
                            </tr>
                            @endfor
                            <tr>
                                <td align="center" colspan="3" width="50%">Bendahara Penerimaan/</td>
                                <td align="center" colspan="3">Pembayar/Penyetor</td>
                            </tr>
                            <tr>
                                <td colspan="3" align="center" width="50%">Bendahara Penerimaan Pembantu</td>
                                <td colspan="3" align="center">Pihak Ketiga</td>
                            </tr>

                            {{-- @if($ttd_bendahara != null )
                            <tr>
                                <td height="80px" style="background-image: url({{$ttd_bendahara}}); background-size:contain;
                                                background-position: center center;background-repeat: no-repeat; background-size: 250px 150px">
                                </td>

                                <td align="center" height="80px"></td>
                            </tr>
                            @else --}}
                            <tr>
                                <td colspan="3" align="center">
                                    <div class="text-center visible-print mt-2">
                                        {{QrCode::size(100)->generate($url)}}
                                    </div>
                                </td>
                                <td align="center">

                                </td>
                            </tr>

                            {{-- @endif --}}
                            <tr>
                                <td colspan="3" align="center" style="text-align: center;font-size: 16px">{{strtoupper(@$bendahara->name ?? "")}}</td>
                                <td align="center" style="text-align: center;font-size: 16px">{{strtoupper(@$tbp->atas_nama ?? "")}}</td>
                            </tr>
                            <tr>
                                <td colspan="3 align="center" style="text-align: center;font-size: 16px">NIP. {{@$bendahara->nip ?? ""}}</td>
                                <td align="center" style="text-align: center;font-size: 16px"></td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<br/>

<div class="footer">
    <div class="content">
        <table width="100%">
            <tr>
                <td width="5%"></td>
                <td width="20%">Lembar Asli</td>
                <td colspan="3" width="75%">: Untuk pembayar/penyetor/pihak ketiga</td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td width="20%">Salinan 1</td>
                <td colspan="3" width="75%">: Untuk Bendahara Penerimaan/Bendahara Pembantu</td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td width="20%">Salinan 2</td>
                <td colspan="3" width="75%">: Arsip</td>
            </tr>
        </table>
    </div>
</div>
@endsection
