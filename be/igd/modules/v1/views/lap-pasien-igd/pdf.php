<?php

use yii\helpers\ArrayHelper;
/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-09 13:19:07
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-09 13:27:32
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
<table border="0" style="width: 100%">
    <tr>
        <td style="text-align:center"><strong>Laporan Pasien Rawat Darurat</strong></td>
    </tr>
    <?php
    if (isset($filter['advanced-filter'])) {
        foreach ($filter['advanced-filter'] as $key => $value) :
        ?>
        <tr>
            <td style="text-align:center">
                <strong><?=preg_replace('/[^A-Za-z0-9\-]/', ' ', $key) . ' : ' . $value?></strong>
            </td>
        </tr>
        <?php
        endforeach;
    }
    ?>
</table>

<br>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th width="1">No</th>
            <th><?= \Yii::t("app", "Tanggal Pendaftaran"); ?></th>
            <th><?= \Yii::t("app", "No Pendaftaran"); ?></th>
            <th><?= \Yii::t("app", "No Rekam Medik"); ?></th>
            <th><?= \Yii::t("app", "Nama pasien"); ?></th>
            <th><?= \Yii::t("app", "Jenis Kelamin"); ?></th>
            <th><?= \Yii::t("app", "Cara Bayar"); ?></th>
            <th><?= \Yii::t("app", "Penjamin"); ?></th>
            <th><?= \Yii::t("app", "Jenis Kasus Penyakit"); ?></th>
            <th><?= \Yii::t("app", "Dokter Jaga"); ?></th>
            <th><?= \Yii::t("app", "Dokter Penanggungjawab"); ?></th>
            <th><?= \Yii::t("app", "Status"); ?></th>
            <th><?= \Yii::t("app", "No telph 1"); ?></th>
            <th><?= \Yii::t("app", "No telph 2"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 0;
        foreach($detail as $value):
            $no++;
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran'])) ?></td>
            <td><?= ArrayHelper::getValue($value,'no_pendaftaran'); ?></td>
            <td><?= ArrayHelper::getValue($value,'no_rekam_medik');?></td>
            <td><?= ArrayHelper::getValue($value,'nama_pasien');?></td>
            <td><?= ArrayHelper::getValue($value,'jenis_kelamin');?></td>
            <td><?= ArrayHelper::getValue($value,'carabayar_nama');?></td>
            <td><?= ArrayHelper::getValue($value,'penjamin_nama');?></td>
            <td><?= ArrayHelper::getValue($value,'jeniskasuspenyakit_nama');?></td>
            <td><?= ArrayHelper::getValue($value,'dokter_jaga');?></td>
            <td><?= ArrayHelper::getValue($value,'dokter');?></td>
            <td><?= ArrayHelper::getValue($value,'status_periksa_nama');?></td>
            <td><?= ArrayHelper::getValue($value, 'no_telepon_pasien') ?></td>
            <td><?= ArrayHelper::getValue($value, 'no_mobile_pasien') ?></td>
        </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>