<?php

/**
 * @Author: Dede Herdiana
 * @Date:   2021-04-04 06:28
 * @Last Modified by:   Dede Herdiana
 * @Last Modified time: 2021-04-04
 */

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;


$form = ActiveForm::begin([
   'id' => 'form-laporan-endoskopi',
   'type' => ActiveForm::TYPE_VERTICAL,
   'action' => '\bedah\informasi-pasien-operasi\simpan-laporan-endoskopi?id=' . $id,
   'options' => ['enctype' => 'multipart/form-data']
]);
?>

<style lang="">
    .js .inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
    #file-1 {
        display:none;
        /* margin: 10px; */
    }
    .inputfile + label {
        max-width: 100%;
        font-size: 1.25rem;
        /* 20px */
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        display: inline-block;
        overflow: hidden;
        /* padding: 0.625rem 1.25rem; */
        padding: 7px 45px;
        max-width: 350px;
        /* 10px 20px */
    }

    .no-js .inputfile + label {
        display: none;
    }

    .inputfile:focus + label,
    .inputfile.has-focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }

    .inputfile + label * {
        /* pointer-events: none; */
        /* in case of FastClick lib use */
    }

    .inputfile + label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        /* 4px */
        margin-right: 0.25em;
        /* 4px */
    }


    .inputfile-1 + label {
        color: #ffffff;
        background-color: #009ACD;
        
    }

    .inputfile-1:focus + label,
    .inputfile-1.has-focus + label,
    .inputfile-1 + label:hover {
        background-color: #00688B;
    }

    .lurus {
        float: left;
        margin-left: 5px;
    }

    .link-upload {
        font-size: 14px;
        color: black;
        margin: 5px;
        padding: 5px;
        font-weight: bold;
    }

    .content {
        min-height: 480px;
    }
    .customform {
        margin:2px;
        padding: 10px;
        background: #009ACD;
        color: #fff;
        border-radius: 4px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <legend><?= Yii::t('fe', 'Laporan Endoskopi') ?> </legend>
    </div>
</div>
<div class="row">
   <div class="col-md-3">
   </div> 
   <div class="col-md-6">
        <?= $form->field($model, 'pasienmasukpenunjang_id')->hiddenInput(['value' => $pasienmasukpenunjang_id])->label(false); ?>
        <?= $form->field($model, 'pendaftaran_id')->hiddenInput(['value' => ''])->label(false); ?>
        <?= $form->field($model, 'simptoms')->textArea(['rows' => 3]); ?>
        <?= $form->field($model, 'pre_diagnosis')->widget(Select2::classname(), [
            'showToggleAll' => false,
            'data' => $options['pre_diagnosis'],
            'options' => [
                'multiple' => false,
                'placeholder' => '-- Pilih --',
                'class' => 'form-control input-sm select2 selectPreDiagnosis'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'language' => [
                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-new-diagnosa?id_with_text=1']),
                'dataType' => 'json',
                'data' => new JsExpression('
                    function(params) {
                        return {
                            type: "diagnosa_utama",
                            q: params.term,
                            page:params.page || 1,
                            limit:params.limit || 10,
                        }; 
                    }
                '),
                'processResults' => new JsExpression('
                    function (data, params) {
                        params.page = params.page || 1;
                        return {
                        results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        }
                    }
                ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(res){ return res.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);?>
        <?= $form->field($model, 'pre_diagnosis_sekunder')->widget(Select2::classname(), [
            'showToggleAll' => false,
            'data' => $options['pre_diagnosis_sekunder'],
            'options' => [
                'multiple' => true,
                'placeholder' => '-- Pilih --',
                'class' => 'form-control input-sm select2 selectPreDiagnosis'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'language' => [
                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-new-diagnosa?id_with_text=1']),
                'dataType' => 'json',
                'data' => new JsExpression('
                    function(params) {
                        return {
                            type: "diagnosa_penyerta",
                            q: params.term,
                            page:params.page || 1,
                            limit:params.limit || 10,
                        }; 
                    }
                '),
                'processResults' => new JsExpression('
                    function (data, params) {
                        params.page = params.page || 1;
                        return {
                        results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        }
                    }
                ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(res){ return res.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);?>
        <?= $form->field($model, 'indications_examinations')->textInput()->label('Indications For Examinations'); ?>
        <?= $form->field($model, 'instrument')->textInput()->label('Instrument(s) Used'); ?>
        <?= $form->field($model, 'pre_medications')->textInput(); ?>
        <?= $form->field($model, 'procedure_performed')->widget(Select2::classname(), [
            'showToggleAll' => false,
            'data' => $options['procedure_performed'],
            'options' => [
                'multiple' => true,
                'placeholder' => '-- Pilih --',
                'class' => 'form-control input-sm select2 ProcedurePerformed'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'language' => [
                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-tindakan-endoskopi?']),
                'dataType' => 'json',
                'data' => new JsExpression('
                    function(params) {
                        return {
                            all_text: 1,
                            q: params.term,
                            page:params.page || 1,
                            limit:params.limit || 10,
                        }; 
                    }
                '),
                'processResults' => new JsExpression('
                    function (data, params) {
                        params.page = params.page || 1;
                        return {
                        results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        }
                    }
                ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(res){ return res.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);?>
        <?= $form->field($model, 'findings')->textArea(['rows' => 5]); ?>
        <?= $form->field($model, 'sampling')->textInput(); ?>
        <?= $form->field($model, 'endoscopic_diagnosis')->widget(Select2::classname(), [
            'showToggleAll' => false,
            'data' => $options['endoscopic_diagnosis'],
            'options' => [
                'multiple' => false,
                'placeholder' => '-- Pilih --',
                'class' => 'form-control input-sm select2 selectPreDiagnosis'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'language' => [
                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-new-diagnosa?id_with_text=1']),
                'dataType' => 'json',
                'data' => new JsExpression('
                    function(params) {
                        return {
                            type: "diagnosa_utama",
                            q: params.term,
                            page:params.page || 1,
                            limit:params.limit || 10,
                        }; 
                    }
                '),
                'processResults' => new JsExpression('
                    function (data, params) {
                        params.page = params.page || 1;
                        return {
                        results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        }
                    }
                ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(res){ return res.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);?>
        <?= $form->field($model, 'endoscopic_diagnosis_sekunder')->widget(Select2::classname(), [
            'showToggleAll' => false,
            'data' => $options['endoscopic_diagnosis_sekunder'],
            'options' => [
                'multiple' => true,
                'placeholder' => '-- Pilih --',
                'class' => 'form-control input-sm select2 selectPreDiagnosis'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'maximumInputLength' => 50,
                'language' => [
                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-new-diagnosa?id_with_text=1']),
                'dataType' => 'json',
                'data' => new JsExpression('
                    function(params) {
                        return {
                            type: "diagnosa_penyerta",
                            q: params.term,
                            page:params.page || 1,
                            limit:params.limit || 10,
                        }; 
                    }
                '),
                'processResults' => new JsExpression('
                    function (data, params) {
                        params.page = params.page || 1;
                        return {
                        results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        }
                    }
                ')
                ],
                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                'templateResult' => new JsExpression('function(res){ return res.text;}'),
                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
            ],
        ]);?>
        <?= $form->field($model, 'recommendations')->textArea(['rows' => 3]); ?>
   </div>
   
    
    <div class="col-md-12">
        <div class="row ">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="form-group field-uploadhasilform-upload">
                    <label class="control-label text-left control-label col-sm-2" for="uploadhasilform-upload"><?= Yii::t('fe', 'Import Foto') ?></label>
                    <div class="col-md-4">
                        <?=
                            Html::button('<b><i class="fa fa-plus"></i></b>' . \Yii::t('fe', 'Tambah berkas'), [
                                'class' => 'btn btn-info btn-labeled btn-xs',
                                'id' => 'add-upload',
                                'disabled' => false
                            ]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
            </div>
        </div>
        <div id="formUpload">
            <?php if (!empty($model->additional_photo)){
                foreach($model->additional_photo as $i=> $val){ 
                    $i+=1;
                    $namaFile = explode('/',$val);
                    $namaFile = !empty(end($namaFile)) ? end($namaFile) : "Lihat File";
                    ?>
                    <div class="row mt-5" id="row-<?=$i;?>">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-sm btn-danger delete" data-idrow="<?=$i;?>"><i class="fa fa-trash"></i></button>
                            <a class="btn btn-info btn-sm lihat_file" href="<?=$val; ?>" target="blank" data-file="" data-id=""> <span id="lihat"> <?=$namaFile;?></span></a>
                            <input type="file" name="LaporanEndoskopiForm[additional_photo][]" id="file-<?=$i;?>" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected" >
                            <input type="hidden" name="LaporanEndoskopiForm[additional_photo_arr][]" value="<?=$val;?>" id="arr-<?=$i?>" class="" >
                        </div>
                        <div class="col-md-3">
                        </div>
                    </div>
                <?php
                }
            }else{?>
                <div class="row" id="row-1">
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-sm btn-danger delete" data-idrow="1"><i class="fa fa-trash"></i></button>
                        <label for="file-1" class="customform">
                            <i class="fa fa-upload"></i>
                            <span id="label-file-1">Pilih Berkas</span>
                        </label>
                        <input type="file" name="LaporanEndoskopiForm[additional_photo][]" id="file-1" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected" >
                        <div style="display:inline;" id="nama-file-1"> </div>
                        <div class="error-upload" id="error-file-1"></div>

                    </div>
                    <div class="col-md-3">
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                * <b><?= Yii::t('fe', 'maksimal 10 mb'); ?></b>
            </div>
            <div class="col-md-3">
            </div>
        </div>
        <div class="row">
            
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
            </div>
            <div class="col-md-3">
            </div>
        </div>
    </div> 
     
</div>
<?php
ActiveForm::end();
?>
<?php
$this->registerJs('
var penunjangId = ' . $id . ';
var max_upload = ' . $max_upload . ';
', View::POS_END, 'b-index');
$this->registerJs($this->render('../js/laporanendoskopi.js'), View::POS_END)
?>