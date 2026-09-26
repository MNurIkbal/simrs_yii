<?php

/**
 * @author Randy Vianda Putra
 * @todo View Informasi Pasien Radiologi
 * @copyright 23 Juli 2018 aweutist
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
use app\components\DocoConstants;


$this->title = \Yii::t('fe', 'Informasi Pasien Radiologi');
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['/radiologi']];
$this->params['breadcrumbs'][] = $title;
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
                        'search'=>[
                            'attributes'=>[
                                'id'=>'btn-cari'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'id' => 'btn-reset',
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'hasil' => [
                            'title' => \Yii::t('fe', 'Hasil Pemeriksaan'),
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'id'=>'btn-hasil',
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-url' => '/radiologi/hasil-rad/confirm-periksa?id=',
                                'data-conditions' => 'tindakanpelayanan_id,daftartindakan_id'
                            ]
                        ],
                        'cetak-pemeriksaan' => [
                            'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'id'=>'btn-hasil',
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-url' => '/radiologi/informasi-pasien-rad/show-popup?id=',
                                'data-conditions' => 'tindakanpelayanan_id,daftartindakan_id'
                            ]
                        ],
                        'tagihan' => [
                            'title' => \Yii::t('fe', 'Rincian Tagihan'),
                            'icon' => 'fa fa-money',
                            'attributes' => [
                                'id'=>'cetak-tagihan',
                                'target' => '_blank',
                                'data-options' => 'link',
                                'data-url' => '/radiologi/informasi-pasien-rad/print-rincian?id=',
                                'disabled' => 'disabled'
                            ]
                        ],
                        'cetaklabel' => [
                            'title' => \Yii::t('fe', 'Cetak Label'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id' => 'cetak-label-luar',
                                'data-target'=>'/radiologi/hasil-rad/cetak-label?',
                                'data-conditions' => 'id,tindakan_id,penunjang_id',
                                'data-pages' => '_blank',
                                'disabled' => 'disabled',
                            ]
                        ],
                        'batal-periksa' => [
                            'title' => \Yii::t('fe', 'Batal Pemeriksaan'),
                            'icon' => 'fa fa-times-circle-o',
                            'attributes' => [
                                'id'=>'btn-batal-periksa',
                                'data-url' => '#',
                                'data-options' => 'link',
                                'disabled' => 'disabled',
                            ]
                        ], 
                    ],'#table-pasien-rad');
                ?>
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
                                    <div class="legend-information" id="selesai" data-type="1">
                                        <div class="legend-information__color" style="background-color: #2bcc6e"></div>
                                        <div class="legend-information__text" >Selesai</div>
                                    </div>
                                    <div class="legend-information" id="belum" data-type="2">
                                        <div class="legend-information__color" style="background-color: #FFFfff"></div>
                                        <div class="legend-information__text" >Belum Periksa</div>
                                    </div>
                                    <div class="legend-information" id="periksa" data-type="3">
                                        <div class="legend-information__color" style="background-color: #05bbbe"></div>
                                        <div class="legend-information__text" >Periksa</div>
                                    </div>
                                    <div class="legend-information" id="batal" data-type="4">
                                        <div class="legend-information__color" style="background-color: #d64541"></div>
                                        <div class="legend-information__text" >Batal</div>
                                    </div>
                                    <div class="legend-information" id="cito" data-type="5">
                                        <div class="legend-information__color" style="background-color: #ff8900"></div>
                                        <div class="legend-information__text" >Cito</div>
                                    </div>
                                    <div class="legend-information" id="cito" data-type="6">
                                        <div class="legend-information__color" style="background-color: #FFC300"></div>
                                        <div class="legend-information__text" >Dirujuk</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table
                            class="table table-striped table-condensed table-hover"
                            id="table-pasien-rad" style="width: 100%"
                        >
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">&nbsp;</th>
                                    <th><?= Yii::t('fe', 'No') ?></th>
                                    <th><?=Yii::t('fe', 'No Antrian'); ?></th>
                                    <th><?=Yii::t('fe', 'Status Periksa'); ?></th>
                                    <th><?=Yii::t('fe', 'Tanggal Pendaftaran'); ?></th>
                                    <th></th>
                                    <th><?=Yii::t('fe', 'Tanggal Lahir'); ?></th>
                                    <th><?=Yii::t('fe', 'No Rekam Medis'); ?></th>
                                    <th><?=Yii::t('fe', 'Nama Pemeriksaan'); ?></th>
                                    <th><?=Yii::t('fe', 'Pasien'); ?></th>
                                    <th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
                                    <th><?=Yii::t('fe', 'Dokter'); ?></th>
                                    <th><?=Yii::t('fe', 'Cara Bayar / Penjamin'); ?></th>
                                    <!-- <th><?=Yii::t('fe', 'No Radiologi'); ?></th>
                                    <th><?=Yii::t('fe', 'Asal Rujukan'); ?></th> -->
                                    <th><?=Yii::t('fe', 'Ruangan Asal'); ?></th>
                                    <th><?=Yii::t('fe', 'Ruangan ID'); ?></th>
                                    <th><?=Yii::t('fe', 'Status Bayar'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // save into localStorage
    // localStorage.clear();
    // Global vars
    
    const statusBelumPeriksa = '.DocoConstants::LAB_ST_PEN_BELUMPERIKSA.';
    const statusPeriksa = '.DocoConstants::LAB_ST_PEN_PERIKSA.';
    const statusBatal = '.DocoConstants::LAB_ST_PEN_BATAL.';
    const statusSelesai = '.DocoConstants::LAB_ST_PEN_SELESAI.';

    const status_periksa = '.DocoConstants::STATUS_SELESAI_RAD.';
    const no_antrian = "' . (\Yii::t("fe", "No Antrian")) . '";
    const tanggal_pendaftaran = "' . (\Yii::t("fe", "Tanggal Pendaftaran")) . '";
    const no_pendaftaran = "' . (\Yii::t("fe", "No Pendaftaran")) . '";
    const no_rekam_medik = "' . (\Yii::t("fe", "No Rekam Medis")) . '";
    const nama_pasien = "' . (\Yii::t("fe", "Nama Pasien")) . '";
    const tanggal_lahir = "' . (\Yii::t("fe", "Tanggal Lahir")) . '";
    const dokter = "' . (\Yii::t("fe", "Dokter")) . '";
    const cara_bayar = "' . (\Yii::t("fe", "Cara Bayar / Penjamin")) . '";
    const no_rad = "' . (\Yii::t("fe", "No Radiologi")) . '";
    const asal_rujukan = "' . (\Yii::t("fe", "Asal Rujukan")) . '";
    const status = "' . (\Yii::t("fe", "Status Periksa")) . '";
    const no = "' . (\Yii::t("fe", "No")) . '";

    const updateUrl = "/radiologi/inf-pasien-rujukan-rad/update?id=";
    const approveUrl = "/radiologi/inf-pasien-rujukan-rad/form-aproval?id=";
    const batalUrl = "/radiologi/informasi-pasien-rad/form-batal?id=";
    const cetakUrl = "/radiologi/informasi-pasien-rad/print-rincian?id=";
    const cetakPemerikssan = "/radiologi/hasil-rad/cetak?id=";

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
    
    var filterTanggalPendaftaran = \'<div class="input-group"><input type="text" value="'.date("d-M-Y").'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" value="'.date("d-M-Y").'" id="rangeDemoFinish" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\';

    var filterTanggalLahir = \'<div class="input-group"><input type="text" id="rangeLahirStart" class="form-control rangeLahirStart"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeLahirFinish" class="form-control rangeLahirFinish"/><input type="text" style="display:none" class="targetDateLahir"></div>\';

    var dropdownStatus = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', $status, ['id' => 'filter_kelompok_2', 'class' => 'form-control select2 ', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'kode', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';

    var dropdownStatusExpert = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', $statusExpertise, ['id' => 'filter_kelompok_3', 'class' => 'form-control select2 ', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'kode', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';

    var asal_1 = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', $arrAsalRujukan, ['id' => 'asal_rujukan_1', 'class' => 'form-control select2 ', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'kode', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';

    var asal_2 = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', array(), ['id' => 'asal_rujukan_2', 'class' => 'form-control select2 ', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'kode', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';


    var asal_3 = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', array(), ['id' => 'asal_rujukan_3', 'class' => 'form-control select2 ', 'prompt' => \Yii::t('fe', 'Pilih'), 'data-url' => '/', 'data-depend_id' => 'filter_jenis', 'data-depend_prompt' => \Yii::t('fe', 'Pilih'), 'data-storage' => 'ruangan', 'data-key' => 'kode', 'data-val' => 'pasienkirimkeunitlain_nama']))) . '\';

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
                // 'data-url' => Url::home() . 'kasir/end-point/get-penjamin',
                // 'data-depend_id' => 'filter_penjamin',
                'data-depend_prompt' => \Yii::t('fe', '--Penjamin--'),
                'data-storage' => 'penjamin',
                'data-key' => 'penjamin_nama',
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
                'url' => Url::home() . (Yii::$app->controller->module->id) . '/informasi-pasien-rad/get-dokter',
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
        ],
    ])
)) . '</div>\'

', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
