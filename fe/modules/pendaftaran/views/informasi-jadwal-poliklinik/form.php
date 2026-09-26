<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'jam_mulai', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm', 'id'=>'updateJamMulai']); ?>
    <?=$form->field($model, 'jam_tutup', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm', 'id'=>'updateJamTutup']); ?>
    <?=$form->field($model, 'maxantiran_poli', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm']); ?>
</div>
<div class="modal-footer">
            <?= Html::submitButton("<i class='fa fa-floppy-o'> ". Yii::t('fe', 'Simpan')."</i>", ['class' => 'btn bg-teal']) ?>
            <?= Html::button("<i class='fa fa-arrow-left'> ". Yii::t('fe', 'Kembali')."</i>",[
                                'class' => 'btn bg-slate',
                                'data-dismiss' => 'modal'
                                ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            table.draw();
        }
    });
    $(function(){
        $('#updateJamMulai').pickatime({
            formatSubmit: 'H-i-A',

        });
        $('#updateJamTutup').pickatime({
            formatSubmit: 'H-i-A',
        });
    })
</script>