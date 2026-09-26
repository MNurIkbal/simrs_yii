<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<?php $form = ActiveForm::begin([
    'id' => 'form-kala-dua',
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
        <?=Html::activeHiddenInput($model, 'persalinan_id')?>

        <div class="form-group highlight-addon field-kaladuaform-k2_episitomi required">
            <?= Html::label($model->attributeLabels()['k2_episitomi'], null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8">
                <?= $form->field($model, 'k2_episitomi', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormk2_episitomi"></div>
            </div>
        </div>

        <?= $form->field($model, 'k2_indikasi')->textInput(['readonly' => 'readonly']) ?>

        <div class="form-group highlight-addon field-kaladuaform-k2_pendamping required">
            <?= Html::label($model->attributeLabels()['k2_pendamping'], null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8">
                <?= $form->field($model, 'k2_pendamping', ['template'=>"{input}\n{hint}\n{error}"])->radioList(
                    ArrayHelper::map($pendamping, 'lookupkeperawatan_id', 'lookup_name'),
                    [
                        'item' => function($index, $label, $name, $checked, $value) {
                            $return = '<label class="modal-radio">';
                            $return .= $checked ? '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" checked>' : '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3">';
                            $return .= '<i></i>';
                            $return .= '<span>'.ucwords($label).'</span>';
                            $return .= '</label>';
                            return $return;
                        }
                    ]
                ) ?>
                <div id="error_KalaDuaFormk2_pendamping"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-k2_gawatjanin required">
            <?= Html::label($model->attributeLabels()['k2_gawatjanin'], null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8">
                <?= $form->field($model, 'k2_gawatjanin', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormk2_gawatjanin"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-k2_tindakanjanin hidden">
            <?= Html::label($model->attributeLabels()['k2_tindakanjanin'], null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8" id="detail-tindakanjanin">
                <?php if (!empty($k2_tindakanjanin)): ?>
                <?php foreach ($k2_tindakanjanin as $key => $value): ?>
                <?php if ($key == 0): ?>
                <div class="row">
                <?php else: ?>
                <div class="row" style="margin-top:10px;">
                <?php endif ?>
                    <div class="col-sm-10">
                        <?= Html::textInput('k2_tindakanjanin[]', $value, ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-sm-1">
                        <?= Html::button('<b><i class="fa fa-plus"></i></b>', [
                            'class' => 'btn btn-success',
                            'onclick' => 'addTindakanJanin(this)'
                        ]) ?>
                    </div>
                    <?php if ($key == 0): ?>
                    <div class="col-sm-1 remove-tindakanjanin hidden">
                        <?= Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-danger',
                            'onclick' => 'removeTindakanJanin(this)'
                        ]) ?>
                    </div>
                    <?php else: ?>
                    <div class="col-sm-1 remove-tindakanjanin">
                        <?= Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-danger',
                            'onclick' => 'removeTindakanJanin(this)'
                        ]) ?>
                    </div>
                    <?php endif ?>
                <?php if ($key == 0): ?>
                </div>
                <?php else: ?>
                </div>
                <?php endif ?>
                <?php endforeach ?>
                <?php else: ?>
                <div class="row">
                    <div class="col-sm-10">
                        <?= Html::textInput('k2_tindakanjanin[]', null, ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-sm-1">
                        <?= Html::button('<b><i class="fa fa-plus"></i></b>', [
                            'class' => 'btn btn-success',
                            'onclick' => 'addTindakanJanin(this)'
                        ]) ?>
                    </div>
                    <div class="col-sm-1 remove-tindakanjanin hidden">
                        <?= Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-danger',
                            'onclick' => 'removeTindakanJanin(this)'
                        ]) ?>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>

        <br>
        <?= $form->field($model, 'k2_hasil') ?>

        <div class="form-group highlight-addon field-kaladuaform-k2_distosiabahu required">
            <?= Html::label($model->attributeLabels()['k2_distosiabahu'], null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8">
                <?= $form->field($model, 'k2_distosiabahu', ['template' => "{input}\n{hint}\n{error}"])
                    ->radioList([
                        1 => Yii::t('fe', 'Ya'),
                        0 => Yii::t('fe', 'Tidak')
                    ]) ?>
                <div id="error_KalaDuaFormk2_distosiabahu"></div>
            </div>
        </div>

        <div class="form-group highlight-addon field-kaladuaform-k2_tindakandistosia hidden">
            <?= Html::label($model->attributeLabels()['k2_tindakandistosia'], null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8" id="detail-tindakandistosia">
                <?php if (!empty($k2_tindakandistosia)): ?>
                <?php foreach ($k2_tindakandistosia as $key => $value): ?>
                <?php if ($key == 0): ?>
                <div class="row">
                <?php else: ?>
                <div class="row" style="margin-top:10px;">
                <?php endif ?>
                    <div class="col-sm-10">
                        <?= Html::textInput('k2_tindakandistosia[]', $value, ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-sm-1">
                        <?= Html::button('<b><i class="fa fa-plus"></i></b>', [
                            'class' => 'btn btn-success',
                            'onclick' => 'addTindakanDistosia(this)'
                        ]) ?>
                    </div>
                    <?php if ($key == 0): ?>
                    <div class="col-sm-1 remove-tindakandistosia hidden">
                        <?= Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-danger',
                            'onclick' => 'removeTindakanDistosia(this)'
                        ]) ?>
                    </div>
                    <?php else: ?>
                    <div class="col-sm-1 remove-tindakandistosia">
                        <?= Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-danger',
                            'onclick' => 'removeTindakanDistosia(this)'
                        ]) ?>
                    </div>
                    <?php endif ?>
                <?php if ($key == 0): ?>
                </div>
                <?php else: ?>
                </div>
                <?php endif ?>
                <?php endforeach ?>
                <?php else: ?>
                <div class="row">
                    <div class="col-sm-10">
                        <?= Html::textInput('k2_tindakandistosia[]', null, ['class' => 'form-control']) ?>
                    </div>
                    <div class="col-sm-1">
                        <?= Html::button('<b><i class="fa fa-plus"></i></b>', [
                            'class' => 'btn btn-success',
                            'onclick' => 'addTindakanDistosia(this)'
                        ]) ?>
                    </div>
                    <div class="col-sm-1 remove-tindakandistosia hidden">
                        <?= Html::button('<b><i class="fa fa-trash"></i></b>', [
                            'class' => 'btn btn-danger',
                            'onclick' => 'removeTindakanDistosia(this)'
                        ]) ?>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>

        <br>
        <?= $form->field($model, 'k2_masalah') ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-right">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b> ".Yii::t('fe', 'Sebelumnya'), [
            'class' => 'btn btn-xs btn-labeled btn-info btn-sebelumnya hidden',
            'data-index' => 2
        ]) ?>
        <?= Html::button('<b><i class="fa fa-floppy-o"></i></b> Simpan', [
            'class' => 'btn btn-xs btn-labeled btn-info',
            'id' => 'btn-simpan-kaladua',
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-right'></i></b> ".Yii::t('fe', 'Selanjutnya'), [
            'class' => 'btn btn-xs btn-labeled btn-info btn-selanjutnya hidden',
            'data-index' => 2
        ]) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
<?php $this->registerJs('
    var count_tindakanjanin = '.count($k2_tindakanjanin).';
    var count_tindakandistosia = '.count($k2_tindakandistosia).';
    var k2_episitomi = '.$model->k2_episitomi.';

    if (count_tindakanjanin > 0) {
        $(".field-kaladuaform-k2_tindakanjanin").removeClass("hidden");
    }

    if (count_tindakandistosia > 0) {
        $(".field-kaladuaform-k2_tindakandistosia").removeClass("hidden");
    }

    if (k2_episitomi != 0) {
        $("#kaladuaform-k2_indikasi").removeAttr("readonly");
    }
', View::POS_END, 'register-kala-dua') ?>
<?php $this->registerJs($this->render('js/_kala.js'), View::POS_END, 'kala-dua') ?>