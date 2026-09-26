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
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <?=$form->field($model, 'kamarruangan_nokamar')
            ->dropDownList($kamar, [
                'class'=>'form-control select2 kmr',
                'prompt'=> 'Pilih', 
                'id' => 'kamarruangan_nokamar',
                'disabled' => !empty($model->kamarruangan_nokamar) ? true : false,
            ])?>
            <?php if (!empty($model->kamarruangan_nokamar)): ?>
                <?= Html::activeHiddenInput($model, 'kamarruangan_nokamar', ['value' => $model->kamarruangan_nokamar]) ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <?=$form->field($model, 'klasifikasikamar_id')->dropDownList($klasifikasi, ['class'=>'form-control select2','prompt'=> 'Pilih', 'id' => 'klasifikasikamar_id'])?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>

</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            $("#ajax-form")[0].reset();
            $('#modal_backdrop').modal('hide');
            table.draw();
        }
    });
    $(function () {
        // $(".kmr").docoPaginationSelec2(
        //     config = {
        //         placeholder : "-- Pilih Kamar --",  
        //         _api : "/master/master-api/get-list-kamar",
        //     }
        // );
    });
</script>


