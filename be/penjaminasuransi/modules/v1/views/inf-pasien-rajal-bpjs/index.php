<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-06 19:00:55
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-06 19:20:29
 */
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
<table class="tbl-bordered" style="width:100%" border="1" cellpadding="5" cellspacing="1">
    <thead>
        <tr>
            <td>No</td>
            <th><?=\Yii::t("app", "Tanggal Pendaftaran");?></th>
            <th><?=\Yii::t("app", "No Rekam Medik");?></th>
            <th><?=\Yii::t("app", "No Pendaftaran");?></th>
            <th><?=\Yii::t("app", "Nama Pasien");?></th>
            <th><?=\Yii::t("app", "Cara Bayar");?></th>
            <th><?=\Yii::t("app", "Penjamin");?></th>
            <th><?=\Yii::t("app", "Ruangan");?></th>
            <th><?=\Yii::t("app", "Jenis Kasus Penyakit");?></th>
            <th><?=\Yii::t("app", "Dokter Penanggung Jawab");?></th>
            <th><?=\Yii::t("app", "Status");?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($data as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['tgl_pendaftaran']) ? date('d M Y', strtotime($value['tgl_pendaftaran'])) : '' ?></td>
                <td><?= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '' ?></td>
                <td><?= isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '' ?></td>
                <td><?= isset($value['nama_pasien']) ? $value['nama_pasien'] : '' ?></td>
                <td><?= isset($value['carabayar_nama']) ? $value['carabayar_nama'] : '' ?></td>
                <td><?= isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '' ?></td>
                <td><?= isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                <td><?= isset($value['jeniskasuspenyakit_nama']) ? $value['jeniskasuspenyakit_nama'] : '' ?></td>
                <td><?= isset($value['dokter_dpjp']) ? $value['dokter_dpjp'] : '' ?></td>
                <td><?= isset($value['status_verif']) ? $value['status_verif'] : '' ?></td>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>