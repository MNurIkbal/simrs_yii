<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 13:34:22
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-24 14:08:20
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;

?>
<style type="text/css">
    .blured-row{
        color: red;
    }
</style>
<?php 
    $form = ActiveForm::begin([
        'id' => 'form-intra-operasi', 
        'action'=>Url::to(['intra-operasi', 'id'=>$model->pasienmasukpenunjang_id]),
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM]
    ]); 
?>
<?=Html::activeHiddenInput($model, 'inpostoperasi_id',['class'=>'inpostoperasi-id'])?>
<?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pasienmasukpenunjang-id'])?>
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Data operasi')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-dokter" class="collapse-click rotate-180" data-toggle="collapse" data-target="#panel-data-operasi"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-operasi" aria-expanded="true" class="collapse in">
<div class="row">
    <div class="col-md-6">
        <div class="form-group field-intraoperasiform-is_surgicalsavety">
            <label class="control-label col-md-4" for="intraoperasiform-is_surgicalsavety"><?=$model->attributeLabels()['is_surgicalsavety']?></label>
            <div class="col-md-6">
                <?=Html::activeRadioList($model, 'is_surgicalsavety',[1=>'Ya',0=>'Tidak'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-masuk_kamar">
            <label class="control-label col-md-6" for="intraoperasiform-masuk_kamar"><?=$model->attributeLabels()['masuk_kamar']?></label>
            <div class="col-md-6">
                <div class="input-group bootstrap-timepicker timepicker">
                    <?=Html::activeTextInput($model, 'masuk_kamar', ['class'=>'form-control txt-timepicker', 'readonly'=>true])?>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-mulai_anastesi">
            <label class="control-label col-md-6" for="intraoperasiform-mulai_anastesi"><?=$model->attributeLabels()['mulai_anastesi']?></label>
            <div class="col-md-6">
                <div class="input-group bootstrap-timepicker timepicker">
                    <?=Html::activeTextInput($model, 'mulai_anastesi', ['class'=>'form-control txt-timepicker', 'readonly'=>true])?>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-selesai_anastesi">
            <label class="control-label col-md-6" for="intraoperasiform-selesai_anastesi"><?=$model->attributeLabels()['selesai_anastesi']?></label>
            <div class="col-md-6">
                <div class="input-group bootstrap-timepicker timepicker">
                    <?=Html::activeTextInput($model, 'selesai_anastesi', ['class'=>'form-control txt-timepicker', 'readonly'=>true])?>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-mulai_operasi">
            <label class="control-label col-md-6" for="intraoperasiform-mulai_operasi"><?=$model->attributeLabels()['mulai_operasi']?></label>
            <div class="col-md-6">
                <div class="input-group bootstrap-timepicker timepicker">
                    <?=Html::activeTextInput($model, 'mulai_operasi', ['class'=>'form-control txt-timepicker', 'readonly'=>true])?>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-selesai_operasi">
            <label class="control-label col-md-6" for="intraoperasiform-selesai_operasi"><?=$model->attributeLabels()['selesai_operasi']?></label>
            <div class="col-md-6">
                <div class="input-group bootstrap-timepicker timepicker">
                    <?=Html::activeTextInput($model, 'selesai_operasi', ['class'=>'form-control txt-timepicker', 'readonly'=>true])?>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- List Dokter -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Data tim operasi')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-tim-operasi" class="collapse-click" data-toggle="collapse" data-target="#panel-data-tim-operasi"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-tim-operasi" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-dokterbedah_id">
            <label class="control-label col-md-6" for="intraoperasiform-dokterbedah_id"><?=$model->attributeLabels()['dokterbedah_id']?></label>
            <div class="col-md-6">
                <?=Html::activeHiddenInput($model, 'dokterbedah_id', ['class'=>'dokterbedah-id'])?>
                <label class="control-label" style="padding: 8px"><b class="dokterbedah-nama"></b></label>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-dokteranastesi_id">
            <label class="control-label col-md-6" for="intraoperasiform-dokteranastesi_id"><?=$model->attributeLabels()['dokteranastesi_id']?></label>
            <div class="col-md-6">
                <?=Html::activeHiddenInput($model, 'dokteranastesi_id', ['class'=>'dokteranastesi-id'])?>
                <label class="control-label" style="padding: 8px"><b class="dokteranastesi-nama"></b></label>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <a href="<?=Url::to(['intra-tambah-pegawai'])?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah pegawai</a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <br>
        <table id="table-tim-operasi" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th data-key="no">No</th>
                    <th data-key="nama_pegawai"><?=Yii::t('fe', 'Nama pegawai')?></th>
                    <th data-key="posisi_tim_nama"><?=Yii::t('fe', 'Posisi tim operasi')?></th>
                    <th data-key="hapus" style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>
</div>
<!-- End List Dokter -->
<!-- Detail Operasi -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'List dan Detail operasi')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-list-detail-operasi" class="collapse-click" data-toggle="collapse" data-target="#panel-data-list-detail-operasi"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-list-detail-operasi" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <a href="<?=Url::to(['intra-tambah-item-operasi'])?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah operasi</a>
    </div>
    <div class="col-md-12">
        <br>
        <table id="table-item-operasi" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th data-key="no">No</th>
                    <th data-key="nama_operasi"><?=Yii::t('fe', 'Nama Operasi')?></th>
                    <th data-key="cyto"><?=Yii::t('fe', 'Cyto')?></th>
                    <th data-key="jenis_luka_nama"><?=Yii::t('fe', 'Jenis luka')?></th>
                    <th data-key="jenis_operasi_nama"><?=Yii::t('fe', 'Jenis operasi')?></th>
                    <th data-key="jenis_anastesi_nama"><?=Yii::t('fe', 'Jenis anastesi')?></th>
                    <th data-key="hapus" style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-set_instrumen">
            <label class="control-label col-md-6" for="intraoperasiform-set_instrumen"><?=$model->attributeLabels()['set_instrumen']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 'set_instrumen',$opsi['set_instrumen'], ['class'=>'form-control select-instrumen', 'prompt'=>''])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-penunjang_khusus_id">
            <label class="control-label col-md-6" for="intraoperasiform-penunjang_khusus_id"><?=$model->attributeLabels()['penunjang_khusus_id']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 'penunjang_khusus_id',$opsi['penunjang_khusus'], ['class'=>'form-control select-penunjangkhusus', 'prompt'=>''])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="form-group field-intraoperasiform-perlengkapan_pribadi">
            <label class="control-label col-md-3" for="intraoperasiform-perlengkapan_pribadi"><?=$model->attributeLabels()['perlengkapan_pribadi']?></label>
            <div class="col-md-6">
                <?=Html::activeTextArea($model, 'perlengkapan_pribadi', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-is_diathermy">
            <label class="control-label col-md-6" for="intraoperasiform-is_diathermy"><?=$model->attributeLabels()['is_diathermy']?></label>
            <div class="col-md-6">
                <?=Html::activeRadioList($model, 'is_diathermy',[1=>'Ya',0=>'Tidak'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-posisi_elektroda">
            <label class="control-label col-md-6" for="intraoperasiform-posisi_elektroda"><?=$model->attributeLabels()['posisi_elektroda']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'posisi_elektroda',
                    isset($options['posisi_elektroda']) ? ArrayHelper::map($options['posisi_elektroda'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2', 'prompt'=>''])
                ?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-kondisi_kulit_sebelum">
            <label class="control-label col-md-6" for="intraoperasiform-kondisi_kulit_sebelum"><?=$model->attributeLabels()['kondisi_kulit_sebelum']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'kondisi_kulit_sebelum',
                    isset($options['keadaan_kulit']) ? ArrayHelper::map($options['keadaan_kulit'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2', 'prompt'=>''])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-kondisi_kulit_setelah">
            <label class="control-label col-md-6" for="intraoperasiform-kondisi_kulit_setelah"><?=$model->attributeLabels()['kondisi_kulit_setelah']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 'kondisi_kulit_setelah',
                isset($options['keadaan_kulit']) ? ArrayHelper::map($options['keadaan_kulit'], 'lookup_id', 'lookup_value') : [],
                ['class'=>'form-control select2', 'prompt'=>''])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-posisi_operasi">
            <label class="control-label col-md-6" for="intraoperasiform-posisi_operasi"><?=$model->attributeLabels()['posisi_operasi']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'posisi_operasi',
                    isset($options['posisi_operasi']) ? ArrayHelper::map($options['posisi_operasi'], 'lookup_id', 'lookup_value') : [],
                    ['class'=>'form-control select2', 'prompt'=>''])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-fiksasi_balon">
            <label class="control-label col-md-6" for="intraoperasiform-fiksasi_balon"><?=$model->attributeLabels()['fiksasi_balon']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'fiksasi_balon', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-kateter_urin">
            <label class="control-label col-md-6" for="intraoperasiform-kateter_urin"><?=$model->attributeLabels()['kateter_urin']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'kateter_urin', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group field-intraoperasiform-pemakaian_implan">
            <label class="control-label col-md-4" for="intraoperasiform-pemakaian_implan"><?=$model->attributeLabels()['pemakaian_implan']?></label>
            <div class="col-md-6">
                <?=Html::activeTextArea($model, 'pemakaian_implan', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-pencucian_operasi">
            <label class="control-label col-md-6" for="intraoperasiform-pencucian_operasi"><?=$model->attributeLabels()['pencucian_operasi']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'pencucian_operasi',
                    isset($options['pencucian_operasi']) ? ArrayHelper::map($options['pencucian_operasi'], 'lookup_id', 'lookup_value') : [],
                    ['class'=>'form-control select2', 'prompt'=>''])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- End Detail Operasi -->

<!-- List BPMHP -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Penggunaan BMHP')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-penggunaan-bmhp" class="collapse-click" data-toggle="collapse" data-target="#panel-penggunaan-bmhp"></a></li>
        </ul>
    </div>
</div>
<div id="panel-penggunaan-bmhp" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <a href="<?=Url::to(['intra-tambah-penggunaan-bmhp'])?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah penggunaan</a><br>
    </div>
    <div class="col-md-12">
        <br>
        <table id="table-penggunaan-bmhp" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Jenis alat')?></th>
                    <th><?=Yii::t('fe', 'Persediaan')?></th>
                    <th><?=Yii::t('fe', 'Tambahan')?></th>
                    <th><?=Yii::t('fe', 'Terpakai')?></th>
                    <th><?=Yii::t('fe', 'Sisa')?></th>
                    <th><?=Yii::t('fe', 'Ditagihkan')?></th>
                    <th style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <p style="color: red">*Obat tidak tersedia</p>
        <br>
        <br>
    </div>
</div>
</div>
<!-- End List BPMHP -->

<!-- List Cairan -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Penggunaan cairan')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-penggunaan-cairan" class="collapse-click" data-toggle="collapse" data-target="#panel-data-penggunaan-cairan"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-penggunaan-cairan" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <a href="<?=Url::to(['intra-tambah-penggunaan-cairan'])?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah penggunaan</a>
    </div>
    <div class="col-md-12">
        <br>
        <table id="table-penggunaan-cairan" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Kegiatan')?></th>
                    <th><?=Yii::t('fe', 'Cairan masuk')?></th>
                    <th><?=Yii::t('fe', 'Cairan keluar')?></th>
                    <th><?=Yii::t('fe', 'Keterangan')?></th>
                    <th style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-lokasi_drainvacum">
            <label class="control-label col-md-6" for="intraoperasiform-lokasi_drainvacum"><?=$model->attributeLabels()['lokasi_drainvacum']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'lokasi_drainvacum', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-lokasi_drainpenrose">
            <label class="control-label col-md-6" for="intraoperasiform-lokasi_drainpenrose"><?=$model->attributeLabels()['lokasi_drainpenrose']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'lokasi_drainpenrose', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-lokasi_drainselang">
            <label class="control-label col-md-6" for="intraoperasiform-lokasi_drainselang"><?=$model->attributeLabels()['lokasi_drainselang']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'lokasi_drainselang', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-is_jaringantubuh">
            <label class="control-label col-md-6" for="intraoperasiform-is_jaringantubuh"><?=$model->attributeLabels()['is_jaringantubuh']?></label>
            <div class="col-md-6">
                <?=Html::activeRadioList($model, 'is_jaringantubuh',[1=>'Ya',0=>'Tidak'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<?php 

$hideClass = ($model->is_jaringantubuh) ? '' : 'hidden';

?>
<div class="pa-jaringan-tubuh <?=$hideClass?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-jenis_jaringan">
                <label class="control-label col-md-6" for="intraoperasiform-jenis_jaringan"><?=$model->attributeLabels()['jenis_jaringan']?></label>
                <div class="col-md-6">
                    <?=Html::activeTextInput($model, 'jenis_jaringan', ['class'=>'form-control'])?>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-is_diserahkan">
                <label class="control-label col-md-6" for="intraoperasiform-is_diserahkan"><?=$model->attributeLabels()['is_diserahkan']?></label>
                <div class="col-md-6">
                    <?=Html::activeRadioList($model, 'is_diserahkan',[1=>'Ya',0=>'Tidak'])?>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <?php
        $hidePenerima = ($model->is_diserahkan) ? '' : 'hidden';
        ?>
        <div class="penerima-pemberi <?=$hidePenerima?>">
            <div class="col-md-4">
                <div class="form-group field-intraoperasiform-penerima">
                    <label class="control-label col-md-6" for="intraoperasiform-penerima"><?=$model->attributeLabels()['penerima']?></label>
                    <div class="col-md-6">
                        <?=Html::activeTextInput($model, 'penerima', ['class'=>'form-control penerima-txt'])?>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group field-intraoperasiform-pegawai_pemberi_id">
                    <label class="control-label col-md-6" for="intraoperasiform-pegawai_pemberi_id"><?=$model->attributeLabels()['pegawai_pemberi_id']?></label>
                    <div class="col-md-6">
                        <?=Html::activeDropdownList($model, 'pegawai_pemberi_id',$opsi['penerima'], ['class'=>'form-control select-pegawai-pemberi', 'prompt'=>''])?>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- End List Cairan -->

<!-- List Alat yang ditinggal -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Alat yang ditinggal dalam tubuh')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-alat-tubuh" class="collapse-click" data-toggle="collapse" data-target="#panel-data-alat-tubuh"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-alat-tubuh" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <a href="<?=Url::to(['intra-tambah-alat-ditubuh'])?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah alat</a>
    </div>
    <div class="col-md-12">
        <br>
        <table id="table-alat-ditubuh" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Jenis alat')?></th>
                    <th><?=Yii::t('fe', 'Jumlah')?></th>
                    <th><?=Yii::t('fe', 'Lokasi')?></th>
                    <th style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
</div>
<!-- End List Alat yang ditinggal -->

<!-- List pemeriksaan pelengkap -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Pemeriksaan pelengkap')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-pemeriksaan-pelengkap" class="collapse-click" data-toggle="collapse" data-target="#panel-data-pemeriksaan-pelengkap"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-pemeriksaan-pelengkap" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <a href="<?=Url::to(['intra-tambah-pemeriksaan-pelengkap'])?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah pemeriksaan</a>
    </div>
    <div class="col-md-12">
        <br>
        <table id="table-pemeriksaan-pelengkap" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
                    <th><?=Yii::t('fe', 'Nama jaringan')?></th>
                    <th><?=Yii::t('fe', 'Ukuran')?></th>
                    <th style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
</div>
<!-- End List pemeriksaan pelengkap -->

<!-- List konsultasi -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Konsultasi tindakan')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-konsultasi-tindakan" class="collapse-click" data-toggle="collapse" data-target="#panel-data-konsultasi-tindakan"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-konsultasi-tindakan" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <a href="<?=Url::to(['intra-tambah-konsul-tindakan'])?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah konsultasi</a>
    </div>
    <div class="col-md-12">
        <br>
        <table id="table-konsul-tindakan" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Tindakan')?></th>
                    <th><?=Yii::t('fe', 'Bagian')?></th>
                    <th><?=Yii::t('fe', 'Nama dokter')?></th>
                    <th><?=Yii::t('fe', 'Alasan')?></th>
                    <th style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
</div>
<div class="text-right">
    <?=Html::button("<b><i class='fa fa-floppy-o'></i></b> Simpan", ['class'=>'btn btn-xs btn-labeled btn-info submit-intra-operasi'])?>
</div>
<!-- End List konsultasi -->
<?php 
    ActiveForm::end();
?>
<?php 
    $this->registerJs('
        var _tableoperasi;
        var _tableitemoperasi;
        var _tablepenggunaancairan;
        var _tablealatditubuh;
        var _tablepemeriksaanpelengkap;
        var _tablekonsultindakan;
        var _tablepenggunaanbmhp;

        var _cacheoperasi = "pegawaioperasi-'.$unique.'";
        var _cacheitemoperasi = "itemoperasi-'.$unique.'";
        var _cachepenggunaancairan = "penggunaancairan-'.$unique.'";
        var _cachealatditubuh = "alatditubuh-'.$unique.'";
        var _cachepemeriksaanpelengkap = "pemeriksaanpelengkap-'.$unique.'";
        var _cachekonsultindakan = "konsultindakan-'.$unique.'";
        var _cachepenggunaanbmhp = "penggunaanbmhp-'.$unique.'";

        var _opsipenunjang = $.parseJSON(\''.json_encode($opsi["penunjang_khusus"]).'\');
        var _opsiinstrumen = $.parseJSON(\''.json_encode($opsi["set_instrumen"]).'\');
        var _opsipenerima = $.parseJSON(\''.json_encode($opsi["penerima"]).'\');
        '.$this->render('../js/function.js'), View::POS_END, 'js');
?>
