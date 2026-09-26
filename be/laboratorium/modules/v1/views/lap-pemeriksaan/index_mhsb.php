<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-19 16:15:40
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-24 22:26:05
 */
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
<br>
<table width="100%" class="tbl-bordered">
    <tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Tanggal masuk");?></th>
        <th><?=\Yii::t("app", "No Pendaftaran");?></th>
        <th><?=\Yii::t("app", "No Rekam Medis");?></th>
        <th><?=\Yii::t("app", "Nama Pasien");?></th>
        <th><?=\Yii::t("app", "Nama dokter");?></th>
        <th><?=\Yii::t("app", "Dokter DPJP");?></th>
        <th><?=\Yii::t("app", "Kelas Pelayanan");?></th>
        <th><?=\Yii::t("app", "Kelompok pemeriksaan");?></th>
        <th><?=\Yii::t("app", "Jenis pemeriksaan");?></th>
        <th><?=\Yii::t("app", "Nama pemeriksaan");?></th>
        <th><?=\Yii::t("app", "Qty");?></th>
    </tr>
    <tbody style="font-size: 13px">
        <?php
        $no = 1;
        $kelompok = [];
        $jenispemeriksaan = [];
        $namapemeriksaan = [];
        $totalsatuan = 0;
        $qty = 0;
        $cyto = 0;
        $total = 0;
        foreach($data as $value):
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=date('d M Y', strtotime($value['tglmasukpenunjang']))?></td>
            <td><?=$value['no_pendaftaran']?></td>
            <td><?=$value['no_rekam_medik']?></td>
            <td><?=$value['nama_pasien']?></td>
            <td><?=$value['dokter']?></td>
            <td><?=$value['dokter_dpjp_nama']?></td>
            <td><?=$value['kelaspelayanan_nama']?></td>
            <td><?=$value['nama_kelompok']?></td>
            <td><?=$value['jenispemeriksaanlab_nama']?></td>
            <td><?=$value['daftartindakan_nama']?></td>
            <td style="text-align: right;"><?=$value['qty_tindakan']?></td>
        </tr>
        <?php
        $totalsatuan += $value['tarif_satuan'];
        $cyto += $value['tarifcyto_tindakan'];
        $total += ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty_tindakan'];
        $qty += $value['qty_tindakan'];
        if(!in_array($value['nama_kelompok'], $kelompok)){
            array_push($kelompok, $value['nama_kelompok']);
        }
        if(!in_array($value['jenispemeriksaanlab_nama'], $jenispemeriksaan)){
            array_push($jenispemeriksaan, $value['jenispemeriksaanlab_nama']);
        }
        if(!in_array($value['daftartindakan_nama'], $namapemeriksaan)){
            array_push($namapemeriksaan, $value['daftartindakan_nama']);
        }
        $no++;
        endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align: center;"><b>Jumlah</b></td>
            <td><?=count($kelompok)?></td>
            <td><?=count($jenispemeriksaan)?></td>
            <td><?=count($namapemeriksaan)?></td>
            <td style="text-align: right;"><?=$qty?></td>
        </tr>
    </tfoot>
</table>