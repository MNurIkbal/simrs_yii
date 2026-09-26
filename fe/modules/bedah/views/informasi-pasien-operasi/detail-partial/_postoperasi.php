<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 13:39:38
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-12 16:52:46
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

?>
<?php

$yaHidden = '';
$tdkHidden = 'hidden';
$readonly = false;
$disButton = '';
if($model->is_recovery == 0){
    $yaHidden = 'hidden';
    $tdkHidden = '';
}
if($model->is_skrining_nyeri == 0){
    $readonly = true;
}
if($model->is_pasanginfus == 0){
    $disButton = 'disabled';
}


    $form = ActiveForm::begin([
        'id' => 'form-post-operasi', 
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM]
    ]); 
?>
<?=Html::activeHiddenInput($model, 'inpostoperasi_id',['class'=>'inpostoperasi-id'])?>
<?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pasienmasukpenunjang-id'])?>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-is_recovery">
            <label class="control-label col-md-5" for="postoperasiform-is_recovery"><?=$model->attributeLabels()['is_recovery']?></label>
            <div class="col-md-6">
                <?=Html::activeRadioList($model, 'is_recovery',[1=>'Ya',0=>'Tidak'], ['style'=>'padding: 5px'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="recovery-ya <?=$yaHidden?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-jam_masuk_rec required">
                <label class="control-label col-md-4" for="intraoperasiform-jam_masuk_rec"><?=$model->attributeLabels()['jam_masuk_rec']?></label>
                <div class="col-md-6 required">
                    <div class="input-group bootstrap-timepicker timepicker">
                        <?=Html::activeTextInput($model, 'jam_masuk_rec', ['class'=>'form-control txt-timepicker', 'readonly'=>true])?>
                        <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-jam_keluar_rec required">
                <label class="control-label col-md-4" for="intraoperasiform-jam_keluar_rec"><?=$model->attributeLabels()['jam_keluar_rec']?></label>
                <div class="col-md-6">
                    <div class="input-group bootstrap-timepicker timepicker">
                        <?=Html::activeTextInput($model, 'jam_keluar_rec', ['class'=>'form-control txt-timepicker', 'readonly'=>true])?>
                        <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="recovery-tidak <?=$tdkHidden?>">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group field-postoperasiform-kembali_ruangan_id required">
                <label class="control-label col-md-4" for="postoperasiform-kembali_ruangan_id"><?=$model->attributeLabels()['kembali_ruangan_id']?></label>
                <div class="col-md-6">
                    <?=Html::activeDropdownList($model, 'kembali_ruangan_id',ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'), ['class'=>'form-control select2', 'prompt'=>''])?>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-kesadaran_umum">
            <label class="control-label col-md-4" for="postoperasiform-kesadaran_umum"><?=$model->attributeLabels()['kesadaran_umum']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'kesadaran_umum',
                    isset($options['kesadaran_umum']) ? ArrayHelper::map($options['kesadaran_umum'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2 unhide-trigger', 'prompt'=>'', 'data-target'=>'unhider-kesadaran'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 hidden unhider-kesadaran">
        <div class="form-group field-postoperasiform-kesadaran_umum_lain">
            <label class="control-label col-md-4" for="postoperasiform-kesadaran_umum_lain"><?=$model->attributeLabels()['kesadaran_umum_lain']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'kesadaran_umum_lain', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-tingkat_kesadaran">
            <label class="control-label col-md-4" for="postoperasiform-tingkat_kesadaran"><?=$model->attributeLabels()['tingkat_kesadaran']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'tingkat_kesadaran',
                    isset($options['tingkat_kesadaran']) ? ArrayHelper::map($options['tingkat_kesadaran'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2 unhide-trigger', 'prompt'=>'', 'data-target'=>'unhider-tingkatkesadaran'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 hidden unhider-tingkatkesadaran">
        <div class="form-group field-postoperasiform-tingkat_kesadaran_lain">
            <label class="control-label col-md-4" for="postoperasiform-tingkat_kesadaran_lain"><?=$model->attributeLabels()['tingkat_kesadaran_lain']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'tingkat_kesadaran_lain', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-jalan_napas">
            <label class="control-label col-md-4" for="postoperasiform-jalan_napas"><?=$model->attributeLabels()['jalan_napas']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'jalan_napas',
                    isset($options['jalan_napas']) ? ArrayHelper::map($options['jalan_napas'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2 unhide-trigger', 'prompt'=>'', 'data-target'=>'unhider-jalannapas'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 hidden unhider-jalannapas">
        <div class="form-group field-postoperasiform-jalan_napas_lain">
            <label class="control-label col-md-4" for="postoperasiform-jalan_napas_lain"><?=$model->attributeLabels()['jalan_napas_lain']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'jalan_napas_lain', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-terapi_oksigen">
            <label class="control-label col-md-4" for="postoperasiform-terapi_oksigen"><?=$model->attributeLabels()['terapi_oksigen']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'terapi_oksigen',
                    isset($options['terapi_oksigen']) ? ArrayHelper::map($options['terapi_oksigen'], 'lookup_id', 'lookup_value') : [],
                    ['class'=>'form-control select2 unhide-trigger', 'prompt'=>'', 'data-target'=>'unhider-terapioksigen'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 hidden unhider-terapioksigen">
        <div class="form-group field-postoperasiform-terapi_oksigen_lain">
            <label class="control-label col-md-4" for="postoperasiform-terapi_oksigen_lain"><?=$model->attributeLabels()['terapi_oksigen_lain']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'terapi_oksigen_lain', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-l_mnt">
            <label class="control-label col-md-4" for="postoperasiform-l_mnt"><?=$model->attributeLabels()['l_mnt']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'l_mnt', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-kulit_datang">
            <label class="control-label col-md-4" for="postoperasiform-kulit_datang"><?=$model->attributeLabels()['kulit_datang']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'kulit_datang',
                    isset($options['keadaan_kulit']) ? ArrayHelper::map($options['keadaan_kulit'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2 unhide-trigger', 'prompt'=>'', 'data-target'=>'unhider-kulitdatang'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 hidden unhider-kulitdatang">
        <div class="form-group field-postoperasiform-kulit_datang_lain">
            <label class="control-label col-md-4" for="postoperasiform-kulit_datang_lain"><?=$model->attributeLabels()['kulit_datang_lain']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'kulit_datang_lain', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-kulit_keluar">
            <label class="control-label col-md-4" for="postoperasiform-kulit_keluar"><?=$model->attributeLabels()['kulit_keluar']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'kulit_keluar',
                    isset($options['keadaan_kulit']) ? ArrayHelper::map($options['keadaan_kulit'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2 unhide-trigger', 'prompt'=>'', 'data-target'=>'unhider-kulitkeluar'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 hidden unhider-kulitkeluar">
        <div class="form-group field-postoperasiform-kulit_keluar_lain">
            <label class="control-label col-md-4" for="postoperasiform-kulit_keluar_lain"><?=$model->attributeLabels()['kulit_keluar_lain']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'kulit_keluar_lain', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-sirkulasi_badan">
            <label class="control-label col-md-4" for="postoperasiform-sirkulasi_badan"><?=$model->attributeLabels()['sirkulasi_badan']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'sirkulasi_badan',
                    isset($options['sirkulasi_badan']) ? ArrayHelper::map($options['sirkulasi_badan'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2 unhide-trigger', 'prompt'=>'', 'data-target'=>'unhider-sirkulasibadan'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 hidden unhider-sirkulasibadan">
        <div class="form-group field-postoperasiform-sirkulasi_badan_lain">
            <label class="control-label col-md-4" for="postoperasiform-sirkulasi_badan_lain"><?=$model->attributeLabels()['sirkulasi_badan_lain']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'sirkulasi_badan_lain', ['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-area_luka">
            <label class="control-label col-md-4" for="postoperasiform-area_luka"><?=$model->attributeLabels()['area_luka']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'area_luka',['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-is_skrining_nyeri">
            <label class="control-label col-md-4" for="postoperasiform-is_skrining_nyeri"><?=$model->attributeLabels()['is_skrining_nyeri']?></label>
            <div class="col-md-6">
                <?=Html::activeRadioList($model, 'is_skrining_nyeri',[1=>'Ya',0=>'Tidak'], ['style'=>'padding: 5px'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-ket_skrining">
            <label class="control-label col-md-4" for="postoperasiform-ket_skrining"><?=$model->attributeLabels()['ket_skrining']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'ket_skrining',['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-skala_nyeri">
            <label class="control-label col-md-4" for="postoperasiform-skala_nyeri"><?=$model->attributeLabels()['skala_nyeri']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'skala_nyeri',['class'=>'form-control skala-nyeri', 'readonly'=>$readonly])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-lokasi">
            <label class="control-label col-md-4" for="postoperasiform-lokasi"><?=$model->attributeLabels()['lokasi']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'lokasi',['class'=>'form-control lokasi', 'readonly'=>$readonly])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-metode_nyeri">
            <label class="control-label col-md-4" for="postoperasiform-metode_nyeri"><?=$model->attributeLabels()['metode_nyeri']?></label>
            <div class="col-md-6">
                <?=Html::activeDropdownList($model, 
                    'metode_nyeri',
                    isset($options['metode_nyeri']) ? ArrayHelper::map($options['metode_nyeri'], 'lookup_id', 'lookup_value') : [], 
                    ['class'=>'form-control select2', 'prompt'=>''])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-resiko_jatuh">
            <label class="control-label col-md-4" for="postoperasiform-resiko_jatuh"><?=$model->attributeLabels()['resiko_jatuh']?></label>
            <div class="col-md-6">
                <?=Html::activeTextInput($model, 'resiko_jatuh',['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-barang_pasien">
            <label class="control-label col-md-4" for="postoperasiform-barang_pasien"><?=$model->attributeLabels()['barang_pasien']?></label>
            <div class="col-md-6">
                <?=Html::activeTextArea($model, 'barang_pasien',['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-is_pasanginfus">
            <label class="control-label col-md-4" for="postoperasiform-is_pasanginfus"><?=$model->attributeLabels()['is_pasanginfus']?></label>
            <div class="col-md-6">
                <?=Html::activeRadioList($model, 'is_pasanginfus',[1=>'Ya',0=>'Tidak'], ['style'=>'padding: 5px'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>

<div class="pasanginfus-ya <?=($model->is_pasanginfus) ? '' : 'hidden';?>">
    <div class="row">
        <div class="col-md-4">
            <a href="<?=Url::to(['post-tambah-infus'])?>" class="btn btn-xs btn-labeled btn-info btn-add-infus <?=$disButton?>" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah cairan infus</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <br>
            <table id="table-pemasangan-infus" class="table table-striped table-condensed " style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th data-key="no">No</th>
                        <th data-key="nama_pegawai"><?=Yii::t('fe', 'Jenis cairan infus')?></th>
                        <th data-key="posisi_tim_nama"><?=Yii::t('fe', 'Tanggal pemasangan')?></th>
                        <th data-key="posisi_tim_nama"><?=Yii::t('fe', 'Jumlah tetesan')?></th>
                        <th data-key="hapus" style="width: 100px"><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group field-postoperasiform-pemberitahu_perawat required">
            <label class="control-label col-md-4" for="postoperasiform-pemberitahu_perawat"><?=$model->attributeLabels()['pemberitahu_perawat']?></label>
            <div class="col-md-6">
                <?= DateTimePicker::widget([
                    'model' => $model,
                    'attribute' => 'pemberitahu_perawat',
                    'options' => ['placeholder' => '','readonly'=>true],
                    'language' => 'en',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd M yyyy hh:ii',
                        'endDate' => date('Y-m-d H:i'),
                        
                    ]
                ]); ?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group field-postoperasiform-perawat_datang required">
            <label class="control-label col-md-4" for="postoperasiform-perawat_datang"><?=$model->attributeLabels()['perawat_datang']?></label>
            <div class="col-md-6">
                <?= DateTimePicker::widget([
                    'model' => $model,
                    'attribute' => 'perawat_datang',
                    'options' => ['placeholder' => '','readonly'=>true],
                    'language' => 'en',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd M yyyy hh:ii',
                        'endDate' => date('Y-m-d H:i'),
                    ]
                ]); ?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="form-group field-postoperasiform-keterangan">
            <label class="control-label col-md-3" for="postoperasiform-keterangan"><?=$model->attributeLabels()['keterangan']?></label>
            <div class="col-md-6">
                <?=Html::activeTextArea($model, 'keterangan',['class'=>'form-control'])?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<?php 
    ActiveForm::end();
?>
<br>
<br>
<br>
<?php 

$this->registerJs('
    var _cachepemasanganinfus = "pemasanganinfus-'.$unique.'";
    '.$this->render('../js/post.js'), View::POS_END, 'js')

?>