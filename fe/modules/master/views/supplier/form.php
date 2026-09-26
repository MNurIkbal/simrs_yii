<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-06 10:20:16
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-06-08 11:22:56
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
	.input-group-addon {
		background-color: #34bfa3 !important;
	}
</style>

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
					if(isset($model->supplier_nama)){
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
				?>
				<?=DocoHelpers::generateToolbar([
					'simpan' => $btn_simpan,
					'custom-reset'=>[
						'type' => 'click',
						'title' => \Yii::t('fe', 'Muat Ulang'),
						'icon' => 'fa fa-refresh',
						'attributes' => [
							'id' => 'btn-reset',
						]
					],
					'back'
				]) ?>
			</div>

			<div class="panel-body">
				<br>
				<!-- Start avtive form -->
				<?php $form = ActiveForm::begin([
					'id' => 'form', 
					'type' => ActiveForm::TYPE_HORIZONTAL,
					'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false,
					'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
				]) ?>
				<div class="row">
					<div class="col-md-6">
                          <?= $form->field($model, 'supplier_kode', [
                          		'labelOptions' => [
                          			'class' => 'text-right'
                          		],
                          		'addon' => [
                          			'append' => [
                          				'content' => '<i class="fa fa-refresh btn-info"></i>',
                          				'options' => ['class' => 'btn-kode']
                          			]
                          		]
                          	])->textInput([
                          		'class' => 'form-control input-sm kode-oa',
                          		'value' => isset($model->supplier_kode) ? $model->supplier_kode : $kodeSupplier
                          	]) ?>

						<?= $form->field($model, 'supplier_nama', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'supplier_namalain', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'supplier_alamat', ['labelOptions' => ['class' => 'text-right']])->textarea(['rows' => '6']) ?>
					</div>
					<div class="col-md-6">
						<?=$form
							->field($model, 'propinsi_id', ['labelOptions' => ['class' => 'text-right']])
							->dropDownList(ArrayHelper::map($propinsi, 'propinsi_id', 'propinsi_nama'), [
								'id' => 'filter_propinsi', 
								'class' => 'form-control select2 dep-to-child', 
								'prompt' => \Yii::t('fe', '— Pilih —'),
								'data-url' =>  '/master/supplier/get-kabupaten',
								'data-depend_id' => 'filter_kabupaten',
								'data-depend_prompt' => \Yii::t('fe', '— Pilih —'),
								'data-storage' => 'kabupaten',
								'data-key' => 'kabupaten_id',
							]);
						?>
						<?=$form
							->field($model, 'kabupaten_id', ['labelOptions' => ['class' => 'text-right']])
							->dropDownList(ArrayHelper::map(!empty($selectedKabupaten) ? $selectedKabupaten : [], 'kabupaten_id', 'kabupaten_nama'), [
								'id' => 'filter_kabupaten', 
								'class' => 'form-control select2 dep-to-parent', 
								'prompt' => \Yii::t('fe', '— Pilih —'),
								'data-url' =>  '/master/supplier/get-propinsi',
								'data-depend_id' => 'filter_propinsi',
							]);
						?>
						<?= $form->field($model, 'no_tlp', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm docoNumberOnly']) ?>
						<?= $form->field($model, 'email', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'no_fax', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm docoNumberOnly']) ?>
						<?= $form->field($model, 'no_npwp', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm docoNumberOnly']) ?>
						<?= $form->field($model, 'no_rekening', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm docoNumberOnly']) ?>
						<?= $form->field($model, 'nama_pemilikrek', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']) ?>
						<?= $form->field($model, 'bank_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList(ArrayHelper::map($bank, 'bank_id', 'nama_bank'), ['class' => 'select2', 'prompt' => '— PILIH —']) ?>
						<?= $form->field($model, 'pajak_id', ['labelOptions' => ['class' => 'text-right']])->dropDownList(ArrayHelper::map($pajak, 'pajak_id', 'pajak_name'), ['class' => 'select2', 'prompt' => '— PILIH —']) ?>
					</div>
				</div>
				<?= Html::hiddenInput('supplier_id', isset($encryptedId) ? $encryptedId : '', ['id' => 'supplier-id', 'readonly' => 'readonly']) ?>
				<?= Html::hiddenInput('propinsi_id', $model->propinsi_id, ['id' => 'propinsi-id', 'readonly' => 'readonly']) ?>
				<?= Html::hiddenInput('kabupaten_id', $model->kabupaten_id, ['id' => 'kabupaten-id', 'readonly' => 'readonly']) ?>
				<?php ActiveForm::end() ?>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
	// Event Ready
	$(document).ready(function() {
		// Save into local storage
		localStorage.clear();
		localStorage.setItem("propinsi", JSON.stringify('.json_encode($propinsi).'));
		localStorage.setItem("kabupaten", JSON.stringify('.json_encode($kabupaten).'));

		// Get element by id and remove class
		var element = document.getElementById("btn-reset");
		element.classList.remove("btn-toolbar");

		var is_edit = "'.isset($model->supplier_nama).'";

		// Click simpan btn
		// $("#btn-submit").click(function(event){
		// 	event.preventDefault();
		// 	$("#form").submit();
		// });

		/* UBAH PESANAN KAMAR */
		$("#btn-edit").on("click", function (event) {
			var _form = $("#form");
	        $(this).docoForm("click", {
	            url : _form.attr("action"),
	            data : _form.serializeArray(),
	            success : function(res) {
					if(res.response.code == 200){
						docoNotification("success", "Proses Berhasil!", "Data Berhasil di ubah.");
						window.location.href = "/master/supplier";
					}else{
						docoNotification("warning", "Proses Gagal!", "Data Gagal di ubah.");
					}
	            }
	        });
		});

		// After submit
		$("#btn-submit").on("click", function (event) {
	        var _form = $("#form");
	        $(this).docoForm("click", {
	            url : _form.attr("action"),
	            data : _form.serializeArray(),
	            success : function(res) {
					if(res.response.code == 200){
						docoNotification("success", "Proses Berhasil!", "Data Berhasil di simpan.");
						window.location.href = "/master/supplier";
					}else{
						docoNotification("warning", "Proses Gagal!", "Data Gagal di ubah.");
					}
	            }
	        });
	    });


		// Assign lokasi rak dan kabupaten
		$("#btn-reset").click(function(event) {
			var form = $("#form");
            form[0].reset();
            // $("#filter_propinsi, #filter_kabupaten, #supplierform-bank_id").select2("null").trigger();
            $("#filter_propinsi").val("").trigger("change");
            $("#filter_kabupaten").val("").trigger("change");
            $("#supplierform-bank_id").val("").trigger("change");
		});

		$(".btn-kode").on("click", function(){
	        $.ajax({
	            url: "'.Url::to(["get-kode"]).'",
	            beforeSend: function(){
	                $(".kode-oa").val("Harap tunggu....")
	                $(".kode-oa").attr("readonly", true)
	                $(this).attr("disabled", true)
	            },
	            success: function(data){
	                $(".kode-oa").val(data)
	                $(".kode-oa").attr("readonly", false)
	                $(this).attr("disabled", false)
	            }
	        })
	    });
	});
', View::POS_END, 'form');
