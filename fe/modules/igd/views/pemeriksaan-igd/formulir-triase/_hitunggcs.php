<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
?>


<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Glasgow Coma Scale</h5>
</div>
<div class="modal-body">
    <div class="col-lg-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title"><?=Yii::t('fe', 'Glasgow coma scale')?></h6>
                
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-gcs',
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]); 
                ?>
                    <div class="col-md-12">
                        <?=$form->field($model, 'gcseye_id')->dropDownList(ArrayHelper::map($data_gcsEye, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_eye','prompt'=>'--Pilih--','options'=>$gcsEyeOptions])?>
                        <?=$form->field($model, 'gcsverbal_id')->dropDownList(ArrayHelper::map($data_gcsVerbal, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_verbal','prompt'=>'--Pilih--','options'=>$gcsVerbalOptions])?>
                        <?=$form->field($model, 'gcsmotorik_id')->dropDownList(ArrayHelper::map($data_gcsMotorik, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_motorik','prompt'=>'--Pilih--','options'=>$gcsMotorikOptions])?>
                        
                        <?=$form->field($model, 'jumlah_gcs')->textInput(['class'=>'nilai_gcs', 'readonly'=>true]);?>
                        <!-- <?=$form->field($model, 'is_kapitis')->checkbox(['type'=>'hidden'])?> -->
                        <!-- <?=$form->field($model, 'hasil_gcs')->textInput(['class'=>'hasil_gcs','readonly'=>true])?> -->
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
            
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-floppy-o'> " . Yii::t('fe', 'Hitung') . "</i>", ['id' => 'btn-hitung', 'class' => 'btn bg-teal', 'data-dismiss' => 'modal']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'> " . Yii::t('fe', 'Kembali') . "</i>", [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>

<?php
$this->registerJs("

    // define data master map
    var dataGcs = ".json_encode($data_gcs)."
    var listGcs = ".json_encode($data_listgcs)."
", View::POS_END, 'js2');
$this->registerJs($this->render('gcs.js'), View::POS_END, 'js')

?>