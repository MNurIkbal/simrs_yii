<?php

use app\components\DocoConstants;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
?>

<style type="text/css">
    .segment-title{
        font-weight: bold !important;
        font-size: 14px;
    }
    .div-button-save{
        margin-bottom: 20px; 
        margin-top: 15px;
    }
</style>

<div id="component-anesthetic">
    <?php 
        $form = ActiveForm::begin([
            'id' => 'anesthetic-form',
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_VERTICAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
    ?>

    <?= $form->field($model, 'anestesi_id')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'pasienmasukpenunjang_id')->hiddenInput()->label(false); ?>

    <!-- first segment -->
    <div class="row">
        <div class="col-md-6">
            <label class="control-label">Premedication</label>
            <br>
            <a href="<?=Url::to(['tambah-obat-anestesi'])?>" class="btn btn-xs btn-labeled btn-info ml-2" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah</a>

            <table id="tb-premedication" class="table table-striped table-condensed ml-2 mt-10" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'Drug')?></th>
                        <th><?=Yii::t('fe', 'Dose')?></th>
                        <th><?=Yii::t('fe', 'Time Delivery')?></th>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'anestesi_result')->radioList($resultList,
                [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $html = '<div class="radio ml-10">';
                        $html .= '<label>';
                        $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                        $html .= '<span>' . $label . '</span>';
                        $html .= '</label>';
                        $html .= '</div>';
                        return $html;
                    },
                ]) 
            ?>
        </div>
    </div>

    <hr>
    <!-- second segment -->
    <div class="row">
        <div class="col-md-12 mb-6">
            <label class="segment-title"><?=Yii::t('fe', 'Anasthetic Technique')?></label>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'anestesi_regional')->radioList($regionalList,
                [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $html = '<div class="radio ml-10 form-inline">';
                        $html .= '<label>';
                        $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                        $html .= '<span>' . $label . '</span>';
                        if ($value == DocoConstants::ANES_REG_OTHER) {
                            $html .= '<input 
                                        type = "text" 
                                        id = "regional_other" 
                                        class = "form-control ml-10"
                                    >';
                        }
                        $html .= '</label>';
                        $html .= '</div>';
                        return $html;
                    },
                ]) 
            ?>
            <?= $form->field($model, 'anestesi_regional_other', [
                'inputOptions' => [
                    'class' => 'form-control input-sm anestesi_regional_other'
                ]
            ])->hiddenInput()->label(false); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'type_needle_size', [
                'inputOptions' => [
                    'class' => 'form-control input-sm'
                ]
            ]); ?>
            <?= $form->field($model, 'lenght_catheter_isertion', [
                'inputOptions' => [
                    'class' => 'form-control input-sm'
                ]
            ]); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'anestesi_general')->radioList($generalList,
                [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $html = '<div class="radio ml-10">';
                        $html .= '<label>';
                        $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                        $html .= '<span>' . $label . '</span>';
                        $html .= '</label>';
                        $html .= '</div>';
                        return $html;
                    },
                ]) 
            ?>
        </div>
    </div>

    <hr>
    <!-- third segment -->
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'patient_position', [
                'inputOptions' => [
                    'class' => 'form-control input-sm'
                ]
            ]); ?>
        </div>
    </div>

    <hr>
    <!-- fourth segment -->
    <div class="row">
        <div class="col-md-12 mb-6">
            <label class="segment-title">
                <?=Yii::t('fe', 'Anasthetic Complication and Counter Measurment During')?>
            </label>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'preinduction', [
                'inputOptions' => [
                    'class' => 'form-control input-sm'
                ]
            ])->textarea(['rows' => '3']); ?>
            <?= $form->field($model, 'maintenance', [
                'inputOptions' => [
                    'class' => 'form-control input-sm'
                ]
            ])->textarea(['rows' => '3']); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'induction', [
                'inputOptions' => [
                    'class' => 'form-control input-sm'
                ]
            ])->textarea(['rows' => '3']); ?>
            <?= $form->field($model, 'recovery', [
                'inputOptions' => [
                    'class' => 'form-control input-sm'
                ]
            ])->textarea(['rows' => '3']); ?>
        </div>
    </div>

    <div class="row div-button-save">
        <div class="col-sm-6">
            <button type="button" id="btn-save-anesthetic" class="btn btn-info btn-labeled btn-xs pull-right">
                <b><i class="fa fa-floppy-o"></i></b>Simpan
            </button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$phpVars = [
    'premedicationList' => $detailAnestesi,
];
$this->registerJsVar('pageVars', $phpVars);
$this->registerJs($this->render('js/anesthetic.js'));
?>
