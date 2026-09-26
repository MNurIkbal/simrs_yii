<?php

use yii\web\View;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
// use yii\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;

use yii\web\JsExpression;

?>


<?php 
    $form = ActiveForm::begin([
        'id'=>'pendidikan-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?= $form->field($model, 'pendidikan_nama')->textInput(['class' => 'form-control input-sm','placeholder' => $model->getAttributeLabel('pendidikan_nama')]); ?>
    <?php 
    // echo $form->field($model, 'indexing_id', [
    //                         'horizontalCssClasses' => [
    //                             'label' => 'text-left control-label col-sm-4',
    //                             'wrapper' => 'col-md-8'
    //                         ]
    //                     ])->dropDownList($ddl_indexing, ['prompt' => '-- Pilih --', 'class' => 'select2']);
                        ?>

    <?= $form->field($model, 'pendidikan_namalainnya')->textInput(['class' => 'form-control','placeholder' => $model->getAttributeLabel('pendidikan_namalainnya')]); ?>
    <?= $form->field($model, 'pendidikan_urutan')->textInput(['class' => 'form-control docoNumberOnly','placeholder' => $model->getAttributeLabel('pendidikan_urutan')]); ?>
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
    $('#reset-pendidikan').on('click', function () {
        location.reload();
    });

    $('#pendidikan-form').docoForm('submit',{
        success : function(data) {
            var form = $("#pendidikan-form");
            form[0].reset();
            tablePendidikan.draw();
            $("#modal_backdrop").modal('toggle');
        },
    });

    $('.hidebtn').hide();
JS;

$this->registerJs($script,View::POS_END,'jkun');
?>