<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

?>
<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe','Asuransi baru')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'asuransi-form',
                        // 'action' => '/pendaftaran/daftar/save-asuransi',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        // 'options' => [
                        //     'skip-confirm' => "true"
                        // ]
                    ]);
                ?>
                <?=Html::activeHiddenInput($modelAsuransi, 'carabayar_id', ['class'=>'carabayar-id'])?>
                <?=Html::activeHiddenInput($modelAsuransi, 'penjamin_id', ['class'=>'penjamin-id'])?>
                <?=Html::activeHiddenInput($modelAsuransi, 'pasien_id', ['class'=>'pasien-id'])?>
               
                <?=
                    $form->field($modelAsuransi, 'nokartuasuransi', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5',
                            'wrapper' => 'col-md-6'
                        ]
                    ])->textInput();
                ?>
                <?=
                    $form->field($modelAsuransi, 'namapemilikasuransi', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5',
                            'wrapper' => 'col-md-6'
                        ]
                    ])->textInput();
                ?>
                <?=
                    $form->field($modelAsuransi, 'nomorpokokperusahaan', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5',
                            'wrapper' => 'col-md-6'
                        ]
                    ])->textInput();
                ?>
                <?=
                    $form->field($modelAsuransi, 'kelastanggungan_id', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5',
                            'wrapper' => 'col-md-6'
                        ]
                    ])->dropDownList($kelaspelayanan, [
                        'class' => 'select2',
                        'prompt' => '-'
                    ]);
                ?>
                <?=
                    $form->field($modelAsuransi, 'namaperusahaan', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5',
                            'wrapper' => 'col-md-6'
                        ]
                    ])->textInput();
                ?>
                <?=
                    $form->field($modelAsuransi, 'tgl_konfirmasi', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5',
                            'wrapper' => 'col-md-6'
                        ]
                    ])->textInput(['class'=>'pickadate-w-month']);
                ?>
                <?=
                    $form->field($modelAsuransi, 'status_konfirmasi', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-5',
                            'wrapper' => 'col-md-5 col-md-offset-5'
                        ]
                    ])->checkbox();
                ?>
                <div class="row">
                    <div class="col-md-10 text-right">
                    <?php //echo Html::submitButton(Yii::t('fe','Simpan'), ['class'=>'btn btn-success btn-sm'])?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
