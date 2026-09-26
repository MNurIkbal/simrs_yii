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
        ->field($model, 'row_id', ['labelOptions' => ['class' => 'text-right']])
        ->dropDownList(ArrayHelper::map($pasien, 'row_id', 'pasien_m.no_rekam_medik'), [
            'class' => 'form-control input-sm select2',
            'prompt' => 'Pilih Pasien',
            'onchange'=>'
                $.post( "'.Yii::$app->urlManager->createUrl("rm/pencatatan-dok-rekam-medik/set-pencatatan?id=").'"+$(this).val(), function( data ) {
                  $( "#nomorprimer" ).val( data.nomorprimer );
                  $( "#nomorsekunder" ).val( data.nomorsekunder );
                  $( "#nomortertier" ).val( data.nomortertier );
                  $( "#tglmasukrak" ).val( data.tglmasukrak );
                });
            ' 
        ]);
    ?>
    <?=$form->field($model, 'nomorprimer', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm','id'=>'nomorprimer','readonly'=>true]); ?>
    <?=$form->field($model, 'nomorsekunder', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm','id'=>'nomorsekunder','readonly'=>true]); ?>
    <?=$form->field($model, 'nomortertier', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm','id'=>'nomortertier','readonly'=>true]); ?>
    <?=$form->field($model, 'tglmasukrak', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm pickadate','id'=>'tglmasukrak']); ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton('Simpan', ['class' => 'btn btn-success btn-sm']); ?>
    <?=Html::button('Kembali',['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            $('#modal_backdrop').modal('toggle');
            table.draw();
        }
    });
    $(function(){
        $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
        });
    })
</script>