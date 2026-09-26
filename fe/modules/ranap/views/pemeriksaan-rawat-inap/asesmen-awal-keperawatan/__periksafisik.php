<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Url;

use app\modules\ranap\components\AsesmenWizardWidget;
use app\modules\ranap\components\AsesmenHtml;
use app\modules\ranap\components\widget\SkoringWidget;
use app\modules\ranap\components\widget\RadioTextWidget;
use app\modules\ranap\components\widget\CheckboxTextWidget;
use app\modules\ranap\components\widget\TextWithPlusWidget;
?>
<?php 
    $form = ActiveForm::begin([
        'id' => 'periksafisikForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
?>

<?= $form->field($modelAsesmen, 'nyeri_lain_lain')->widget(TextWithPlusWidget::className(), [
    // configure additional widget properties here
]) ?>
<?= $form->field($modelAsesmen, 'nyeri_lain_lain2')->widget(TextWithPlusWidget::className(), [
    // configure additional widget properties here
]) ?>

<?= $form->field($modelAsesmen, 'r_alergi')->widget(CheckboxTextWidget::className(), [
	'textfieldAttribute' => 'nama_alergi',
	'textfieldDepends' => 1,
	'textfieldOptions' => [],
	'data' => $list_ada_tidak
    // configure additional widget properties here
]) ?>

<?= $form->field($modelAsesmen, 'masuk_dengan')->widget(RadioTextWidget::className(), [
	'textfieldAttribute' => 'masuk_denganlain',
	'textfieldDepends' => 1,
	'textfieldOptions' => [],
	'data' => $list_ada_tidak,
	'widgetOptions'=>['class'=>'ceks','data-id'=>1]
    // configure additional widget properties here
]) ?>
	<button type="submit">OK</button>
<?php ActiveForm::end(); ?>

<?php
	$this->registerJs("

		$('#periksafisikForm').on('beforeSubmit', function(e){

		    var form = $(this);
		    var formData = form.serializeArray();
		    console.log(formData);
		}).on('submit', function(e){
		    e.preventDefault();
		});
		",View::POS_END);
?>