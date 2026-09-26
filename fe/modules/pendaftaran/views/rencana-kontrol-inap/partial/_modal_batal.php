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
    $formAction = '/pendaftaran/rencana-kontrol-inap/confirm-hapus';
    $form = ActiveForm::begin([
        'id' => 'batal-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>false,
        'action' => $formAction,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>

    <?= $form->field($hapusform, 'username')->textInput(['value'=>$username,'readonly'=>true]) ?>
    <?= $form->field($hapusform, 'password')->passwordInput() ?>
    <?= $form->field($hapusform, 'rencanakontrol_id')->hiddenInput(['value'=>$rencanakontrol_id])->label(false); ?>
    <?= $form->field($hapusform, 'noSuratKontrol')->hiddenInput(['value'=>$noSuratKontrol])->label(false); ?>
    <?= $form->field($hapusform, 'is_from_vclaim')->hiddenInput(['value'=>isset($is_from_vclaim) ? $is_from_vclaim : 0])->label(false); ?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('.doco-number').trigger('change')
    })
    $("#batal-form").docoForm("submit",{
        success : function(data) {
            table.ajax.reload();
            // table.draw();
            $("#modal_backdrop").modal("toggle");
        },
    });
</script>
