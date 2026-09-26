<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;
    use kartik\select2\Select2;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php
    $formAction = '/pendaftaran/informasi-pasien/confirm-update-status?no_pendaftaran='.$no_pendaftaran.'&jenis='.$jenis;

    $form = ActiveForm::begin([
        'id' => 'update-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'action' => $formAction,//
        'formConfig' => [
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>
    
    <?= $form->field($editForm, 'username')->textInput(['value'=>$username,'readonly'=>true]) ?>
    <?= $form->field($editForm, 'password')->passwordInput() ?>
    <?= $form->field($editForm, 'status_periksa')->dropDownList(
        ArrayHelper::map($listStatusPeriksa, 'lookup_id', 'lookup_name'), [
            'class' => 'select2 form-control',
            'prompt' => '-- Pilih --',
            'disabled' => false,
    ]) ?>
    <?= $form->field($editForm, 'no_pendaftaran')->hiddenInput(['value'=>$no_pendaftaran])->label(false); ?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $("#update-form").docoForm("submit",{
        success : function(data) {
            table.draw();
            $("#modal_backdrop").modal("toggle");
        },
    });
</script>