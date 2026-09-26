<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\widgets\DateTimePicker;
use yii\web\JsExpression;

?>

<!-- Section Left -->
<div class="col-md-6">
    <div class="row">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Jenis Rawat") ?></b></label>
        </div>
        <div class="col-sm-8 delete-on-edit" style="display: flex;">
            <p><b>:</b></p>
            <div style="margin-left: 7px;">
                <?= Html::activeRadioList($model, 'jenis', [2 => 'JALAN', 1 => 'INAP', 3 => 'IGD'], [
                    'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                        $disabled = '';
                        $check = "";
                        if ($model->jenis == $value) {
                            $check = 'checked="checked"';
                        }
                        $return = '<label class="delete-on-edit radio-' . $value . ' radio-jenis-rawat-'.$value.'">';
                        $return .= '<input class="delete-on-edit" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . ' id="jenis-' . $value . '">';
                        $return .= ' <i></i>';
                        $return .= '<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                    }
                ]);
                ?>
                <span class="section-naik-turunkelas">
                    <?= Html::activeCheckbox($model, 'is_naikkelas', [
                        'label' => 'Naik/Turun Kelas',
                        'class' => 'delete-on-edit'
                    ]) ?>
                </span>
                <span class="section-rawat-intensif">
                    <?= Html::activeCheckbox($model, 'is_rawatintensif', [
                        'label' => 'Rawat Intensif',
                        'class' => 'delete-on-edit'
                    ]) ?>
                </span>
                <span class="section-eksekutif">
                    <?= Html::activeCheckbox($model, 'kelas_eksekutif', [
                        'label' => Yii::t("fe", "Eksekutif"),
                        'class' => 'kelas_eksekutif delete-on-edit'
                    ]) ?>
                </span>
            </div>
            
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Nama Pasien") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= Html::activeTextInput(
                $model,
                'nama_pasien',
                [
                    'id' => 'nama_pasien',
                    'class' => 'form-control input-sm free-txt',
                    'readonly' => true
                ]
            ) ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "No Rekamedik") ?></b></label>
        </div>
        <div class="col-sm-8" style="display: flex;">
            <?= Html::activeTextInput(
                $model,
                'no_rekam_medik',
                [
                    'id' => 'no_rekam_medik',
                    'class' => 'form-control free-txt',
                    'readonly' => true
                ]
            ) ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Tanggal Rawat") ?></b></label>
        </div>
        <div class="col-md-6 delete-on-edit">
            <div>
                <label class="text-left control-label col-sm-4" style="padding: 0 10px; margin-top: 3px"><?= Yii::t("fe", "Masuk") ?></label>
                <div class="col-md-1" style="padding: 0 10px; margin-top: 7px"><i class="glyphicon glyphicon-calendar kv-dp-icon"></i></div>
                <div class="col-md-5"> <?php $model['tgl_masuk'] = date('d-M-Y H:i', strtotime($model['tgl_masuk'])); ?>
                <?= Html::activeTextInput($model, 'tgl_masuk', ['class' => 'form-control date delete-on-edit', 'style' => 'width: 150px', 'readonly' => true]) ?></div>
            </div>
            <div class="mt-3">
                <label class="text-left control-label col-sm-4" style="padding: 0 10px; margin-top: 3px"><?= Yii::t("fe", "Keluar") ?></label>
                <div class="col-md-1" style="padding: 0 10px; margin-top: 7px"><i class="glyphicon glyphicon-calendar kv-dp-icon"></i></div>
                <div class="col-md-5"> <?php $model['tgl_keluar'] = date('d-M-Y H:i', strtotime($model['tgl_keluar'])); ?>
                <?= Html::activeTextInput($model, 'tgl_keluar', ['class' => 'form-control date delete-on-edit mt-3', 'style' => 'width: 150px', 'readonly' => true]) ?></div>
                <!-- <?= $form->field($model, 'tgl_keluar', [])->widget(DateTimePicker::classname(), [
                        'name' => 'date_keluar',
                        'id' => 'tgl_keluar',
                        'removeButton' => false,
                        'value' => date('Y-M-d H:i'),
                        'readonly' => true,
                        'language' => 'en',
                        'options' => [
                            'tabindex' => 4,
                        ],
                        'pluginOptions' => [
                            'startDate' => $model['tgl_masuk'],
                            'endDate' => date('d-M-Y H:i', strtotime('now')),
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy hh:ii',
                            'minView' => 2
                        ]
                    ])->label(Yii::t('fe', 'Keluar')); ?>  -->
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Cara Masuk") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= Html::activeDropDownList($model, 'rujukanrs', ArrayHelper::map($opsi['rujukanrs'], 'lookup_value', 'lookup_name'), ['class' => 'form-control select2 delete-on-edit']) ?>
        </div>
    </div>
    <div class="row hide-naik-kelas hidden mt-3" style="margin-bottom: 10px">
    <div class="col-sm-4">
        <label class="text-left control-label " style="padding: 0 10px; margin-top: 3px"><b><?= Yii::t("fe", "Kelas Pelayanan") ?></b></label>
    </div>
        <div class="col-sm-8 delete-on-edit">
            <?= Html::activeRadioList($model, 'naik_kelas', [3 => 'Kelas 3', 2 => 'Kelas 2', 1 => 'Kelas 1', 4 => 'Diatas kelas 1'], [
                'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                    $disabled = '';
                    $check = "";
                    if ($model->naik_kelas == $value) {
                        $check = 'checked="checked"';
                    }
                    $return = '<label class="radio-' . $value . '">';
                    $return .= '<input class="delete-on-edit" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . ' id="naikkelas-' . $value . '">';
                    $return .= ' <i></i>';
                    $return .= '<span>' . ucwords($label) . '</span>';
                    $return .= '</label>';
                    return $return;
                }
            ]);
            ?>

        </div>
    </div>
    <div class="row covid-select hidden mt-3" style="margin-bottom: 5px" style="padding: 0 10px">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "RS Darurat / Lapangan") ?></b></label>
        </div>
        <div class="col-sm-8">
            
            <?= Html::activeRadioList($model, 'rs_darurat', [1 => 'Ya', 0 => 'Tidak'], [
                'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                        $disabled = '';
                        $check = "";
                        if ($model->rs_darurat == $value) {
                            $check = 'checked="checked"';
                        }
                        $return = '<label class="radio-' . $value . '">';
                        $return .= '<input class="delete-on-edit" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . ' id="isolasi_rs-' . $value . '">';
                        $return .= ' <i></i>';
                        $return .= '<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                    }
                ]);
            ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "LOS (hari)") ?></b></label>
        </div>
        <div class="col-sm-8">
            <p id="los"><b>:</b>&nbsp; <?= $info['los'] ?> </p>
        </div>
    </div>
    <div class="row hide-rawat-intensif hidden" style="margin-bottom: 10px">
        <div class="col-sm-4">
                <label class="text-left control-label" style="padding: 0 10px; margin-top: 8px"><b><?= Yii::t("fe", "Ventilator") ?></b></label>
        </div>
        <div class="col-sm-8" style="display: flex; justify-content: space-between;">
            <div class="checkbox">
                <?= Html::activeCheckbox($model, 'ventilator', [
                    'label' => Yii::t("fe", "Ya"),
                    'class' => 'delete-on-edit'
                ]) ?>
            </div>
            <div style="display: flex;" id="date-ventilator" class="hidden">
                <div style="display: flex;">
                    <label class="text-left control-label" style="padding: 0 10px; margin-top: 10px"><?= Yii::t("fe", "Intubasi") ?></label>
                    <span> <?php $model['intubasi'] = date('d-M-Y H:i', strtotime($model['intubasi'])); ?></span>
                    <?= Html::activeTextInput($model, 'intubasi', ['class' => 'form-control date-intubasi delete-on-edit', 'style' => 'width: 150px', 'readonly' => true]) ?>
                </div>
                <div style="display: flex;">
                    <label class="text-left control-label" style="padding: 0 10px; margin-top: 10px"><?= Yii::t("fe", "Ekstubasi") ?></label>
                    <span> <?php $model['ekstubasi'] = date('d-M-Y H:i', strtotime($model['ekstubasi'])); ?>
                    <?= Html::activeTextInput($model, 'ekstubasi', ['class' => 'form-control date-intubasi delete-on-edit', 'style' => 'width: 150px', 'readonly' => true]) ?></span>
                </div>
            </div>
            
            <!-- <?= Html::activeTextInput($model, 'ventilator', ['class' => 'form-control input-sm validate-minus delete-on-edit doco-number free-txt']) ?> -->
        </div>
    </div>
    <div class="row mt-3" style="margin-bottom: 10px">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "ADL Score") ?></b></label>
        </div>
        <div class="col-sm-8">
            <div class="col-sm-2">
                <b>Sub Acute:</b>

            </div>
            <div class="col-sm-4">
                <?= Html::activeTextInput(
                    $model,
                    'adl_subacute',
                    [
                        'id' => 'adl_subacute',
                        'class' => 'form-control delete-on-edit free-txt',
                        'type' => 'number'
                    ]
                ) ?>
            </div>
            <div class="col-sm-2">
                <b>Cronic:</b>
            </div>
            <div class="col-sm-4">
                <?= Html::activeTextInput(
                    $model,
                    'adl_cronic',
                    [
                        'id' => 'adl_cronic',
                        'class' => 'form-control delete-on-edit free-txt',
                        'type' => 'number'
                    ]
                ) ?>
            </div>
            <br>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Dokter Penanggung Jawab") ?></b></label>
        </div>
        <div class="col-sm-8" id="dokter-single">
            <?= Html::dropDownList('KlaimInacbgRanapForm[dokter_id]', '', $dokterDpjp, ['class' => 'form-control select2 select-dokter delete-on-edit', 'id' => "dokter_new", 'data-url' => Url::to(['get-dokter'])]) ?>
            <div class="text-danger err-dokter-dpjp"></div>
        </div>
        <div class="col-sm-8" id="dokter-multiple">
            <?= $form->field($model, 'dokter_additional')->widget(Select2::classname(),[
                    'showToggleAll' => false,
                    'data' => $dokterDpjp,
                    'options' => [
                        'multiple' => true,
                        'placeholder' => '-- Pilih --',
                        'class' => 'form-control input-sm select2 delete-on-edit',
                        'id' => 'dokter_new_multiple'
                    ],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => [',', '_'],
                        'maximumInputLength' => 50,
                        // 'allowClear' => true,
                        // 'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                        ],
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-new-dokter']),
                            'dataType' => 'json',
                            'data' => new JsExpression('
                                function(params) {
                                    return {
                                        q: params.term,
                                        all_text: 0,
                                        id_with_text: 1,
                                        page:params.page || 1
                                    }; 
                                }
                            '),
                            'processResults' => new JsExpression('
                                function (data, params) {
                                    params.page = params.page || 1;
                                    return data
                                }
                            ')
                        ],
                        'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                        'templateResult' => new JsExpression ('function(dokter){ return dokter.text;}'),
                        'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                    ],
                ])->label(false);
            ?>
            <div class="text-danger err-dokter-dpjp"></div>
        </div>
        
    </div>
    <div class="row sitb-section" style="margin-top: 20px;">
        <div class="col-md-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Pasien TB") ?></b></label>
        </div>
        <div class="col-md-8">
            <div class="row" style="padding-left: 10px;">
                <div class="col-md-1">
                    <div class="form-group highlight-addon">
                        <div class="checkbox">
                            <?= Html::activeCheckbox($model, 'pasien_tb', [
                                'label' => Yii::t("fe", "Ya"),
                                'class' => 'pasien_tb'
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-11">
                    <div class="sitb-field" style="display: flex;">
                        <input type="number" class="form-control mr-1 pasien_tb" style="max-width: 200px;" name="sitb" id="sitb">
                        <button type="button" class="btn btn-primary btn-xs btn-labeled delete-on-edit search-sitb" style="min-width: 110px;"><b><i class="fa fa-search"></i></b>Validasi SITB</button>
                        <button type="button" class="btn btn-primary btn-xs btn-labeled delete-on-edit ubah-sitb" onclick="batalSitb()"><b><i class="fa fa-edit"></i></b>Ubah</button>
                        <div class="mt-3 ml-2 ubah-sitb"><span style="font-size: 12px;">Nomor register valid</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Section Right -->
<div class="col-md-6" style="padding-left: 70px;">
    <div class="row">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Umur") ?></b></label>
        </div>
        <div class="col-sm-8">
            <p><b>:</b>&nbsp; <?= isset($info['umur']) ? $expUmur : '-' ?> </p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Hak Kelas") ?></b></label>
        </div>
        <div class="col-sm-8 delete-on-edit section-jenis-kelas-rawat">
            <?= Html::activeRadioList($model, 'jenis_kelasrawat', [3 => 'Kelas 3', 2 => 'Kelas 2', 1 => 'Kelas 1'], [
                'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                    $disabled = '';
                    $check = "";
                    if ($model->naik_kelas == $value) {
                        $check = 'checked="checked"';
                    }
                    $return = '<label class="radio-' . $value . '">';
                    $return .= '<input class="delete-on-edit" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . ' id="hakkelas-' . $value . '">';
                    $return .= ' <i></i>';
                    $return .= '<span>' . ucwords($label) . '</span>';
                    $return .= '</label>';
                    return $return;
                }
            ]);
            ?>
        </div>
    </div>
    <div class="row hide-naik-kelas hidden" style="margin-bottom: 10px">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px; margin-top: 8px"><b><?= Yii::t("fe", "Lama (Hari)") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= Html::activeTextInput($model, 'lama_rawatkelas', ['class' => 'form-control input-sm validate-minus delete-on-edit free-txt', 'style' => 'width:70px']) ?>
        </div>
    </div>
    <div class="row hide-rawat-intensif hidden" style="margin-bottom: 10px">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px; margin-top: 8px"><b><?= Yii::t("fe", "Rawat Intensif (Hari)") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= $form->field($model, 'lama_rawatintensif',[
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-12'
                ],
            ])->textInput([
                'class' => 'form-control input-sm validate-minus delete-on-edit free-txt',
                'style' => 'width: 20%'
            ])->label(false); ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Berat Lahir (gram)") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= Html::activeTextInput($model, 'berat_lahir', ['class' => 'form-control input-sm validate-minus delete-on-edit doco-number free-txt']) ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px; margin-top: 8px"><b><?= Yii::t("fe", "Cara Pulang") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= Html::activeDropDownList($model, 'carapulang_id', ArrayHelper::map($opsi['carakeluar'], 'lookup_value', 'lookup_name'), ['class' => 'form-control select2 delete-on-edit']) ?>
        </div>
    </div>
    <div class="row covid-select hidden mt-3" style="margin-bottom: 5px" style="padding: 0 10px">
        <div class="col-sm-4">
            <label class="text-left control-label"><b><?= Yii::t("fe", "Isolasi RS") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= Html::activeRadioList($model, 'isolasi_rs', [1 => 'Ya', 0 => 'Tidak'], [
                'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                        $disabled = '';
                        $check = "";
                        if ($model->isolasi_rs == $value) {
                            $check = 'checked="checked"';
                        }
                        $return = '<label class="radio-' . $value . '">';
                        $return .= '<input class="delete-on-edit" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' ' . $disabled . ' id="isolasi_rs-' . $value . '">';
                        $return .= ' <i></i>';
                        $return .= '<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                    }
                ]);
            ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label" style="padding: 0 10px"><b><?= Yii::t("fe", "Tarif") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?= Html::activeDropDownList($model, 'tarif', $jenistarif, ['class' => 'form-control select2 delete-on-edit']) ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-4">
            <label class="text-left control-label " style="padding: 0 10px"><b><?= Yii::t("fe", "Tarif Rumah Sakit") ?></b></label>
        </div>
        <div class="col-sm-8">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Rp.</span>
                <?php $model['total_tarifrs'] = isset($model['total_tarifrs']) ? number_format($model['total_tarifrs'], 0, ',', '.') : '0' ?>
                <?= Html::activeTextInput($model, 'total_tarifrs', ['class' => 'form-control doco-number', 'style' => 'text-align: right', 'readonly' => true]) ?>
            </div>
        </div>
    </div>
    <div class="row  mt-3 tarif_poli_eks hidden">
        <div class="col-sm-4">
            <label class="text-left control-label " style="padding: 0 10px"><b><?= Yii::t("fe", "Tarif Polis Eks.") ?></b></label>
        </div>
        <div class="col-sm-8">
            <?=Html::activeTextInput($model, 'tarif_poli_eks', ['class'=>'form-control doco-number', 'style'=>'text-align: right'])?>
        </div>
    </div>
</div>