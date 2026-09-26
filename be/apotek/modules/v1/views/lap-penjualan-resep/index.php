<?php

use Doco\components\DocoHelpers;

?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<table style="width: 100%">
    <tr>
        <td style="text-align: center;"><?=Yii::t('app', 'Laporan penjualan resep')?></td>      
    </tr>
    <tr>
        <td style="text-align: center;"><?= Yii::t('app', 'Apotek Farmasi')?></td>
    </tr>
    <tr>
        <td style="text-align: center;"><?='Periode '.$filter?></td>
    </tr>
</table>
<br>
<table width="100%" class="tbl-bordered">
    <tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Tanggal Penjualan");?></th>
        <th><?=\Yii::t("app", "Nomor Resep");?></th>
        <th><?=\Yii::t("app", "Nama");?></th>
        <th><?=\Yii::t("app", "Nomor Pendaftaran");?></th>
        <th><?=\Yii::t("app", "Cara Bayar");?></th>
        <th><?=\Yii::t("app", "Penjamin");?></th>
        <th><?=\Yii::t("app", "Total tagihan (Rp)");?></th>        
    </tr>
    <tbody style="font-size: 13px">
        <?php 
        $no = 1;
        $total = 0;
        foreach($data as $value):  
        if($value['jenispenjualan'] == 343) {
            $nama_pasien = !empty($value['nama_pembeli']) ? $value['nama_pembeli'] : '-';
        }elseif($value['jenispenjualan'] == 344) {
            $nama_pasien = !empty($value['nama_pegawai']) ? $value['nama_pegawai'] : '-';
        }else {
            $nama_pasien = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '-';
        }
        $subtotal = $value['totalhargajual']+$value['totaltarifservice']+$value['biayaadministrasi']+$value['biayakonseling']+$value['pembulatanharga']+$value['jasadokterresep'];
        $total += $subtotal;
        $value['tglpenjualan'] = date("j M Y", strtotime($value['tglpenjualan']));
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?= date('d-M-Y', strtotime($value['tglpenjualan'])) ?></td>
            <td><?=$value['noresep']?></td>
            <td><?=$nama_pasien?></td>
            <td><?=!empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '-'; ?></td>
            <td><?=$value['carabayar_nama']?></td>
            <td><?=$value['penjamin_nama']?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></td>            
        </tr>
        <?php 
        $no++;
        endforeach;
        ?>
        <tr>
            <td colspan="7">Total</td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber($total) ?></td>
        </tr>
    </tbody>  
</table>