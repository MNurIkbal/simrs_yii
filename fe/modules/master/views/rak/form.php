<?php

/**
 * @Author: Muhammad Fajar N A 
 * @Date:   2021-03-19 14:22:02
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use yii\helpers\Url;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
             <div class="row">
                <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
					<?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                </div>
             </div>
             <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
             </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?php
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

                    $btn_toolbar = [
						'simpan' => $btn_simpan,
						'back'
					];

                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar) ?>
            </div>
            <div class="panel-body">
                <br>
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
                        <?= $form->field($model, 'ruangan_id', ['labelOptions' => ['class' => 'text-left']])->dropDownList(ArrayHelper::map($result['data'], 'ruangan_id', 'ruangan_nama'),[
                                                    'class' => 'select2',
                                                    'prompt' => '-- Pilih --',
                                                    'id' => 'ruangan_id'
                                                ]); ?>
                        <?= $form->field($model, 'parentrakobat_id')
                        ->widget(DepDrop::classname(), [
                                'options' => ['id'=>'parentrakobat_id',
                                'class' => 'form-control select2'],
                                'pluginOptions'=>[
                                    'initialize'=>true,
                                    'loadingText' => Yii::t('fe', 'Memuat...'),
                                    'depends' => ['ruangan_id'],
                                    'placeholder' => Yii::t('fe', '-- Pilih Parent Rak --'),
                                    'url'=> Url::to(['/master/rak/get-parent-rak']),
                                    'prompt' => Yii::t('fe', '-- Pilih Parent Rak --'),
                                ],
                                'data'=> $parentList,
                            ])->label(Yii::t('fe', 'Parent'));
                        ?>
                        <?= $form->field($model, 'rakobat_nama', ['labelOptions' => ['class' => 'text-left']])->label('Nama Rak / Laci')->textInput(['class' => 'form-control input-sm']) ?>                                                    
                    
                    </div>
                <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function(){
        $("#btn-submit").on("click", function (event) {
	        var _form = $("#form");
	        $(this).docoForm("click", {
	            url : _form.attr("action"),
	            data : _form.serializeArray(),
	            success : function(res) {
					if(res.response.code == 200){
						docoNotification("success", "Proses Berhasil!", "Data Berhasil di simpan.");
						window.location.href = "/master/rak/";
					}else if(res.response.code == 422) {
						// docoNotification("warning", res.response.title, res.response.text);
					}else{
						docoNotification("warning", "Proses Gagal!", "Data Gagal di ubah.");
					}
	            },
                error : function(data) {
                    var data = data.responseJSON.response.data
                    var msg = [];
                    $.each(data, function(i,val) {
                        msg.push(val.join(","));
                    });
                    docoNotification("error", "Proses Gagal", msg.join(","));
                } 
	        });
	    }); 
    });
');

?>