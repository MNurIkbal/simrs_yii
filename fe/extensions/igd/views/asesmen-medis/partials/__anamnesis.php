<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>

<div class="row form-row">

    <!-- Anamnesis & GCS Section -->
    <div class="col-12 col-sm-12 col-md-5">
      <!--  Anamnesis -->
      <div class="col-12 ">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Anamnesis</h5>
            </div>
            <div class="panel-body">
              <div class="row form-row">
                  <div class="col-sm-12">
                      <div class="row">
                          <label for="" class="control-label has-star col-sm-4">Anamnesis</label>
                          <div class="col-sm-8">
                              <?= Html::activeCheckBox($model, 'asesmen_auto', [
                                  'label' => 'Auto Anamnesa',
                                  'id' => 'auto_anamnesa',
                              ]) ?>
                          </div>
                      </div>
                      <div class="row">
                          <label for="" class="has-star col-sm-4"></label>
                          <div class="col-sm-4">
                              <?= Html::activeCheckBox($model, 'asesmen_allo', [
                                  'label' => 'Allo Anamnesa',
                                  'id' => 'allo_anamnesa',
                              ]) ?>
                          </div>
                          <div class="col-sm-4">
                              <?= $form->field($model, 'asesmen_allo_anamnesa', ['horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'form-control default-disabled input-tag', 'id' => 'asesmen_allo_anamnesa--form'])->label(false) ?>
                          </div>
                      </div>
                      <?= $form->field($model,'keluhan_utama', ['horizontalCssClasses' => ['label' => 'col-sm-4 col-md-4', 'wrapper' => 'col-sm-8 col-md-8']])->textarea(['class' => 'form-control'])->label() ?>

                      <?= $form->field($model,'riwayat_penyakit_sekarang', ['horizontalCssClasses' => ['label' => 'col-sm-4 col-md-4', 'wrapper' => 'col-sm-8 col-md-8']])->textarea(['class' => 'form-control'])->label() ?>

                      <?= $form->field($model,'riwayat_penyakit_dahulu', ['horizontalCssClasses' => ['label' => 'col-sm-4 col-md-4', 'wrapper' => 'col-sm-8 col-md-8']])->textarea(['class' => 'form-control'])->label() ?>

                      <?= $form->field($model,'riwayat_terapi_sebelumnya', ['horizontalCssClasses' => ['label' => 'col-sm-4 col-md-4', 'wrapper' => 'col-sm-8 col-md-8']])->textarea(['class' => 'form-control'])->label() ?>

                      <?= $form->field($model,'alergi', ['horizontalCssClasses' => ['label' => 'col-sm-4 col-md-4', 'wrapper' => 'col-sm-8 col-md-8']])->textarea(['class' => 'form-control'])->label() ?>

                  </div>
              </div>
            </div>
        </div>
      </div>

      <!-- GCS -->
      <div class="col-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Glasgow Coma Scale (GCS)</h5>
            </div>
            <div class="panel-body">
              <div class="row form-row">
                  <div class="col-sm-12">
                      <?= $form->field($model, 'gcseye_id', ['horizontalCssClasses' => ['label' => 'col-sm-3 col-md-2', 'wrapper' => 'col-sm-9 col-md-8']])->dropDownList([], ['id' => 'gcseyeasmedForm']) ?>
                  </div>
                  <div class="col-sm-12">
                      <?= $form->field($model, 'gcsverbal_id', ['horizontalCssClasses' => ['label' => 'col-sm-3 col-md-2', 'wrapper' => 'col-sm-9 col-md-8']])->dropDownList([], ['id' => 'gcsverbalasmedForm']) ?>
                  </div>
                  <div class="col-sm-12">
                      <?= $form->field($model, 'gcsmotorik_id', ['horizontalCssClasses' => ['label' => 'col-sm-3 col-md-2', 'wrapper' => 'col-sm-9 col-md-8']])->dropDownList([], ['id' => 'gcsmotorikasmedForm']) ?>
                  </div>
                  <div class="col-sm-12">
                      <?= $form->field($model, 'hasil_gcs', ['horizontalCssClasses' => ['label' => 'col-sm-3 col-md-2', 'wrapper' => 'col-sm-3 col-md-2']])->textInput(['class' => 'default-disabled']) ?>
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

    <!-- Tanda tanda vital & skrining gizi -->
    <div class="col-12 col-sm-12 col-md-7">

      <!-- Tanda tanda vital -->
      <div class="col-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Tanda tanda vital</h5>
            </div>
            <div class="panel-body">
              <div class="col-6 col-sm-6">
                  <?= $form->field($model, 'tekanandarah', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(['class'=> 'doco-td-mmhg-format']) ?>
              </div>
              <div class="col-6 col-sm-6">
                <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => 'C']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
              </div>
              <div class="col-6 col-sm-6">
                <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']) ?>
              </div>
              <div class="col-6 col-sm-6">
                <?= $form->field($model, 'saturasi_o2', ['addon' => ['append' => ['content' => '%']]])->textInput(['class' => 'doco-decimal-wcomma']) ?>
              </div>
              <div class="col-6 col-sm-6">
                <?= $form->field($model, 'pernapasan', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']) ?>
              </div>
            </div>
        </div>
      </div>

      <!-- Skrining nyeri -->
      <div class="col-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Skrining nyeri</h5>
            </div>
            <div class="panel-body">
                <label class="control-label has-star col-sm-2 col-4">Skala Nyeri</label>
                <?= DHtml::multipleRadio([
                        'model' => $model,
                        'fieldName' => 'pilih_skala',
                        'colSize' => 3,
                        'data' => $arrayConfig['pilih_skala']
                    ])
                ?>
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
                                <div class="box-scale-line__hidePercentage"></div>
                            </div>
                            <div class="box-scale-line" data-type="skala_nyeri">
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
                                <div class="box-scale-line__hidePercentage"></div>
                            </div>
                            <div class="box-scale-line" data-type="skala_nyeri_anak">
                                <div data-percentage="10" data-info="tidak-nyeri-anak" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__1">1</div>
                                </div>
                                <div data-percentage="20" data-info="sedikit-nyeri" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__2">2</div>
                                </div>
                                <div data-percentage="30" data-info="sedikit-nyeri" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__3">3</div>
                                </div>
                                <div data-percentage="40" data-info="agak-mengganggu" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__4">4</div>
                                </div>
                                <div data-percentage="50" data-info="agak-mengganggu" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__5">5</div>
                                </div>
                                <div data-percentage="60" data-info="nyeri-mengganggu" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__6">6</div>
                                </div>
                                <div data-percentage="70" data-info="nyeri-mengganggu" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__7">7</div>
                                </div>
                                <div data-percentage="80" data-info="sangat-mengganggu" class="box-scale-line__point">
                                    <div class="box-scale-line__btn box-scale-line__8">8</div>
                                </div>
                                <div data-percentage="90" data-info="sangat-mengganggu" class="box-scale-line__point">
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
        </div>

      </div>

    </div>
</div>
