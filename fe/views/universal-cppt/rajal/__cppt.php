<?php
/*
@author: Ardi Pratama
*/

// Using

use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DatePicker;
?>
<style>
    #tb-cppt_wrapper {
        height: <?= $isPerawat ? '1150px' : '1400px'?> !important;
        overflow-y: scroll;
    }

    .dataTables_scroll {
        height: 100% !important;
        max-height: 100%;
    }

    #tb-cppt_wrapper tr th,
    #tb-cppt_wrapper tr td {
        padding: 8px !important;
    }

    .select2-selection__clear:after {
        content: '' !important;
    }

    .range_custom{
		height:20px;
		padding:13px 12px;
	}
    .select2-selection__clear{
        position: absolute;
        top: 0;
        left: -10px;
        height: 100%;
        padding: 0 12px;
    }

    #tb-cppt_wrapper #menu-action-cppt th div.flex-menu-cppt{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        font-size: 12px;
        padding: 5px 10px;
    }

    #tb-cppt_wrapper #menu-action-cppt .link-action-cppt-table:first-child{
        padding-right: 10px;
    }

    #tb-cppt_wrapper #menu-action-cppt .link-action-cppt-table{
        padding-right: 10px;
        padding-left: 10px;
        margin: 0px;
        color: #34bfa3;
    }

    #tb-cppt_wrapper #menu-action-cppt .link-action-cppt-table:last-child{
        padding-left: 10px;
    }

    #tb-cppt_wrapper .link-action-cppt-table:hover{
        color: #1ca189;
    }

    #tb-cppt_wrapper .link-action-cppt-table:disabled{
        color: #1ca189;
    }

   
    #btnSearch,
    #btnClear{
        display: inline-block;
        vertical-align: top;
    }
    #btnSearch,
    #btnClear{
        display: inline-block;
        vertical-align: top;
    }

    #modal_backdrop
    {
        z-index: 1040 !important;
    }

