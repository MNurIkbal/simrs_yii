<?php

/**
 * @author Randy Vianda Putra
 * @todo ubah jenis antrian
 * @copyright 19 November 2018 aweutist
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\components\DocoHelpers;
use yii\helpers\Url;

?>


<!-- Modal header -->
<div id="content">
    <div class="modal-header bg-inverse">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title"><?= Yii::t('fe', 'Pasien Asuransi') ?></h5>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead class="bg-inverse">
                        <tr>
                            <th colspan="3">Identitas Pasien</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="20%">Nama Peserta</td>
                            <td width="1%">:</td>
                            <td><?= isset($pasienData['nama_pasien']) ? $pasienData['nama_pasien'] : '' ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td>:</td>
                            <td>
                                <?php if (isset($pasienData['tanggal_lahir'])): ?>
                                    <?=date('d M Y', strtotime($pasienData['tanggal_lahir'])) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td><?= isset($pasienData['jenis_kelamin']) ? $pasienData['jenis_kelamin'] : '' ?></td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <table class="table table-striped" style="margin-top: 30px;">
                    <thead class="bg-inverse">
                        <tr>
                            <th colspan="3">Identitas Pasien Asuransi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="20%">No. Asuransi</td>
                            <td width="1%">:</td>
                            <td><?= isset($insuranceData['nokartu']) ? $insuranceData['nokartu'] : '' ?></td>
                        </tr>
                        <tr>
                            <td width="20%">Nama Peserta</td>
                            <td>:</td>
                            <td><?= isset($insuranceData['namapeserta']) ? $insuranceData['namapeserta'] : '' ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td>:</td>
                            <td>
                                <?php if (isset($insuranceData['tanggallahir'])): ?>
                                    <?=date('d M Y', strtotime($insuranceData['tanggallahir'])) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>:</td>
                            <td><?= isset($insuranceData['jeniskelamin']) ? $insuranceData['jeniskelamin'] : '' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal footer -->
    <div class="modal-footer mt-5">
        <div style="display: flex; justify-content: center;">
            <button type="button" id="btn-batal-peserta-asuransi" data-dismiss="modal" style="width: 200px;" class="btn btn-danger btn-xs btn-labeled"><b><i class="fa fa-close"></i></b>Kembali</button>
            <button type="button" id="btn-confirm-peserta-asuransi" style="width: 200px;" class="btn btn-success btn-xs btn-labeled"><b><i class="fa fa-check"></i></b>Confirm</button>
        </div>
    </div>
</div>