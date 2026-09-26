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
    <?=$form
        ->field($model, 'lokasirak_id', ['labelOptions' => ['class' => 'text-right']])
        ->dropDownList(ArrayHelper::map($lokasirak, 'lokasirak_id', 'lokasirak_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => 'Pilih Lokasi Rak'
        ]);
    ?>
    <?=$form->field($model, 'subrak_nama', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'subrak_namalainnya', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton('Simpan', ['class' => 'btn btn-success btn-sm']); ?>
    <?=Html::button('Kembali',['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
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