<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-17 13:51:59
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-19 14:09:26
 */
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
        <h5 class="panel-title"><?=Yii::t('fe','Rujukan')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <!-- <li><a data-action="collapse"></a></li> -->
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
            'id' => 'rujukan-form',
            'action' => '/pendaftaran/daftar/save-rujukan?params=rujukan',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);?>
        <?=Html::activeHiddenInput($modelRujukan, 'asalrujukan_id', ['id'=>'asalrujukan-id'])?>
        <?=
            $form->field($modelRujukan, 'no_rujukan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textInput();
        ?>
        <?=
            $form->field($modelRujukan, 'rujukandari_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->widget(DepDrop::classname(), [
                'name' => 'rujukandari_id',
                'options' => [
                    'disabled' => false,
                    'class' => 'form-control select2 selectRujukandari',
                    'id' => 'rujukandari_select',
                ],
                'pluginOptions' => [
                    'depends' => ['asalrujukan_id'],
                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                    'url' => Url::to(['end-point/get-rujukan-dari'])
                ]
            ]);
        ?>
        <?=
            $form->field($modelRujukan, 'nama_perujuk', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textInput();
        ?>
        <?=
            $form->field($modelRujukan, 'tanggal_rujukan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textInput(['class'=>'pickadate-w-month']);
        ?>
        <?=
            $form->field($modelRujukan, 'diagnosa_id',[
                    'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->widget(Select2::classname(), [
                'initValueText' => '', // set the initial display text
                'options' => ['placeholder' => 'Diagnosa'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Loading'; }"),
                    ],
                    'ajax' => [
                        'url' => Url::to(['get-diagnosa', 'type'=>'10']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ]);
        ?>
        <div class="row">
            <div class="col-md-11 text-right">
            <?php //echo Html::submitButton(Yii::t('fe','Simpan'), ['class'=>'btn btn-success btn-sm'])?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>