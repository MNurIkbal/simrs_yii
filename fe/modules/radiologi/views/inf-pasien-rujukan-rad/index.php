<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoConstants;



// Some variables
$this->title = Yii::t('fe', 'Informasi Pasien Rujukan Radiologi');
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
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

    .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
    .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;

    }
    .legend-index {
        margin: 8px 0px;
    }
    .legend-information__color {
        min-width: 16px;
        min-height: 16px;
        height: 16px;
        width: 16px;
        border-radius: 16px;
        margin-right: 8px;
        border: 1px solid #dddddd;
    }
    
    .legend-wrapper {
            display: flex;
            flex-flow: wrap;
    }
    .legend-information {
        padding: 8px;
        margin-right: 8px;
        margin-top: 8px;
        border: 1px solid #dddddd;
        border-radius: 4px;
        display: flex;
    }
    .filter-selected {
        border-color : #2ca38b;
    }

</style>

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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
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
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'approve' => [
                        'title' => \Yii::t('fe', 'Approve'),
                        'icon' => 'fa fa-check-square-o',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'link',
                            'id' => 'btn-approve',
                            // 'data-content' => 'content-perda',
                            'data-target' => '/radiologi/inf-pasien-rujukan-rad/form-aproval?id=',
                        ]
                    ],
                    'rujuk' => [
                        'title' => \Yii::t('fe', 'Dirujuk'),
                        'icon' => 'fa fa-arrow-right',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'link',
                            'id' => 'btn-rujuk',
                            'disabled' => true,
                            'data-target' => '/radiologi/inf-pasien-rujukan-rad/rujuk?id=',
                        ]
                    ],
                    'batal' => [
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-times-circle-o',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'link',
                            'id' => 'btn-batal',
                            // 'data-content' => 'content-perda',
                            'data-target' => '/radiologi/inf-pasien-rujukan-rad/form-batal?id=',
                            'data-pesan-error'=>'',
                        ]
                    ],
                    'edit-tanggal' => [
                        'title' => \Yii::t('fe', 'Edit Tanggal Rujukan'),
                        'icon' => 'fa fa-pencil-square-o',
                        'attributes' => [
                            'class' => 'spa',
                            'id' => 'btn-edit-tanggal',
                            // 'data-content' => 'content-perda',
                            'data-width' => '75%',
                            'data-pesan-error' =>'',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/radiologi/inf-pasien-rujukan-rad/edit-tanggal-rujukan?id=',
                        ],
                    ],
                    'cetak-rujukan' => [
                        'title' => \Yii::t('fe', 'Cetak Rujukan'),
                        'icon' => 'fa fa-print',
                        'method' => '#',
                        'attributes' => [
                        'id'=>'cetak-rujukan',
                        'disabled' => true,
                        'data-options' => 'link',
                            'target'=>'_blank',
                        ]
                    ],
                ], '#tb-inf-pasien-rujukan-rad') ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="legend-index">
                            <div class="col-md-6">
                                <div class="legend-header">Keterangan</div>
                                <div class="legend-wrapper">
                                    <div class="legend-information" data-type="1" id="disetujui">
                                        <div class="legend-information__color" style="background-color: #2bcc6e"></div>
                                        <div class="legend-information__text">Sudah Disetujui</div>
                                    </div>
                                    <div class="legend-information" data-type="2" id="belum">
                                        <div class="legend-information__color" style="background-color: #FFFfff"></div>
                                        <div class="legend-information__text" >Belum Disetujui</div>
                                    </div>
                                    <div class="legend-information" data-type="3" id="batal">
                                        <div class="legend-information__color" style="background-color: #d64541"></div>
                                        <div class="legend-information__text" >Batal</div>
                                    </div>
                                    <div class="legend-information" data-type="4" id="cito">
                                        <div class="legend-information__color" style="background-color: #ff8900"></div>
                                        <div class="legend-information__text" >Cito</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <table id="tb-inf-pasien-rujukan-rad" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "No Rekam Medis") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Rujukan") ?></th>
                            <th></th>
                            <th><?= Yii::t("fe", "Pasien") ?></th>
                            <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                            <th><?= Yii::t("fe", "Dokter Perujuk") ?></th>
                            <th><?= Yii::t("fe", "Nomor Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Asal Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Cara Bayar") ?></th>
                            <th><?= Yii::t("fe", "Status Pembayaran") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Lahir") ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // save into localStorage
    localStorage.clear();
    // Global vars

    const STATUS_BELUM_DISETUJUI = "'.DocoConstants::BELUM_SETUJU.'";
    const STATUS_DISETUJUI = "'.DocoConstants::DISETUJUI.'";
    const STATUS_BATAL_APPROVE = "'.DocoConstants::BATAL_APPROVE.'";
    const STATUS_BELUM_PERIKSA = "'.DocoConstants::BLM_PERIKSA.'";
    const INSTALASI_RJ = "'.DocoConstants::INSTALASI_ID_RJ.'";
    const INSTALASI_RADIOLOGI = "'.DocoConstants::INSTALASI_ID_RAD.'";
    const CARA_BAYAR_UMUM = "'.DocoConstants::CARA_BAYAR_UMUM.'";
    const PENJAMIN_UMUM = "'.DocoConstants::VAR_P_Perseorangan.'";

    const no = "'.(\Yii::t("fe", "No")).'";
    const tgl_rujukan = "'.(\Yii::t("fe", "Tanggal Rujukan")).'";
    const no_rujukan = "'.(\Yii::t("fe", "Nomor Rujukan")).'";
    const no_rekam_medik = "'.(\Yii::t("fe", "No Rekam medis")). '";
    const nama_pasien = "'.(\Yii::t("fe", "Pasien")). '";
    const tanggal_lahir = "'.(\Yii::t("fe", "Tanggal Lahir")). '";
    const ruangan_nama = "'.(\Yii::t("fe", "Asal Rujukan")). '";
    const dokter_perujuk = "'.(\Yii::t("fe", "Dokter Perujuk")). '";
    const carabayar_nama = "'.(\Yii::t("fe", "Cara bayar / Penjamin")). '";
    const penjamin_nama = "'.(\Yii::t("fe", "Penjamin")). '";
    const status_penunjang = "'.(\Yii::t("fe", "Status")). '";
    const pemeriksaan = "'.(\Yii::t("fe", "Nama Pemeriksaan")). '";
    const updateUrl = "/radiologi/inf-pasien-rujukan-rad/update?id=";
    const approveUrl = "/radiologi/inf-pasien-rujukan-rad/form-aproval?id=";
    const batalUrl = "/radiologi/inf-pasien-rujukan-rad/form-batal?id=";
    const rujukUrl = "/radiologi/inf-pasien-rujukan-rad/rujuk?id=";
    const cetakRujukUrl = "/radiologi/inf-pasien-rujukan-rad/cetak-rujukan?instalasi_id='.$instalasiId.'&id=";
    
    // Datatable language
    const emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    const info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    const infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    const infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    const lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    const loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    const processing = "'.(\Yii::t("fe", "Memproses...")).'";
    const search = "'.(\Yii::t("fe", "Cari:")).'";
    const zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    const first = "'.(\Yii::t("fe", "Pertama")).'";
    const last = "'.(\Yii::t("fe", "Terakhir")).'";
    const next = "'.(\Yii::t("fe", "Selanjutnya")).'";
    const previous = "'.(\Yii::t("fe", "Sebelumnya")).'";
    const sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    const sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")). '";

    

    // Custom dropdown
    
    var filterTanggalRujukan = \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'. date('d-M-Y', strtotime('-1 months')) .'"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y', strtotime('+1 months')) .'"/><input type="text" style="display:none" class="targetDate"></div>\';

    var filterTanggalLahir = \'<div class="input-group"><input type="text" id="rangeLahirStart" class="form-control rangeLahirStart"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeLahirFinish" class="form-control rangeLahirFinish"/><input type="text" style="display:none" class="targetDateLahir"></div>\';

    var dropdownRujukan = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pasienkirimkeunitlain_id', '', $arrAsalRujukan, ['id' => 'filter_kelompok', 'class' => 'form-control select2 dep-to-child', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/master/ruangan/generate-api', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'pasienkirimkeunitlain_id', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';

     var dropdownCaraBayar =  \'' . (preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'carabayar_nama',
                                '',
                                ArrayHelper::map($cara_bayar, 'carabayar_nama', 'carabayar_nama'),
                                [
                                    'id' => 'filter_carabayar',
                                    'class' => 'form-control select2 dep-to-child',
                                    'prompt' => \Yii::t('fe', '--Cara bayar--'),
                                    'data-url' => Url::home()  . 'kasir/end-point/get-penjamin',
                                    'data-depend_id' => 'filter_penjamin',
                                    'data-depend_prompt' => \Yii::t('fe', '--Penjamin--'),
                                    'data-storage' => 'penjamin',
                                    'data-key' => 'penjamin_nama',
                                ]
                            )
                        )) . '\';

     var dropdownPenjamin =  \'' . (preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'penjamin_nama',
                                '',
                                ArrayHelper::map($penjamin, 'penjamin_nama', 'penjamin_nama'),
                                [
                                    'id' => 'filter_penjamin',
                                    'class' => 'form-control select2 dep-to-parent',
                                    'prompt' => \Yii::t('fe', '--Penjamin--'),
                                    'data-url' => Url::home() . 'kasir/end-point/get-carabayar',
                                    'data-depend_id' => 'filter_carabayar',
                                ]
                            )
                        )) . '\';

    var dropdownStatus = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', $status, ['id' => 'filter_kelompok_2', 'class' => 'form-control select2 dep-to-child', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/master/ruangan/generate-api', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'kode', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';
    var dropdownPembayaran = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('', '', 
            [
                'Batal Bayar' => 'Batal Bayar',
                'Belum Bayar' => 'Belum Bayar', 
                'Sudah Bayar' => 'Sudah Bayar', 
            ], ['id' => 'filter_kelompok_3', 'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\';


        var autocompleteDokter = \'<div class="input-group">' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Select2::widget([
        'name' => 'dokter_perujuk',
        'options' => ['placeholder' => \Yii::t('fe', 'Dokter Perujuk'), 'autocomplete' => 'off'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => Url::home() . (Yii::$app->controller->module->id) . '/inf-pasien-rujukan-rad/get-dokter',
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
        ],
    ])
)) . '</div>\'

', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
