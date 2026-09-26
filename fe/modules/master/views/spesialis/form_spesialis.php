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
            <?= $form->field($model, 'spesialis_kode')
                ->textInput(['class' => 'form-control']); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <?= $form->field($model, 'spesialis_nama')
                ->textInput(['class' => 'form-control']); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <?= $form->field($model, 'spesialis_namalainnya')
                ->textInput(['class' => 'form-control']); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <?= $form->field($model, 'is_active')->dropDownList(
                [
                    1 => 'Aktif',
                    0 => 'Tidak Aktif'
                ],
                [
                    'class' => 'select2',
                    'prompt' => Yii::t('fe', '-- Pilih --')
                ]); 
            ?>
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
</script>


