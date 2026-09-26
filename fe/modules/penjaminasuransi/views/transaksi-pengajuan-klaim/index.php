<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DHtml;
use kartik\widgets\DepDrop;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = DHtml::getTitleMenu("Pengajuan Klaim");
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Transaksi', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="panel panel-white">
        <!-- Panel Heading -->
        <div class="panel-heading">
            <!-- Breadcrumb -->
            <div class="row">
                <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::t('fe', $title) ?></b></h3>
                    <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                </div>
            </div>
        </div>
        <!-- Toolbar -->
        <div class="panel-toolbar clearfix">
            <!-- Add here -->
        </div>
        <!-- Panel Body -->
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'pengajuan-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options'  => [
                            'role' => 'form'
                        ]
                    ]);

                    echo Html::hiddenInput('PengajuanKlaimForm[carabayar_nama]', '', [
                        'class' => 'carabayar_nama'
                    ]);

                    echo Html::hiddenInput('PengajuanKlaimForm[penjamin_nama]', '', [
                        'class' => 'penjamin_nama'
                    ]);
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                            $form->field($model, 'carabayar_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-6'
                                ],
                            ])->dropDownList(ArrayHelper::map($requestCaraBayar, 'carabayar_id', 'carabayar_nama'), [
                                'class' => 'form-control select2',
                                'id' => 'carabayar_id',
                                'prompt' => '-- Pilih Cara Bayar --'
                            ])->label(Yii::t('fe', 'Cara Bayar'))
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                            $form->field($model, 'penjamin_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-6'
                                ],
                            ])->widget(DepDrop::classname(), [
                                'options' => [
                                    'id' => 'penjamin_id',
                                    'class' => 'form-control select2'
                                ],
                                'pluginOptions' => [
                                    'depends' => ['carabayar_id'],
                                    'placeholder' => Yii::t('fe', '-- Pilih Penjamin --'),
                                    'initialize' => true,
                                    'url' => Url::to(['/penjamin-asuransi/transaksi-pengajuan-klaim/get-penjamin?selected=' . $model->penjamin_id]),
                                    'prompt' => Yii::t('fe', '-- Pilih Penjamin --'),
                                ]
                            ])->label(Yii::t('fe', 'Penjamin'))
                            ?>
                            <div class="form-group">
                                <label class="control-label text-left col-sm-4"></label>
                                <div class="col-md-8">
                                    <?=
                                    Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>' . Yii::t('fe', 'Set'), [
                                        'class' => 'btn btn-success btn-labeled',
                                        'id' => 'setcarabayar'
                                    ])
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>