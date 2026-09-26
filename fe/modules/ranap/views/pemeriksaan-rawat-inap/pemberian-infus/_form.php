<?php
use app\components\DocoHelpers;

use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>
<div class="col-md-12">
    <?php $form = ActiveForm::begin([
        'id' => 'pemberian-infus-form',
        'action' => Url::to(['/ranap/pemeriksaan-rawat-inap/save-pemberian-infus', 'id' => $id]),
        'enableClientValidation' => false,
        'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
    ]);?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'tgl_pemasangan')->textInput([
                'class' => 'form-control',
                'disabled' => true,
                'value' => $now,
            ])?>
            <?= $form->field($model, 'tgl_pemasangan')->hiddenInput()->label(false)?> 
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'instruksitindakan_id')->dropDownList($listTindakan, [
                'prompt' => '-- Pilih Tindakan --',
                'class' => 'form-control',
            ])?> 
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'instruksitindakanbmhp_id[]')->dropDownList($listObat, [
                'class' => 'form-control',
            ])?>
            <?= $form->field($model, 'instruksitindakanbmhp_id')->hiddenInput(['id'=>'instruksitindakanbmhp_id', 'disabled' => true])->label(false)?> 
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'volume', ['addon' => ['append' => ['content' => 'ml']]])->textInput([
                'class' => 'form-control doco-decimal-wcomma',
            ])?> 
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'durasi', ['addon' => ['append' => ['content' => 'menit']]])->textInput([
                'class' => 'form-control doco-decimal-wcomma',
            ])?> 
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'jumlah_tetesan', ['addon' => ['append' => ['content' => 'ml/menit']]])->textInput([
                'class' => 'form-control doco-decimal-wcomma',
                'readonly' => true,
            ])?> 
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'titik_pemasangan')->textInput([
                'class' => 'form-control',
            ])?> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" id="save-pemberian-infus" class="btn btn-xs btn-labeled bg-teal"><b><i class="fa fa-save"></i></b> Simpan</button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
    $this->registerJs("
    var is_nurse = '".$is_nurse."';
    ".$this->render('_form.js'), View::POS_END);
?>

<?php 
