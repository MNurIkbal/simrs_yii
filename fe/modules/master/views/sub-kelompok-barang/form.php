<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'form', 
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">
        <?= $title ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?=$form->field($model, 'kelompokbarang_id', 
        [
            'labelOptions' => [
                'class' => 'text-left'
            ]
        ])
        ->dropDownList($kelompokbarang, [
            'class'=>'select2', 'prompt' => Yii::t('fe', 'Pilih Nama Kelompok')
        ]) ?>

    <?= $form->field($model, 'subkelompok_nama', 
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
    ]) ?>
    <?= $form->field($model, 'subkelompok_namalain', 
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
    ]) ?>
    <?= $form->field($model, 'subkelompok_kode', 
    [
        'labelOptions' => [
            'class' => 'text-left'
            ]
        ])->textInput([
            'class' => 'form-control input-sm', 
    ]) ?>
    <?= $form->field($model, 'is_active', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-5'
            ]
        ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
    ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
$("#form").docoForm("submit", {
    success : function(data) {
        $('.data-reset').trigger('click');
        this.formInput[0].reset();
        $('#modal_backdrop').modal('toggle');
        table.draw();
    }
});
$("#subkelompokbarangform-subkelompok_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#subkelompokbarangform-subkelompok_namalain").val();
    if(nama_lainnya == '') {
        $("#subkelompokbarangform-subkelompok_namalain").val(nama);
    }
    else {
        $("#subkelompokbarangform-subkelompok_namalain").val(nama_lainnya);
    }
});
</script>