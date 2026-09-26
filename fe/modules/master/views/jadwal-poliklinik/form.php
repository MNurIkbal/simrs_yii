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
    <?=$form
        ->field($model, 'ruangan_id', ['labelOptions' => ['class' => 'text-left']])
       ->dropDownList(ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>

 

    <?=$form
        ->field($model, 'hari', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($hari, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>
    <?=$form->field($model, 'jam_mulai', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm', 'id'=>'jamMulai']); ?>
    <?=$form->field($model, 'jam_tutup', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm', 'id'=>'jamTutup']); ?>
    <?=$form->field($model, 'waktu_pelayanan', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm']); ?>
    <?php
    // $form->field($model, 'maxantiran_poli', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm']); 
    ?>
 
    <?=$form
        ->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($status, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
            'options' => [$model->is_active => ['selected' => true]]
        ]);
    ?>
</div>
<div class="modal-footer">
            <?= Html::submitButton("<i class='fa fa-floppy-o'> ". Yii::t('fe', 'Simpan')."</i>", ['class' => 'btn bg-teal']) ?>
            <?= Html::button("<i class='fa fa-refresh'> ". Yii::t('fe', 'Ulang')."</i>",[
                                'class' => 'btn btn-lime-green', 'id'=>'reset',
                                ]); ?>
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
        $('#jamMulai').pickatime({
            formatSubmit: 'H-i-A',
        });
        $('#jamTutup').pickatime({
            formatSubmit: 'H-i-A',
        });
    })
</script>