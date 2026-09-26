<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-07 13:44:05
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-11 16:00:49
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<?php $form = ActiveForm::begin([
            'id' => 'form-kala-satu',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 4,
                'deviceSize' => ActiveForm::SIZE_SMALL,
                'enableClientValidation' => false,
                'enableAjaxValidation' => false
            ]
        ]) ?>
<div class="row">
    <div class="col-md-8">
        <div class="form-group highlight-addon field-kalasatuform-k1_gariswaspada required">
            <?= Html::label($model->attributeLabels()['k1_gariswaspada'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-7">
                <?= $form->field($model, 'k1_gariswaspada', ['template'=>"{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => 'Ya',
                        0 => 'Tidak'
                    ]) ?>
                <div id="error_KalaSatuFormk1_gariswaspada"></div>
            </div>
        </div>
        <div class="form-group highlight-addon field-kalasatuform-k1_masalah">
            <?= Html::label($model->attributeLabels()['k1_masalah'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-7">
                <?= $form->field($model, 'k1_masalah', ['template'=>"{input}\n{hint}\n{error}"])->textArea(['rows' => 4]) ?>
                <div id="error_KalaSatuFormk1_masalah"></div>
            </div>
        </div>
        <div class="form-group highlight-addon field-kalasatuform-k1_pelaksanaanmasalah">
            <?= Html::label($model->attributeLabels()['k1_pelaksanaanmasalah'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-7">
                <?= $form->field($model, 'k1_pelaksanaanmasalah', ['template'=>"{input}\n{hint}\n{error}"])->textArea(['rows' => 4]) ?>
                <div id="error_KalaSatuFormk1_pelaksanaanmasalah"></div>
            </div>
        </div>
        <div class="form-group highlight-addon field-kalasatuform-k1_hasil">
            <?= Html::label($model->attributeLabels()['k1_hasil'], null, ['class' => 'control-label col-sm-3']) ?>
            <div class="col-sm-7">
                <?= $form->field($model, 'k1_hasil', ['template'=>"{input}\n{hint}\n{error}"])->textArea(['rows' => 4]) ?>
                <div id="error_KalaSatuFormk1_hasil"></div>
            </div>
        </div>
        <?=Html::activeHiddenInput($model, 'persalinan_id')?>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-right">
        <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b> Simpan', [
            'class' => 'btn btn-xs btn-labeled btn-info'
        ]) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
<?php $this->registerJs($this->render('js/_kala.js'), View::POS_END, 'kala-satu') ?>