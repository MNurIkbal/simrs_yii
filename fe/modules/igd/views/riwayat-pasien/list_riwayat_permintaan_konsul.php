<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-10 17:37:05
 */

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::t('fe', 'Riwayat Pasien Konsul');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
                    "detail" => [
                        "attributes" => [
                            "data-options" => "modal",
                            "data-target" => "#modal_backdrop",
                            "data-url" => Url::home().("igd/riwayat-pasien/detail?id=")
                        ]
                    ],
                    "pdf" => [
                        "attributes" => [
                            "url" => Url::home().("igd/riwayat-pasien/cetak-riwayat-permintaan-konsul?id=".$id."&norm=".$norm."&"),
                            "target" => "_blank"
                        ]
                    ],
                ], "#tb-riwayat-permintaan-konsul") ?>
            </div>
            <div class="panel-body">
                <table id="tb-riwayat-permintaan-konsul" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Tgl Permintaan Konsul") ?></th>
                            <th><?= Yii::t("fe", "No RM / No Pendaftaran") ?></th>
                            <th><?= Yii::t("fe", "Nama Pasien") ?></th>
                            <th><?= Yii::t("fe", "Jenis Kelamin") ?></th>
                            <th><?= Yii::t("fe", "Dokter DPJP") ?></th>
                            <th><?= Yii::t("fe", "Cara Bayar / Penjamin") ?></th>
                            <th><?= Yii::t("fe", "Hak Kelas / Kelas Saat Ini") ?></th>
                            <th><?= Yii::t("fe", "Nama Ruangan / No Kamar - No Bed") ?></th>
                            <th><?= Yii::t("fe", "Jenis Konsul") ?></th>
                            <th><?= Yii::t("fe", "Dokter Konsul") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var no = "'.(\Yii::t("fe", "No")).'";
    var tgl_permintaan = "'.(\Yii::t("fe", "Tgl Permintaan Konsul")).'";
    var no_rekam_medik = "'.(\Yii::t("fe", "No RM / No Pendaftaran")).'";
    var nama_pasien = "'.(\Yii::t("fe", "Nama Pasien")).'";
    var jenis_kelamin = "'.(\Yii::t("fe", "Jenis Kelamin")).'";
    var dokter_dpjp = "'.(\Yii::t("fe", "Dokter DPJP")).'";
    var cara_bayar = "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'";
    var hak_kelas = "'.(\Yii::t("fe", "Hak Kelas / Kelas Saat Ini")).'";
    var nama_ruangan = "'.(\Yii::t("fe", "Nama Ruangan / No Kamar - No Bed")).'";
    var jenis_konsul = "'.(\Yii::t("fe", "Jenis Konsul")).'";
    var dokter_konsul = "'.(\Yii::t("fe", "Dokter Konsul")).'";
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

    var norm = "'.$norm.'";
', View::POS_END, 'index');

$this->registerJs($this->render('js/list_riwayat_permintaan_konsul.js'), View::POS_END);
?>