</style>
<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title" id="header-form-soap"><?= Yii::t('fe', 'Tambah SOAP') ?></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?php $form = ActiveForm::begin([
                            'id' => 'form-soap-cppt',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'action' => '/rajal/pemeriksaan/save-soap?pendaftaran_id=' . $encrytedPendaftaranId .'&ruangan_asal_id=' . $ruangancppt_id,
                            'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL]
                        ]) ?>
                        <?= Html::activeHiddenInput($model, 'soaprj_id') ?>
                        <div class="form-group field-cpptform-tgl_cppt" id="cpptDate-form-wrapper">
                            <label class="control-label" for="cpptform-tgl_cppt">Tanggal</label>
                            <?= DateTimePicker::widget([
                                    'model' => $model,
                                    'attribute' => 'tgl_soaprj',
                                    'options' => ['readonly'=>true],
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd/mm/yyyy hh:ii:ss',
                                        'startDate' => date('d/m/Y H:i:s', strtotime($tgl_pendaftaran)),
                                        'endDate' => date('d/m/Y H:i:s', strtotime("+5 minute", strtotime("now"))),
                                    ]
                            ]); ?>
                        </div>
                        <!-- Subject -->
                        <?= $form->field($model, 'subject')->textarea([
                            'class' => 'form-control input-sm soaprj-form',
                            'rows' => '7',
                        ])->label('Subjektif') ?>

                        <!-- Object -->
                        <?= $form->field($model, 'object')->textarea([
                            'class' => 'form-control input-sm soaprj-form',
                            'rows' => '7',
                        ])->label('Objektif') ?>

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

                        <!-- Asesmen -->
                        <div class="form-group">
                            <div class="">
                                <!-- Diagnosa utama -->
                                <?php echo $form->field($model, 'a_diag_utama')->widget(Select2::classname(), [
                                    'options' => [
                                        'placeholder' => '-- Pilih --',
                                        'class' => 'form-control input-sm select2 soaprj-form'
                                    ],
                                    'pluginOptions' => [
                                        'tags' => true,
                                        'tokenSeparators' => [',', '_'],
                                        'minimumInputLength' => 3,
                                        'allowClear' => true,
                                        'language' => [
                                            'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                        ],
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['/rajal/end-point/get-new-diagnosa']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('
                                                function(params) {
                                                    return {
                                                        q: params.term,
                                                        page: params.page || 1,
                                                        type: "diagnosa_utama",
                                                        all_text: 0,
                                                        id_with_text: 1
                                                    };
                                                }
                                            ')
                                        ],
                                        'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                                        'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                                        'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                                    ],
                                ]) ?>

                                <!-- Diagnosa penyerta -->
                                <?= $form->field($model, 'a_diag_penyerta')->widget(Select2::classname(), [
                                    'showToggleAll' => false,
                                    'class' => 'soaprj-form',
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
                                            'url' => \yii\helpers\Url::to(['/rajal/end-point/get-new-diagnosa']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('
                                                function(params) {
                                                    return {
                                                        q: params.term,
                                                        page: params.page || 1,
                                                        type: "diagnosa_penyerta",
                                                        all_text: 0,
                                                        id_with_text: 1
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

                        <!-- Planning -->
                        <?= $form->field($model, 'planning')->textarea([
                            'class' => 'form-control input-sm soaprj-form',
                            'rows' => '7',
                        ])->label('Planning') ?>

                        <?php if ($config_soap): ?>
                            <!-- Cek pegawai -->
                            <?php if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS) : ?>
                                <!-- Catatan dokter -->
                                <?= $form->field($model, 'catatan_dokter')->textarea([
                                    'class' => 'form-control input-sm soaprj-form',
                                    'rows' => '7'
                                ]) ?>
                            <?php endif ?>

                            <?= $form->field($model, 'instruksi')->textarea([
                                'class' => 'form-control input-sm soaprj-form',
                                'rows' => '7',
                            ])->label('Instruksi') ?>
                            
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
                                    <!-- Cek pegawai -->

                                    <?= $form->field($model, 'instruksi')->textarea([
                                        'class' => 'form-control input-sm soaprj-form',
                                        'rows' => '7',
                                    ])->label('Instruksi') ?>
                                    
                                    <?php if (Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS) : ?>
                                        <!-- Catatan dokter -->
                                        <?= $form->field($model, 'catatan_dokter')->textarea([
                                            'class' => 'form-control input-sm soaprj-form',
                                            'rows' => '7'
                                        ]) ?>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endif ?>

                        <!-- Hidden inputs -->
                        <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['value' => $encrytedPendaftaranId]) ?>
                        <?php ActiveForm::end() ?>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" id="btn-save-soap" style="width: 100%;" class="btn btn-info btn-labeled btn-xs btn-custom-save"><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="panel-footer">
                <div class="col-sm-12" id="button-wrapper">
                    <button type='button' class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-type='reseptur'><b><i class='fa fa-medkit'></i></b> Resep</button>
                    <button type='button' class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-type='penunjang'><b><i class='fa fa-stethoscope'></i></b> Penunjang</button>
                    <button type='button' class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-type='tindakan'><b><i class='fa fa-stethoscope'></i></b> Tindakan & BMHP</button>
                </div>
            </div> -->
        </div>
    </div>
    <div class="col-sm-9">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'CPPT') ?></h5>
            </div>
            <div class="panel-heading clearfix">
               
                    <div class="row">
                        <div class="col-sm-12 ">
                            <?=DocoHelpers::generateToolbar([
                                'print' => [
                                    'type' => 'button',
                                    'title' => 'Cetak',
                                    'icon' => 'fa fa-print',
                                    'method' => 'not-exist',
                                    'attributes' => [
                                        'id'=>'btn-cetak-cppt',
                                        'data-options' => 'excel-serconn',
                                        'data-target' => '#modal_backdrop',
                                        'data-width' => '50%'
                                    ]
                                ],
                            ], '#tb-cppt');?>
                            <div id='btnClear'>
                                <div id='button-laboratorium'></div>                    
                            </div>
                            <button type="button" id="btn-hasil-laboratorium btnClear" 
                            class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
                            data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/rajal/pemeriksaan/history-patient?norm=<?=$patientData['no_rekam_medik']?>&instalasi=<?=$instalasi_id?>&is_jenis=lab&is_penunjang=true" >
                            Hasil Laboratorium</button> 
                            
                            
                            <div id='btnClear'>
                                <div id='button-radiologi'></div>                    
                            </div>
                            <button type="button" id="btn-hasil-radiologi btnClear" 
                            class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
                            data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/igd/riwayat-pasien/list-penunjang-radiologi?id=<?=$encrytedPendaftaranId?>&norm=<?=$patientData['no_rekam_medik']?>" >
                            Hasil Radiologi<span class="badge" style="background: #FF5722; color: #fff; right: -11px; top: -10px; position:absolute;"><?=$total_belum_baca_rad?></span></button>                        
                            
                            <!-- <div id='btnClear'>
                                <div id='button-laporan-terapi'></div>                    
                            </div> -->
                            <button type="button" id="btn-riwayat-order-bedah btnClear" 
                            class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
                            data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/igd/riwayat-pasien/history-surgery-orders?id=<?=$encrytedPendaftaranId?>&norm=<?=$patientData['no_rekam_medik']?>&instalasi=<?=$instalasi_id?>" >
                            Riwayat Order Bedah</button>  

                            <!-- <div id='btnClear'> -->
                            <button type='button'  id='btnClear' style='margin-right: -7px' class='btn btn-xs btn-only btn-primary-color btn-order-cppt' data-width='90%' ><b><i class='fa fa-list-ul'></i></b></button>
                            <!-- </div> -->
                            <button type="button" id="btn-hasil-radiologi btnClear" 
                            class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
                            data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/rajal/pemeriksaan/list-data-tindakan?id=<?=$encrytedPendaftaranId?>&type=rajalLaporanTerapi" >
                            Laporan Terapi</button>  

                            <!-- <div id='btnClear'>
                                <div id='button-fisioterapi'></div>                    
                            </div> -->
                            <button type="button" id="btn-hasil-fisioterapi btnClear" 
                            class="btn btn-xs btn-only btn-info btn-labeled btn-xs data-print btn-toolbar" 
                            data-width="90%" data-toggle="modal" data-target='#modal_backdrop' action="/rajal/pemeriksaan/list-history-fisio?pendaftaran_id=<?=$encrytedPendaftaranId?>&norm=<?=$patientData['no_rekam_medik']?>" >
                            Hasil Fisioterapi</button>                        
                        </div>
                    </div>
               
            </div>
            <div class="panel-body">
                
                <div class="row">
               
                    <div class="table-wrapper table-scroll-x">
                        <div class="legend-index">
                            <div class="col-md-10">
                                <div class="panel-footer">
                                    <div class="col-sm-12" id="button-wrapper">
                                        <button type='button' class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-type='reseptur'><b><i class='fa fa-medkit'></i></b> Resep</button>
                                        <button type='button' class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-type='penunjang'><b><i class='fa fa-stethoscope'></i></b> Penunjang</button>
                                        <button type='button' class='btn btn-labeled btn-info btn-xs btn-order-cppt' data-type='tindakan'><b><i class='fa fa-stethoscope'></i></b> Tindakan & BMHP</button>
                                 </div>
                            </div>
                                </div>
                                <div class="col-md-2">
                                <div class="legend-header">Keterangan</div>
                                <div class="legend-wrapper">
                                    <div class="legend-information">
                                        <div class="legend-information__color" style="background-color: #1FA345"></div>
                                        <div class="legend-information__text">SOAP Dokter</div>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">


                    <div class="form-group new-filter col-md-3 col-xs-6 1">
                            <label>Tanggal CPPT</label>
                            <br>
                            <div class="input-group" >
                                <input type="text"  id="rangeDemoStart" class="form-control startDate1 pickadate range_custom" value="" col-index="3" readonly="">
                                <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                                <input type="text" id="rangeDemoFinish" readonly="" class="form-control endDate1 pickadate range_custom" value="" col-index="3" disabled="true">
                                <input type="text" style="display:none" class="targetDate dateTarget1" col-index="3" value="">
                            </div>
                    </div>

                    <div class="col-sm-2">
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
                                    'url' => '/rajal/pemeriksaan/cppt-filters',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {term:params.term, pendaftaran_id:"'.$encrytedPendaftaranId.'", type:"ruangan"}; }'),
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
                                    'url' => '/rajal/pemeriksaan/cppt-filters',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {term:params.term, pendaftaran_id:"'.$encrytedPendaftaranId.'", type:"dokter"}; }'),
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

                    <div class="col-sm-2 ">
						<br/>
						<button type="button" id="btn-reset-filter-cppt"
							class="btn btn-xs btn-only btn-primary-color btn-reset-filter-cppt"
							data-toggle="tooltip" title data-original-title="Reset Filter">
							<i class="fa fa-undo"></i>
						</button>
					</div>
                </div>
                <hr/>
                <table class="table table-bordered datatable-basic dataTable" id="tb-cppt" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="5" class="text-center"><?= Yii::t('fe', 'SOAP / Verbal Order') ?></th>
                        </tr>
                        <tr class="bg-inverse">
                            <th width="10">No</th>
                            <th width="20%"><?= Yii::t('fe', 'Ruangan') ?></th>
                            <th width="50%"><?= Yii::t('fe', 'Hasil Asesmen Penatalaksanaan Pasien') ?></th>
                            <th width="30%"><?= Yii::t('fe', 'Instruksi') ?></th>
                            <th width="10%">Aksi</th>
                        </tr>
                        <tr id="menu-action-cppt">
                            <th colspan="5">
                                <div class="flex-menu-cppt">
                                    <div class="list-action-button-cppt-table">
                                        <a href="#" class="link-action-cppt-table" data-event="show">Lihat CPPT Sebelumnya</a>
                                        <a href="#" class="link-action-cppt-table" data-event="hide" style="visibility: hidden;">Tutup CPPT yang sudah dibuka</a>
                                        <input type="hidden" id="filter-cppt-limit" value="5">
                                    </div>
                                    <div class="">
                                        Total <span id="total-cppt-data">#</span> CPPT
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id="div-verbal-order" hidden>
        </div>
    </div>
</div>

<!-- modal -->
<div id="modal-lab" class="modal fade" style="z-index: 1041 !important; overflow-y:auto !important" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="modal-form" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        </div>
    </div>
</div>
<div id="modal-order-pemeriksaan" style="z-index: 2041 !important; overflow-y:auto !important" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>
<div id="modal-jadwal-dokter" style="z-index: 2041 !important; overflow-y:auto !important" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>


<div id="modal-gambar-radiologi" class="modal">
    <div class="modal-dialog modal-xl" style="height: 100%;width: 99%;margin: 2px;">
        <div class="modal-content" style=" height: 100%;">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Gambar Radiologi</h5>
            </div>
            <div class="modal-body" style="height: 100%;">
                <iframe  style="width: 100%; height: 95%;" src=""></iframe>
            </div>
        </div>
    </div>
</div>


<div id="modal-show-konsul" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn-konsul" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Konsul Poli</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper-konsul">
                <!-- <div class="overlay-preview"></div> -->
                <iframe frameborder="0" id="preview-content-konsul" style="width:100%;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>

<div id="modal-reseptur" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="modal-batal-instruksi" tabindex="-1" role="dialog" style="z-index: 1050 !important">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- <div id="modal-preview-img" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div> -->

<div id="modal-informasi-pasien" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>
<div id="modal-template-resep" class="modal fade" style="z-index: 1050 !important;">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
<?php
$this->registerJs(
    '
    // Datatable language
    var emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    var info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    var infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    var infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    var lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    var loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    var processing = "'.(\Yii::t("fe", "Memproses...")).'";
    var search = "'.(\Yii::t("fe", "Cari:")).'";
    var zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";

    var ruangancpptId = "'.$ruangancppt_id.'";

    var _universalCpptUrl = "'.$_universalCpptUrl.'"
    var _no_masukpenunjang = "'.$_no_masukpenunjang.'"
    var encrytedPendaftaranId = "'.$encrytedPendaftaranId.'"
    var pasienpulang_id = "'.$pasienpulang_id.'";
    var pasien_id = "'. $pasien_id .'"
    var soapDiagnosa = ' . json_encode($soapDiagnosa) . '
    var soapDate = "' . $model->tgl_soaprj . '"
    var id_pegawai = "' . $id_pegawai . '"
    var time_reset = "' . $time_reset . '"
    var instalasiId = "'.Yii::$app->docoVars->workspace('instalasi_id').'"
    var id_ruangan = "' . $id_ruangan . '"
    var pelayananConfigButton = ' . json_encode($pelayananConfigButton),
    View::POS_END
);
$this->registerJs($this->render('__cppt.js'), View::POS_END);
?>
