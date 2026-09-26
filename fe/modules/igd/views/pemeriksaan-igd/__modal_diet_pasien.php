<?php
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
    'id' => 'diet-pasien-form',
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
<div class="modal-header">
    <button type="button" class="close close-modal-diet" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Diet Pasien</h5>
</div>
<div class="modal-body">
    <div class="row">
        <label class="control-label col-md-6"><?= Yii::t('fe', 'Jenis Diet') ?></label>
        <?= Html::activeTextarea($model, 'catatan_diet', ['class' => 'form-control']) ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" style="margin-right: 5px;" id="btn-save-diet-pasien" class="btn btn-labeled btn-info btn-xs"><b><i class="fa fa-save"></i></b> <?= Yii::t('fe', 'Simpan') ?></button>
</div>

<?php ActiveForm::end() ?>

<?php 
    $this->registerJs($this->render('js/__modal_diet_pasien.js'), View::POS_END);
?>
