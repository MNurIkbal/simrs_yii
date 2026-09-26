<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'lokasirak_nama', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'lokasirak_namalainnya', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(Yii::t('fe', 'Simpan'), ['class' => 'btn btn-success btn-sm']); ?>
    <?=Html::button(Yii::t('fe', 'Kembali'),['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            $('#modal_backdrop').modal('toggle');
            table.draw();
        }
    });
</script>