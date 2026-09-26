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
            'id' => 'warnadokumen-form',
            'options' => [
                    'class' => 'form-horizontal',
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]);
    ?>
    <div class="form-group required">
        <label for="warnadokrm_kodewarna" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Digit Pertama Nomor Primer'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'warnadokrm_kodewarna')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="warnadokrm_namawarna" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Dokumen Warna'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'warnadokrm_namawarna')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>

    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('Simpan', ['class' => 'btn btn-success btn-md']) ?>
            <?= Html::button('Kembali',[
                                'class' => 'btn btn-default btn-md',
                                'data-dismiss' => 'modal'
                                ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#warnadokumen-form').docoForm('submit', {
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            tableWarnaDokumen.draw();
        }
    });
</script>
