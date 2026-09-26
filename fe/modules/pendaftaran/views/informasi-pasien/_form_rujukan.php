<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-17 13:51:59
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-06-12 09:57:57
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
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
	</div>
	<div class="panel-body">
		<?php
        $form = ActiveForm::begin([
            'id' => 'rujukan-form',
            'action' => '/pendaftaran/informasi-pasien/save-rujukan?params=rujukan',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);?>
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
            ])->dropDownList(['1'=>'tesetr'], [
                'class' => 'select2',
                'prompt' => '-'
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
            ])->textInput(['class'=>'pickadate']);
        ?>
        <?=
            $form->field($modelRujukan, 'diagnosa_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textInput();
        ?>
        <!-- <div class="row">
            <div class="col-md-11 text-right">
            <?=Html::submitButton(Yii::t('fe','Simpan'), ['class'=>'btn btn-success btn-sm'])?>
            </div>
        </div> -->
        <?php ActiveForm::end(); ?>
	</div>
</div>