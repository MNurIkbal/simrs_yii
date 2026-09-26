<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'jadwallibur-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="alert alert-danger alert-bordered alert-modal" style="display:none">
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
        <span class="text-semibold alert-message"></span>
    </div>

    <?= $form->field($model, 'jadwallibur_id')->hiddenInput(['id'=>'jadwallibur_id'])->label(false); ?>
    <?= $form->field($model, 'tgl_libur', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-9'
            ],
            'inputOptions'=>['id'=>'tgl_libur'],
            'addon' => [
                'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
            ]
        ]); 
    ?>
    <?= $form->field($model, 'ket_libur')->textarea(['rows' => '4'],['class' => 'form-control']); ?>

    <div class="form-group">
        <label for="is_liburnasional" class="col-lg-1 control-label">
            <?= Yii::t('fe', 'Libur Nasional'); ?>
        </label>
        <div class="col-lg-6">
            <?=$form->field($model, 'is_liburnasional')->checkbox()?>
        </div>
    </div>

</div>

<div class="modal-footer">
    <?= Html::button('Simpan', ['class' => 'btn btn-success btn-md btn-simpan']) ?>
    <?= Html::button('Kembali',[
        'class' => 'btn btn-default btn-md',
        'data-dismiss' => 'modal'
    ]); ?>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">

$(function() {
    initPickDate("input[name='JadwalLiburForm[tgl_libur]']", {
        min: new Date(),
    }, new Date());
})

function initPickDate(inputId, options, startDate, minDate = null) {
    options.format = 'dd mmm yyyy';
    options.formatSubmit = 'yyyy-mm-dd';
    options.onStart = function () {
        var date = startDate;
        this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
    }
    var pickDate = $(inputId).pickadate(options);

    if (minDate) {
        pickDate.pickadate('picker').set('min', new Date(minDate))
    }
}

$('.btn-simpan').on('click', function(e){
    e.preventDefault();
    $().docoForm('click',{
        url     : $('#jadwallibur-form').attr('action'),
        data    : $('#jadwallibur-form').serializeArray(),
        success : function(data) {
            location.reload();
        } 
    });
});

</script>
