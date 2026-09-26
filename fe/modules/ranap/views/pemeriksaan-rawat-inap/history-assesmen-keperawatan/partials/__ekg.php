<?php

use app\components\DHtml;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="row form-row" id="__ekg">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Eksposure / EKG / Elektrolit</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-4">
                    <?= $form->field($model, 'kepala')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'abdomen')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'maksilofacial')->textInput(['disabled' => true]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'parineum')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'tulan_leher')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'muskuloskeletal')->textInput(['disabled' => true]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'paru_paru')->textInput(['disabled' => true]) ?>
                    <?= $form->field($model, 'extremitas')->textInput(['disabled' => true]) ?>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row form-row">
                    <label class="control-label has-star col-sm-12">Skrining Nyeri</label>
                    <?=
                        DHtml::trueFalseRadio($model, 'is_nyeri');
                    ?>
                </div>
                <div class="row form-row" id="pilih-wrapper-history">
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
            <div class="col-sm-12">
                <div class="row form-row" id="nyeri-wrapper-history">
                    <div class="col-sm-12" id="dewasa-wrapper-history">
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
                    <div class="col-sm-12" id="anak-wrapper-history">
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
                                <div data-percentage="0"  data-info="tidak-nyeri-anak"class="box-scale-line__point">
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
                    <div class="clearfix"></div>
                    <div class="col-sm-12">
                        <table class="table table-bordered" id="resiko-jatuh__table_history">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="5%">No</th>
                                    <th width="18%">Pengkajian</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Skala Nyeri</td>
                                    <td class="form-column"><?= Html::activeTextInput($modelResiko, 'skala_nyeri', ['class' => 'form-control textbox-resiko-jatuh-form', 'readonly' => true]) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Lokasi Nyeri</td>
                                    <td class="form-column"><?= Html::activeTextInput($modelResiko, 'lokasi_nyeri', ['class' => 'form-control textbox-resiko-jatuh-form', 'disabled' => true]) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>Intensitas Nyeri</td>
                                    <td class="form-column">
                                        <div class="row">
                                            <?=
                                                DHtml::multipleRadio([
                                                    'model' => $modelResiko,
                                                    'fieldName' => 'intesitas_nyeri',
                                                    'class' => 'resiko-jatuh-form',
                                                    'withoutId' => true,
                                                    'data' => $arrayConfig['intesitas_nyeri'],
                                                    'colSize' => 12
                                                ])
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td>Durasi Nyeri</td>
                                    <td class="form-column">
                                        <div class="row">
                                            <?=
                                                DHtml::multipleRadio([
                                                    'model' => $modelResiko,
                                                    'fieldName' => 'durasi_nyeri',
                                                    'class' => 'resiko-jatuh-form',
                                                    'withoutId' => true,
                                                    'data' => $arrayConfig['durasi_nyeri'],
                                                    'colSize' => 12
                                                ])
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td>Frekuensi Nyeri</td>
                                    <td class="form-column">
                                        <div class="row">
                                            <?=
                                                DHtml::multipleRadio([
                                                    'model' => $modelResiko,
                                                    'fieldName' => 'frekuensi_nyeri',
                                                    'class' => 'resiko-jatuh-form',
                                                    'withoutId' => true,
                                                    'data' => $arrayConfig['frekuensi_nyeri'],
                                                    'colSize' => 12
                                                ])
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">6</td>
                                    <td>Karakteristik Nyeri</td>
                                    <td class="form-column">
                                        <div class="row">
                                            <?=
                                                DHtml::multipleRadio([
                                                    'model' => $modelResiko,
                                                    'fieldName' => 'karakteristik_nyeri',
                                                    'class' => 'resiko-jatuh-form',
                                                    'withoutId' => true,
                                                    'data' => $arrayConfig['karakteristik_nyeri'],
                                                    'colSize' => 12
                                                ])
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">7</td>
                                    <td>Lamanya Nyeri</td>
                                    <td class="form-column">
                                        <div class="row">
                                            <?=
                                                DHtml::multipleRadio([
                                                    'model' => $modelResiko,
                                                    'fieldName' => 'lamanya_nyeri',
                                                    'class' => 'resiko-jatuh-form',
                                                    'withoutId' => true,
                                                    'data' => $arrayConfig['lamanya_nyeri'],
                                                    'colSize' => 12
                                                ])
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">8</td>
                                    <td>Faktor yang Meningkatkan Nyeri</td>
                                    <td class="form-column">
                                        <div class="row">
                                            <?=
                                                DHtml::multipleRadio([
                                                    'model' => $modelResiko,
                                                    'fieldName' => 'faktor_nyeri',
                                                    'class' => 'resiko-jatuh-form',
                                                    'withoutId' => true,
                                                    'data' => $arrayConfig['faktor_nyeri'],
                                                    'colSize' => 12
                                                ])
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">9</td>
                                    <td>Rencana tindakan</td>
                                    <td class="form-column">
                                        <div class="row">
                                            <?=
                                                DHtml::multipleRadio([
                                                    'model' => $modelResiko,
                                                    'fieldName' => 'rencana_tindakan',
                                                    'class' => 'resiko-jatuh-form',
                                                    'withoutId' => true,
                                                    'data' => $arrayConfig['rencana_tindakan'],
                                                    'colSize' => 12
                                                ])
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">&nbsp;</div>
            <br>
            <div class="col-sm-12">
                <p class="header-form">Resiko Jatuh</p>
                <div class="row form-row">
                    <label class="control-label col-sm-1">Risiko Jatuh</label>
                    <div class="col-sm-10">
                        <div class="col-sm-4">
                            <?= Html::activeTextInput($model, 'hasil_resiko_jatuh', ['class' => 'form-control', 'name' => 'risiko_jatuh', 'disabled' => true]) ?>
                        </div>
                        <div class="col-sm-4">
                        <?= $form->field($model, 'jenis_resiko')->textInput(['disabled' => true])->label(false) ?>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <hr>
                    </div>
                </div>
                <p class="header-form">Risiko Decubitus</p>
                <div class="row form-row">
                    <?=
                        // DHtml::multipleRadio([
                        //     'model' => $model,
                        //     'fieldName' => 'nilai_decubitus',
                        //     'data' => $arrayConfig['nilai_decubitus'],
                        //     'colSize' => 12
                        // ])
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'nilai_decubitus',
                            'data' => [
                                'ya' => 'Ya',
                                'tidak' => 'Tidak',
                            ]
                        ])
                    ?>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
                <p class="header-form">Luka Bakar</p>
                <div class="row form-row">
                    <?=
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'nilai_luka_bakar',
                            'data' => [
                                'ya' => 'Ya',
                                'tidak' => 'Tidak',
                            ]
                        ])
                    ?>
                </div>
                <div class="row form-row">  
                    <div class="col-sm-3">
                        <?php //$form->field($model, 'persen_luka_bakar', ['addon' => ['append' => ['content' => '%']], 'horizontalCssClasses' => ['wrapper' => 'col-sm-12']])->textInput(['class' => 'doco-number default-disabled', 'id' => 'persen_luka_bakar--form'])->label(false) ?>
                    </div>
                </div>
            
            </div>
        </div>
    </div>
</div>