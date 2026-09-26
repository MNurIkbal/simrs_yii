<?php

/**
 * @Author: rizal
 * @Date:   2018-11-28 10:41:34
 */

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>
<style type="text/css">
    .m-left {
        margin-left: 7px;
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'form-sga',
    'action'=> '/gizi/asesmen-gizi/simpan-sga?id='.$id,
    'formConfig' => ['labelSpan' => 2,
                    'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
]) ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">

                <?= $form->field($model, 'sumberdata',[
                        'horizontalCssClasses' => [
                            'label' => 'control-label col-sm-2',
                            'wrapper' => 'col-md-10'
                        ],
                    ])
                    ->radioList(ArrayHelper::map($lookupPerawat['asmen_dari'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>true, 'itemOptions'=>['disabled'=>$preview]]) ?>
                <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormsumberdata"></div>
                <div class="sumber-dari <?= $model->sumberdata == 2 ? '' : 'hidden' ?>">
                    <div class=row>
                        <div class='col-sm-12'>
                            <label class="control-label col-sm-2"></label>
                            <span class="col-sm-10">
                                <?= $form->field($model, 'sumberdata_dari', [
                                    'horizontalCssClasses' => [
                                        'label' => 'control-label col-sm-2',
                                        'wrapper' => 'col-md-10'
                                    ],
                                    'template'=>"{input}\n{hint}\n{error}",
                                ])->textInput(['disabled'=>$preview]); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <br>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Riwayat') ?></h5>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <legend><?=Yii::t('fe', 'Perubahan berat badan')?></legend>

                            <div class="row">
                                <div class="col-sm-4 required">
                                    <label class="control-label col-sm-6 required"><?= $model->getAttributeLabel('bb_biasanya'); ?></label>
                                    <?= $form->field($model, 'bb_biasanya', [
                                        'addon'=>[
                                            'append' => [
                                                'content'=>'Kg'
                                            ]
                                        ],
                                        'template'=>"{input}\n{hint}\n{error}",
                                    ])->textInput(['class'=>'form-control doco-decimal-wcomma hitung_bb m-left',
                                        // 'onchange'=> "return convertToDecimal(this, '.' , ',');",
                                        'id'=>'bb_biasanya',
                                        'data-limiter' => 'true',
                                        'disabled'=>$preview
                                    ]) ?>

                                    <div class="col-sm-offset-6" id="error_AsesmenAwalGiziFormbb_biasanya"></div>
                                </div>
                                <div class="col-sm-3 required">
                                    <label class="control-label col-sm-5"><?= $model->getAttributeLabel('bb_saatini'); ?></label>
                                    <?= $form->field($model, 'bb_saatini', [
                                        'horizontalCssClasses' => [
                                            'label' => 'control-label col-sm-5',
                                            'wrapper' => 'col-md-7'
                                        ],
                                        'addon'=>[
                                            'append' => [
                                                'content'=>'Kg'
                                            ]
                                        ],
                                        'template'=>"{input}\n{hint}\n{error}",
                                    ])->textInput([
                                        'class'=>'form-control doco-decimal-wcomma hitung_bb',
                                        'id'=>'bb_saatini',
                                        'data-limiter' => 'true',
                                        // onkeypress="return check_digit(event,this,8,2);"
                                        'disabled'=>$preview
                                    ]) ?>
                                    <div class="col-sm-offset-4" id="error_AsesmenAwalGiziFormbb_saatini"></div>
                                </div>
                                <div class="col-sm-5"></div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4 required">
                                    <label class="control-label col-sm-6 required"><?= Yii::t('fe', 'Perubahan'); ?></label>
                                    <?= $form->field($model, 'perubahan_kg_data', [
                                        'addon'=>[
                                            'append' => [
                                                'content'=>'Kg'
                                            ]
                                        ],
                                        'template'=>"{input}\n{hint}\n{error}",
                                    ])->textInput(['class'=>'form-control hasil_hitung_kg m-left', 'disabled'=>true]) ?>
                                    <div class="col-sm-offset-6" id="error_AsesmenAwalGiziFormperubahan_kg"></div>
                                    <?= Html::activeHiddenInput($model, 'perubahan_kg') ?>
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label col-sm-5"></label>
                                    <?= $form->field($model, 'perubahan_persen_data', [
                                        'addon'=>[
                                            'append' => [
                                                'content'=>'%'
                                            ]
                                        ],
                                        'template'=>"{input}\n{hint}\n{error}",
                                    ])->textInput(['class'=>'form-control hasil_hitung_persen', 'disabled'=>true]) ?>
                                    <div class="col-sm-offset-4" id="error_AsesmenAwalGiziFormperubahan_persen"></div>
                                    <?= Html::activeHiddenInput($model, 'perubahan_persen') ?>
                                </div>
                                <div class="col-sm-5"></div>
                            </div>
                            <div class=row>
                                <div class='col-sm-12 required'>
                                    <label class="control-label col-sm-2 required"><?=  $model->getAttributeLabel('perubahan_hasil'); ?></label>
                                    <span class="col-sm-5">
                                        <?= $form->field($model, 'perubahan_hasil_nama', [
                                            'template'=>"{input}\n{hint}\n{error}",
                                        ])->textInput([
                                            'class'=>'form-control hasil_hitung_perubahan',
                                            'readonly'=>'readonly',
                                        ]); ?>

                                        <?= Html::activeHiddenInput($model, 'perubahan_hasil') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 required">
                                    <label class="control-label col-sm-2 required"><?= $model->getAttributeLabel('kategori_bb'); ?></label>
                                    <span class="col-sm-5">
                                        <?= $form->field($model, 'kategori_bb', [
                                            'template'=>"{input}\n{hint}\n{error}",
                                        ])->radioList(ArrayHelper::map($lookupPerawat['gizi_kategori'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>true, 'itemOptions'=>['disabled'=>$preview]]) ?>
                                        <div id="error_AsesmenAwalGiziFormkategori_bb"></div>
                                    </span>
                                </div>
                            </div>

                        </fieldset>
                        <br>

                        <fieldset>
                            <legend><?=Yii::t('fe', 'Perubahan asupan makanan')?></legend>
                            <?= $form->field($model, 'asupanmkn',[
                                'horizontalCssClasses' => [
                                        'label' => 'control-label col-sm-2',
                                        'wrapper' => 'col-md-10'
                                    ],
                                ])
                                ->radioList(ArrayHelper::map($lookupPerawat['gizi_asupanmkn'], 'lookupkeperawatan_id', 'lookup_name'), ['itemOptions'=>['disabled'=>$preview]]) ?>
                            <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormasupanmkn"></div>
                            <?= $form->field($model, 'kategori_asupanmkn',[
                                'horizontalCssClasses' => [
                                        'label' => 'control-label col-sm-2',
                                        'wrapper' => 'col-md-10'
                                    ],
                                ])
                                ->radioList(ArrayHelper::map($lookupPerawat['gizi_kategori'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>true, 'itemOptions'=>['disabled'=>$preview]]) ?>
                            <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormkategori_asupanmkn"></div>
                        </fieldset>
                        <br>

                        <fieldset>
                            <legend><?=Yii::t('fe', 'Perubahan gastrointestinal')?></legend>
                            <div class="row">
                              <div class="col-sm-4">
                                    <?= $form->field($model, 'gastrointestinal_mual',[
                                        'horizontalCssClasses' => [
                                            'label' => 'control-label col-sm-3',
                                            'wrapper' => 'col-md-9'
                                        ],
                                    ])
                                    ->radioList(ArrayHelper::map($lookupPerawat['gastrointestinal_mual'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>false, 'itemOptions'=>['disabled'=>$preview]]) ?>
                                    <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormgastrointestinal_mual"></div>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'gastrointestinal_muntah',[
                                        'horizontalCssClasses' => [
                                            'label' => 'control-label col-sm-3',
                                            'wrapper' => 'col-md-9'
                                        ],
                                    ])
                                    ->radioList(ArrayHelper::map($lookupPerawat['gastrointestinal_muntah'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>false, 'itemOptions'=>['disabled'=>$preview]]) ?>
                                    <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormgastrointestinal_muntah"></div>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'gastrointestinal_diare',[
                                        'horizontalCssClasses' => [
                                            'label' => 'control-label col-sm-3',
                                            'wrapper' => 'col-md-9'
                                        ],
                                    ])
                                    ->radioList(ArrayHelper::map($lookupPerawat['gastrointestinal_diare'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>false, 'itemOptions'=>['disabled'=>$preview]]) ?>
                                    <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormgastrointestinal_diare"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'gastrointestinal_anoreksia',[
                                            'horizontalCssClasses' => [
                                                'label' => 'control-label col-sm-3',
                                                'wrapper' => 'col-md-9'
                                            ],
                                        ])
                                        ->radioList(ArrayHelper::map($lookupPerawat['gastrointestinal_anoreksia'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>false, 'itemOptions'=>['disabled'=>$preview]]) ?>
                                    <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormgastrointestinal_anoreksia"></div>
                                </div>
                                <div class="col-sm-4">
                                </div>
                                <div class="col-sm-4">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'kategori_gastrointestinal',[
                                            'horizontalCssClasses' => [
                                                'label' => 'control-label col-sm-3',
                                                'wrapper' => 'col-md-9'
                                            ],
                                        ])
                                        ->radioList(ArrayHelper::map($lookupPerawat['gizi_kategori'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>true, 'itemOptions'=>['disabled'=>$preview]]) ?>
                                    <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormkategori_gastrointestinal"></div>
                                </div>
                                <div class="col-sm-4">
                                </div>
                                <div class="col-sm-4">
                                </div>
                            </div>
                        </fieldset>
                        <br>

                        <fieldset>
                            <legend><?=Yii::t('fe', 'Perubahan kapasitas fungsional')?></legend>
                            <?= $form->field($model, 'fungsional')
                                ->radioList(ArrayHelper::map($lookupPerawat['gizi_fungsional'], 'lookupkeperawatan_id', 'lookup_name'), ['itemOptions'=>['disabled'=>$preview]]) ?>
                            <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormfungsional"></div>
                            <?= $form->field($model, 'kategori_fungsional')
                                ->radioList(ArrayHelper::map($lookupPerawat['gizi_kategori'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>true, 'itemOptions'=>['disabled'=>$preview]]) ?>
                            <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormkategori_fungsional"></div>
                        </fieldset>
                        <br>

                        <fieldset>
                            <legend><?=Yii::t('fe', 'Penyakit dan hubungannya dengan kebutuhan gizi')?></legend>

                            <?php //echo $form->field($model, 'diagnosa_medis'); ?>
                            <?php echo $form->field($model, 'diagnosa_medis')->widget(Select2::classname(), [
                            'options' => ['disabled'=>$preview],
                            'pluginOptions' => [
                                'tags' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                ],
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/gizi/end-point/get-diagnosa']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('
                                        function(params) {
                                            return {
                                                q: params.term,
                                                type: 10,
                                                all_text: 0,
                                            };
                                        }
                                    ')
                                ],
                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                            ],
                        ]) ?>
                            <?= $form->field($model, 'keb_metabolik')
                                ->radioList(ArrayHelper::map($lookupPerawat['gizi_metabolik'], 'lookupkeperawatan_id', 'lookup_name'), ['itemOptions'=>['disabled'=>$preview]]) ?>
                            <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormdiagnosa_medis"></div>
                            <?= $form->field($model, 'kategori_hubungan')
                                ->radioList(ArrayHelper::map($lookupPerawat['gizi_kategori'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>true, 'itemOptions'=>['disabled'=>$preview]]) ?>
                            <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormkategori_hubungan"></div>
                        </fieldset>
                        <br>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Penilaian fisik') ?></h5>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <?= $form->field($model, 'fisik_lemak')->checkbox(['disabled'=>$preview]); ?>
                            <?= $form->field($model, 'fisik_otot')->checkbox(['disabled'=>$preview]); ?>
                            <?= $form->field($model, 'fisik_udem')->checkbox(['disabled'=>$preview]); ?>
                            <?= $form->field($model, 'fisik_asites')->checkbox(['disabled'=>$preview]); ?>
                            <?= $form->field($model, 'kategori_fisik')
                                ->radioList(ArrayHelper::map($lookupPerawat['gizi_kategori'], 'lookupkeperawatan_id', 'lookup_name'), ['inline'=>true, 'itemOptions'=>['disabled'=>$preview]]) ?>
                            <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormkategori_fisik"></div>
                        </fieldset>
                        <br>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Penilaian SGA') ?></h5>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'penilaian_sga')
                            ->radioList(ArrayHelper::map($lookupPerawat['gizi_sga'], 'lookupkeperawatan_id', 'lookup_name'), ['itemOptions'=>['disabled'=>$preview]]) ?>
                        <div class="col-sm-offset-2" id="error_AsesmenAwalGiziFormpenilaian_sga"></div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?= Yii::t('fe', 'Tindak lanjut') ?></h5>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'diet')->textarea([
                            // 'class' => 'form-control input-sm',
                            'rows' => '3',
                            'disabled'=>$preview
                        ]) ?>
                        <?= $form->field($model, 'pagt')->textarea([
                            // 'class' => 'form-control input-sm',
                            'rows' => '3',
                            'disabled'=>$preview
                        ]) ?>
                        <?= $form->field($model, 'saran_terapi')->textarea([
                            // 'class' => 'form-control input-sm',
                            'rows' => '3',
                            'disabled'=>$preview
                        ]) ?>
                    </div>
                </div>
                <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['value'=>$pendaftaran_id]) ?>
            </div>


            <?php
            if (!$preview) {
                echo Html::submitButton(Yii::t('fe', 'Simpan'), [
                    'class' => 'btn btn-success btn-md save-asesmen-gizi',
                    'id' => 'save-asesmen-gizi',
                ]);
            } else {
                echo Html::a(Yii::t('fe', 'Cetak'), Url::to(['asesmen-gizi/cetak-sga', 'id'=>$id]), [
                    'class' => 'btn btn-info btn-md print-asesmen-gizi',
                    'id' => 'print-asesmen-gizi',
                    'target' => '_blank',
                ]);
            }
            ?>

        </div>
    </div>
</div>
<?php ActiveForm::end() ?>

<?php $this->registerJs('
    var listPerubahan = '.json_encode($lookupPerawat['gizi_perubahan']).'
    '
    . $this->render('js/index.js'), View::POS_END) ?>
