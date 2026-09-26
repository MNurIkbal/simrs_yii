<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-11 10:20:31
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-10 14:55:28
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;


$this->title = \Yii::t('fe', $title);
$form = ActiveForm::begin([
    'id' => 'asesmenmedis-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
]);
$disabled = '';
if(empty($model->asesmenmedis_id)){
    $disabled = 'disabled';
}
?>
<br>
<div class="panel">
    <div class="panel-white">
        <div class="panel-body">
            <br>
            <div class="form-asesmen">
                <fieldset title="1" class="stepy-step" onmouseover="this.title='';">
                    <legend class="stepy-legend"><?=Yii::t('fe', 'Asesmen awal medis')?></legend>
                    <div class="col-md-6">
                        <?=Html::activeHiddenInput($model, 'dokter_id', ['class'=>'form-control'])?>
                        <?=Html::activeHiddenInput($model, 'pendaftaran_id', ['class'=>'form-control pendaftaran_id'])?>
                        <?=Html::activeHiddenInput($model, 'pasienadmisi_id', ['class'=>'form-control pasienadmisi_id'])?>
                        <?=Html::activeHiddenInput($model, 'asesmenmedis_id', ['class'=>'form-control'])?>
                        <?=Html::hiddenInput('golongan_umur',4, ['class'=>'form-control golongan_umur'])?>
                        <?=Html::hiddenInput('pasien_id',2, ['class'=>'form-control pasien_id'])?>
                        <div class="form-group required">
                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['dokter_id']?></label>
                            <div class="col-sm-9">
                                <?=Html::textInput('',$dokterNama ,['readonly'=>'true', 'class'=>'form-control'])?>
                            </div>
                        </div>
                        <?=$form->field($model, 'keluhan_utama')->textInput(['class'=>' input-tags','data-role' => 'tagsinput'])?>
                        <?=$form->field($model, 'keluhan_tambahan')->textInput(['class'=>' input-tags','data-role' => 'tagsinput'])?>
                        <?=$form->field($model, 'r_penyakitsekarang')->textArea()?>
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['lama_sakit']?></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?=Html::activeTextInput($model, 'lama_sakit', ['class'=>'form-control docoNumberOnly'])?>
                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Hari')?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['r_penyakitdahulu']?></label>
                                <div class="col-sm-9 isi-loop">
                                    <div class="row row-default">
                                        <div class="col-sm-3">
                                            <?=Html::dropDownList('riwayat_penyakit[0][tahun]', [], ArrayHelper::map($tahun, 'tahun', 'tahun') , ['class'=>'select2 form-control select-tahun'])?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?=Html::textInput('riwayat_penyakit[0][penyakit]', '', ['class'=>'form-control penyakit-default docoNumberOnly'])?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?=Html::textInput('riwayat_penyakit[0][terapi]', '', ['class'=>'form-control  terapi-default docoNumberOnly'])?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?=Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-info btn-append'])?>
                                        </div>
                                    </div>
                                    <div class="row clone-div hidden" style="margin-top: 5px">
                                        <div class="col-sm-3">
                                            <?=Html::dropDownList('', [], ArrayHelper::map($tahun, 'tahun', 'tahun'), ['class'=>'form-control select-tahun','data-name'=>'tahun'])?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?=Html::textInput('', '', ['class'=>'form-control','data-name'=>'penyakit'])?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?=Html::textInput('', '', ['class'=>'form-control','data-name'=>'terapi'])?>
                                        </div>
                                        <div class="col-sm-3">
                                            <?=Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-info btn-append'])?>
                                            <?=Html::button('<i class="fa fa-trash"></i>', ['class'=>'btn btn-sm btn-danger btn-remove'])?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <?=$form->field($model, 'r_penyakitkeluarga')->dropDownList(ArrayHelper::map($data_diagnosa, 'diagnosa_nama', 'diagnosa_nama'), [
                                'class' => 'form-control input-sm select2', 
                                'multiple'=>'multiple',
                            ])?>
                        <?=$form->field($model, 'r_imunisasi')->dropDownList(ArrayHelper::map($data_imunisasi, 'diagnosa_nama', 'diagnosa_nama'), [
                                'class' => 'form-control input-sm select2', 
                                'multiple'=>'multiple',
                            ])?>
                        <?=$form->field($model, 'r_peskk')->textArea()?>
                    </div>
                    <div class="col-md-6">
                        <?=$form->field($model, 'tgl_asesmenmedis')->textInput(['class'=>'pickadate', 'readonly' => true])?>
                        <div class="form-group required">
                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['sumber_informasi']?></label>
                            <div class="col-sm-5">
                                <?= Html::activeRadioList($model, 'sumber_info', $data_sumberInfo , ['inline'=>true, 'class'=>'sumber-info'] ) ?>
                            </div>
                            <div class="control-label col-sm-4">
                                    <?=Html::activeTextInput($model, 'sumber_hubungan', ['class'=>'form-control sumber-hubungan','readonly'=>'true'])?>
                            </div>
                        </div>
                        
                        <?=$form->field($model, 'is_merokok')->radioList($data_isMerokok)?>
                        <div class="jml-rokok">
                            <div class="form-group">
                                <label class="control-label col-sm-3"><?=$model->attributeLabels()['jml_rokok']?></label>
                                <div class="col-sm-7">
                                    <div class="input-group">
                                        <?=Html::activeTextInput($model, 'jml_rokok', ['class'=>'form-control jml_rokok docoNumberOnly'])?>
                                        <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Hari')?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?=$form->field($model, 'obat_diberikan')->textArea()?>
                        <?=$form->field($model, 'r_makanan')->textArea()?>
                        <?=$form->field($model, 'r_kelahiran')->textArea()?>
                        <!-- // untuk kebutuhan change field alergi menjadi free text - issue 1699 -->
                        <!-- <?=$form->field($model, 'r_alergiobat')->dropDownList(ArrayHelper::map($data_obatalkes, 'obatalkes_namalain', 'obatalkes_namalain'), [
                                'class' => 'form-control input-sm select2', 
                                'multiple'=>'multiple',
                            ])?> -->
                        <?=$form->field($model, 'r_alergiobat')->textArea()?>
                        <?=$form->field($model, 'keterangan')->textArea()?>
                    </div>
                </fieldset>
                <fieldset title="2" class="stepy-step" onmouseover="this.title='';">
                    <legend class="stepy-legend"><?=Yii::t('fe', 'Asesmen awal medis')?></legend>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Tanda tanda vital')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">

                                    <br>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['tekanan_darah']?></label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <?=Html::activeTextInput($model, 'td_systolic', ['class'=>'form-control systolic docoNumberOnly'])?>
                                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Mm')?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <?=Html::activeTextInput($model, 'td_diastolic', ['class'=>'form-control diastolic sysdia docoNumberOnly'])?>
                                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Hg')?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <?=Html::activeTextInput($model, 'tekanan_darah', ['class'=>'form-control tekanan-darah', 'readonly'=>'true'])?>
                                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'MmHg')?></span>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"></label>
                                            <div class="col-sm-7">
                                                <?= Html::activeTextInput($model, 'hasil_td', ['class'=>'form-control hasil-td'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['tinggi_badan']?></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <?= Html::activeTextInput($model, 'tinggi_badan', ['class'=>'form-control tinggi-badan docoNumberOnly'])?>
                                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Cm')?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['bb_ideal']?></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <?=Html::activeTextInput($model, 'bb_ideal', ['class'=>'form-control bb-ideal','readonly'=>'true'])?>
                                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Kg')?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['detak_nadi']?></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <?= Html::activeTextInput($model, 'detak_nadi', ['class'=>'form-control docoNumberOnly'])?>
                                                    <span class="input-group-addon" id="basic-addon2">/<?=Yii::t('fe', 'Menit')?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['pernapasan']?></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <?=Html::activeTextInput($model, 'pernapasan', ['class'=>'form-control docoNumberOnly'])?>
                                                    <span class="input-group-addon" id="basic-addon2">/<?=Yii::t('fe', 'Menit')?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['berat_badan']?></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <?=Html::activeTextInput($model, 'berat_badan', ['class'=>'form-control berat-badan docoNumberOnly'])?>
                                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Kg')?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['imt']?></label>
                                            <div class="col-sm-3">
                                                <?=Html::activeTextInput($model, 'imt', ['class'=>'form-control imt','readonly'=>'true'])?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"></label>
                                            <div class="col-sm-7">
                                                <?=Html::activeTextInput($model, 'ket_imt', ['class'=>'form-control ket-imt','readonly'=>'true'])?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['denyut_jantung']?></label>
                                            <div class="col-sm-7">
                                                <?=Html::activeDropDownList($model, 'denyut_jantung',ArrayHelper::map($data_denyutJantung, 'lookup_value', 'lookup_name'), ['class'=>'form-control select2', 'prompt'=>''])?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['suhu_tubuh']?></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <?=Html::activeTextInput($model, 'suhu_tubuh', ['class'=>'form-control docoNumberOnly'])?>
                                                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Celcius')?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Keadaan umum')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <br>
                                    <div class="col-md-4">
                                        <fieldset>
                                            <legend><?=Yii::t('fe', 'Glasgow coma scale')?></legend>
                                            <?=$form->field($model, 'gcs_eye')->dropDownList(ArrayHelper::map($data_gcsEye, 'metodegcs_id', 'metodegcs_nama'), ['class'=>'select2 gcs_eye','prompt'=>'','options'=>$gcsEyeOptions])?>
                                            <?=$form->field($model, 'gcs_verbal')->dropDownList(ArrayHelper::map($data_gcsVerbal, 'metodegcs_id', 'metodegcs_nama'), ['class'=>'select2 gcs_verbal','prompt'=>'','options'=>$gcsVerbalOptions])?>
                                            <?=$form->field($model, 'gcs_motorik')->dropDownList(ArrayHelper::map($data_gcsMotorik, 'metodegcs_id', 'metodegcs_nama'), ['class'=>'select2 gcs_motorik','prompt'=>'','options'=>$gcsMotorikOptions])?>
                                            <?=$form->field($model, 'hasil_gcs')->textInput(['class'=>'hasil_gcs'])?>
                                            <?=$form->field($model, 'kontak')->radioList($data_kontak)?>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-md-offset-1">
                                        <fieldset>
                                            <legend><?=Yii::t('fe', 'Metode asesmen nyeri')?></legend>
                                            
                                            <?=$form->field($model, 'metod_asmennyeri')->radioList(ArrayHelper::map($data_metodAsesmen, 'lookup_value', 'lookup_name'), [])?>
                                            <?=$form->field($model, 'is_terintubasi')->radioList($data_isTerintubasi)?>
                                            <?=$form->field($model, 'skala')->textInput(['class'=>'docoNumberOnly'])?>
                                            <?=$form->field($model, 'lokasi_nyeri')?>
                                        </fieldset>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Kepala & leher')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'inspeksi_kepala')?>
                                    <?=$form->field($model, 'palpasi_kepala')?>
                                    <?=$form->field($model, 'neurologi_kepala')?>
                                    <br>
                                    <h6><?=Yii::t('fe', 'Leher')?></h6>
                                    <?=$form->field($model, 'is_kakududuk')->radioList($data_kakududuk,['inline'=>true])?>
                                    <?=$form->field($model, 'jvp')->textInput(['class'=>'docoNumberOnly'])?>
                                    <?=$form->field($model, 'kgb')->textInput(['class'=>'docoNumberOnly'])?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Toraks')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'inspeksi_toraks')?>
                                    <?=$form->field($model, 'palpasi_toraks')?>
                                    <?=$form->field($model, 'perkusi_toraks')?>
                                    <?=$form->field($model, 'auskultasi_toraks')?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Punggung')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'inspeksi_punggung')?>
                                    <?=$form->field($model, 'palpasi_punggung')?>
                                    <?=$form->field($model, 'perkusi_punggung')?>
                                    <?=$form->field($model, 'auskultasi_punggung')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Abdomen')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'inspeksi_abdomen')?>
                                    <?=$form->field($model, 'palpasi_abdomen')?>
                                    <?=$form->field($model, 'perkusi_abdomen')?>
                                    <?=$form->field($model, 'auskultasi_abdomen')?>
                                    <?=$form->field($model, 'hepar')?>
                                    <?=$form->field($model, 'lien')?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Ekstrimitas')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'inspeksi_ekstrim')?>
                                    <?=$form->field($model, 'palpasi_ekstrim')?>
                                    <?=$form->field($model, 'neurologi_ekstrim')?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Anus/Genitalia (hanya bila diperlukan)')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'anus_genitalia')?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Status lokalis')?></h6>
                                    <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="image-frame">
                                            <?php
                                                echo Html::img('@web/media/img/img-pemeriksaan/bagian_tubuh_medis.jpg', ['class'=>'img-responsive']);
                                            ?>
                                            </div>
                                            <!-- modal anatomi start -->
                                            <div class="tag" style="display: none" data-show="1">
                                                <span style="box-sizing: border-box;position: absolute;border: 6px solid #b93d3d;border-color: transparent transparent #ff0000 #ff0000;transform-origin: 0 0;transform: rotate(135deg);box-shadow: -3px 3px 3px -3px rgba(0,0,0,0.3);margin-left: 18px;"></span>
                                                <div class="well well-sm" style="min-height:130px;">
                                                    <div class="form-group">
                                                     <label class="col-lg-3">Bagian<sup style="color: red">*</sup></label>
                                                        <div class="col-lg-9">
                                                            <?php echo Html::dropDownList('bagain_tubuh', null, ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'),
                                                                           array(
                                                                            'class' => 'form-control bagian-tubuh',
                                                                            'style' => 'padding : 9px 12px !important;',
                                                                            'empty' => '-- Pilih --',
                                                                           )); ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                     <label class="col-lg-3">Detail Bagian<sup style="color: red">*</sup></label>
                                                        <div class="col-lg-9">
                                                            <?php echo Html::dropDownList('bagain_tubuh', null, [],
                                                                           array(
                                                                            'class' => 'form-control bagian-tubuh-detail',
                                                                            'style' => 'padding : 9px 12px !important;',
                                                                            'empty' => '-- Pilih --',
                                                                           )); ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                     <label class="col-lg-3">Catatan<sup style="color: red">*</sup></label>
                                                        <div class="col-lg-9">
                                                            <input type="text"
                                                                   placeholder="catatan.." 
                                                                   class="form-control add-caption">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-lg-12">
                                                            <p class="helper-text ">(tekan <b>enter</b> untuk selesai)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- modal anatomi end -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="col-lg-6">
                                                <h6 class="panel-title"><?=Yii::t('fe', 'Tabel Pemeriksaan Anatomi Tubuh')?></h6>
                                            </div>
                                            <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-anggotatubuh">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th>No</th>
                                                        <th><?=Yii::t('fe', 'Tanggal periksa')?></th>
                                                        <th><?=Yii::t('fe', 'Bagian tubuh')?></th>
                                                        <th><?=Yii::t('fe', 'Bagian tubuh detail')?></th>
                                                        <th><?=Yii::t('fe', 'Keterangan')?></th>
                                                        <th><?=Yii::t('fe', 'Aksi')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody> 
                                                    <!-- table data -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Pemeriksaan penunjang')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['catatan_rad']?></label>
                                        <div class="col-sm-3" style="padding: 10px">
                                            <a href="#" class="btn btn-success">Lihat Hasil</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <?=Html::activeTextArea($model, 'catatan_rad', ['class'=>'form-control'])?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['catatan_lab']?></label>
                                        <div class="col-sm-3" style="padding: 10px">
                                            <a href="#" class="btn btn-success">Lihat Hasil</a>
                                        </div>
                                        <div class="col-sm-6">
                                            <?=Html::activeTextArea($model, 'catatan_lab', ['class'=>'form-control'])?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Daftar masalah / Diagnosa')?></h6>
                                    <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'diagnosa_id')->dropDownList(ArrayHelper::map($data_diagnosa, 'diagnosa_id', 'diagnosa_nama'), ['class'=>'select2', 'prompt'=>''])?>
                                    <?=$form->field($model, 'masalah')->textArea()?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Discharge planning')?></h6>
                                    <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'discharge_plan')->radioList($data_discharge)?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Care planning')?></h6>
                                    <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'care_plan')->textArea(['class'=>'care-plan'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php if ($model['asesmenmedis_id'] != ''): ?>
                <?= Html::button("<b><i class='fa fa-trash'></i></b> Hapus", ['class'=>'btn btn-xs btn-labeled btn-info stepy-finish btn-hapus']) ?>
                <?php endif ?>
                <?=Html::button("<b><i class='fa fa-print'></i></b> ".Yii::t('fe','print'), ['class'=>'stepy-finish btn-cetak-asesmen btn btn-xs btn-labeled btn-info '.$disabled, 'data-target'=>Url::to(['cetak-asesmen','id'=>DocoHelpers::encrypt($pendaftaran_id)])])?>
                <?=Html::submitButton($model['asesmenmedis_id'] != '' ? "<b><i class='fa fa-pencil'></i></b> Ubah" : "<b><i class='fa fa-floppy-o'></i></b> Simpan", ['class'=>'stepy-finish btn btn-xs btn-labeled btn-info'])?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php 
$jsonBagianTubuh = json_encode(ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'));
$jsonDetailBagianTubuh = json_encode($data_detailbagiantubuh);
$this->registerJs("

    var removeDisable = function(){
        $('.btn-cetak-asesmen').removeClass('disabled')
    }
    $('.btn-cetak-asesmen').on('click', function(){
        let url = window.location.origin;
        let target = $(this).attr('data-target');
        window.open(url+target);
    })
    var tabel_anggotatubuh = $('.tabel-anggotatubuh').DataTable({
        filter: false,
        bLengthChange: false,
        bInfo: false,
        processing: true,
        paging: false,
    });
    $('.form-asesmen').stepy({
          backLabel: '".Yii::t('fe', 'Kembali')." <b><i class=\'fa fa-chevron-left\'></i></b>',
          nextLabel: '".Yii::t('fe', 'Selanjutnya')." <b><i class=\'fa fa-chevron-right\'></i></b>',
          enter: false,
        })
    $('.form-asesmen').find('.button-next').addClass('btn btn-xs btn-labeled btn-info');
    $('.form-asesmen').find('.button-back').addClass('btn btn-xs btn-labeled btn-info');
    $('.pickadate').pickadate({
        format: 'dd mmm, yyyy',
        selectMonths: true,
        selectYears: 99,
        // disabled => true,
        formatSubmit: 'yyyy-mm-dd',
    });

    // define data master tekanan darah & map
    var data_tekanandarah = ".json_encode($data_tekanandarah).";
    var tmpData = ".$jsonAnatomi."
    var bagianTubuh = ".$jsonBagianTubuh."
    var detailBagianTubuh = ".$jsonDetailBagianTubuh."
    var counter = ".$counter."
    var metodeGcs = ".json_encode($data_metodegcs)."
    var dataGcs = ".json_encode($data_gcs)."
    var listGcs = ".json_encode($data_listgcs)."
    var dataBmi = ".json_encode($data_bmi)."
    var dataRiwayatPenyakit = ".json_encode($model->r_penyakitdahulu)."
", View::POS_END, 'js2');
$this->registerJs($this->render('js/asesmenmedis.js'), View::POS_END, 'js')

?>