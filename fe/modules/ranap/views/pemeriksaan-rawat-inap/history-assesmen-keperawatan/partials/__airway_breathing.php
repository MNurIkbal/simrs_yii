<?php
use app\components\DHtml;
use yii\helpers\Html;
?>
<div class="row form-row" id="__airway_breathing">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Airway-Breathing-Circulation</h5>
        </div>
        <div class="panel-body">
            <p class="header-form">Airway/Jalan Napas</p>
            <div class="row form-row">
                <div class="col-sm-4">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'normal',
                                'label' => 'Normal',
                                'id' => 'jalur_nafas-normal',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'bersih_sumbatan',
                                'label' => 'Bersih Sumbatan',
                                'id' => 'jalur_nafas-bersih_sumbatan',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <div class="indent-form">
                                <?=
                                    DHtml::multipleCheckbox([
                                        'model' => $model,
                                        'fieldName' => 'jalan_nafas_bersin',
                                        'class' => 'bersih_sumbatan--dependent default-disabled',
                                        'data' => $arrayConfig['sumbatan'],
                                        'colSize' => '12'
                                    ])
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'oral_airway',
                                'label' => 'Oral Airway/Mulut',
                                'id' => 'jalur_nafas-oral_airway',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'ett',
                                'label' => 'ETT',
                                'id' => 'jalur_nafas-ett',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'tracheostomi',
                                'label' => 'Tracheostomi',
                                'id' => 'jalur_nafas-tracheostomi',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'bag_mask',
                                'label' => 'Bag Mask',
                                'id' => 'jalur_nafas-bag_mask',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'suctioning',
                                'label' => 'Suctioning',
                                'id' => 'jalur_nafas-suctioning',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= Html::activeCheckBox($model, 'jalur_nafas', [
                                'value' => 'oksigen',
                                'label' => 'Oksigen',
                                'id' => 'jalur_nafas-oksigen',
                                'data-fieldname' => 'jalur_nafas'
                            ]) ?>
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($model, 'jalur_nafas_oksigen', ['addon' => ['append' => ['content' => 'L/menit']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'jalur_nafas_oksigen--form'])->label(false) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <p class="header-form">Breathing/Pernapasan</p>
            <div class="row form-row">
                <div class="col-sm-4">
                    <div class="row form-row">
                        <div class="col-sm-4">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'spontan',
                                'id' => 'pernafasan-spontan',
                                'label' => 'Spontan',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($model, 'pernafasan_spontan', ['addon' => ['append' => ['content' => 'x/menit']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'pernafasan_spontan--form'])->label(false) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'retraksi_dada',
                                'id' => 'pernafasan-retraksi_dada',
                                'label' => 'Retraksi Dada',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'dyspnea',
                                'id' => 'pernafasan-dyspnea',
                                'label' => 'Dyspnea',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'takipnea',
                                'id' => 'pernafasan-takipnea',
                                'label' => 'Takipnea',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($model, 'pernafasan_takipnea', ['addon' => ['append' => ['content' => 'x/menit']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'pernafasan_takipnea--form'])->label(false) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'cuping_hidung',
                                'id' => 'pernafasan-cuping_hidung',
                                'label' => 'Cuping Hidung',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'ngorok',
                                'id' => 'pernafasan-ngorok',
                                'label' => 'Ngorok',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'stridor',
                                'id' => 'pernafasan-stridor',
                                'label' => 'Suctioning',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= Html::activeCheckBox($model, 'pernafasan', [
                                'value' => 'gargling',
                                'id' => 'pernafasan-gargling',
                                'label' => 'Gargling',
                                'data-fieldname' => 'pernafasan'
                            ]) ?>
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($model, 'pernafasan_gargling', ['addon' => ['append' => ['content' => 'L/menit']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'pernafasan_gargling--form'])->label(false) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <p class="header-form">Circulation/Sirkulasi</p>
            <div class="row form-row">
                <div class="col-sm-4">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'tensi', ['addon' => ['append' => ['content' => 'mMhg']]])->textInput([
                                'placeholder' => 'mm/Hg'
                            ]) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'detak_nadi', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']) ?>
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-4"></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php 
                                            // Html::activeRadio($model, 'tipe_nadi', [
                                            //     'value' => 'reguler',
                                            //     'id' => 'tipe_nadi-reguler',
                                            //     'label' => 'Reguler',
                                            //     'data-fieldname' => 'tipe_nadi'
                                            // ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?php 
                                            // Html::activeRadio($model, 'tipe_nadi', [
                                            //     'value' => 'ireguler',
                                            //     'id' => 'tipe_nadi-ireguler',
                                            //     'label' => 'Ireguler',
                                            //     'data-fieldname' => 'tipe_nadi'
                                            // ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'hasil_nadi')->textInput(['readonly' => 'true']) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'suhu_tubuh', ['addon' => ['append' => ['content' => '°C']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-4">Capilary refill</label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?= Html::activeRadio($model, 'capilary_refill', [
                                                'value' => '>2',
                                                'id' => 'capilary_refill-morethan',
                                                'label' => '> 2 Detik',
                                                'data-fieldname' => 'capilary_refill'
                                            ]) ?>
                                        </div>
                                        <div class="col-sm-12">
                                            <?= Html::activeRadio($model, 'capilary_refill', [
                                                'value' => '<2',
                                                'id' => 'capilary_refill-lessthan',
                                                'label' => '< 2 Detik',
                                                'data-fieldname' => 'capilary_refill'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-4">Perfusi</label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model' => $model,
                                                'fieldName' => 'perfusi',
                                                'colSize' => 12,
                                                'data' => $arrayConfig['perfusi']
                                            ])
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="">
                                <label class="control-label has-star col-sm-4">Akral</label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model' => $model,
                                                'fieldName' => 'akral',
                                                'colSize' => 12,
                                                'data' => $arrayConfig['akral']
                                            ])
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <label class="control-label has-star col-sm-4">Pendarahan</label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?= Html::activeRadio($model, 'pendarahan', [
                                            'value' => '0',
                                            'id' => 'pendarahan-0',
                                            'label' => 'Tidak Ada',
                                            'data-dependent' => [
                                                'id' => 'pendarahan_cc--form'
                                            ],
                                            'data-fieldname' => 'pendarahan'
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= Html::activeRadio($model, 'pendarahan', [
                                            'value' => '1',
                                            'id' => 'pendarahan-1',
                                            'label' => 'Ada',
                                            'data-dependent' => [
                                                'id' => 'pendarahan_cc--form'
                                            ],
                                            'data-fieldname' => 'pendarahan'
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= $form->field($model, 'pendarahan_cc', ['addon' => ['append' => ['content' => 'cc']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'pendarahan_cc--form'])->label(false) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>