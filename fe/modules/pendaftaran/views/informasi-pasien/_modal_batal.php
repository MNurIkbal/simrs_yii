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
    if ($jenis == 'ranap') {
        $formAction = '/pendaftaran/informasi-pasien/confirm-batal-ranap?no_pendaftaran='.$no_pendaftaran.'&jenis='.$jenis;
    } else {
        $formAction = '/pendaftaran/informasi-pasien/confirm-batal?no_pendaftaran='.$no_pendaftaran.'&jenis='.$jenis;
    }

    $form = ActiveForm::begin([
        'id' => 'batal-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'action' => $formAction,// '/pendaftaran/informasi-pasien/confirm-batal?pendaftaran_id='.$pendaftaran_id.'&jenis='.$jenis,
        'formConfig' => [
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>
    
    <?= $form->field($batalForm, 'username')->textInput(['value'=>$username,'readonly'=>true]) ?>
    <?= $form->field($batalForm, 'password')->passwordInput() ?>
    <?= $form->field($batalForm, 'alasan_batal')->textarea(['rows' => '4'],['class' => 'form-control']); ?>
    <?= $form->field($batalForm, 'total_tagihan',[
        'addon' => ['prepend' => ['content'=>'Rp.']],
    ])->textInput([
        'class' => 'form-control input-sm text-right doco-number',
        'autocomplete' => "off",
        'disabled' => true
    ]); ?>
    <?= $form->field($batalForm, 'no_pendaftaran')->hiddenInput(['value'=>$no_pendaftaran])->label(false); ?>
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
            table.draw();
            $("#modal_backdrop").modal("toggle");
        },
    });
</script>