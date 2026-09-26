<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\web\JsExpression;

use app\components\DocoHelpers;

use kartik\widgets\Select2;
use kartik\widgets\Depdrop;
use kartik\widgets\ActiveForm;
// use yii\widgets\ActiveForm;

?>

<?php
    $form = ActiveForm::begin([
        'id'=>'suku-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?= $form->field($model, 'suku_nama')->textInput(['class' => 'form-control input-sm','placeholder' => $model->getAttributeLabel('suku_nama')]); ?>
    <?= $form->field($model, 'suku_namalainnya')->textInput(['class' => 'form-control','placeholder' => $model->getAttributeLabel('suku_namalainnya')]); ?>
    <?= $form->field($model, 'is_active')
        ->radioList(
            [
                1=> Yii::t('fe', 'Aktif'),
                0=> Yii::t('fe', 'Tidak Aktif'),
             ], 
            ['id'=>'is_active', 'inline'=>true, 'value'=> $model->is_active ]
        ); 
    ?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['id'=>'btn-simpan','class' => 'btn btn bg-teal btn-sm']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
    <?php ActiveForm::end() ?>    
</div>
<?php
$script = <<< JS
	$('#suku-form').docoForm('submit',{
		success : function(data) {
            var form = $("#suku-form");
            form[0].reset();
            tableSuku.draw();
            $("#modal_backdrop").modal('toggle');
        },
	});

  $(document).on('click', '.btn-custom-reset', function(){
    var data_hid_suku_nama = $('#hid_suku_nama').val();
    var data_hid_suku_namalainnya = $('#hid_suku_namalainnya').val();
      if (data_hid_suku_nama=="" && data_hid_suku_namalainnya=="") {
        $('#sukuform-suku_nama').val("");
        $('#sukuform-suku_namalainnya').val("");
      }else{
        $('#sukuform-suku_nama').val(data_hid_suku_nama);
        $('#sukuform-suku_namalainnya').val(data_hid_suku_namalainnya);
      }
  });

JS;

$this->registerJs($script,View::POS_END,'jkun');
?>
