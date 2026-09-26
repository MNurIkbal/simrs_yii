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




// Some variables
$this->title = Yii::t('fe', 'Laporan Pasien Rujukan Radiologi');
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
                        'title'=> \Yii::t('fe', 'Muat Ulang'),
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    // 'excel' => [
                    //     'attributes' => [
                    //         'data-target' => '/radiologi/lap-pasien-rujukan-rad/export-excel?'
                    //     ]
                    // ],
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/radiologi/lap-pasien-rujukan-rad/show-popup-excel?',
                        ]
                    ],
                    'pdf-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh PDF',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'pdf-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/radiologi/lap-pasien-rujukan-rad/show-popup-pdf?',
                        ]
                    ],

                    // 'pdf' => [
                    //     'attributes' => [
                    //         'data-target' => '/radiologi/lap-pasien-rujukan-rad/export-pdf?'
                    //     ]
                    // ],
                ], '#tb-lap-pasien-rujukan-rad') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 advanced-filter "></div>
                </div>
                
                <table id="tb-lap-pasien-rujukan-rad" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Tanggal Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Persetujuan") ?></th>
                            <th><?= Yii::t("fe", "No. Pendaftaran") ?></th>
                            <th><?= Yii::t("fe", "No. Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Pasien") ?></th>
                            <th><?= Yii::t("fe", "Pemeriksaan") ?></th>
                            <th><?= Yii::t("fe", "Jenis Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Asal Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Rujukan RS") ?></th>
                            <th><?= Yii::t("fe", "Dokter Perujuk") ?></th>
                            <th><?= Yii::t("fe", "Cara Bayar / Penjamin") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                            <th><?= Yii::t("fe", "No Rekam Medik") ?></th>
                            <th><?= Yii::t("fe", "Nama RS Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                            <th><?= Yii::t("fe", "Jenis Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                            <th><?= Yii::t("fe", "Dokter Perujuk") ?></th>
                            <th><?= Yii::t("fe", "Jenis Rujukan") ?></th>
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
    const no = "'.(\Yii::t("fe", "Nomor")).'";
    const tgl_rujukan = "'.(\Yii::t("fe", "Tanggal Rujukan")).'";
    const tgl_persetujuan = "'.(\Yii::t("fe", "Tanggal Persetujuan")).'";
    const no_rujukan = "'.(\Yii::t("fe", "No Rujukan")).'";
    const no_pendaftaran = "'.(\Yii::t("fe", "No Pendaftaran")).'";
    const no_rekam_medik = "'.(\Yii::t("fe", "No Rekam medis")). '";
    const pasien = "'.(\Yii::t("fe", "Pasien")). '";
    const tanggal_lahir = "'.(\Yii::t("fe", "Tanggal Lahir")). '";
    const asal_rujukan = "'.(\Yii::t("fe", "Asal Rujukan")). '";
    const dokter_perujuk = "'.(\Yii::t("fe", "Dokter Perujuk")). '";
    const jenis_rujukan = "'.(\Yii::t("fe", "Jenis Rujukan")). '";
    const carabayar_nama = "'.(\Yii::t("fe", "Cara bayar")). '";
    const penjamin_nama = "'.(\Yii::t("fe", "Penjamin")). '";
    const status_penunjang = "'.(\Yii::t("fe", "Status")). '";
    const pemeriksaan = "'.(\Yii::t("fe", "Pemeriksaan")). '";
    const updateUrl = "/radiologi/inf-pasien-rujukan-rad/update?id=";
    const approveUrl = "/radiologi/inf-pasien-rujukan-rad/form-aproval?id=";
    const batalUrl = "/radiologi/inf-pasien-rujukan-rad/form-batal?id=";

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
    
    var filterTanggalRujukan = \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'. date('d-M-Y') .'"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y') .'"/><input type="text" style="display:none" class="targetDate"></div>\';

    var filterTanggalPersetujuan = \'<div class="input-group"><input type="text" id="rangePersetujuanStart" class="form-control rangePersetujuanStart"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangePersetujuanFinish" class="form-control rangePersetujuanFinish"/><input type="text" style="display:none" class="targetDatePersetujuan"></div>\';
    var urlAsalRujukan = "'.Url::to(['get-asal-rujukan']).'";
    var urlGetRsRujukan = "'.Url::to(['get-rs-rujukan']).'";
    var dropdownRujukan = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pasienkirimkeunitlain_id', '', $rujukan, ['id' => 'filter_kelompok', 'class' => 'form-control select2 dep-to-child', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/master/ruangan/generate-api', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'pasienkirimkeunitlain_id', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';
    var dropdownAsalRujukan = \'' . (preg_replace(
        "/[\n\t\r]/i",
        '',
        Html::dropDownList(
            'asal_rujukan',
            '',
            [],
            [
                'id' => 'filter_asal_rujukan',
                'class' => 'form-control select2 selectAsalRujukan',
                'prompt' => \Yii::t('fe', 'Pilih'),
            ]
        )
    )) . '\';
    var dropdownJenisRujukan = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_rujukan_id', '', $jenisRujukan, ['id' => 'filter_jenis_rujukan', 'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\';
    var dropdownJenisPemeriksaan = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('daftartindakan_id', '', $jenisPemeriksaanRad, ['id' => 'filter_jenis_pemeriksaan', 'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\';
    var dropdownNamaRsRujukan = \'' . (preg_replace(
        "/[\n\t\r]/i",
        '',
        Html::dropDownList(
            'rs_rujukan',
            '',
            [],
            [
                'id' => 'filter_nama_rs_rujukan',
                'class' => 'form-control select2 selectNamaRsRujukan',
                'prompt' => \Yii::t('fe', 'Pilih'),
            ]
        )
    )) . '\';

    var dropdownStatusPeriksa = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status', '', $statusPeriksa, ['id' => 'filter_status_periksa', 'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\';
    var dropdownDokterPerujuk = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pegawai_id', '', $dokterPerujuk, ['id' => 'filter_dokter_perujuk', 'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\';

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
