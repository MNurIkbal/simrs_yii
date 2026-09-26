<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-26 14:04:11
 */

use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Laporan Pasien Malnutrisi');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Gizi'), 'url' => ['/gizi']];
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
</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= $this->title ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'pdf',
                    'excel',
                    'reset',
                ], '#tb-lap-pasien-malnutrisi');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'><?= Yii::t('fe', 'Keterangan') ?></div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#55efc4;'></span><?= Yii::t('fe', 'Rendah') ?></li>
                                    <li><span style='background:#ffeaa7;'></span><?= Yii::t('fe', 'Sedang') ?></li>
                                    <li><span style='background:#fab1a0;'></span><?= Yii::t('fe', 'Tinggi') ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover dataTable" id="tb-lap-pasien-malnutrisi" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Tgl. Masuk') ?></th>
                            <th><?= Yii::t('fe', 'Info Pasien') ?></th>
                            <th><?= Yii::t('fe', 'Jenis Kelamin') ?></th>
                            <th><?= Yii::t('fe', 'Dokter DPJP') ?></th>
                            <th><?= Yii::t('fe', 'Kasus Penyakit') ?></th>
                            <th><?= Yii::t('fe', 'Info Kamar') ?></th>
                            <th><?= Yii::t('fe', 'Hari Rawat') ?></th>
                            <th><?= Yii::t('fe', 'Kategori') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var no = '".(\Yii::t("fe", "No"))."';
    var tgl_masuk = '".(\Yii::t("fe", "Tgl. Masuk"))."';
    var no_rm_pendaftaran = '".(\Yii::t("fe", "Info Pasien"))."';
    var nama_pasien = '".(\Yii::t("fe", "Nama Pasien"))."';
    var jenis_kelamin = '".(\Yii::t("fe", "Jenis Kelamin"))."';
    var dokter_dpjp = '".(\Yii::t("fe", "Dokter DPJP"))."';
    var kasus_penyakit = '".(\Yii::t("fe", "Kasus Penyakit"))."';
    var ruangan_kamar = '".(\Yii::t("fe", "Info Kamar"))."';
    var hari_rawat = '".(\Yii::t("fe", "Hari Rawat"))."';
    var kategori = '".(\Yii::t("fe", "Kategori"))."';
    var no_rekam_medik = '".(\Yii::t("fe", "No. Rekam Medik"))."';
    var no_pendaftaran = '".(\Yii::t("fe", "No. Pendaftaran"))."';
    var ruangan_nama = '".(\Yii::t("fe", "Ruangan"))."';
    var kamarruangan_nokamar = '".(\Yii::t("fe", "Kamar"))."';

    var emptyTable = '".(\Yii::t("fe", "Tidak ada data yang tersedia"))."';
    var info = '".(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"))."';
    var infoEmpty = '".(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data"))."';
    var infoFiltered = '".(\Yii::t("fe", "(disaring dari _MAX_ total data)"))."';
    var lengthMenu = '".(\Yii::t("fe", "Menampilkan _MENU_ data"))."';
    var loadingRecords = '".(\Yii::t("fe", "Memuat..."))."';
    var processing = '".(\Yii::t("fe", "Memproses..."))."';
    var search = '".(\Yii::t("fe", "Cari:"))."';
    var zeroRecords = '".(\Yii::t("fe", "Tidak ada data yang ditemukan"))."';
    var sortAscending = '".(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar"))."';
    var sortDescending = '".(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil"))."';

    var dropdownKategori = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('kategori', '',
            $listKategori,
            [
                'id' => 'filter_status_permintaan_makan',
                'class' => 'form-control select2',
                'prompt' => \Yii::t('fe', '-- Pilih --'),
            ]
        )
    ))."<div>\";

    var dropdownRuangan = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('ruangan_nama', '',
            $listRuangan,
            [
                'id' => 'filter_ruangan',
                'class' => 'form-control select2 dep-to-child',
                'style' => 'width:100%;',
                'prompt' => \Yii::t('fe', '-- Pilih Ruangan --'),
                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/filter-kamar',
                'data-depend_id' => 'filter_kamar',
                'data-depend_prompt' => \Yii::t('fe', '-- Pilih Kamar --'),
                'data-storage' => 'kamar',
                'data-key' => 'kamar_id',
            ]
        )
    ))."<div>\";

    var dropdownKamar = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('kamar_nama', '',
            $listKamar,
            [
                'id' => 'filter_kamar',
                'class' => 'form-control select2 dep-to-parent',
                'style' => 'width:100%;',
                'prompt' => \Yii::t('fe', '-- Pilih Kamar --'),
                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/filter-ruangan',
                'data-depend_id' => 'filter_ruangan',
            ]
        )
    ))."<div>\";

    var dropdownDokter = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('pegawai_nama', '',
            $listDokter,
            [
                'id' => 'pegawai_nama',
                'class' => 'form-control select2',
                'style' => 'width:100%;',
                'prompt' => Yii::t('fe', '-- Pilih Dokter --'),
            ]
        )
    ))."<div>\";

    var dropdownJenisKasusPenyakit = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('jeniskasuspenyakit_nama', '',
            $listJenisKasusPenyakit,
            [
                'id' => 'jeniskasuspenyakit_nama',
                'class' => 'form-control select2',
                'style' => 'width:100%;',
                'prompt' => Yii::t('fe', '-- Pilih Jenis Kasus Penyakit --'),
            ]
        )
    ))."<div>\";

    var inputTanggal = \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' value=".date('d-M-Y')." /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' value=".date('d-M-Y')." /><input type='text' style='display:none' class='targetDate' id='targetDate' col-index=2 readonly='true'></div>\";

    var ruangan = '".json_encode($listRuangan)."';
    var kamar = '".json_encode($listKamar)."';
", View::POS_END, 'index');

$this->registerJs($this->render('js/index.js'), View::POS_END);
?>