<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-13 10:34:52
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use app\components\DocoConstants;
use kartik\widgets\DateTimePicker;
?>
<style>
    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        width: 50px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 50px;
        border: solid 0.2px;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }

    .control-label {
        /*font-weight: bold;*/
    }

    #tb-asesmen-dpjp_wrapper {
        height: 1400px !important;
        overflow-y: scroll;
    }

    .dataTables_scroll {
        height: 600px !important;
        max-height: 600px;
    }

	.range_custom{
		height:20px;
		padding:13px 12px;
	}

	.select2-selection__clear:after {
    	content: '' !important;
	}

  .btnSearch,
  .btnClear{
    display: inline-flex !important;
    align-items: center;
    white-space: nowrap !important;
    gap: 4px; /* optional: jarak icon ke label */
  }

    fieldset.box-bordered {
        border: 1px solid #ccc;
        padding: 5px 5px;
        margin-bottom: 15px;
        border-radius: 5px;
    }

    .btn-order-cppt, .btn-size-custom {
        width: auto !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        white-space: nowrap; /* biar teks tidak pindah baris */
        padding: 4px 4px !important; /* sesuaikan ukuran */
    }
    .col-md-3.no-pad,
    .col-md-9.no-pad {
        padding-left: 1px !important;
        padding-right: 1px !important;
    }
