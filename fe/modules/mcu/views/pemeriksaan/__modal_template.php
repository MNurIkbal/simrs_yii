<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\ArrayHelper;
    use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?=Yii::t('fe', 'Template');?></b></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]) ?>
                <p>Apakah anda ingin menambahkannya ke template ?</p>
                <p>Jika ya, masukan nama template</p>
                <?= $form->field($model, 'temp_nama', ['labelOptions' => ['class' => 'text-left']])
                ->textInput([
                    'class' => 'form-control input-sm temp-nama',
                    'placeholder' => 'Masukan nama template MCU',
                ])->label(false); ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <?= Html::button("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm', 'id' => 'btn-add', 'disabled' => true]) ?>
        <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
            'class' => 'btn bg-slate btn-sm',
            'data-dismiss' => 'modal',
            'id' => 'close'
        ]); ?>
    </div>
</div>

<?php
$this->registerJs('
    var saveHasil = [];
    if(detail_type == "pemeriksaan_fisik"){
        $.each(tmpHasil, function(index,value){
            $.each(value, function(index, data){
                saveHasil.push(data)
            });
        });
    }

    $(".temp-nama").on("input", function(){
        if ($(this).val() == "") {
            $("#btn-add").attr("disabled", true)
        } else {
            $("#btn-add").attr("disabled", false)
        }
    });
');
?>