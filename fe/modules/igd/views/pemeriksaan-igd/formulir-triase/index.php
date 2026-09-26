<?php

use app\components\DHtml;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<style type="text/css">
    #modal_backdrop {
        z-index: 1045 !important;
    }
</style>
<div class="panel panel-body">
    <div class="panel-toolbar clearfix">
        <div class="col-md-5">
            <?= DocoHelpers::generateToolbar([
                'custom-save' => [
                    'title' => \Yii::t('fe', 'Simpan'),
                    'icon' => 'fa fa-save',
                    'attributes' => [
                        'id' => 'submit-triase',
                        'data-options' => 'click'
                    ]
                ],
                'custom-pdf' => [
                    'title' => \Yii::t('fe', 'Cetak'),
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'id' => 'cetak-triase',
                        'data-options' => 'click'
                    ]
                ],
            ], ''); ?>
        </div>
        <div class="col-md-4">
            <label class="control-label" style="font-size:16px;"><b>Kategori Triase : </b></label>
            <label class="label-value-form" id="kategori-triase">-</label>
        </div>
        <div class="col-md-3" style="text-align: right;">
            <?= DocoHelpers::generateToolbar([
                'custom-btn' => [
                    'type'  => 'button',
                    'title' => Yii::t('fe', 'List Triase'),
                    'icon'  => 'fa fa-list-ul',
                    'attributes' => [
                        'id'          => 'btn-list-triase',
                        'data-width'  => '90%',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action'      => '/igd/pemeriksaan-igd/modal-list-triase',
                        'disabled' => $disabled
                    ]
                ],
            ], ''); ?>
        </div>
    </div>
    <div class="panel-body panel-body-collapse-form-triase">
        <div class="row">
            <div class="form-triase" style="padding: 12px;">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-triase',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_SMALL,
                    ],
                ]);
                ?>
                <div class="row form-row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'dokter_id')->dropdownList([], ['id' => 'dokter_id-form']); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'perawat_id')->dropdownList([], ['id' => 'perawat_id-form']); ?>
                    </div>
                </div>
                <div class="row form-row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="text-label">Tanggal/Jam Pasien Datang</label>
                            <div class="col-sm-8">
                                <?= DateTimePicker::widget([
                                    'model' => $model,
                                    'attribute' => 'tgl_triase',
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'readonly' => true,
                                    'convertFormat' => true,
                                    'value' => date('d/m/Y H:i:s'),
                                    'pluginOptions' => [
                                        'format' => 'dd/MM/yyyy HH:mm:ss',
                                        'showMeridian' => true,
                                        'autoclose' => true,
                                        'todayBtn' => true,
                                        'endDate' => date('d/m/Y H:i:s'),
                                        'startDate' => $tgl_pendaftaran
                                    ]
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label col-sm-4" for="text-label">Pindah Bed</label>
                        <div class="col-md-8 btn-triage-group">
                            <?php foreach ($dataBed as $key => $value) : ?>
                                <button class="btn btn-bed-option<?= ArrayHelper::getValue($triaseData, 'kamartempattidur_id', null) == $value['kamartempattidur_id'] ? ' btn-info --selected' : '' ?>" <?= is_null(ArrayHelper::getValue($triaseData, 'kamartempattidur_id', null)) || ($value['status_isi'] && ArrayHelper::getValue($triaseData, 'kamartempattidur_id', null) != $value['kamartempattidur_id']) || $disabled ? 'disabled' : '' ?> type="button" data-bed-id="<?= $value['kamartempattidur_id'] ?>"><?= $value['no_tempattidur'] ?></button>
                            <?php endforeach; ?>
                            <button class="btn btn-info mb-5 hidden" id="add-bed">+</button>
                            <?= Html::activeHiddenInput($model, 'kamartempattidur_id', [
                                'value' => ArrayHelper::getValue($triaseData, 'kamartempattidur_id', null)
                            ]) ?>
                            <?= Html::activeHiddenInput($model, 'old_kamartempattidur_id', [
                                'value' => ArrayHelper::getValue($triaseData, 'kamartempattidur_id', null)
                            ]) ?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row form-row">
                    <table class="table table-bordered">
                        <thead>
                            <td>Pemeriksaan</td>
                            <td>Gangguan</td>
                            <td width="20%" class="asdasd">Resusitasi</td>
                            <td width="20%">Emergent</td>
                            <td width="20%">Urgent</td>
                            <td width="20%">Less Urgent</td>
                            <td width="20%">Non Urgent</td>
                        </thead>
                        <tr>
                            <td rowspan="<?= count($configVal['jalan_napas_extra']) ?>">Jalan Napas (Airway)</td>
                            <?php foreach ($configVal['jalan_napas_extra'] as $gangguan => $pilihan) : ?>
                                <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                                <?php foreach ($pilihan as $kategori => $list) : ?>
                                    <td data-triage_group="jalan_napas_extra">
                                        <?=
                                        DHtml::multipleCheckbox([
                                            'model' => $model,
                                            'fieldName' => 'jalan_nafas',
                                            'data' => $list,
                                            'colSize' => 12,
                                            'triage_group' => 'jalan_napas_extra',
                                            'class' => 'btn-triage-option jalan_nafas_' . $kategori . ' ' . $kategori . '-group'
                                        ])
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                                    <td></td>
                                <?php endfor; ?>
                        </tr>
                        <?php if (array_keys($configVal['jalan_napas_extra'])[count($configVal['jalan_napas_extra']) - 1] !== $gangguan) : ?>
                            <tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td rowspan="<?= count($configVal['pernapasan_extra']) ?>">Pernapasan (Breathing)</td>
                                <?php foreach ($configVal['pernapasan_extra'] as $gangguan => $pilihan) : ?>
                                    <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                                    <?php foreach ($pilihan as $kategori => $list) : ?>
                                        <td data-triage_group="pernapasan_extra">
                                            <?=
                                            DHtml::multipleCheckbox([
                                                'model' => $model,
                                                'fieldName' => 'pernafasan',
                                                'data' => $list,
                                                'colSize' => 12,
                                                'triage_group' => 'pernapasan_extra',
                                                'class' => 'btn-triage-option pernafasan_' . $kategori . ' ' . $kategori . '-group'
                                            ])
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                                        <td></td>
                                    <?php endfor; ?>
                            </tr>
                                <?php if (array_keys($configVal['pernapasan_extra'])[count($configVal['pernapasan_extra']) - 1] !== $gangguan) : ?>
                                    <tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <td rowspan="<?= count($configVal['sirkulasi_extra']) ?>">Sirkulasi (Circulation)</td>
                                        <?php foreach ($configVal['sirkulasi_extra'] as $gangguan => $pilihan) : ?>
                                            <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                                            <?php foreach ($pilihan as $kategori => $list) : ?>
                                                <td data-triage_group="sirkulasi_extra">
                                                    <?=
                                                    DHtml::multipleCheckbox([
                                                        'model' => $model,
                                                        'fieldName' => 'sirkulasi',
                                                        'data' => $list,
                                                        'colSize' => 12,
                                                        'triage_group' => 'sirkulasi_extra',
                                                        'class' => 'btn-triage-option sirkulasi_' . $kategori . ' ' . $kategori . '-group'
                                                    ])
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                                                <td></td>
                                            <?php endfor; ?>
                                    </tr>
                                    <?php if (array_keys($configVal['sirkulasi_extra'])[count($configVal['sirkulasi_extra']) - 1] !== $gangguan) : ?>
                                        <tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Kesadaran (Disability)</td>
                                            <td colspan="5">
                                                <div class="row form-row">
                                                    <div class="col-sm-3">
                                                        <?= $form->field($model, 'hasil_gcs')->textInput(['id' => 'hasil_gcs-form', 'class' => 'doco-number', 'readonly' => true]); ?>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <a href="<?= Url::to(['hitung-gcs', 'pendaftaran_id' => $pendaftaranId]) ?>" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Hitung GCS</a>
                                                        <?= Html::activeHiddenInput($model, 'gcseye_id', ['id' => 'gcseye_id-form']) ?>
                                                        <?= Html::activeHiddenInput($model, 'gcsverbal_id', ['id' => 'gcsverbal_id-form']) ?>
                                                        <?= Html::activeHiddenInput($model, 'gcsmotorik_id', ['id' => 'gcsmotorik_id-form']) ?>
                                                        <?= Html::activeHiddenInput($model, 'is_kapitis', ['id' => 'is_kapitis-form']) ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td rowspan="<?= count($configVal['disability']) ?>">Disability (Gangguan Lain)</td>
                                            <?php foreach ($configVal['disability'] as $gangguan => $pilihan) : ?>
                                                <td><?= str_replace('_', ' ', ucwords($gangguan, '_')) ?></td>
                                                <?php foreach ($pilihan as $kategori => $list) : ?>
                                                    <td data-triage_group="disability">
                                                        <?=
                                                        DHtml::multipleCheckbox([
                                                            'model' => $model,
                                                            'fieldName' => 'disability',
                                                            'data' => $list,
                                                            'colSize' => 12,
                                                            'triage_group' => 'disability',
                                                            'class' => 'btn-triage-option disability_' . $kategori . ' ' . $kategori . '-group'
                                                        ])
                                                        ?>
                                                    </td>
                                                <?php endforeach; ?>
                                                <?php for ($i = count($pilihan); $i < 5; $i++) : ?>
                                                    <td></td>
                                                <?php endfor; ?>
                                        </tr>
                                        <?php if (array_keys($configVal['disability'])[count($configVal['disability']) - 1] !== $gangguan) : ?>
                                            <tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Response Time</td>
                                                <td>
                                                    <?=
                                                    DHtml::multipleRadio([
                                                        'model' => $model,
                                                        'fieldName' => 'waktu_respon',
                                                        'data' => $configVal['waktu_respon']['resusitasi'],
                                                        'colSize' => 12
                                                    ])
                                                    ?>
                                                </td>
                                                <td>
                                                    <?=
                                                    DHtml::multipleRadio([
                                                        'model' => $model,
                                                        'fieldName' => 'waktu_respon',
                                                        'data' => $configVal['waktu_respon']['emergent'],
                                                        'colSize' => 12
                                                    ])
                                                    ?>
                                                </td>
                                                <td>
                                                    <?=
                                                    DHtml::multipleRadio([
                                                        'model' => $model,
                                                        'fieldName' => 'waktu_respon',
                                                        'data' => $configVal['waktu_respon']['urgent'],
                                                        'colSize' => 12
                                                    ])
                                                    ?>
                                                </td>
                                                <td>
                                                    <?=
                                                    DHtml::multipleRadio([
                                                        'model' => $model,
                                                        'fieldName' => 'waktu_respon',
                                                        'data' => $configVal['waktu_respon']['less_urgent'],
                                                        'colSize' => 12
                                                    ])
                                                    ?>
                                                </td>
                                                <td>
                                                    <?=
                                                    DHtml::multipleRadio([
                                                        'model' => $model,
                                                        'fieldName' => 'waktu_respon',
                                                        'data' => $configVal['waktu_respon']['non_urgent'],
                                                        'colSize' => 12
                                                    ])
                                                    ?>
                                                </td>
                                            </tr>
                    </table>
                </div>
                <hr>
                <div class="row form-row">
                    <div class="col-sm-6">
                        <div class="row form-row">
                            <div class="col-sm-4">
                                <label for="control-label text-label">Keluhan Utama</label>
                            </div>
                            <div class="col-sm-8 form-group">
                                <?=
                                Html::activeTextarea($model, 'keluhan_utama', ['class' => 'form-control']);
                                ?>
                                <div class="help-block">
                                </div>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-4">
                                <label for="control-label text-label">Alergi</label>
                            </div>
                            <div class="col-sm-8 form-group">
                                <?=
                                DHtml::dontKnowRadio($model, 'alergi', [
                                    'childDependent' => [
                                        'id' => 'is_alergi-form',
                                        'class' => 'is_alergi-check',
                                        'colSize' => 12
                                    ]
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-2">
                                <input type="checkbox" disabled name="alergi_obat_check" class="is_alergi-check" data-type="obat" id="cb-obat-alergi"> Obat
                            </div>
                            <div class="col-sm-6">
                                <?=
                                Html::activeTextInput($model, 'alergi_obat', [
                                    'class' => 'form-control default-disabled input-tag',
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-2">
                                <input type="checkbox" disabled name="alergi_lainnya_check" class="is_alergi-check" data-type="lainnya" id="cb-lainnya-alergi"> Lainnya
                            </div>
                            <div class="col-sm-6">
                                <?=
                                Html::activeTextInput($model, 'alergi_lainnya', [
                                    'class' => 'form-control default-disabled input-tag'
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-4">
                                <label for="control-label text-label">Trauma</label>
                            </div>
                            <div class="col-sm-8">
                                <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'trauma',
                                    'data' => $configVal['trauma'],
                                    'colSize' => 12
                                ])
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row form-row">
                            <div class="col-sm-12">
                                <label for="control-label text-label">
                                    <h5><b>Tanda Vital (Vital Sign)</b></h5>
                                </label>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(); ?>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']); ?>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'nafas', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']); ?>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => '°C']]])->textInput(['class' => 'doco-decimal-wcomma']); ?>
                            </div>
                        </div>
                        <div class="row form-row">
                            <div class="col-sm-12">
                                <?= $form->field($model, 'saturasi_oksigen', ['addon' => ['append' => ['content' => '%']]])->textInput(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var pendaftaranId = "' . $pendaftaranId . '"
    var triaseData    = ' . json_encode($triaseData) . '
    var configRules = ' . json_encode($configRules) . '
    var configData = ' . json_encode($configVal) . '
');
$this->registerJs($this->render('_index.js'), View::POS_END);
?>
