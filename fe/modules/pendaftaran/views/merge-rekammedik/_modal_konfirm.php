<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php 
        $form = ActiveForm::begin([
            'id' => 'merge_form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'action' => 'merge-rekammedik/save',
            'formConfig' => [
                'labelSpan' => 3, 
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
        ?>
    <?= $form->field($mergeForm, 'username')->textInput(['value'=>$username,'readonly'=>true]) ?>
    <?= $form->field($mergeForm, 'password')->passwordInput() ?>
    <?= $form->field($mergeForm, 'no_rekammedik_asal')->hiddenInput()->label(false); ?>
    <?= $form->field($mergeForm, 'no_rekammedik_tujuan')->hiddenInput()->label(false); ?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $("#merge_form").docoForm("submit",{
        success : function(data) {
            if(data) {
                // $("#modal_backdrop").modal("toggle");
                setTimeout(location.reload(), 10000);
            }
        },
    });
</script>