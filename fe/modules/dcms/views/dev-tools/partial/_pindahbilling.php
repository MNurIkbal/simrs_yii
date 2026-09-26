<?php
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
?>
<div class="panel panel-white">
    <div class="panel-heading">
        <div class="row">
            <div class="column-1">
            </div>
            <div class="column-2">
                <h6 class="panel-title">
                    <b>Pindah Billing</b>
                </h6>
            </div>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'form-pindah-billing',
            'type' => ActiveForm::TYPE_VERTICAL,
            'action' => '/dcms/dev-tools/pindah-billing',
            'method' => 'POST',
            'formConfig' => [
                'labelSpan' => 5,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ]
        ]) ?>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'pendaftaran_sumber', [
                        'inputOptions' => [
                            'id' => 'pendaftaran_sumber',
                            'class' => 'form-control input-sm'
                        ]
                    ])->textInput(['class' => 'pendaftaran_sumber'])->label('No Pendaftaran Sumber') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'pendaftaran_tujuan', [
                        'inputOptions' => [
                            'id' => 'pendaftaran_tujuan',
                            'class' => 'form-control input-sm'
                        ]
                    ])->textInput(['class' => 'pendaftaran_tujuan'])->label('No Pendaftaran Tujuan') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <button type="submit" class="btn btn-info btn-sm btn-simpan" ><b><?=Yii::t('fe','Pindah')?></b></button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        
        <div class="row mt-5 response-ws" style="display:none;">
            <div class="col-md-6">
                <div class="alert alert-bordered response-alert">
                    <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
$('#form-pindah-billing').submit(function(event){
    event.preventDefault();

    var _data = $(this).serializeArray();
    $(this).docoForm('submit', {
        data: _data,
        skipSuccessNotif: true,
        skipConfirm: true,
        success: function (response) {
            if ((typeof(response.response !== 'undefined')) && (response.response.status_message)) {
                if(response.response.status_message == 'Success'){
                    docoNotification('success', 'Sukses', 'Pindah Billing Berhasil');
                }else{
                    docoNotification('error', 'Gagal', response.response.status_message);
                }
            }
        }
    });
});
",View::POS_END, 'index');
?>