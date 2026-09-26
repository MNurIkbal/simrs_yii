<?php

use app\components\DocoConstants;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['/index']];
$this->params['breadcrumbs'][] = $title;

?>
<style>
    .dataTables_scrollBody {
        overflow: hidden;
        max-height: unset;
    }

    .dataTables_scroll {
        overflow-x: scroll !important;
        overflow-y: hidden !important;
        max-height: unset;
    }

    .DTFC_LeftBodyLiner table thead tr,
    #table-patient thead tr {
        visibility: hidden;
    }

    .dataTables_scroll .open .dropdown-menu {
        position: relative;
    }

    .tooltip-inner {
        white-space: nowrap;
        max-width: none;
    }

    .select2-selection__rendered {
        height: 35px;
        overflow-y: auto !important;
    }
</style>
<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'id' => 'search'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form', 'id' => 'reset'
                        ]
                    ],
                    'excel' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Unduh Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes'=>[
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url'=> Url::home().'rm/rekap-pasien-per-diagnosa/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
    
                ], '#tab-detail'); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <!-- <div class="col-md-12 filter-form"></div> -->
                </div>
                <div class="advanced-filter">
                </div>
                <div class="col-sm-12">
                    <nav>
                        <div class="nav nav-tabs nav-tab-worklist" id="nav-tab" role="tablist">
                            <a class="nav-item nav-tab-type nav-link active" data-type="rekap" id="nav-all-tab" data-toggle="tab" role="tab" aria-controls="nav-home" aria-selected="true" href="#view_rekap" data-toggle="tab" aria-expanded="true">
                                <?= Yii::t('fe', $_rekapTitle) ?>
                            </a>
                            <a class="nav-item nav-tab-type nav-link" data-type="detail" id="nav-rajal-tab" data-toggle="tab" role="tab" aria-controls="nav-home" aria-selected="true" href="#view_detail" data-toggle="tab" aria-expanded="true">
                                <?= Yii::t('fe', $_detailTitle) ?>
                            </a>
                        </div>
                    </nav>
                    <div class="tab-content">
                        <div class="tab-pane active" id="view_rekap">
                            <div class="col-md-12">
                                <table id="tab-rekap" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                                            <th width="250" class="text-center"><?= \Yii::t("fe", "Kode Diagnosa Utama"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Nama Diagnosa Utama"); ?></th>
                                            <th width="50" class="text-center"><?= \Yii::t("fe", "Jumlah Pasien"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="4"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="view_detail">
                            <div class="col-md-12">
                                <table id="tab-detail" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Nama Pasien"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "No Rekam Medik"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "No Registrasi"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Tanggal Registrasi"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Nama Dokter"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Instalasi"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Ruangan"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Diagnosa Utama"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Diagnosa Sekunder 1"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Diagnosa Sekunder 2"); ?></th>
                                            <th class="text-center"><?= \Yii::t("fe", "Diagnosa Sekunder 3"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="12"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
var tabRekap, tabDetail;
var periode = '';
var dokter = '';
var diagnosa = '';
var nama = '';
var norm = '';
var instalasi = '';
var ruangan = '';
var posNav = 'type=rekap&';

$(function () {
    tabRekap = $('#tab-rekap').docoTabel({
        filter: true,
        displayLength: 10,
        columnDefs: [
            { targets: 1, className: 'text-center'}
        ],
        processing: true,
        serverSide: true,
        // scrollX: true,
        order: [1,'asc'],
        sorting: [[1, 'asc']],
        ajax: baseUrl+'rm/rekap-pasien-per-diagnosa/get-data-rekap',
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },//0
            {title: '".(\Yii::t('fe', "Kode Diagnosa Utama"))."', data: 'diagnosa_utama_kode', searchable: false},//1
            {title: '".(\Yii::t('fe', "Nama Diagnosa Utama"))."', data: 'diagnosa_utama', searchable: false},//2
            {title: '".(\Yii::t('fe', "Jumlah Pasien"))."', data: 'jumlah_pasien', searchable: false, orderable: false},//3
            {title: '".(\Yii::t('fe', "Tanggal Registrasi"))."', data: 'tgl_pendaftaran', orderable: false, visible: false},//4
            {title: '".(\Yii::t('fe', "Nama Dokter"))."', data: 'dokterdpjp_nama', orderable: false, visible: false, searchable: false},//5
            {title: '".(\Yii::t('fe', "Diagnosa utama id"))."', data: 'diagnosa_utama_id', orderable: false, visible: false},//6
            {title: '".(\Yii::t('fe', "Nama Pasien"))."', data: 'nama_pasien', orderable: false, visible: false},//7
            {title: '".(\Yii::t('fe', "No Rekam Medik"))."', data: 'no_rekam_medik', orderable: false, visible: false},//8
            {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_id', orderable: false, visible: false},//9
            {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', orderable: false, visible: false},//10
            {title: '".(\Yii::t('fe', "DPJP"))."', data: 'dokterdpjp_id', orderable: false, visible: false, searchable: true},//11
        ],
    });

    tabDetail = $('#tab-detail').docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        columnDefs: [
            { targets:'_all', className: 'text-center'}
        ],
        serverSide: true,
        // scrollX: true,
        // order: [10,'asc'],
        sorting: [[1, 'asc']],
        ajax: baseUrl+'rm/rekap-pasien-per-diagnosa/get-data-detail',
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },//0
            {title: '".(\Yii::t('fe', "Nama Pasien"))."', data: 'nama_pasien'},//1
            {title: '".(\Yii::t('fe', "No Rekam Medik"))."', data: 'no_rekam_medik'},//2
            {title: '".(\Yii::t('fe', "No Registrasi"))."', data: 'no_pendaftaran', searchable: false},//3
            {title: '".(\Yii::t('fe', "Tanggal Registrasi"))."', data: 'tgl_pendaftaran'},//4
            {title: '".(\Yii::t('fe', "Nama Dokter"))."', data: 'dokterdpjp_nama', searchable: false},//5
            {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_nama', searchable: false},//6
            {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_nama', searchable: false},//7
            {title: '".(\Yii::t('fe', "Diagnosa Utama"))."', data: 'diagnosa_utama', searchable: false},//8
            {title: '".(\Yii::t('fe', "Diagnosa Sekunder 1"))."', data: 'diagnosa_sekunder1', searchable: false},//9
            {title: '".(\Yii::t('fe', "Diagnosa Sekunder 2"))."', data: 'diagnosa_sekunder2', searchable: false},//10
            {title: '".(\Yii::t('fe', "Diagnosa Sekunder 3"))."', data: 'diagnosa_sekunder3', searchable: false},//11
            {title: '".(\Yii::t('fe', "Diagnosa Utama"))."', data: 'diagnosa_utama_id', orderable: false, visible: false},//12
            {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_id', orderable: false, visible: false},//13
            {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', orderable: false, visible: false},//14
            {title: '".(\Yii::t('fe', "DPJP"))."', data: 'dokterdpjp_id', orderable: false, visible: false, searchable: true},//15
        ],
    });

    $('.dataTables_filter').hide();
    generateFillter(tabDetail)
});

function generateFillter(targetTab) {
    $('.filter-form').datatableBootstrapFilter(targetTab, 
    [
        [
            4,
            \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate date' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate date' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
        ],
        [
            15, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('dokter', '',
                    [],
                    [
                        'class' => 'form-control select2',
                        'id' => 'dokter_id',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                    ]
                )
            ))."<div>\"
        ],
        [
            12, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('diagnosadokter_utama', '',
                    [],
                    [
                        'class' => 'form-control select2',
                        'id' => 'diagnosadokter_utama',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                    ]
                )
            ))."<div>\"
        ],
        [
            13, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('instalasi', '',
                    $instalasi,
                    [
                        'id' => 'filter_instalasi', 
                        'class' => 'form-control select2 dep-to-child', 
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                        'data-url' =>  '/rm/info-kunjungan-pasien/get-ruangan',
                        'data-depend_id' => 'filter_ruangan',
                        'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
                        'data-storage' => 'f_ruangan',
                        'data-key' => 'ruangan_id',
                    ]
                )
            ))."<div>\"
        ],
        [
            14, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                Html::dropDownList('f_ruangan', '',
                    $ruangan,
                    [
                        'id' => 'filter_ruangan',
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                    ]
                )
            ))."<div>\"
        ],
        
    ],
    {
        //posisi kolom dan grid
        4:0,
        15:1,
        12:2,
        1:3,
        2:6,
        13:4,
        14:5
    });

    dateRangeHelper('.startDate','.endDate','.targetDate');
}

", View::POS_END, 'b-index');
$this->registerJs($this->render('index.js'), View::POS_END);
?>