<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use Doco\components\DocoHelpers;
?>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?= \Yii::t("app", "Tanggal Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "Nomoer Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "No Rekam Medis"); ?></th>
                        <th><?= \Yii::t("app", "Nama Pasien"); ?></th>
                        <th><?= \Yii::t("app", "Tanggal Lahir"); ?></th>
                        <th><?= \Yii::t("app", "Asal Rujukan"); ?></th>
                        <th><?= \Yii::t("app", "Dokter Perujuk"); ?></th>
                        <th><?= \Yii::t("app", "Cara Bayar"); ?></th>
                        <th><?= \Yii::t("app", "Penjamin"); ?></th>
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
                            <td><?= DocoHelpers::convDateTime($value['tgl_rujukan'], true, false) ?></td>
                            <td><?= $value['no_rujukan'] ?></td>
                            <td><?= $value['no_rekam_medik'] ?></td>
                            <td><?= $value['nama_pasien'] ?></td>
                            <td><?= DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])), true, false) ?></td>
                            <td><?= $value['ruangan_nama'] ?></td>
                            <td><?= $value['dokter_perujuk'] ?></td>
                            <td><?= $value['carabayar_nama'] ?></td>
                            <td><?= $value['penjamin_nama'] ?></td>
                            <td><?= $value['stat_penunjang'] ?></td>
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