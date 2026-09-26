<?php


use yii\web\View;
use kartik\form\ActiveForm;
use app\components\DocoHelpers;
use yii\helpers\Html;

$this->title = $title;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'batal-terapi-form',
    'action' => $submitUrl
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="panel-body panel-body-collapse-form-batal-terapi">
            <div class="row">
                <div class="form-batal-terapi" style="padding: 12px;">
                    <?= $form->field($model, 'alasan_pembatalan')->textArea() ?>
                    <?= $form->field($model, 'deleted_by_password')->passwordInput() ?>

                    <?= $form->field($model, 'pendaftaran_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'pasienadmisi_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'instruksitindakan_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'instruksi_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'permintaankepenunjang_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'tindakanpelayanan_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'obatalkespasien_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'jenis')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'tipe_instruksi')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'instalasi_id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'noresep')->hiddenInput()->label(false) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::submitButton("<b><i class='fa fa-floppy-o'></b></i> " . Yii::t('fe', 'Simpan'), ['id' => 'btn-simpan', 'class' => 'btn btn-info btn-xs btn-labeled']) ?>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs($this->render('js/_batal_tindakan.js'), View::POS_END, 'jskuning');
?>
