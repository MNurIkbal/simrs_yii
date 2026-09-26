<?php

use app\components\DHtml;
use yii\helpers\Html;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Secondary Survey</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Kepala</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_kepala', [
                                        'value' => 'kepala',
                                        'label' => 'Kepala',
                                        'id' => 'survey_kepala-kepala'
                                    ])
                                    ?>
                                </div>
                                <div class="indent-form">
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'survey_kepala_utuh',
                                        'class' => 'kepala--dependent default-disabled',
                                        'data' => ['utuh' => 'Utuh', 'tidak_utuh' => 'Tidak Utuh'],
                                        'colSize' => '12'
                                    ])
                                    ?>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_kepala', [
                                        'value' => 'lacerasi',
                                        'label' => 'Lacerasi',
                                        'id' => 'survey_kepala-lacerasi'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_kepala_lacerasi', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['id' => 'survey_kepala_lacerasi--form', 'class' => 'default-disabled'])->label(false) ?>
                                </div>
                            </div>
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_kepala',
                                    'data' => $arrayConfig['survey_kepala'],
                                    'colSize' => 12
                                ])
                                ?>
                            </div>
                            <div class="row form-row">
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_kepala', [
                                        'value' => 'battle_sign',
                                        'label' => 'Battle Sign',
                                        'id' => 'survey_kepala-battle_sign'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_kepala_battle_sign', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['id' => 'survey_kepala_battle_sign--form', 'class' => 'default-disabled'])->label(false) ?>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_kepala', [
                                        'value' => 'lainnya',
                                        'label' => 'Lain-Lain',
                                        'id' => 'survey_kepala-lainnya'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_kepala_lainnya', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['id' => 'survey_kepala_lainnya--form', 'class' => 'default-disabled'])->label(false) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Mata</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_mata',
                                    'data' => $arrayConfig['survey_mata'],
                                    'colSize' => 12
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Mulut/Maksilofasial</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_mulut',
                                    'data' => ['luka_jaringan_lunak' => 'Luka Jaringan Lunak', 'fraktur' => 'Fraktur'],
                                    'colSize' => 12
                                ])
                                ?>
                            </div>
                            <div class="row form-row">
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_mulut', [
                                        'value' => 'luka_dalam',
                                        'label' => 'Luka Dalam',
                                        'id' => 'survey_mulut-luka_dalam'
                                    ])
                                    ?>
                                </div>
                                <div class="indent-form">
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'survey_mulut_luka_dalam',
                                        'class' => 'luka_dalam--dependent default-disabled',
                                        'data' => ['mulut' => 'Mulut', 'gigi' => 'Gigi'],
                                        'colSize' => '12'
                                    ])
                                    ?>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_mulut', [
                                        'value' => 'kerusakan_syaraf',
                                        'label' => 'Kerusakan Syaraf',
                                        'id' => 'survey_mulut-kerusakan_syaraf'
                                    ])
                                    ?>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_mulut', [
                                        'value' => 'lainnya',
                                        'label' => 'Lain-Lain',
                                        'id' => 'survey_mulut-lainnya'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_mulut_lainnya', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['id' => 'survey_mulut_lainnya--form', 'class' => 'default-disabled'])->label(false) ?>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_mulut', [
                                        'value' => 'tidak_ada_kelainan',
                                        'label' => 'Tidak Ada Kelainan',
                                        'id' => 'survey_mulut-tidak_ada_kelainan'
                                    ])
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Telinga</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_telinga',
                                    'data' => $arrayConfig['survey_telinga'],
                                    'otherColSize' => 12,
                                    'colSize' => 12
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Leher</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_leher',
                                    'otherFieldName' => 'survey_leher_lainnya',
                                    'data' => $arrayConfig['survey_leher'],
                                    'otherColSize' => 12,
                                    'colSize' => 12
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Extremitas</h5>
                        </div>
                        <div class="panel-body">
                            <?php
                            $explodeExtremitas = empty($model->survey_extremitas) ? [] : explode(',', $model->survey_extremitas);
                            foreach ($arrayConfig['survey_extremitas'] as $index => $extremitasItem) {
                            ?>
                                <div class="row form-row">
                                    <div class="col-sm-4">
                                        <?=
                                        Html::activeCheckbox($model, 'survey_extremitas', [
                                            'value' => $index,
                                            'label' => $extremitasItem,
                                            'id' => 'survey_extremitas-' . $index,
                                            'class' => 'survey-extremitas-check',
                                            'data-target' => 'survey_extremitas-' . $index . '--form'
                                        ])
                                        ?>
                                    </div>
                                    <?php if ($index != 'tidak_ada_kelainan') : ?>
                                        <div class="col-sm-6">
                                            <?= $form->field($model, 'extremitas_' . $index, ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['id' => 'survey_extremitas-' . $index . '--form', 'disabled' => in_array($index, $explodeExtremitas) ? false : true])->label(false) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="row form-row">
                                <div class="col-sm-4">
                                    <div class="indent-form">
                                        <?=
                                        DHtml::multipleRadio([
                                            'model' => $model,
                                            'fieldName' => 'survey_extremitas_pulsasi',
                                            'class' => 'survey_extremitas_pulsasi--dependent default-disabled',
                                            'data' => ['hilang' => 'Hilang', 'kurang' => 'Kurang'],
                                            'colSize' => '12'
                                        ])
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Dada</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'perlukaan_dinding_dada',
                                        'label' => 'Perlukaan Dinding Dada',
                                        'id' => 'survey_dada-perlukaan_dinding_dada'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'dada',
                                        'label' => 'Dada',
                                        'id' => 'survey_dada-dada'
                                    ])
                                    ?>
                                </div>
                                <div class="indent-form">
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'survey_dada_simetris_asimetris',
                                        'class' => 'survey_dada--dependent default-disabled',
                                        'data' => ['simetris' => 'Simetris', 'asimetris' => 'Asimetris'],
                                        'colSize' => '12'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'dada1',
                                        'label' => 'Dada',
                                        'id' => 'survey_dada-dada1'
                                    ])
                                    ?>
                                </div>
                                <div class="indent-form">
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'survey_dada_pneumo_hamatotoraks',
                                        'class' => 'survey_dada1--dependent default-disabled',
                                        'data' => ['pneumo' => 'Pneumo', 'hematotoraks' => 'Hematotoraks'],
                                        'colSize' => '12'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'nyeri_dada',
                                        'label' => 'Nyeri Dada',
                                        'id' => 'survey_dada-nyeri_dada'
                                    ])
                                    ?>
                                </div>
                                <div class="indent-form">
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'survey_dada_nyeri_lokasi')->textInput(['class' => 'default-disabled survey_nyeri_dada--dependent']) ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'survey_dada_nyeri_kapan')->textInput(['class' => 'default-disabled survey_nyeri_dada--dependent']) ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'survey_dada_nyeri_durasi')->textInput(['class' => 'default-disabled survey_nyeri_dada--dependent']) ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'survey_dada_nyeri_kegiatan')->textInput(['class' => 'default-disabled survey_nyeri_dada--dependent']) ?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'krepitasi',
                                        'label' => 'Krepitasi',
                                        'id' => 'survey_dada-krepitasi'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'flall_chest',
                                        'label' => 'Flall Chest',
                                        'id' => 'survey_dada-flall_chest'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'bunyi_jantung',
                                        'label' => 'Bunyi Jantung',
                                        'id' => 'survey_dada-bunyi_jantung'
                                    ])
                                    ?>
                                </div>
                                <div class="indent-form">
                                    <?=
                                    DHtml::multipleRadio([
                                        'model' => $model,
                                        'fieldName' => 'survey_dada_bunyi_jantung',
                                        'class' => 'default-disabled survey_dada_bunyi_jantung--dependent',
                                        'data' => ['ada' => 'Ada', 'tidak_ada' => 'Tidak Ada'],
                                        'colSize' => '12'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_dada', [
                                        'value' => 'tidak_ada_kelainan',
                                        'label' => 'Tidak Ada Kelainan',
                                        'id' => 'survey_dada-tidak_ada_kelainan'
                                    ])
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Abdomen</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'datar',
                                        'label' => 'Datar, tidak ada tahanan',
                                        'id' => 'survey_abdomen-datar'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'distensi',
                                        'label' => 'Distensi',
                                        'id' => 'survey_abdomen-distensi'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'memas',
                                        'label' => 'Memas / Jejas Bagian',
                                        'id' => 'survey_abdomen-memas'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_abdomen_memas')->textInput(['class' => 'default-disabled survey_memas--dependent'])->label(false) ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'luka_tembus',
                                        'label' => 'Luka Tembus',
                                        'id' => 'survey_abdomen-luka_tembus'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'cedera_retroperitoneal',
                                        'label' => 'Cedera Retroperitoneal',
                                        'id' => 'survey_abdomen-cedera_retroperitoneal'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'nyeri',
                                        'label' => 'Nyeri tekan bagian',
                                        'id' => 'survey_abdomen-nyeri'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_abdomen_nyeri')->textInput(['class' => 'default-disabled survey_abdomen_nyeri--dependent'])->label(false) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'lainnya',
                                        'label' => 'Lain-lain',
                                        'id' => 'survey_abdomen-lainnya'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_abdomen_lainnya')->textInput(['class' => 'default-disabled survey_abdomen_lainnya--dependent'])->label(false) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'bising_usus',
                                        'label' => 'Bising Usus',
                                        'id' => 'survey_abdomen-bising_usus'
                                    ])
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'survey_abdomen_bising_usus', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number default-disabled survey_abdomen_bising_usus--dependent'])->label(false) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?=
                                    Html::activeCheckbox($model, 'survey_abdomen', [
                                        'value' => 'tidak_ada_kelainan',
                                        'label' => 'Tidak Ada Kelainan',
                                        'id' => 'survey_abdomen-tidak_ada_kelainan'
                                    ])
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Pelvis</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_pelvis',
                                    'otherFieldName' => 'survey_pelvis_lainnya',
                                    'data' => $arrayConfig['survey_pelvis'],
                                    'otherColSize' => 12,
                                    'colSize' => 12
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Medulla Spinalis</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_medulla_spinalis',
                                    'data' => $arrayConfig['survey_medulla_spinalis'],
                                    'colSize' => 12
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">Kolumna Veterbalis</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row form-row">
                                <?=
                                DHtml::multipleCheckbox([
                                    'model' => $model,
                                    'fieldName' => 'survey_kolumna_vertebralis',
                                    'data' => $arrayConfig['survey_kolumna_vertebralis'],
                                    'colSize' => 12
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
