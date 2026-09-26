<?php

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
    <h5 class="modal-title">
        <?= $model->satuanlab_kode == null 
                ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= $form->field($model, 'satuanlab_kode', 
            [
                'labelOptions' => [
                    'class' => 'text-left'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm kode_unique', 
            ]) ?>
    <?= $form->field($model, 'satuanlab_nama', 
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm remove_space'
    ]) ?>
    <?= $form->field($model, 'is_active')->checkbox([
            'label' => '<b>Aktif<b>',
        ]); ?>
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

<script type="text/javascript">
$("#form").docoForm("submit", {
    success : function(data) {
        $('.data-reset').click()
        if (data.metadata.status == 201) {
            $("#form")[0].reset();
            tableSatuan.draw();
            $('#modal_backdrop').modal('toggle');
        }
        else if (data.metadata.status == 200) {
            // Draw tableSatuan
            tableSatuan.draw();
            $('#modal_backdrop').modal('toggle');
        }
    }
});
</script>