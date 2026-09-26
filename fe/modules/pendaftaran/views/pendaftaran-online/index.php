<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-26 10:37:09
 */

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                        <div class="column-1">
                            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                        </div>
                        <div class="column-2">
                            <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b></h3>
                            <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                        </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    "search",
                    "reset",
                    "pdf",
                    "excel",
                ], "#tb-pendaftaran-online") ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="advanced-filter">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label><?= Yii::t('fe', 'Scanner') ?></label>
                                <?= Html::textInput('scanner', '', ['id' => 'scanner', 'class' => 'form-control']) ?>
                            </div>
                        </div>
                    </div>
                    <div id="hidden-btn" hidden>
                        <?= Html::button(Yii::t('fe', 'Proses'), [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-proses',
                            'id' => 'btn-cari',
                            'action' => Url::to([
                                '/pendaftaran/pendaftaran-online/proses?id='
                            ]),
                            'data-target' => '#modal_backdrop',
                            'data-options' => 'link',
                            'data-toggle' => 'modal',
                        ]); ?>
                    </div>
                    <?= Html::hiddenInput('temp_tgl_kunjungan', '', ['id' => 'temp_tgl_kunjungan']) ?>
                </div>
                <table id="tb-pendaftaran-online" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t("fe", "Aksi") ?></th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "No Antrian") ?></th>
                            <th><?= Yii::t("fe", "No Pendaftaran") ?></th>
                            <th><?= Yii::t("fe", "No Rekam Medik / Nama Pasien") ?></th>
                            <th><?= Yii::t("fe", "No Rekam Medik / Nama Pasien") ?></th>
                            <th><?= Yii::t("fe", "No Asuransi") ?></th>
                            <th><?= Yii::t("fe", "Poli Tujuan") ?></th>
                            <th><?= Yii::t("fe", "Dokter") ?></th>
                            <th><?= Yii::t("fe", "Cara Bayar") ?></th>
                            <th><?= Yii::t("fe", "Jam Mulai Pelayanan") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Daftar") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Kunjungan") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_backdrop_full" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var no = "'.(\Yii::t("fe", "No")).'";
    var noAntrian = "'.(\Yii::t("fe", "No Antrian")).'";
    var noPendaftaran = "'.(\Yii::t("fe", "No Pendaftaran")).'";
    var noRekamMedik = "'.(\Yii::t("fe", "No Rekam Medik / Nama Pasien")).'";
    var namaPasien = "'.(\Yii::t("fe", "Nama Pasien")).'";
    var noAsuransi = "'.(\Yii::t("fe", "No Asuransi")).'";
    var poliTujuan = "'.(\Yii::t("fe", "Poli Tujuan")).'";
    var dokter = "'.(\Yii::t("fe", "Dokter")).'";
    var caraBayar = "'.(\Yii::t("fe", "Cara Bayar")).'";
    var jamMulaiPelayanan = "'.(\Yii::t("fe", "Jam Mulai Pelayanan")).'";
    var tanggalDaftar = "'.(\Yii::t("fe", "Tanggal Daftar")).'";
    var tanggalKunjungan = "'.(\Yii::t("fe", "Tanggal Kunjungan")).'";
    var aksi = "'.(\Yii::t("fe", "Aksi")).'";
    var status = "'.(\Yii::t("fe", "Status")).'";

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

    var date = "'.date('d-M-Y', strtotime('NOW')).'";
    var url = "'.Url::to(['/pendaftaran/pendaftaran-online/proses?id=']).'";
    var cekUrl = "'.Url::to(['/pendaftaran/pendaftaran-online/cek-pendaftaran-online?no=']).'";
    var errorTitle = "'.Yii::t('fe', 'Proses Gagal.').'";
    var errorMessage = "'.Yii::t('fe', 'Data pendaftaran online tidak ditemukan/sudah diproses.').'";

    var inputTanggalKunjungan = \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tanggal_kunjungan', '', ['class' => 'form-control daterange', 'id' => 'tanggal_kunjungan']))).'\';

    var dropdownPoliTujuan = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', $listPoliTujuan, [
        'id' => 'filter_ruangan_nama',
        'class' => 'form-control select2',
        'prompt' => \Yii::t('fe', '-- Pilih --'),
    ]))).'\';

    var dropdownDokter = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', $listDokter, [
        'id' => 'filter_pegawai_nama',
        'class' => 'form-control select2',
        'prompt' => \Yii::t('fe', '-- Pilih --'),
    ]))).'\';

    var dropdownCaraBayar = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_nama', '', $listCaraBayar, [
        'id' => 'filter_carabayar_nama',
        'class' => 'form-control select2',
        'prompt' => \Yii::t('fe', '-- Pilih --'),
    ]))).'\';

    var dropdownStatus = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('lookup_name', '', $listStatus, [
        'id' => 'filter_lookup_name',
        'class' => 'form-control select2',
        'prompt' => \Yii::t('fe', '-- Pilih --'),
    ]))).'\';
', View::POS_END, 'index');

$this->registerJs($this->render('js/index.js'), View::POS_END);
$this->registerJs($this->render('js/pendaftaran_online_listener.js'), View::POS_END);
?>