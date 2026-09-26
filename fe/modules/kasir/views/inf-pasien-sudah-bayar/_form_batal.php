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
        'id' => 'batal-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'action' => '/kasir/inf-pasien-sudah-bayar/cancel',
        'formConfig' => [
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>
    
    <?= $form->field($batalForm, 'username')->textInput(['id' => 'username']) ?>
    <?= $form->field($batalForm, 'password')->passwordInput(['id' => 'password']) ?>
    <?= $form->field($batalForm, 'alasan_batal')->textarea(['id' => 'alasan_batal'], ['rows' => '4'],['class' => 'form-control']); ?>
    <?= $form->field($batalForm, 'pendaftaran_id')->hiddenInput(['value'=>$pendaftaran_id])->label(false); ?>
    <?= $form->field($batalForm, 'pembayaran_id')->hiddenInput(['value'=>$pembayaran_id])->label(false); ?>
    <?= $form->field($batalForm, 'penjualanresep_id')->hiddenInput(['value'=>$penjualanresep_id])->label(false); ?>
    
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save', 'id' => 'submit-batal','style' => 'display:none']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save', 'id' => 'simpan-batal', 'disabled' => true]); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('.doco-number').trigger('change')
    })

    $('#simpan-batal').on('click', function (e) {
        var user = $('#username').val();
        var pass = $('#password').val();
        $().docoForm('click', {
            url: baseUrl + 'kasir/end-point/check-authorization',
            skipConfirm: true,
            skipSuccessNotif: true,
            data: {
                nama_pemakai: user,
                katakunci_pemakai: pass,
                akses: 'cancel'
            },
            success: function (data) {
                $("#submit-batal").trigger("click")
            }
        })
    })
    
    $("#batal-form").docoForm("submit",{
        skipConfirm : true,
        success : function(data) {
            table.draw();
            $("#modal_backdrop").modal("toggle");
        },
        error : function () {
            $("#modal_backdrop").modal("toggle");
        }
    });

    
</script>