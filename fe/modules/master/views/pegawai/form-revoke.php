<?php
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
    'id' => 'revoke-esign-form',
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<div class="modal-header">
    <button type="button" class="close close-modal-esign" data-dismiss-confirmation="modal">&times;</button>
    <h5 class="modal-title text-bold"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-8 col-offset-md-2">
            <label class="control-label col-md-6"><?= Yii::t('fe', 'Alasan Revoke') ?></label>
            <?= Html::dropDownList('reason', null, $reasonList,  ['id' => 'revoke_reason', 'class' => 'form-control']) ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="row">
            <div class="col-md-8 col-offset-md-2">
            <button type="button" style="margin-right: 5px;" id="btn-save-revoke" class="btn btn-labeled btn-info btn-xs"><b><i class="fa fa-save"></i></b> <?= Yii::t('fe', 'Simpan') ?></button>

            </div>
        </div>
</div>

<?php ActiveForm::end() ?>