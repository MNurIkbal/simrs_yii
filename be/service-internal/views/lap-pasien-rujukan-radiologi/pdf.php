<?php
use yii\helpers\ArrayHelper;
?>


<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <h5 style="text-align: center;">Periode : <?= $periode ?> </h5>
            <h5>Tanggal Cetak : <?= $tanggal ?> </h5>
            <table cellSpacing="2" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?= \Yii::t("app", "Tanggal Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "Tanggal Persetujuan"); ?></th>
                        <th><?= \Yii::t("app", "No Pendaftaran"); ?></th>
                        <th><?= \Yii::t("app", "No Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "Nama Pasien"); ?></th>
                        <th><?= \Yii::t("app", "No Rekam Medik"); ?></th>
                        <th><?= \Yii::t("app", "Tanggal Lahir"); ?></th>
                        <th><?= \Yii::t("app", "Pemeriksaan"); ?></th>
                        <th><?= \Yii::t("app", "Jenis Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "Asal Rujukan / Nama RS"); ?></th>
                        <th><?= \Yii::t("app", "Dokter Perujuk"); ?></th>
                        <th><?= \Yii::t("app", "Cara Bayar / Penjamin"); ?></th>
                        <th><?= \Yii::t("app", "Status"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $no = 1;
                    foreach ($model as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= ArrayHelper::getValue($value, 'tgl_rujukan',''); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'tgl_persetujuan'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'no_pendaftaran'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'no_rujukan'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'nama_pasien'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'no_rekam_medik'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'tanggal_lahir'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'daftartindakan_nama'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'jenis_rujukan'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'rujukan','-'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'asalrujukan_nama','-') ." / ". ArrayHelper::getValue($value, 'nama_rs','-'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'dokter_perujuk','-'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'carabayar_nama','-')." / ". ArrayHelper::getValue($value, 'penjamin_nama','-'); ?></td>
                            <td><?= ArrayHelper::getValue($value, 'status_periksa_nama','-'); ?></td>
                        </tr>
                    <?php
                    $no++;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
</div>
