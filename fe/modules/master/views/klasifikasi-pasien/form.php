<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
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
<!-- <div class="modal-body"> -->
    <?=$form->field($model, 'klasifikasipasien_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('klasifikasipasien_nama'),'class' => 'form-control input-sm']); ?>
    <?php
        if(empty($id)):
        $model->is_active = true;
    ?>
        <?=$form->field($model, 'klasifikasipasien_kode', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('klasifikasipasien_kode'),'class' => 'form-control input-sm']); ?>

    <?php
        elseif (!empty($id)) :
    ?>
        <?= $form->field($model, 'klasifikasipasien_kode', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('klasifikasipasien_kode'), 'class' => 'form-control input-sm']); ?>
    <?php
        endif;
        
    ?>
    
    <?=$form
        ->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])
        ->checkbox()
        ->label($model->getAttributeLabel('Status Aktif'));
    ?>
    <script type="text/javascript">
        $(".form-group").find(".col-sm-offset-4").removeClass('col-sm-offset-4');
        $(".switch").bootstrapSwitch();
        $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
            if (e.target.checked == true) {
                $value = '1';
                $('input.prop_state').val($value);
            } else {
                $value = '0';
                $('input.prop_state').val($value);
            }
        });
        // var checked = "<?=$model->is_active?>";
        // console.log(checked);
    </script>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
                $('#modal_backdrop').modal('toggle');
                tableklasifikasipasien.draw();
        }
    });
</script>