<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
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
    <?=$form->field($model, 'golonganoperasi_kode', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('golonganoperasi_kode'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'golonganoperasi_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('golonganoperasi_nama'),'class' => 'form-control input-s']); ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-repeat"></i> Ulang'),['class' => 'btn btn-lime-green btn-sm reset']); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            $('.data-reset').trigger('click');
            if (data.status == 201)
                this.formInput[0].reset();
            $('#modal_backdrop').modal('toggle');
            table.draw();
        }
    });
    
    $(".reset").on("click", function(){
        resetForm($("#ajax-form"));
    });

    function resetForm($form) {
        $form.find("input:text, input:password, input:file, select, textarea").val("");
        $form.find("input:radio")
             .removeAttr("checked").removeAttr("selected");
        $(".select2").val(null).trigger("change");
    }
</script>