</style>
<div class="row body">
    <div class="col-md-3">
        <div class="panel panel-default" id="panel-form-soap">
            <div class="panel-heading">
                <h5 class="panel-title" id="header-form-soap"><?= Yii::t('fe', 'Tambah SOAP') ?></h5>
            </div>
            <div class="panel-body">
                <div id="div-asesmen-dpjp-soap" class="row">
                    <div class="col-lg-12">
                        <?php $form = ActiveForm::begin([
                            'id' => 'form-soap-index',
                            'type' => ActiveForm::TYPE_VERTICAL,
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'action' => '/igd/pemeriksaan-igd/create-soap?id=' . $id,
                            'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL]
                        ]) ?>
                        <div class="form-group field-cpptform-tgl_cppt" id="cpptDate-form-wrapper">
                            <label class="control-label" for="cpptform-tgl_cppt">Tanggal CPPT</label>
                            <?= DateTimePicker::widget([
                                'model' => $model,
                                'attribute' => 'tgl_cppt',
                                'options' => ['readonly' => true],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd/mm/yyyy hh:ii:ss',
                                    'endDate' => date('d/m/Y H:i:s', strtotime("+5 minute", strtotime("now"))),
                                ]
                            ]); ?>
                        </div>
                        <?= $form->field($model, 'subject')->textarea([
                            'class' => 'form-control input-sm',
                            'rows' => '7',
                        ]) ?>

                        <?= $form->field($model, 'object')->textarea([
                            'class' => 'form-control input-sm',
                            'rows' => '7',
                        ]) ?>

                        <?php
                            $labelCheckbox = 'ICD X';
                            if ($isPerawat) $labelCheckbox = 'SDKI';
                            echo $form->field($model, 'is_icd_x')->checkbox([
                                'label' => $labelCheckbox,
                            ]);
                        ?>

                        <?= $form->field($model, 'a_diag_utama_text')->textarea([
                        'class' => 'form-control input-sm diag_utama_text',
                        'rows' => '7',
                        ])->label('Diagnosa Utama<span style="color:red; "> *</span>',['class'=>'label-class diag_utama_text_label']) ?>

                        <?php
                        echo $form->field($model, 'a_diag_utama')->widget(Select2::classname(), [
                            'initValueText' => isset($text_diagnosa) && $text_diagnosa != '' ? $text_diagnosa : null,
                            'data' => $optDiagUtama,
                            'options' => [
                                'placeholder' => '-- Pilih --',
                                'class' => 'form-control input-sm select2'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => true,
                                'tokenSeparators' => [',', '_'],
                                'minimumInputLength' => 3,
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
                                                    page: params.page || 1,
                                                    type: "diagnosa_utama",
                                                    all_text: 0,
                                                    id_with_text: 1,
                                                    page:params.page || 1,
                                                    is_perawat: ' . ($isPerawat ? '1' : '0') . '
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
                                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                            ],
                        ]);
                        ?>

                        <?= $form->field($model, 'a_diag_penyerta')->widget(Select2::classname(), [
                            'showToggleAll' => false,
                            'data' => $optDiagPnyrt,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => '-- Pilih --',
                                'class' => 'form-control input-sm select2'
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
                                                page: params.page || 1,
                                                type: "diagnosa_penyerta",
                                                all_text: 0,
                                                id_with_text: 1,
                                                page:params.page || 1,
                                                is_perawat: ' . ($isPerawat ? '1' : '0') . '
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
                                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                            ],
                        ]);
                        ?>


                        <?= $form->field($model, 'planning')->textarea([
                            'class' => 'form-control input-sm',
                            'rows' => '7',
                        ]) ?>

                        <?php if ($config_soap): ?>
                            <?php if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS) : ?>
                                <!-- Catatan dokter -->
                                <?= $form->field($model, 'catatan_dokter')->textarea([
                                    'class' => 'form-control input-sm',
                                    'rows' => '7'
                                ])->label(Yii::t('fe', 'Catatan')) ?>
                            <?php endif ?>

                            <?= $form->field($model, 'instruksi')->textarea([
                                'class' => 'form-control input-sm',
                                'rows' => '7'
                            ])->label(Yii::t('fe', 'Instruksi')) ?>

                        <?php else: ?>
                            <div class="panel panel-default">
                                <a id="info-heading" data-toggle="collapse" href="#catatan-instruksi" role="button" aria-expanded="false" aria-controls="catatan-instruksi">
                                    <div class="panel-heading flex-container ">
                                        <h6 class="panel-title text-bold">Catatan dan Instruksi</h6>
                                        <ul class="icons-list">
                                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                        </ul>
                                    </div>
                                </a>

                                <div class="panel-body collapse multi-collapse" id="catatan-instruksi">
                                    <?= $form->field($model, 'instruksi')->textarea([
                                        'class' => 'form-control input-sm',
                                        'rows' => '7'
                                    ])->label(Yii::t('fe', 'Instruksi')) ?>

                                    <!-- Cek pegawai -->
                                    <?php if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS) : ?>
                                        <!-- Catatan dokter -->
                                        <?= $form->field($model, 'catatan_dokter')->textarea([
                                            'class' => 'form-control input-sm',
                                            'rows' => '7'
                                        ])->label(Yii::t('fe', 'Catatan')) ?>
                                    <?php endif ?>

                                </div>
                            </div>
                        <?php endif ?>






                        <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
                        <?= Html::activeHiddenInput($model, 'pegawai_id') ?>
                        <?= Html::activeHiddenInput($model, 'pasien_id') ?>
                        <?= Html::activeHiddenInput($model, 'ruangan_id') ?>
                        <?= Html::activeHiddenInput($model, 'cppt_id') ?>
                        <!--                         <div class="col-sm-12">
                            <button type="button" id="btn-add-terapi" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-plus"></i></b>Terapi</button>
                            <button type="button" id="btn-verbal-order" class="btn btn-info btn-labeled btn-xs btn-custom-save" onClick="showVerbalOrder()"><b><i class="fa fa-plus"></i></b>Verbal Order</button>
                        </div> -->
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="button" id="btn-save-soap-index" style="width:100%;" class="btn btn-info btn-labeled btn-xs btn-custom-save">
                                    <b><i class="fa fa-floppy-o"></i></b>Simpan
                                </button>
                            </div>
                        </div>
                        <!-- <div class="col-sm-12"><hr></div> -->
                        <?php ActiveForm::end() ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default" id="panel-cppt-igd">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'CPPT IGD') ?></h5>
            </div>
            <div class="panel-body">
                <div id="div-asesmen-dpjp">
                    <div class="panel panel-white">
                        <div class="panel-heading clearfix">
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <div class="col-md-3 no-pad">
                                        <fieldset class="box-bordered">
                                            <div class="btn-group" role="group">
                                                <button type="button" id="btn-cetak-cppt"
                                                    class="btn btn-info btn-labeled btn-xs data-print btn-toolbar btn-size-custom"
                                                    data-options="excel-serconn" data-target="#modal_backdrop" data-table="#tb-asesmen-dpjp">
                                                    Cetak
                                                </button>

                                                <button 
                                                    type="button"
                                                    id="btn-laporan-terapi"
                                                    class="btn btn-info btn-labeled btn-xs btn-toolbar btn-size-custom"
                                                    action="/igd/end-point/list-data-tindakan?id=<?= $id ?>&pasien_id=<?= $pasien_encrypt_id ?>&type=igdLaporanTerapi"
                                                    data-width="90%"
                                                    data-toggle="modal"
                                                    data-target="#modal_backdrop">
                                                    Laporan Terapi
                                                </button>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-9 no-pad">
                                        <fieldset class="box-bordered">
                                           <div class='btnClear'>
                                                <div id='button-laboratorium'></div>
                                            </div>
                                            <button type="button" id="btn-hasil-laboratorium"
                                            class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar btnClear btn-size-custom"
                                            data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/igd/pemeriksaan-igd/history-patient?id=<?=$id?>&norm=<?=$data_pasien['no_rekam_medik']?>&instalasi=<?=$data_pasien['instalasi_id']?>&is_jenis=lab&is_penunjang=true" >
                                            Hasil Laboratorium</button>

                                            <div class='btnClear'>
                                                <div id='button-penjadwalan'></div>
                                                <button type="button" id="btn-riwayat-order-bedah"
                                                class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar btnClear btn-size-custom"
                                                data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/igd/pemeriksaan-igd/history-surgery-orders?id=<?=$id?>&norm=<?=$data_pasien['no_rekam_medik']?>&instalasi=<?=$data_pasien['instalasi_id']?>" >
                                                Riwayat Order Bedah </button>
                                            </div>

                                            <button 
                                                type="button"
                                                id="btn-hasil-fisioterapi"
                                                class="btn btn-info btn-labeled btn-xs btn-toolbar btn-size-custom"
                                                data-width="90%"
                                                data-toggle="modal"
                                                data-target="#modal_backdrop"
                                                action="/igd/pemeriksaan-igd/list-history-fisio?pendaftaran_id=<?= $id ?>&norm=<?= $data_pasien['no_rekam_medik'] ?>">
                                                Hasil Fisioterapi
                                            </button>

                                            <div class='btnClear'>
                                                <div id='button-radiologi'></div>
                                                <button type="button" id="btn-hasil-radiologi"
                                                    class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar btnClear btn-size-custom"
                                                    data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/igd/riwayat-pasien/list-penunjang-radiologi?id=<?=$id?>&norm=<?=$data_pasien['no_rekam_medik']?>" >
                                                    Hasil Radiologi  <span class="badge" style="background: #FF5722; color: #fff; right: -11px; top: -10px; position:absolute;"><?=$total_belum_baca_rad?></span>
                                                </button>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="panel-body">
                            <div class="panel-footer">
                                <div class="col-sm-12" id="button-wrapper">
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group new-filter col-md-4 col-xs-6 1">
                                    <label>Tanggal CPPT</label>
                                    <br>
                                    <div class="input-group" >
                                        <input type="text"  id="rangeDemoStart" class="form-control startDate1 pickadate range_custom" style="background-color:white;" value="" col-index="3" readonly="">
                                        <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                                        <input type="text" id="rangeDemoFinish" readonly="" class="form-control endDate1 pickadate range_custom" style="background-color:white;" value="" col-index="3" disabled="true">
                                        <input type="text" style="display:none" class="targetDate dateTarget1" col-index="3" value="">
                                    </div>
                                </div>

                                <div class="col-sm-2 ">
                                    <?= Html::label('Ruangan', null, ['class' => 'control-label']) ?>
                                    <?= Select2::widget([
                                        'name' => 'ruangan_id',
                                        'id' => 'filter-cppt-ruangan_id',
                                        'options' => [
                                            'placeholder' => 'pilih'
                                        ],
                                        'pluginOptions' => [
                                            'minimumInputLength' => 1,
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => '/igd/pemeriksaan-igd/cppt-filters',
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {term:params.term, pasien_id:"'.$data_pasien['pasien_id'].'", type:"ruangan"}; }'),
                                                'processResults' => new JsExpression('function(result) { return {results:result.data}; }'),
                                                'cache' => true
                                            ],
                                            'templateResult' => new JsExpression('function(data) { return data.text }'),
                                            'templateSelection' => new JsExpression('function(data) { return data.text }'),
                                        ],
                                    ]) ?>
                                </div>

                                <div class="col-sm-3 form-group">
                                    <?= Html::label('Dokter', null, ['class' => 'control-label']) ?>
                                    <?= Select2::widget([
                                        'name' => 'pegawai_id',
                                        'id' => 'filter-cppt-pegawai_id',
                                        'options' => [
                                            'placeholder' => 'pilih'
                                        ],
                                        'pluginOptions' => [
                                            'minimumInputLength' => 1,
                                            'allowClear' => true,
                                            'ajax' => [
                                                'url' => '/igd/pemeriksaan-igd/cppt-filters',
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {term:params.term, pasien_id:"'.$data_pasien['pasien_id'].'", type:"dokter"}; }'),
                                                'processResults' => new JsExpression('function(result) { return {results:result.data}; }'),
                                                'cache' => true
                                            ],
                                            'templateResult' => new JsExpression('function(data) { return data.text }'),
                                            'templateSelection' => new JsExpression('function(data) { return data.text }'),
                                        ]
                                    ]) ?>
                                </div>

                                <div class="col-sm-2 form-group">
                                    <?= Html::label('PPA', null, ['class' => 'control-label']) ?>
                                    <?= Select2::widget([
                                        'name' => 'kelompokpegawai_id',
                                        'id' => 'filter-cppt-kelompokpegawai_id',
                                        'options' => [
                                            'placeholder' => 'pilih'
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                        ],
                                        'data' => [
                                            \app\components\DocoConstants::KELOMPOK_MEDIS => 'Dokter',
                                            \app\components\DocoConstants::KELOMPOK_KEPERAWATAN => 'Perawat',
                                        ]
                                    ]) ?>
                                </div>
                                <div class="col-sm-1">
									<br/>
									<button type="button" id="btn-reset-filter-cppt"
										class="btn btn-xs btn-only btn-primary-color btn-reset-filter-cppt"
										data-toggle="tooltip" title data-original-title="Reset Filter">
										<i class="fa fa-undo"></i>
									</button>
								</div>

                            </div>
                            <br>
                            <table class="table table-bordered datatable-basic dataTable" id="tb-asesmen-dpjp" style="width:100%; height:50px">
                                <thead>

                                    <tr class="bg-inverse">
                                        <th><?= Yii::t('fe', 'Tanggal/Jam') ?></th>
                                        <th><?= Yii::t('fe', 'Profesional Pemberi Asuhan') ?></th>
                                        <th><?= Yii::t('fe', 'Hasil Asesmen Penatalaksanaan Pasien') ?></th>
                                        <th><?= Yii::t('fe', 'Instruksi') ?></th>
                                        <!--<th><?= Yii::t('fe', 'Instruksi PPA Termasuk Pasca Bedah') ?></th>-->
                                        <th><?= Yii::t('fe', 'Aksi') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row body">
    <div id="div-verbal-order" hidden>
    </div>

    <div id="div-terapi" hidden>
    </div>
</div>

<?php
$this->registerJs('
    var pendaftaran_id = "'.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'";
    var pasien_id = "'.DocoHelpers::encrypt($data_pasien['pasien_id']).'";
    var pasien_id_now = "'.$model->pasien_id.'";
    var ruangan_now = "'.$model->ruangan_id.'";
    var last_cppt = "'.$lastCppt.'";
    var time_reset = "'.$time_reset.'";
    var pegawai_id = "'.$pegawai_id.'";
    var id_ruangan = "'.$id_ruangan.'";
    var cppt_id = "";
    var pelayananConfigButton = ' . json_encode($pelayananConfigButton) . '
', View::POS_END);
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
