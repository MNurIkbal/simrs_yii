<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form-perujuk', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form
        ->field($model, 'asalrujukan_id', ['labelOptions' => ['class' => 'text-right']])
        ->dropDownList(ArrayHelper::map($asalrujukan, 'asalrujukan_id', 'asalrujukan_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => 'Pilih Asal Rujukan'
        ])->label(Yii::t('fe', 'Asal Perujuk')); 
    ?>
    <?=$form->field($model, 'namaperujuk', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'spesialis', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'perujuk_kode', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm'])->label('Kode Perujuk'); ?>
    <?=$form->field($model, 'alamatlengkap', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'notelp', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']); ?>
    <?= $form->field($model, 'is_active', ['labelOptions' => ['class' => 'text-right']])->dropDownList($status,['class' => 'select2'])->label("Status"); ?>
</div>
<div class="modal-footer">
                <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
                <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
                                    'class' => 'btn bg-slate',
                                    'data-dismiss' => 'modal'
                                    ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#ajax-form-perujuk").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            table2.draw();
        }
    });
</script>