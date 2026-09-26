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
                    <b>Pengajuan Finger Print</b>
                </h6>
            </div>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'form-sep-tanpa-finger',
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
                <?= $form->field($model, 'jnsPelayanan')->dropDownList(
                    [1=>'1. R.Inap',2=>'2. R.Jalan'], [
                        'id' => 'jnsPelayanan',
                        'class' => 'select2'
                    ]
                )->label('Jenis Pelayanan') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'tglSep', [
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
                    ])->label('Tanggal SEP', ['class' => 'text-bold']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'keterangan', [
                        'inputOptions' => [
                            'id' => 'keterangan',
                            'class' => 'form-control input-sm'
                        ]
                    ])->textInput(['class' => 'keterangan']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <button type="submit" class="btn btn-info btn-sm btn-simpan" ><b><?=Yii::t('fe','Simpan')?></b></button>
            </div>
            
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$this->registerJs("
$('#jnsPelayanan').select2({
    minimumResultsForSearch: -1
});
$('.keterangan').val('Approval Pengajuan SEP Fingerprint');
$('#form-sep-tanpa-finger').submit(function(event){
    event.preventDefault();

    var _data = $(this).serializeArray();
    $(this).docoForm('submit', {
        data: _data,
        success: function (response) {
        }
    });
});
",View::POS_END, 'index');
?>