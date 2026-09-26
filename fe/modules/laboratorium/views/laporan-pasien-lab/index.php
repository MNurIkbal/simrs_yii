<?php

/**
 * @author Si Kasep
 * @todo View Informasi Pasien Laboratorium
 * @copyright 19 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use yii\helpers\ArrayHelper;



$this->title = \Yii::t('fe', 'Laporan Pasien Laboratorium');
$this->params['breadcrumbs'][] = ['label' => 'Laboratorium', 'url' => ['/laboratorium']];
$this->params['breadcrumbs'][] = $title;
?>

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                    'excel',
                    'pdf',
                ], '#example');
                ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="example"
                    data-source="<?= Url::home(); ?>laboratorium/laporan-pasien-lab/get-data"
                    data-filter=".form-filter"
                    data-test="true">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th><?= \Yii::t("fe", "Tanggal Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Nomor Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "No Rekam Medis"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Lahir"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter"); ?></th>
                            <th><?= \Yii::t("fe", "Cara Bayar"); ?></th>
                            <th><?= \Yii::t("fe", "Penjamin"); ?></th>
                            <th><?= \Yii::t("fe", "No.Lab"); ?></th>
                            <th><?= \Yii::t("fe", "Asal Rujukan"); ?></th>
                            <th><?= \Yii::t("fe", "Status"); ?></th>
                            <th><?= \Yii::t("fe", "Jumlah Tagihan"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan") ?></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    // save into localStorage
    localStorage.clear();
    // Global vars

    _ruanganDefault = ' . Yii::$app->docoVars->workspace("ruangan_id") . '

    const no_antrian_title = "' . (\Yii::t("fe", "No Antrian")) . '";
    const tanggal_pendaftaran_title = "' . (\Yii::t("fe", "Tanggal Pendaftaran")) . '";
    const no_pendaftaran_title = "' . (\Yii::t("fe", "No Pendaftaran")) . '";
    const no_rekam_medik_title = "' . (\Yii::t("fe", "No Rekam Medis")) . '";
    const nama_pasien_title = "' . (\Yii::t("fe", "Nama Pasien")) . '";
    const tanggal_lahir_title = "' . (\Yii::t("fe", "Tanggal Lahir")) . '";
    const dokter_title = "' . (\Yii::t("fe", "Dokter")) . '";
    const cara_bayar_title = "' . (\Yii::t("fe", "Cara Bayar")) . '";
    const penjamin_title = "' . (\Yii::t("fe", "Penjamin")) . '";
    const no_lab_title = "' . (\Yii::t("fe", "No Lab")) . '";
    const asal_rujukan_title = "' . (\Yii::t("fe", "Asal Rujukan")) . '";
    const status_title = "' . (\Yii::t("fe", "Status")) . '";
    const harga_title = "' . (\Yii::t("fe", "Jumlah Tagihan")) . '";
    const ruangan_title = "' . (\Yii::t("fe", "Ruangan")) . '";
    

    // Datatable language
    const emptyTable = "' . (\Yii::t("fe", "Tidak ada data yang tersedia")) . '";
    const info = "' . (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")) . '";
    const infoEmpty = "' . (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) . '";
    const infoFiltered = "' . (\Yii::t("fe", "(disaring dari _MAX_ total data)")) . '";
    const lengthMenu = "' . (\Yii::t("fe", "Menampilkan _MENU_ data")) . '";
    const loadingRecords = "' . (\Yii::t("fe", "Memuat...")) . '";
    const processing = "' . (\Yii::t("fe", "Memproses...")) . '";
    const search = "' . (\Yii::t("fe", "Cari:")) . '";
    const zeroRecords = "' . (\Yii::t("fe", "Tidak ada data yang ditemukan")) . '";
    const first = "' . (\Yii::t("fe", "Pertama")) . '";
    const last = "' . (\Yii::t("fe", "Terakhir")) . '";
    const next = "' . (\Yii::t("fe", "Selanjutnya")) . '";
    const previous = "' . (\Yii::t("fe", "Sebelumnya")) . '";
    const sortAscending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) . '";
    const sortDescending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) . '";

    // Custom dropdown
    
    var filterTanggalPendaftaran = \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="' . date('d-M-Y') . '"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="' . date('d-M-Y') . '"/><input type="text" style="display:none" class="targetDate"></div>\';

    var filterTanggalLahir = \'<div class="input-group"><input type="text" id="rangeLahirStart" class="form-control rangeLahirStart"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeLahirFinish" class="form-control rangeLahirFinish"/><input type="text" style="display:none" class="targetDateLahir"></div>\';

    var dropdownStatus = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', $status, ['id' => 'filter_kelompok_2', 'class' => 'form-control select2 dep-to-child', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/master/ruangan/generate-api', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'kode', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';

    var dropdownAsalRujukan = \'' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Html::dropDownList('asalrujukan_id', '', [], [
        'id' => 'select_asalrujukan', 
        'class' => 'form-control select2 ', 
        'prompt' => \Yii::t('fe', 'Pilih'), 
    ])
)) . '\';

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
            'data-url' => Url::home() . 'kasir/end-point/get-penjamin',
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
            'class' => 'form-control select2 dep-to-child',
            'prompt' => \Yii::t('fe', '--Penjamin--'),
            // 'data-url' => Url::home() . 'kasir/end-point/get-penjamin',
            // 'data-storage' => 'penjamin',
            // 'data-key' => 'penjamin_nama',
        ]
    )
)) . '\';
     
    var autocompleteDokter = \'<div class="input-group">' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Select2::widget([
        'name' => 'dokter_perujuk',
        'options' => ['placeholder' => \Yii::t('fe', 'Dokter'), 'autocomplete' => 'off'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => Url::home() . (Yii::$app->controller->module->id) . '/inf-pasien-rujukan-lab/get-dokter',
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
        ],
    ])
)) . '</div>\';

    var dropdownRuangan = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
    'ruangan_id',
    Yii::$app->docoVars->workspace("ruangan_id"),
    [Yii::$app->docoVars->workspace("ruangan_id") => Yii::$app->docoVars->workspace("ruangan_name")],
    [
        'class' => 'form-control select2 selectRuangan',
        'id' => 'select-ruangan',
        'prompt' => Yii::t('fe', 'Semua Ruangan'),
    ]
))) . '\';

    semuaRuanganText = \'' . Yii::t('fe', 'Semua Ruangan') . '\'

', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>