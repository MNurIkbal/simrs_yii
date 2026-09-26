<?php

/**
 * @Author: Iqbal Qurahman
 * @Date:   2018-07-13 17:07:43
 * @Last Modified by:   Iqbal Qurahman
 * @Last Modified time: 2018-07-16 17:39:55
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h5 class="panel-title"><?= Yii::t('fe', 'Verbal Order') ?></h5>
		<div class="heading-elements">
			<ul class="icons-list">
				<li><a data-action="collapse"></a></li>
			</ul>
		</div>
	</div>
	<div class="panel-toolbar panel-toolbar-hidden clearfix">
		<?= DocoHelpers::generateToolbar([
			'custom-back' => [
				'type' => 'button',
				'title' => Yii::t('fe', 'Kembali'),
				'icon' => 'fa fa-arrow-left',
				'attributes' => [
					'id' => 'btn-back-verbal-order',
				],
			],
			'custom-save' => [
				'type' => 'submit',
				'title' => Yii::t('fe', 'Simpan'),
				'icon' => 'fa fa-floppy-o',
				'attributes' => [
					'id' => 'btn-save-verbal-order',
				],
			],
			'custom-reset' => [
				'type' => 'button',
				'title' => Yii::t('fe', 'Muat Ulang'),
				'icon' => 'fa fa-refresh',
				'attributes' => [
					'id' => 'btn-reset-verbal-order',
				],
			],
		]) ?>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-6">
				<?php $form = ActiveForm::begin([
					'id' => 'form-verbal-order',
					'enableAjaxValidation' => true,
					'enableClientValidation' => true,
					'type' => ActiveForm::TYPE_HORIZONTAL,
					'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
				]) ?>
				<?= Html::hiddenInput('VerbalOrderRanapForm[pendaftaran_id]', $modelVerbalOrder->pendaftaran_id);?>
				<?= Html::hiddenInput('VerbalOrderRanapForm[pasienadmisi_id]', $modelVerbalOrder->pasienadmisi_id);?>
				<?= Html::hiddenInput('VerbalOrderRanapForm[pasien_id]', $modelVerbalOrder->pasien_id);?>
				<?= Html::hiddenInput('VerbalOrderRanapForm[pegawai_id]', $modelVerbalOrder->pegawai_id);?>
				<!-- Ruangan -->
				<?php 
					if(count($listRuangan) == 1){
						$formRuangan = $form->field($modelVerbalOrder, 'ruangan_id')->dropDownList($listRuangan,
							['class'=>'form-control select2',
							'id'=>'ruangan_id-verbal-order', 
							'disabled'=>'disabled',
							'value' => array_values($listRuangan)[0],
							'style' => ['padding'=>'0 0 0 0']
						]);
						$formHiddenRuangan = Html::hiddenInput('VerbalOrderRanapForm[ruangan_id]', array_keys($listRuangan)[0]);
					}else{
						$formRuangan = $form->field($modelVerbalOrder, 'ruangan_id')->dropDownList(
							$listRuangan, [
								'id'=>'ruangan_id-verbal-order',
								'class' => 'form-control select2',
								'style' => ['padding'=>'0 0 0 0']
							]
						);
						$formHiddenRuangan = '';
					}
			 	?>

				<?= $formRuangan; ?>
				<?= $formHiddenRuangan; ?>
				<?= $form->field($modelVerbalOrder, 'instruksi')->textArea(); ?>
				<?php echo $form->field($modelVerbalOrder, 'pemberi_instruksi_id')->widget(Select2::classname(), [
					'options' => ['placeholder' => '-- Pilih instruksi --',
						'class' => 'form-control input-sm select2'
					],
					'pluginOptions' => [
						'minimumInputLength' => 3,
						'allowClear' => true,
						'language' => [
							'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
						],
						'ajax' => [
							'url' => \yii\helpers\Url::to(['/ranap/end-point/get-list-pemberi-instruksi']),
							'dataType' => 'json',
							'data' => new JsExpression('
								function(params) {
									return {
										term: params.term,
										id_ruangan: '.$id_ruangan.'
									};
								}
							'),
							'processResults' => new JsExpression('function(result) { return {results:result.data}; }'),
						],
						'templateResult' => new JsExpression ('function(data){ return data.text;}'),
						'templateSelection' => new JsExpression ( 'function (data) { return data.text; }' ) ,
					],
				])->label('Pemberi Instruksi') ?>

				<?php ActiveForm::end() ?>
			</div>
		</div>
	</div>
</div>

<?php // File
$this->registerJs($this->render('js/index.js'), View::POS_END);
$this->registerJs($this->render('js/form-verbal-order.js'), View::POS_END);
?>