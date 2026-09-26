<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use kartik\datetime\DateTimePicker;

?>
<div class="col-md-6">
    <?=Html::activeHiddenInput($model, 'dokter_id', ['class'=>'form-control'])?>
    <?=Html::activeHiddenInput($model, 'pendaftaran_id', ['class'=>'form-control pendaftaran_id'])?>
    <?=Html::activeHiddenInput($model, 'pasienadmisi_id', ['class'=>'form-control pasienadmisi_id'])?>
    <?=Html::activeHiddenInput($model, 'asesmenmedis_id', ['class'=>'form-control'])?>
    <?=Html::hiddenInput('golongan_umur',4, ['class'=>'form-control golongan_umur'])?>
    <?=Html::hiddenInput('pasien_id',2, ['class'=>'form-control pasien_id'])?>

    <div class="form-group required">
    <?=$form->field($model, 'kategori_asmed')->radioList($data_kategori_asesmen)?>
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
    <div class="form-group" style="margin-bottom: 8px !important">
        <label class="control-label col-sm-3"><?=$model->attributeLabels()['r_penyakitdahulu']?></label>
            <div class="col-sm-9 isi-loop wrapper-riwayat-penyakit-dahulu">
                
                <div class="row row-default">
                    <div class="col-sm-3">
                        <?=Html::dropDownList('riwayat_penyakit[0][tahun]', [], ArrayHelper::map($tahun, 'tahun', 'tahun') , ['class'=>'select2 form-control select-tahun'])?>
                    </div>
                    <div class="col-sm-3">
                        <?=Html::textInput('riwayat_penyakit[0][penyakit]', '', ['class'=>'form-control penyakit-default', 'placeholder' => "Penyakit"])?>
                    </div>
                    <div class="col-sm-3">
                        <?=Html::textInput('riwayat_penyakit[0][terapi]', '', ['class'=>'form-control  terapi-default', 'placeholder' => "Terapi"])?>
                    </div>
                    <div class="col-sm-3">
                        <?=Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-info btn-append-riwayat-penyakit-dahulu'])?>
                    </div>
                </div>
                <div class="row clone-div template-riwayat-penyakit-dahulu hidden" style="margin-top: 5px">
                    <div class="col-sm-3">
                        <?=Html::dropDownList('', [], ArrayHelper::map($tahun, 'tahun', 'tahun'), ['class'=>'form-control select-tahun','data-name'=>'tahun'])?>
                    </div>
                    <div class="col-sm-3">
                        <?=Html::textInput('', '', ['class'=>'form-control','data-name'=>'penyakit', 'placeholder' => "Penyakit"])?>
                    </div>
                    <div class="col-sm-3">
                        <?=Html::textInput('', '', ['class'=>'form-control','data-name'=>'terapi', 'placeholder' => "Terapi"])?>
                    </div>
                    <div class="col-sm-3">
                        <?=Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-info btn-append'])?>
                        <?=Html::button('<i class="fa fa-trash"></i>', ['class'=>'btn btn-sm btn-danger btn-remove-riwayat-penyakit-dahulu'])?>
                    </div>
                </div>
            </div>
    </div>
    <?php
    // echo $form->field($model, 'diagnosa_id')->dropDownList(ArrayHelper::map($data_diagnosa, 'diagnosa_id', 'diagnosa_nama'), ['class'=>'select2', 'prompt'=>'']);
    echo $form->field($model, 'r_penyakitkeluarga')->widget(Select2::classname(), [
        'initValueText' => isset($text_diagnosa_id) && $text_diagnosa_id != '' ? $text_diagnosa_id : null,
        'options' => [
            'multiple' => true,
            'id' => 'r_penyakitkeluarga',
            'placeholder' => '-- Pilih --',
            'class' => 'form-control input-sm select2'
        ],
        'pluginOptions' => [
            // 'allowClear' => true,
            'tags' => true,
            'tokenSeparators' => [',', '_'],
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
            ],
            'ajax' => [
                'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                'dataType' => 'json',
                'data' => new JsExpression('
                    function(params) {
                        return {
                            q: params.term,
                            type: "diagnosa_utama",
                            all_text: 0,
                            id_with_text: 1,
                        };
                    }
                ')
            ],
            'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
            'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
            'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
        ],
    ])
    ?>
    <?=$form->field($model, 'r_imunisasi')->dropDownList(ArrayHelper::map($data_imunisasi, 'diagnosa_nama', 'diagnosa_nama'), [
            'class' => 'form-control input-sm select2', 
            'multiple'=>'multiple',
        ])?>
    <?=$form->field($model, 'r_peskk')->textArea()?>
    <?=$form->field($model, 'tgl_asesmenmedis')->widget(DateTimePicker::className(), [
                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                    'readonly' => true,
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'dd/MM/yyyy HH:mm:ss',
                        'autoclose' => true,
                        'todayBtn' => true
                    ]
                ]);
    ?>
</div>

<div class="col-md-6">
    <div class="form-group">
        <div class="required">
            <label class="control-label col-sm-3"><?= $model->attributeLabels()['sumber_info'] ?></label>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <?= Html::activeRadio($model, 'allo_or_auto', [
                    'value' => '0',
                    'id' => 'allo_or_auto-0-asmed',
                    'label' => 'Pasien',
                    'class' => 'allo_or_auto-radio',
                    'data-dependent' => [
                        'id' => 'sumber_hubungan--dependent'
                    ],
                    'data-fieldname' => 'allo_or_auto'
                ]) ?>
            </div>
            <div class="row">
                <?= Html::activeRadio($model, 'allo_or_auto', [
                    'value' => '1',
                    'id' => 'allo_or_auto-1-asmed',
                    'label' => 'Orang lain, Hubungan dengan pasien',
                    'class' => 'allo_or_auto-radio',
                    'data-dependent' => [
                        'id' => 'sumber_hubungan--dependent'
                    ],
                    'data-fieldname' => 'allo_or_auto'
                ]) ?>
            </div>
            <div class="row">
                <?= $form->field($model, 'sumber_hubungan')->textInput(['class' => 'sumber-hubungan default-disabled', 'id' => 'sumber_hubungan--dependent'])->label(false) ?>
            </div>
        </div>
    </div>

    
    
    <?=$form->field($model, 'is_merokok')->radioList($data_isMerokok)?>
    <div class="form-group jumlah-batang-rokok">
        <label class="control-label col-sm-3"><?=$model->attributeLabels()['jumlah_rokok']?></label>
            <div class="col-sm-5">
                <div class="input-group">
                    <?=Html::activeTextInput($model, 'jumlah_rokok', ['class'=>'form-control jml-rokok', 'readonly' => 'true'])?>
                    <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', '/Hari')?></span>
                </div>
            </div>
    </div>
    <?=$form->field($model, 'is_merokok_pasif')->radioList($data_status_meroko)?>
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
