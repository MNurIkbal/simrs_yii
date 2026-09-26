<?php

/**
 * @Author: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Date:   2021-01-22 14:18:16
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
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
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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

                if (isset($model->nama_bank)) {
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
                } else {
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
                ?>
                <?= DocoHelpers::generateToolbar($btn_toolbar) ?>
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
                        <div class="col-md-12">
                            <?= $form->field($model, 'propinsi_id')->dropDownList($list_provinsi, [
                                'class' => 'form-control input-sm select2',
                                'prompt' => Yii::t('fe', '-- Pilih --'),
                                'id' => 'propinsi_id',
                            ])->label(Yii::t('fe', 'Propinsi'));
                            ?>
                        </div>

                        <div class="col-md-12">
                            <?= $form->field($model, 'kabupaten_id')->widget(DepDrop::classname(), [
                                'options' => ['id' => 'kabupaten_id', 'class' => 'select2', 'tabindex' => 15],
                                'data' => [@$list_kabupaten[$model->kabupaten_id]],
                                'pluginOptions' => [
                                    'depends' => ['propinsi_id'],
                                    'placeholder' => '-- Pilih --',
                                    'url' => Url::to(['/master/kabupaten/list-kabupaten'])
                                ],
                            ])->label(Yii::t('fe', 'Kabupaten')); ?>
                        </div>

                        <div class="col-md-12">
                            <?= $form->field($model, 'is_active')->checkbox(['class' => 'form-control']) ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?= $form->field($model, 'nama_bank', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm']) ?>
                        <?= $form->field($model, 'cabang', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm']) ?>
                        <?= $form->field($model, 'no_rekening', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm']) ?>
                        <?= $form->field($model, 'nama_pemilikrek', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm']) ?>
                    </div>
                </div>

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
					if(res.metadata.status == 200){
						docoNotification("success", "Proses Berhasil!", "Data Berhasil di ubah.");
						window.location.href = "/master/bank";
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
					if(res.metadata.status == 200){
						docoNotification("success", "Proses Berhasil!", "Data Berhasil di simpan.");
						window.location.href = "/master/bank";
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
			var is_active = [{name: "BankForm[is_active]", value: "' . $aktif_status . '"}];
			var payload = _form.serializeArray().concat(is_active);

	        $(this).docoForm("click", {
	            url : _form.attr("action"),
	            data : payload,
	            success : function(res) {
        					if(res.response.code == 200){
        						docoNotification("success", "Proses Berhasil!", "Status aktif telah di ubah.");
        						window.location.href = "/master/bank";
        					}else{
        						docoNotification("warning", "Proses Gagal!", "Data Gagal di ubah.");
        					}
	            }
	        });
		  });
	});
', View::POS_END, 'form');
