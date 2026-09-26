<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'spesialisPegawai-form', 
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
        ]); 
    ?>
    <?= Html::activeHiddenInput($model, 'pegawai_id')?>
    <div class="form-group">
        <div class="col-lg-12">
            <?= $form->field($model, 'nama_pegawai')
                ->textInput([
                    'class' => 'form-control',
                    'disabled' => true,
                ]); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <?= $form->field($model, 'spesialis_id')
                ->dropDownList(
                    $listSpesialis,
                    [
                        'class' => 'select2 autoListSpesialis', 
                        'prompt' => Yii::t('fe', '-- Pilih --'),
                        'id' => 'select2_list_spesialis_id'
                    ]
                );
            ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success btn-md']) ?>
        <?= Html::button('Kembali', ['class' => 'btn btn-default btn-md', 'data-dismiss' => 'modal']);?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
$('#spesialisPegawai-form').docoForm('submit',{
    success : function(data) {
        $('#spesialisPegawai-form')[0].reset();
        $('#modal_backdrop').modal('hide');
        tablePegawai.draw();
    }
}); 
</script>

