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
                    <b>Cek Finger Print Peserta</b>
                </h6>
            </div>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'form-check-finger',
            'type' => ActiveForm::TYPE_VERTICAL,
            'formConfig' => [
                'labelSpan' => 5,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ]
        ]) ?>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'noKartu', [
                        'inputOptions' => [
                            'id' => 'noKartu',
                            'class' => 'form-control input-sm'
                        ]
                    ])->textInput(['class' => 'noKartu']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'tglPelayanan', [
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-2 control-label text-bold',
                        'wrapper' => 'col-md-5'
                    ],
                    ])->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'readonly' => true,
                        'value' => date('Y-m-d'),
                        'language' => 'en',
                        'pluginOptions' => [
                            'endDate' => '0d',
                            'format' => 'yyyy-mm-dd',
                            'autoclose' => true,
                            'todayBtn' => true
                        ]
                    ])->label('Tanggal Pelayanan', ['class' => 'text-bold']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <button type="submit" class="btn btn-info btn-sm btn-simpan" ><b><?=Yii::t('fe','Cek')?></b></button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        
        <div class="row mt-5 response-ws" style="display:none;">
            <div class="col-md-6">
                <div class="alert alert-bordered response-alert">
                    <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                    <span id="response-ws"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
$('#form-check-finger').submit(function(event){
    event.preventDefault();

    var _data = $(this).serializeArray();
    $(this).docoForm('submit', {
        data: _data,
        skipSuccessNotif: true,
        skipConfirm: true,
        success: function (response) {
            if ((typeof(response.response !== 'undefined')) && (response.response.status)) {
                if(response.response.kode){
                    if(response.response.kode == '1'){
                        $('.response-alert').addClass('alert-success');
                    }else{
                        $('.response-alert').addClass('alert-danger');
                    }
                }
                $('.response-ws').show();
                $('#response-ws').html(response.response.status);  
            }
        }
    });
});
",View::POS_END, 'index');
?>