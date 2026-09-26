<?php
use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>

<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Pemeriksaan Penunjang</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
                	<div class="col-sm-12">
                        <div class="row form-row">
	                        <div class="col-sm-12">
	                            <div class="">
	                                <label class="control-label has-star col-sm-4">Laboratorium</label>
	                                <div class="col-sm-8">
	                                    <div class="row">
	                                        <?=
	                                Html::activeTextarea($model, 'laboratorium', ['class' => 'form-control']);
	                            ?>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="row form-row">
	                        <div class="col-sm-12">
	                            <div class="">
	                                <label class="control-label has-star col-sm-4">Radiologi</label>
	                                <div class="col-sm-8">
	                                    <div class="row">
	                                        <?=
	                                Html::activeTextarea($model, 'radiologi', ['class' => 'form-control']);
	                            ?>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
                    </div>
                </div>
                <div class="col-sm-6">
                	<div class="col-sm-12">
                        <div class="row form-row">
	                        <div class="col-sm-12">
	                            <div class="">
	                                <label class="control-label has-star col-sm-4">EKG</label>
	                                <div class="col-sm-8">
	                                    <div class="row">
	                                        <?=
	                                Html::activeTextarea($model, 'ekg', ['class' => 'form-control']);
	                            ?>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="row form-row">
	                        <div class="col-sm-12">
	                            <div class="">
	                                <label class="control-label has-star col-sm-4">Lain-Lain</label>
	                                <div class="col-sm-8">
	                                    <div class="row">
	                                        <?=
	                                Html::activeTextarea($model, 'lain_lain', ['class' => 'form-control']);
	                            ?>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Diagnosis</h5>
	        </div>
	        <div class="panel-body">
	            <div class="row form-row">
	                <div class="col-sm-12">
	                	<div class="col-sm-12">
						<?= $form->field($model, 'diagnosis')->widget(Select2::classname(),[
                            'showToggleAll' => false,
							'data' => $optDiagnosa,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '-- Pilih --',
                                'class' => 'form-control input-sm select2',
								'id' => 'formDiagnosis'
                            ],
                            'pluginOptions' => [
                                'tags' => true,
                                'tokenSeparators' => [',', '_'],
                                'maximumInputLength' => 50,
                                // 'allowClear' => true,
                                // 'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                ],
								'ajax' => [
									'url' => \yii\helpers\Url::to(['/igd/end-point/get-new-diagnosa']),
									'dataType' => 'json',
									'data' => new JsExpression('
										function(params) {
											return {
												q: params.term,
												type: "diagnosa_utama",
												all_text: 0,
												id_with_text: 1,
												page:params.page || 1
											}; 
										}
									'),
									'processResults' => new JsExpression('
                                        function (data, params) {
                                            params.page = params.page || 1;
                                            return data
                                        }
                                    ')
								],
                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                            ],
                        ]);
                    ?>
		                </div>
		            </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div class="col-sm-6">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Terapi Saat Meninggalkan IGD</h5>
	        </div>
	        <div class="panel-body">
	            <div class="row form-row">
	                <div class="col-sm-12">
	                    <div class="">
                            <label class="control-label has-star col-sm-4">Terapi</label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <?=
                            Html::activeTextarea($model, 'terapi', ['class' => 'form-control']);
                        ?>
                                </div>
                            </div>
                        </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<div class="col-sm-12">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Tindak Lanjut</h5>
	        </div>
	        <div class="panel-body">
	            <div class="row form-row">
	                <div class="col-sm-12">
	                    <div class="">
	                        <div class="col-sm-8">
	                            <div class="row">
	                                <?=
	                                    DHtml::multipleRadio([
	                                        'model' => $model,
	                                        'fieldName' => 'tindak_lanjut',
	                                        'colSize' => 4,
	                                        'data' => $arrayConfig['tindak_lanjut']
	                                    ])
	                                ?>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<div class="col-sm-12">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Kondisi Saat Meninggalkan IGD</h5>
	        </div>
	        <div class="panel-body">
	            <div class="row form-row">
	                <div class="col-sm-6">
	                    <div class="col-sm-12">
	                        <?= $form->field($model, 'ku_keluar') ?>
	                        <?= $form->field($model, 'tekanandarah_keluar', ['addon' => ['append' => ['content' => 'mmHg']]]) ?>
	                        <?= $form->field($model, 'nadi_keluar', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
	                    </div>
	                </div>
	                <div class="col-sm-6">
	                    <div class="col-sm-12">
	                        <?= $form->field($model, 'pernapasan_keluar', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
	                        <?= $form->field($model, 'saturasi_o2_keluar', ['addon' => ['append' => ['content' => '%']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
	                        <?= $form->field($model, 'suhu_keluar', ['addon' => ['append' => ['content' => 'oC']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>