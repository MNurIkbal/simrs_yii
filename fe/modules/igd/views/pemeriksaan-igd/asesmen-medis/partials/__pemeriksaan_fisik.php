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
            <h5 class="panel-title">Pemeriksaan Fisik</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-6">
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title">Status Gizi</h5></label>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-decimal-wcomma imt_field']) ?>
                        <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'doco-decimal-wcomma imt_field']) ?>
                        <?=$form->field($model, 'bb_ideal', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'form-control input-sm berat-badan doco-decimal-wcomma', 'readonly' => 'readonly',]); ?>
                        <?= $form->field($model, 'imt', ['addon' => ['append' => ['content' => 'kg/m2']]])->textInput(['class' => 'doco-decimal-wcomma', 'readonly' => 'readonly',]) ?>
						<?=$form->field($model, 'imt_kategori', [])->textInput(['class' => 'form-control input-sm', 'readonly' => 'readonly',]); ?>
                    </div>
                    <div class="col-sm-12">
                        <hr>
                    </div>
                    <label class="control-label has-star col-sm-12"><h5 class="panel-title">Tanda Vital</h5></label>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'tekanandarah', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput() ?>
                        <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'pernapasan', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']) ?>
                        <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => '°C']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
                        <?= $form->field($model, 'saturasi_o2', ['addon' => ['append' => ['content' => '%']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
                        <div class="row form-row">
	                    	<label class="control-label has-star col-sm-12">Skrining Nyeri</label>
				            <?=
				                DHtml::trueFalseRadio($model, 'skrining_nyeri');
				            ?>
				        </div>
			            <div class="row form-row" id="pilih-wrapper">
				            <label class="control-label has-star col-sm-12">Skala Nyeri</label>
				            <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'pilih_skala',
                                    'colSize' => 4,
                                    'data' => $arrayConfig['pilih_skala']
                                ])
                            ?>
				        </div>
                    </div>
                </div>

                <div class="col-sm-12">
		            <div class="row form-row" id="nyeri-wrapper">
		                <div class="col-sm-12" id="dewasa-wrapper">
		                    <div class="box-scale">
		                        <div class="box-scale-info row">
		                            <div class="col-sm-3 text-left tidak-nyeri">Tidak Nyeri</div>
		                            <div class="col-sm-3 text-center nyeri-ringan">Nyeri Ringan</div>
		                            <div class="col-sm-3 text-center nyeri-sedang">Nyeri Sedang</div>
		                            <div class="col-sm-3 text-right nyeri-berat">Nyeri Berat</div>
		                        </div>
		                        <div class="box-scale-line box-scale-line__separator">
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__hidePercentage"></div>
		                        </div>
		                        <div class="box-scale-line" data-type="skala_nyeri">
		                            <div data-percentage="0" data-info="tidak-nyeri" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__0">0</div>
		                            </div>
		                            <div data-percentage="10" data-info="tidak-nyeri" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__1">1</div>
		                            </div>
		                            <div data-percentage="20" data-info="nyeri-ringan" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__2">2</div>
		                            </div>
		                            <div data-percentage="30" data-info="nyeri-ringan" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__3">3</div>
		                            </div>
		                            <div data-percentage="40" data-info="nyeri-ringan" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__4">4</div>
		                            </div>
		                            <div data-percentage="50" data-info="nyeri-sedang" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__5">5</div>
		                            </div>
		                            <div data-percentage="60" data-info="nyeri-sedang" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__6">6</div>
		                            </div>
		                            <div data-percentage="70" data-info="nyeri-sedang" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__7">7</div>
		                            </div>
		                            <div data-percentage="80" data-info="nyeri-berat" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__8">8</div>
		                            </div>
		                            <div data-percentage="90" data-info="nyeri-berat" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__9">9</div>
		                            </div>
		                            <div data-percentage="100" data-info="nyeri-berat" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__10">10</div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		                <div class="col-sm-12" id="anak-wrapper">
		                    <div class="box-scale">
		                        <div class="box-scale-header">
		                            <img src="/media/img/all-emote.svg" alt="">
		                        </div>
								<div class="box-scale-info row">
									<div class="col-sm-2 text-left tidak-nyeri-anak">Tidak Nyeri</div>
									<div class="col-sm-2 text-center sedikit-nyeri">Sedikit Nyeri</div>
									<div class="col-sm-2 text-center agak-mengganggu">Agak Mengganggu</div>
									<div class="col-sm-2 text-center nyeri-mengganggu">Nyeri Mengganggu</div>
									<div class="col-sm-2 text-center sangat-mengganggu">Sangat Mengganggu</div>
									<div class="col-sm-2 text-right nyeri-berat-anak">Nyeri Berat</div>
								</div>
		                        <div class="box-scale-line box-scale-line__separator">
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__point">&nbsp;</div>
		                            <div class="box-scale-line__hidePercentage"></div>
		                        </div>
		                        <div class="box-scale-line" data-type="skala_nyeri_anak">
		                            <div data-percentage="0" data-info="tidak-nyeri-anak" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__0">0</div>
		                            </div>
		                            <div data-percentage="10" data-info="sedikit-nyeri" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__1">1</div>
		                            </div>
		                            <div data-percentage="20" data-info="sedikit-nyeri" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__2">2</div>
		                            </div>
		                            <div data-percentage="30" data-info="agak-mengganggu" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__3">3</div>
		                            </div>
		                            <div data-percentage="40" data-info="agak-mengganggu" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__4">4</div>
		                            </div>
		                            <div data-percentage="50" data-info="nyeri-mengganggu" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__5">5</div>
		                            </div>
		                            <div data-percentage="60" data-info="nyeri-mengganggu" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__6">6</div>
		                            </div>
		                            <div data-percentage="70" data-info="sangat-mengganggu" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__7">7</div>
		                            </div>
		                            <div data-percentage="80" data-info="sangat-mengganggu" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__8">8</div>
		                            </div>
		                            <div data-percentage="90" data-info="nyeri-berat-anak" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__9">9</div>
		                            </div>
		                            <div data-percentage="100" data-info="nyeri-berat-anak" class="box-scale-line__point">
		                                <div class="box-scale-line__btn box-scale-line__10">10</div>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
                </div>

	            <div class="row form-row">
	            	<div class="col-sm-12">
		                <p class="header-form">Glasgow Coma Scale (GCS)</p>
		                <div class="col-sm-12">
		                    <?= $form->field($model, 'gcseye_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcseyeasmedForm']) ?>
		                </div>
		                <div class="col-sm-12">
		                    <?= $form->field($model, 'gcsverbal_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcsverbalasmedForm']) ?>
		                </div>
		                <div class="col-sm-12">
		                    <?= $form->field($model, 'gcsmotorik_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcsmotorikasmedForm']) ?>
		                </div>
		                <div class="col-sm-12">
		                    <?= $form->field($model, 'hasil_gcs', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-2']])->textInput(['class' => 'default-disabled']) ?>
		                </div>
		                <div class="col-sm-8 col-sm-offset-2" style="display:none">
		                    <?= $form->field($model, 'is_kapitis', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->checkbox() ?>
		                </div>
		                <div class="col-sm-12" style="display:none">
		                    <div class="form-group">
		                        <label for="" class="control-label col-sm-2">Keterangan GCS</label>
		                        <div class="col-sm-2">
		                            <input type="text" class="form-control default-disabled" name="keterangan_gcs">
		                        </div>
		                    </div>
		                </div>
		            </div>
	            </div>

            </div>
        </div>
    </div>
</div>