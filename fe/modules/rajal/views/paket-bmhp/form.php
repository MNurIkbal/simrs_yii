<?php

/**
 * @Author: afil
 * @Date:   2018-01-22 14:34:40
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-28 14:04:24
 * @Description: 
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
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
    <h5 class="modal-title"><b><?=$title;?></b> - <?=$action?></h5>
</div>
<div class="modal-body">
    <?=$form->field($modelPaketBmhp, 'daftartindakan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($list_tindakan, 'daftartindakan_id', 'daftartindakan_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => Yii::t('fe', 'Pilih'),
            ]); ?>
    <?=$form->field($modelPaketBmhp, 'obatalkes_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($list_alkes, 'obatalkes_id', 'obatalkes_namalain'), [
            'class' => 'form-control input-sm',
            'multiple' => 'multiple',
            'id' => 'dualistbox-tindakan',
        ]); ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-repeat'></i> ". Yii::t('fe', 'Reset'),[
        'class' => 'btn btn-aqua btn-sm reset',
        ]); ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
        ]); ?>
</div>
<?php ActiveForm::end(); ?>

<?php 
$this->registerJs('
    var dualistbox_tindakan = $("#dualistbox-tindakan").bootstrapDualListbox({
        nonSelectedListLabel: "Non-selected",
        selectedListLabel: "Selected",
    });

    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            table.draw();
        }
    });

    $("#btn-reset").on("click", function(){
        $(".select2").val(null).trigger("change");
    });
', View::POS_END);
?>