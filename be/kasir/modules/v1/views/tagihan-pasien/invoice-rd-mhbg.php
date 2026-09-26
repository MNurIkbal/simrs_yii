<?php 
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers; 
$tgl_masuk = isset($header['tgl_pendaftaran']) ? date('d M Y', strtotime($header['tgl_pendaftaran'])) : '-';
$tgl_keluar = isset($header['tgl_pasienpulang']) ? date('d M Y', strtotime($header['tgl_pasienpulang'])) : '-';
?>
<style>
    .tbl-bordered {
        border-collapse: collapse;
        /*border: 1px solid black;*/
        font-size: 12px;
        font-family: Tahoma;
    }
    .tbl-bordered thead th {
        /*border: 1px solid black;*/
        font-family: Tahoma;
    }
    .tbl-bordered tbody td {
        /*border: 1px solid black;*/
        font-family: Tahoma;
    }

    .tbl-bordered tfoot td {
        padding: 3px;
    }
    .tbl-alamat {
        border-collapse: collapse;
        /*border: 1px solid black;*/
        font-size: 12px;
        font-family: Tahoma;
    }
    .tbl-alamat tr td {
        /*border: 1px solid black;*/
        font-family: Tahoma;
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
</style>
<br><br><br>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th colspan="4">&nbsp;&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width:15%;height: 45px;">&nbsp;&nbsp;<strong><?= ArrayHelper::getValue($header, 'no_rekam_medik') ?></strong></td>
            <td style="width:60%;height: 45px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= $header['nama_pasien'] ?></strong></td>
            <td style="height: 45px;"><strong><?= date('d/m/Y H:i') ?></strong></td>
            <td style="height: 45px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
    </tbody>
</table>
<table width="100%" class="tbl-alamat">
    <tr>
        <td style="width:15%;">&nbsp;&nbsp;</td>
        <td rowspan="2" style="width:50%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= ArrayHelper::getValue($header, 'alamat'); ?></strong></td>
        <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= ArrayHelper::getValue($header, 'no_pendaftaran'); ?></strong></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td colspan="2">&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td style="height: 25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= ArrayHelper::getValue($pembayaran, 'no_pembayaran'); ?></strong></td>
        <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= $tgl_masuk.' / '.$tgl_keluar ?></strong></td>
    </tr>
    <tr>
        <td colspan="1">&nbsp;&nbsp;</td>
        <td style="height: 17px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= ArrayHelper::getValue($pembayaran, 'no_pembayaran'); ?></strong></td>
        <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= ArrayHelper::getValue($header, 'dok_pendaftaran') ?></strong></td>
    </tr>
</table><!-- <br><br><br><br> -->
<table width="100%" class="tbl-bordered" style="margin-top: 30px;">
    <thead>
        <tr>
            <th colspan="5">&nbsp;&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1; 
            $total = 0; 
            foreach ($data as $key => $value) :
        ?>
            <tr>
                <td></td>
                <td><strong><?= $key ?></strong> </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            
            <?php 
                foreach ($value as $k => $v) : 
                    $qty = ArrayHelper::getValue($v, 'qty');
                    $tglPelayanan = ArrayHelper::getValue($v, 'tgl_pelayanan');
                    $isKonsultasi = ArrayHelper::getValue($v, 'is_konsultasi');
                    $dokter = ArrayHelper::getValue($v, 'dokter_tindakan');
                    $tindakan = ArrayHelper::getValue($v, 'tindakan_obat_nama');
                    $tarifSatuan = ArrayHelper::getValue($v, 'tarif_satuan', 0);
                    $subTotal = ArrayHelper::getValue($v, 'sub_total', 0);
                    $total += $subTotal;
                    $tindakan = ($isKonsultasi) ? $dokter : $tindakan;
            ?>
                <tr>
                    <td style="width:20%;"><?= date('d/m/Y', strtotime($tglPelayanan)) ?></td>
                    <td style="width:50%;"><?= $tindakan ?></td>
                    <td style="width:5%;" class="number"><?= $qty ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($tarifSatuan) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($subTotal) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td><strong>Total Tagihan</strong></td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td class="number"><strong><?= DocoHelpers::formatNumber(isset($pembayaran['bill_amount']) ? $pembayaran['bill_amount'] : $pembayaran['total_tagihan']); ?></strong></td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td><strong>Sisa Tagihan</strong></td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td class="number"><strong><?= DocoHelpers::formatNumber($pembayaran['total_sisatagihan']); ?></strong></td>
        </tr>
    </tfoot>
</table>
