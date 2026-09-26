<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-19 19:03:39
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-19 19:07:16
 */

use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

?>
<h5 style="text-align: center;">Periode : <?= $periode ?> </h5>
<h5>Tanggal Cetak : <?= $tanggal ?> </h5>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <th><?= \Yii::t("app", "Tanggal Pendaftaran"); ?></th>
            <th><?= \Yii::t("app", "Tanggal Periksa"); ?></th>
            <th><?= \Yii::t("app", "Status Cito"); ?></th>
            <th><?= \Yii::t("app", "Nomor Pendaftaran"); ?></th>
            <th><?= \Yii::t("app", "No Rekam Medis"); ?></th>
            <th><?= \Yii::t("app", "Nama Pasien"); ?></th>
            <th><?= \Yii::t("app", "Tanggal Lahir"); ?></th>
            <th><?= \Yii::t("app", "Dokter Perujuk"); ?></th>
            <th><?= \Yii::t("app", "Dokter"); ?></th>
            <th><?= \Yii::t("app", "Cara Bayar"); ?></th>
            <th><?= \Yii::t("app", "Nama Penjamin"); ?></th>
            <th><?= \Yii::t("app", "No.Radiologi"); ?></th>
            <th><?= \Yii::t("app", "Asal Rujukan"); ?></th>
            <th><?= \Yii::t("app", "Ruangan Asal"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($query as $value) :
            $diffTglExpertise = "";

            if (!empty($value['tgl_hasilrad'])) {
                $diffTglExpertise = DocoHelpers::getLamaTunggu($value['tgl_ambilfoto'], $value['tgl_hasilrad']);
            }
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['tglmasukpenunjang']) ? DocoHelpers::convertDate($value['tglmasukpenunjang'], 'd-M-Y H:i') : '' ?></td>
                <td><?= isset($value['tglmasukpenunjang']) ? DocoHelpers::convertDate($value['tglmasukpenunjang'], 'd-M-Y H:i') : '' ?></td>
                <td><?= isset($value['is_cyto']) ? 'CITO' : 'NON CITO' ?></td>
                <td><?= isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '' ?></td>
                <td><?= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '' ?></td>
                <td><?= isset($value['nama_pasien']) ? $value['nama_pasien'] : '' ?></td>
                <td><?= isset($value['tanggal_lahir']) ? DocoHelpers::convertDate($value['tanggal_lahir'], 'd-M-Y') : '' ?></td>
                <td><?= isset($value['nama_pegawai']) ? $value['nama_pegawai'] : '' ?></td>
                <td><?= isset($value['dokter_penunjang']) ? $value['dokter_penunjang'] : '' ?></td>
                <td><?= isset($value['carabayar_nama']) ? $value['carabayar_nama'] : '' ?></td>
                <td><?= isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '' ?></td>
                <td><?= isset($value['no_masukpenunjang']) ? $value['no_masukpenunjang'] : '' ?></td>
                <td><?= isset($value['asalrujukan_nama']) ? $value['asalrujukan_nama'] : '' ?></td>
                <td><?= isset($value['ruanganasal_nama']) ? $value['ruanganasal_nama'] : '' ?></td>
            </tr>
        <?php
            $no++;
        endforeach;
        ?>
    </tbody>
</table>
