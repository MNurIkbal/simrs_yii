<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
    use yii\helpers\ArrayHelper;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
        'id' => 'batal-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
    ]);
    ?>
    
    <?= $form->field($model, 'username')->textInput(['value'=>$username,'readonly'=>true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>    
    <?=$form->field($model, 'is_ditagihkan')->dropDownList(ArrayHelper::map($listStatusBatal, 'no', 'text'),[
        'class'=>'select2 jenis',
        'prompt'=>'-- Pilih Jenis Alasan --',
        'options'=>''])?>
    <?= $form->field($model, 'alasan_batalstop')->textarea(['rows' => '4'],['class' => 'form-control']); ?>
    

    <?= Html::hiddenInput('BatalStopAkomodasiForm[pendaftaran_id]', $encryptedId);?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">

    $("#batal-form").docoForm("submit",{
        success : function(data) {
            table.draw();
            $("#modal_backdrop").modal("toggle");
        },
    });
</script>