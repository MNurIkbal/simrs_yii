<?php

/**
 * @Author: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Date:   2021-01-22 14:18:16
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
			  <!-- breadcrumbs replace with this -->
			  <div class="row">
				  <div class="column-1">
					  <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
				  </div>
				  <div class="column-2">
					  <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
					  <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
				  </div>
			  </div>
			  <!-- end -->
				<div class="heading-elements">
					<ul class="icons-list">
						<li><a data-action="collapse"></a></li>
					</ul>
				</div>
			</div>

			<div class="panel-toolbar clearfix">
				<?php
					$aktif_status = "";

					if(isset($model->kode)){
						$btn_simpan = [
							'type' => 'button',
							'title' => \Yii::t('fe', 'Simpan'),
							'icon' => 'fa fa-floppy-o',
							'method' => 'not exist',
							'attributes' => [
								'id' => 'btn-edit',
								'data-options' => 'click',
								'class' => 'bg-teal',
							]
						];
					}else{
						$btn_simpan = [
							'type' => 'button',
							'title' => \Yii::t('fe', 'Simpan'),
							'icon' => 'fa fa-floppy-o',
							'method' => 'not exist',
							'attributes' => [
								'id' => 'btn-submit',
								'data-options' => 'click',
								'class' => 'bg-teal',
							]
						];
					}

					$btn_toolbar = [
						'simpan' => $btn_simpan,
						'back'
					];

					if(isset($model->is_active)) {
						if($model->is_active) {
							$title = "Deaktifasi";
							$aktif_status = "0";
						} else {
							$title = "aktifasi";
							$aktif_status = "1";
						}

						$additional_button = [
							'status_aktifasi' => [
								'type' => 'button',
								'title' => \Yii::t('fe', $title),
								'icon' => 'fa fa-floppy-o',
								'method' => 'not exist',
								'attributes' => [
									'id' => 'btn-aktif',
									'data-options' => 'click',
									'class' => 'bg-teal',
								]
							]
						];

						$btn_toolbar = array_merge($btn_toolbar, $additional_button);
					}
				?>
				<?=DocoHelpers::generateToolbar($btn_toolbar) ?>
			</div>

			<div class="panel-body">
				<br>
				<!-- Start avtive form -->
				<?php $form = ActiveForm::begin([
					'id' => 'form', 
					'type' => ActiveForm::TYPE_HORIZONTAL,
					'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'enableClientScript' => true,
                    'validateOnSubmit' => false,
					'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
				]) ?>
				<div class="row">
					<div class="col-md-6">
						<?php
							if($aktif_status == "") {
								echo $form->field($model, 'kode', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']);
							} else {
								echo $form->field($model, 'kode', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm', 'disabled' => 'disabled']);
							}
						?>
						<?= $form->field($model, 'nama', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'kontak', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'alamat', ['labelOptions' => ['class' => 'text-right']])->textarea(['rows' => '6']) ?>
					</div>
				</div>
				<?= Html::hiddenInput('manufaktur_id', isset($encryptedId) ? $encryptedId : '', ['id' => 'manufaktur-id', 'readonly' => 'readonly']) ?>
				<?php ActiveForm::end() ?>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
	// Event Ready
	$(document).ready(function() {
		$("#btn-edit").on("click", function (event) {
			console.log("edit");
			var _form = $("#form");
	        $(this).docoForm("click", {
	            url : _form.attr("action"),
	            data : _form.serializeArray(),
	            success : function(res) {
					if(res.response.code == 200){
						docoNotification("success", "Proses Berhasil!", "Data Berhasil di ubah.");
						window.location.href = "/master/manufaktur";
					}else{
						docoNotification("warning", "Proses Gagal!", "Data Gagal di ubah.");
					}
	            }
	        });
		});

		$("#btn-submit").on("click", function (event) {
	        var _form = $("#form");
	        $(this).docoForm("click", {
	            url : _form.attr("action"),
	            data : _form.serializeArray(),
	            success : function(res) {
					if(res.response.code == 200){
						docoNotification("success", "Proses Berhasil!", "Data Berhasil di simpan.");
						window.location.href = "/master/manufaktur";
					}else if(res.response.code == 422) {
						docoNotification("warning", res.response.title, res.response.text);
					}else{
						docoNotification("warning", "Proses Gagal!", "Data Gagal di ubah.");
					}
	            }
	        });
	    });

	    $("#btn-aktif").on("click", function (event) {
			console.log("edit");
			var _form = $("#form");
			var is_active = [{name: "ManufakturForm[is_active]", value: "'.$aktif_status.'"}];
			var payload = _form.serializeArray().concat(is_active);

	        $(this).docoForm("click", {
	            url : _form.attr("action"),
	            data : payload,
	            success : function(res) {
					if(res.response.code == 200){
						docoNotification("success", "Proses Berhasil!", "Status aktif telah di ubah.");
						window.location.href = "/master/manufaktur";
					}else{
						docoNotification("warning", "Proses Gagal!", "Data Gagal di ubah.");
					}
	            }
	        });
		});
	});
', View::POS_END, 'form');
