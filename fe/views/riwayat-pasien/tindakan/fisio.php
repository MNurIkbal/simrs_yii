<?php

/**
 * @Author: Ardi
 * @Date:   2018-10-11 13:10
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    .modal-body {
    position: relative;
    padding-top: 5px !important;
}
</style>


<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Fisioterapi - <?= ArrayHelper::getValue($infoPasien, 'nama_pasien' , '-') . ' / ' . ArrayHelper::getValue($infoPasien, 'penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($infoPasien, 'kelaspelayanan_nama', '-')?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nama Pasien
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['nama_pasien']?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nomor Rekam Medik
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['no_rekam_medik']?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nomor Telepon Pasien
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['no_telepon_pasien']?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Umur / Jenis Kelamin
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['umur']?> / <?=@$infoPasien['jenis_kelamin']?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?=Yii::t('fe', 'No Pendaftaran')?></th>
                        <th><?=Yii::t('fe', 'Dokter Pemeriksa')?></th>
                        <th><?=Yii::t('fe', 'Tanggal Pemeriksaan')?></th>
                        <th><?=Yii::t('fe', 'Nama Pemeriksaan')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(empty($listData))
                    {
                        echo "<td colspan='9' style='text-align:center'>No data available in table</td>";
                    }
                    ?>
                    <?php
                        $rowNum = 1;
                        foreach ($listData as $data) {
                    ?>
                        <tr>
                            <td><?=$rowNum?></td>
                            <td><?=@$data['no_pendaftaran']?></td>
                            <td><?=@$data['dokter_pemeriksa']?></td>
                            <td><?=$data['tgl_tindakan'] == null ? '-' : DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($data['tgl_tindakan'])), false, true)?></td>
                            <td><?=@$data['tindakan_obat']?></td>
                        </tr>
                    <?php
                            $rowNum++;
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>