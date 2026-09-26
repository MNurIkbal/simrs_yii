<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
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
        ->field($model, 'idx_instalasi', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($instalasi, 'idx_instalasi', 'instalasi_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => 'Pilih Instalasi'
        ]);
    ?>
    <?=$form
        ->field($model, 'idx_ruangan', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($ruangan, 'idx_ruangan', 'ruangan_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => 'Pilih Ruangan'
        ]);
    ?>
    <?=$form->field($model, 'barang_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('barang_nama'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'qty_pesan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('qty_pesan'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'tgl_mintadikirim', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm pickadate','id'=>'tgl_mintadikirim']); ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', 'Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', 'Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
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
        $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
        });
    })
</script>