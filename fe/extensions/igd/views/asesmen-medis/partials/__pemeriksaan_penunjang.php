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
use dosamigos\ckeditor\CKEditor;
?>

<!-- Diagnosis -->
<div class="col-md-6 col-sm-12">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Diagnosis</h5>
	        </div>
	        <div class="panel-body">
	            <div class="row form-row">
	                <div class="col-sm-12">
	                	<div class="col-sm-12">
                      <?= $form->field($model, 'diagnosa_primary', [
                          'horizontalCssClasses' => [
                              'label' => 'col-sm-3 col-md-2 control-label',
                              'wrapper' => 'col-md-10 col-sm-9'
                          ],
                      ])->widget(Select2::classname(), [
                          'initValueText' => !is_null($diagnosa_primary_text) ? $diagnosa_primary_text : null,
                          'data' => $opt_diagnosa_primary,
                          'options' => [
                              'placeholder' => '-- Pilih --',
                              'class' => 'form-control input-sm'
                          ],
                          'pluginOptions' => [
                              'tags' => true,
                              'tokenSeparators' => [',', '_'],
                              'minimumInputLength' => 3,
                              'language' => [
                                  'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                              ],
                              'ajax' => [
                                  'url' => \yii\helpers\Url::to(['get-new-diagnosa']),
                                  'dataType' => 'json',
                                  'data' => new JsExpression('
                                      function(params) {
                                          return {
                                              q: params.term,
                                              type: "diagnosa_utama",
                                              all_text: 0,
                                              id_with_text: 1,
                                              is_perawat: ' . $is_perawat . '
                                          };
                                      }
                                  ')
                              ],
                              'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                              'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                              'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                          ],
                      ]) ?>
                    </div>
	                </div>

                  <div class="col-sm-12">
                    <div class="col-sm-12">
                      <?= $form->field($model, 'diagnosa_secondary', [
                          'horizontalCssClasses' => [
                              'label' => 'col-sm-3 col-md-2 control-label',
                              'wrapper' => 'col-md-10 col-sm-9'
                          ],
                      ])->widget(Select2::classname(), [
                          'showToggleAll' => false,
                          'options' => [
                              'multiple' => true,
                              'placeholder' => '-- Pilih --'
                          ],
                          'pluginOptions' => [
                              'tags' => true,
                              'tokenSeparators' => [',', '_'],
                              'maximumInputLength' => 50,
                              'minimumInputLength' => 3,
                              'language' => [
                                  'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                              ],
                              'ajax' => [
                                  'url' => \yii\helpers\Url::to(['get-new-diagnosa']),
                                  'dataType' => 'json',
                                  'data' => new JsExpression('
                                              function(params) {
                                                  return {
                                                      q: params.term,
                                                      type: "diagnosa_penyerta",
                                                      all_text: 0,
                                                      id_with_text: 1,
                                                      is_perawat: ' . $is_perawat . '
                                                  };
                                              }
                                          ')
                              ],
                              'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                              'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                              'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
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

<!-- Terapi IGD -->
<div class="col-md-6 col-sm-12">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Terapi IGD</h5>
	        </div>
	        <div class="panel-body">
	            <div class="row form-row">
	                <div class="col-sm-12">
	                    <div class="">
                            <div class="col-md-12 col-sm-12">
                                <div class="row">
                                  <?=
                                      $form->field($model, 'terapi', [
                                          'horizontalCssClasses' => [
                                              'label' => 'text-left control-label col-sm-2',
                                              'wrapper' => 'col-md-12'
                                          ],
                                      ])->widget(CKEditor::className(), [
                                          'options' => ['rows' => 20],
                                          'preset' => 'basic',
                                          'clientOptions' => [
                                              'extraPlugins' => '',
                                          ]
                                      ])->label(false);
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

<!-- Tindak Lanjut -->
<div class="col-md-6 col-sm-12">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Tindak Lanjut</h5>
	        </div>
	        <div class="panel-body">
	            <div class="row form-row">
	                <div class="col-sm-12">
                          <?=
                              DHtml::multipleRadio([
                                  'model' => $model,
                                  'fieldName' => 'tindak_lanjut',
                                  'colSize' => 3,
                                  'data' => $arrayConfig['tindak_lanjut']
                              ])
                          ?>
          				</div>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<!-- Keadaan Meninggalkan IGD -->
<div class="col-md-6 col-sm-12">
	<div class="row form-row">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h5 class="panel-title">Keadaan Meninggalkan IGD</h5>
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
	                        <?= $form->field($model, 'suhu_keluar', ['addon' => ['append' => ['content' => 'C']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>
