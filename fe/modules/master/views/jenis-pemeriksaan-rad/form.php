<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $model->jenispemeriksaanrad_kode == null ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?php
        $is_disabled = $model->jenispemeriksaanrad_kode == null ? false : true;
    ?>
    <?= $form->field($model, 'jenispemeriksaanrad_kode', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm kode_unique', 'readonly' => $is_disabled]) ?>
    <?= $form->field($model, 'jenispemeriksaanrad_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm remove_space']) ?>
    <?= $form->field($model, 'kelompokpemeriksaanrad_id', ['labelOptions' => ['class' => 'text-left']])->dropDownList($kelompokPemeriksaanRad, ['class' => 'form-control input-sm select2', 'prompt' => Yii::t('fe', '--Pilih--')]) ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<!-- Javascript -->
<script type="text/javascript">
$("#form").docoForm("submit", {
    success : function(data) {
        // Check data status
        if (data.metadata.status == 201) {
            // Reset form
            $("#form")[0].reset();

            // Draw table
            table.draw();
            $("#btn-edit").prop("disabled", true);
            $("#btn-delete").prop("disabled", true);
            $('#modal_backdrop').modal('toggle');
        }
        else if (data.metadata.status == 200) {
            // Draw table
            table.draw();
            $("#btn-edit").prop("disabled", true);
            $("#btn-delete").prop("disabled", true);
            $('#modal_backdrop').modal('toggle');
        }
    }
});
</script>