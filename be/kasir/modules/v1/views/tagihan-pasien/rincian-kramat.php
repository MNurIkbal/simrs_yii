<?php 
use Doco\components\DocoHelpers;

?>

<style>
    .number {
        text-align: right
    }
    body { 
        font-size: 14px;
        letter-spacing: 1px;
        font-family: "tahoma";
    }
    table thead tr th {
        font:arial !important;
        font-size:12px !important;
    }

    table tbody tr td {
        font:arial !important;
        font-size:12px !important;
    }

    table tfoot tr td {
        font:arial !important;
        font-size:12px !important;
    }
    .tbl-bordered {
        border-collapse: collapse;
        /*border: 1px solid black;*/
        font-size: 12px;
        letter-spacing: 1px;
        font-family: "tahoma";
    }
    .tbl-bordered thead td {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        font-family: "tahoma";
        text-align: center;
        letter-spacing: 1px;
    }
    .tbl-bordered tbody td {
        /*border: 1px solid black;*/
        font-family: "tahoma";
        letter-spacing: 1px;
    }
    .tabel-header {
        font-family: "tahoma";
        letter-spacing: 1px;
    }
    .tbl-alamat tr td {
        /*border: 1px solid black;*/
        font-family: "tahoma";
        letter-spacing: 1px;
    }
    .footer  {
        padding: 3px;
    }

    .number {
        text-align: right
    }
    .center {
        text-align: center
    }
    .tbl-footer {
        font-family: "Arial, Helvetica, sans-serif";
        border-bottom: 2px solid black;
    }
</style>
<table border="0" class="tbl-bordered" style="width:100%; border-collapse: collapse;">
<tbody>
        <thead>
        <tr>
            <td class="center">KAMAR</td>
            <td class="center">KELAS</td>
            <td class="center">TT</td>
            <td class="center">DARI</td>
            <td class="center">SAMPAI</td>
            <td class="center">HARI</td>
            <td class="center">TARIF/HARI</td>
            <td class="center">BIAYA</td>
        </tr>
        </thead>
            <tr>
                <td colspan="8">&nbsp;</td>
            </tr>
            <?php $totalAkom = 0; if(!empty($detailAkomodasi)) : ?>
            <?php foreach ($detailAkomodasi as $key => $value) : 
                $totalKamar = 0; 
                foreach ($value as $val) :
                $lamaRawat = isset($val['lama_rawat']) ? $val['lama_rawat'] : 0;
                $tarifKamar = isset($val['harga_satuan']) ? $val['harga_satuan'] : 0;
                $totalAkomodasi = isset($val['tarif_tindakan']) ? $val['tarif_tindakan'] : 0;
                $kamar = isset($val['kamar']) ? $val['kamar'] : '-';
                $kelas = isset($val['kelas']) ? $val['kelas'] : '-';
                $tempat_tidur = isset($val['tempat_tidur']) ? $val['tempat_tidur'] : '-';
                $tgl_masuk = isset($val['tgl_masuk']) ? $val['tgl_masuk'] : '-';
                $tgl_keluar = isset($val['tgl_keluar']) ? $val['tgl_keluar'] : '-';
                $totalKamar += $totalAkomodasi;
            ?>
            <tr>
                <td style="width:25%;"><?= $kamar ?></td>
                <td style="width:15%;text-align: center;"><?= $kelas ?></td>
                <td style="width:5%;"><?= $tempat_tidur ?></td>
                <td style="width:15%;"><?= $tgl_masuk ?></td>
                <td style="width:15%;"><?= $tgl_keluar ?></td>
                <td style="text-align: right;width:10%;text-align: center;"><?= $lamaRawat ?></td>
                <td style="text-align: right;width:15%;"><?= DocoHelpers::formatNumber($tarifKamar) ?></td>
                <td style="text-align: right;width:10%;"><?= DocoHelpers::formatNumber($totalAkomodasi) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="8">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7" style="border-top:2px solid black;">KAMAR PERAWATAN</td>
                <td style="text-align: right;border-top:2px solid black;"><?= DocoHelpers::formatNumber($totalKamar) ?></td>
            </tr>
            <?php $totalAkom = $totalKamar; ?>
            <tr>
                <td colspan="8">&nbsp;</td>
            </tr>
            <?php endif; ?>
            <?php
            $subTotal = 0;
            $no = 1;
            foreach ($detailTindakan as $key => $value) {
                $total = isset($value['sub_total']) ? $value['sub_total'] : 0;
                $subTotal += $total;
            ?>
                <tr>
                    <td colspan="7"><?= strtoupper(isset($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : '') ?></td>
                    <td style="text-align: right; width: 35%;"><?= DocoHelpers::formatNumber($total) ?></td>
                </tr>
            <?php
            $no++;
            }
            ?>
            <?php if($no < 1): ?>
            <tr>
                <td colspan="8" class="text-center"><i>Data tidak ditemukan</i></td>
            </tr>
            <?php endIf;
             ?>
        </tbody>
</table>