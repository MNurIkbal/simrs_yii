<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-10 17:00:33
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-17 14:53:25
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>

<?php 
$form = ActiveForm::begin([
    'id' => 'form-hasil', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<?php
echo Html::hiddenInput('NilaiRujukanForm[pemeriksaanlab_id]', $attributes['pemeriksaanlab_id']);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'nama_rujukan', ['labelOptions' => ['class' => 'text-left']])->textInput([
    	'placeholder' => \Yii::t('fe', 'Nama Hasil'), 
    	'class' => 'form-control input-sm',
        'value' => $nama_rujukan,
    ])->label(\Yii::t('fe', 'Nama Hasil')); ?>
    <?= $form->field($model, 'is_active')->checkbox()->label(false); ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm', 'id' => 'btn-simpan']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-repeat"></i> Ulang'),['class' => 'btn btn-lime-green btn-sm reset']); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
        $("#form-hasil").docoForm("submit",{
            success : function(data) {
                window.location.reload();
            }
        });

    
    $(".reset").on("click", function(){
        resetForm($("#form-hasil"));
    });

    function resetForm($form) {
        $form.find("input:text, input:password, input:file, select, textarea").val("");
        $form.find("input:radio")
             .removeAttr("checked").removeAttr("selected");
        $(".select2").val(null).trigger("change");
    }
</script>