<?php

use app\components\DHtml;
use yii\helpers\Html;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Identifikasi Kebutuhan Edukasi</h5>
        </div>
        <div class="panel-body">
            <p class="header-form">Identifikasi Dukungan Edukasi</p>
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Bahasa yang dipakai</label>
                        <div class="col-sm-10">
                            <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'bahasa_dipakai',
                                    'otherFieldName' => 'bahasa_dipakai_lainnya',
                                    'data' => $arrayConfig['kebutuhan_edukasi']['bahasa'],
                                    'colSize' => 2
                                ])
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Penerjemah</label>
                        <div class="col-sm-10">
                            <?=
                                DHtml::trueFalseRadio($model, 'is_penerjemah')
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Media</label>
                        <div class="col-sm-10">
                            <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'media',
                                    'otherFieldName' => 'media_lainnya',
                                    'data' => $arrayConfig['kebutuhan_edukasi']['media'],
                                    'colSize' => 2
                                ])
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <p class="header-form">Identifikasi Hambatan Edukasi</p>
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <?=
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'identifikasi_hambatan',
                                'data' => $arrayConfig['kebutuhan_edukasi']['hambatan'],
                                'colSize' => 3
                            ])
                        ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <p class="header-form">Kebutuhan Edukasi</p>
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-4">Perlu Sistem Rujukan</label>
                        <div class="col-sm-8">
                            <?=
                                DHtml::trueFalseRadio($model, 'is_sistem_rujukan', [
                                    'colSize' => 12
                                ])
                            ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'materi') ?>
                    <?= $form->field($model, 'edukator') ?>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Kesediaan Pasien Menerima Informasi</label>
                        <div class="col-sm-8">
                            <?=
                                DHtml::trueFalseRadio($model, 'is_ketersediaan_pasien', [
                                    'colSize' => 12
                                ])
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Kemampuan Membaca</label>
                        <div class="col-sm-8">
                            <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'is_kemampuan_membaca',
                                    'data' => [
                                        1 => 'Mampu',
                                        0 => 'Tidak Mampu'
                                    ],
                                    'withoutOtherField' => true,
                                    'colSize' => 12
                                ])
                            ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'bahasa') ?>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-4">Dibutuhkan penerjemah</label>
                        <div class="col-sm-8">
                            <div class="col-sm-12">
                                <?=
                                    Html::activeRadio($model, 'is_dibutuhkan_penerjemah', [
                                        'value' => '0',
                                        'label' => 'Tidak',
                                        'id' => 'is_dibutuhkan_penerjemah--0',
                                        'data-dependent' => [
                                            'id' => 'penerjemah_bahasa--form'
                                        ]
                                    ])
                                ?>
                            </div>
                            <div class="col-sm-4">
                                <?=
                                    Html::activeRadio($model, 'is_dibutuhkan_penerjemah', [
                                        'value' => '1',
                                        'label' => 'Ya',
                                        'id' => 'is_dibutuhkan_penerjemah--1',
                                        'data-dependent' => [
                                            'id' => 'penerjemah_bahasa--form'
                                        ]
                                    ])
                                ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'penerjemah_bahasa', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['id' => 'penerjemah_bahasa--form', 'class' => 'default-disabled'])->label(false) ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Hambatan Emotional</label>
                        <div class="col-sm-8">
                            <?= DHtml::trueFalseRadio($model, 'is_hambatan_emotional', ['colSize' => 12, 'childDependent' => ['class' => 'hambatan_emotional--dependent']]) ?>
                            <div class="indent-form">
                                <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'hambatan_emotional_lainnya',
                                        'class' => 'hambatan_emotional--dependent default-disabled',
                                        'data' => $arrayConfig['kebutuhan_edukasi']['hambatan_emotional'],
                                        'colSize' => 12
                                    ])
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Keterbatasan Fisik & Kognitif</label>
                        <div class="col-sm-8">
                            <?=
                                Html::activeRadio($model, 'is_keterbatasan_fisik', [
                                    'label' => 'Tidak',
                                    'id' => 'is_keterbatasan_fisik--0',
                                    'data-dependent' => [
                                        'id' => 'keterbatasan_fisik--form'
                                    ],
                                    'value' => '0'
                                ])
                            ?>
                        </div>
                        <div class="col-sm-offset-4 col-sm-8">
                            <div class="row">
                                <div class="col-sm-4">
                                    <?=
                                        Html::activeRadio($model, 'is_keterbatasan_fisik', [
                                            'label' => 'Ada',
                                            'id' => 'is_keterbatasan_fisik--1',
                                            'data-dependent' => [
                                                'id' => 'keterbatasan_fisik--form'
                                            ],
                                            'value' => '1'
                                        ])
                                    ?>
                                </div>
                                <div class="col-sm-8">
                                    <?= $form->field($model, 'keterbatasan_fisik', ['horizontalCssClasses' =>['wrapper' => 'col-sm-12']])->textInput(['id' => 'keterbatasan_fisik--form', 'class' => 'default-disabled'])->label(false) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>