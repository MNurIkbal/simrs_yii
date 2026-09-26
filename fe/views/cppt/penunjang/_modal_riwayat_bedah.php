<?php


use yii\web\View;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
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
                        <p class="form-control-static">: <?=$nama_pasien?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nomor Rekam Medik
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=$no_rekam_medik?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nomor Telepon Pasien
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=$no_telepon_pasien?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Umur / Jenis Kelamin
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=$umur?> / <?=$jenis_kelamin?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                    <table id="table-riwayat" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead class="text-center">
                            <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'No') ?></th>
                                <th><?= Yii::t('fe', 'Nomor Pemeriksaan') ?></th>
                                <th><?= Yii::t('fe', 'Tangaal Permintaan') ?></th>
                                <th><?= Yii::t('fe', 'Jadwal Bedah') ?></th>
                                <th><?= Yii::t('fe', 'Jenis Pemeriksaan') ?></th>
                                <th><?= Yii::t('fe', 'Nama Pemeriksaan') ?></th>
                                <th><?= Yii::t('fe', 'Status') ?></th>
                                <th><?= Yii::t('fe', 'Disetujui Oleh') ?></th>
                                <th><?= Yii::t('fe', 'Tanggal Disetujui') ?></th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                    </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>

<?php
$this->registerJs("
    var url = '$url'
    var table = $('#table-riwayat').docoTabel({
        filter: false,
        columnDefs: [ {
            className: 'text-center',
            targets: [6,7]
        }],
        order: [[0, 'desc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: url+'/riwayat-pasien/get-data-history-surgery?norm=$norm',
        columns: [
            {title: '".(\Yii::t('fe', 'No'))."',  data: 'rowNum', orderable: false},
            {title: '".(\Yii::t('fe', 'Nomor Pemeriksaan'))."',  data: 'no_pemeriksaan', orderable: false},
            {title: '".(\Yii::t('fe', 'Tanggal Permintaan'))."',  data: 'tanggal_permintaan', orderable: false},
            {title: '".(\Yii::t('fe', 'Jadwal Bedah'))."',  data: 'jadwal_bedah', orderable: false},
            {title: '".(\Yii::t('fe', 'Jenis Pemeriksaan'))."',  data: 'jenis_pemeriksaan', orderable: false},
            {title: '".(\Yii::t('fe', 'Nama Pemeriksaan'))."',  data: 'nama_pemeriksaan', orderable: false},
            {title: '".(\Yii::t('fe', 'Status'))."',  data: 'status', orderable: false},
            {title: '".(\Yii::t('fe', 'Disetujui Oleh'))."',  data: 'disetujui_oleh', orderable: false},
            {title: '".(\Yii::t('fe', 'Tanggal Disetujui'))."',  data: 'tanggal_disetujui', orderable: false},
        ],
    });
    table
        .clear()
        .draw();
");
?>