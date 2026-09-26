<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-19 19:03:39
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-19 19:07:16
 */

use Doco\components\DocoConstants;

?>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <th><?= \Yii::t("app", "Tanggal Pendaftaran"); ?></th>
            <th><?= \Yii::t("app", "Nomor Pendaftaran"); ?></th>
            <th><?= \Yii::t("app", "No Rekam Medis"); ?></th>
            <th><?= \Yii::t("app", "Nama Pasien"); ?></th>
            <th><?= \Yii::t("app", "Tanggal Lahir"); ?></th>
            <th><?= \Yii::t("app", "Dokter"); ?></th>
            <th><?= \Yii::t("app", "Cara Bayar"); ?></th>
            <th><?= \Yii::t("app", "Nama Penjamin"); ?></th>
            <th><?= \Yii::t("app", "No.Lab"); ?></th>
            <th><?= \Yii::t("app", "Asal Rujukan"); ?></th>
            <th><?= \Yii::t("app", "Status"); ?></th>
            <th><?= \Yii::t("app", "Jumlah Tagihan"); ?></th>
            <th><?= \Yii::t("app", "Ruangan"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($query as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['tglmasukpenunjang']) ? date('d M Y', strtotime($value['tglmasukpenunjang'])) : '' ?></td>
                <td><?= isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '' ?></td>
                <td><?= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '' ?></td>
                <td><?= isset($value['nama_pasien']) ? $value['nama_pasien'] : '' ?></td>
                <td><?= isset($value['tanggal_lahir']) ? date('d M Y', strtotime($value['tanggal_lahir'])) : '' ?></td>
                <td><?= isset($value['dokter_penunjang']) ? $value['dokter_penunjang'] : '' ?></td>
                <td><?= isset($value['carabayar_nama']) ? $value['carabayar_nama'] : '' ?></td>
                <td><?= isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '' ?></td>
                <td><?= isset($value['no_masukpenunjang']) ? $value['no_masukpenunjang'] : '' ?></td>
                <td><?= isset($value['asalrujukan_nama']) ? $value['asalrujukan_nama'] : '' ?></td>
                <td><?= isset($value['status_periksa']) ? DocoConstants::$status_lab[$value['status_periksa']] : '' ?></td>
                <td><?= isset($value['harga']) ? number_format($value['harga']) : 0 ?></td>
                <td><?= isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
            </tr>
        <?php
            $no++;
        endforeach;
        ?>
    </tbody>
</table>