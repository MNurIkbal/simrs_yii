<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'kelompok-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-3 control-label">Nama</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'kelmenu_nama')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword" class="col-lg-3 control-label">Status</label>
        <div class="col-lg-6">
            <?= $form->field($model, 'is_active')
                ->dropDownList($status,['class' => 'select2'])->label(false); ?>
        </div>
    </div>
    <hr>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#kelompok-form').docoForm('submit',{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            tableJenisKertas.draw();
        }
    });
</script>