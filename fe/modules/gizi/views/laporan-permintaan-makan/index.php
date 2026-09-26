<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-20 15:59:08
 */

use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Laporan Permintaan Makan Pasien');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Gizi'), 'url' => ['/gizi']];
$this->params['breadcrumbs'][] = $this->title;
?>
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
                ], '#tb-lap-permintaan-makan');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table class="table table-striped table-hover dataTable" id="tb-lap-permintaan-makan" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal Permintaan') ?></th>
                            <th><?= Yii::t('fe', 'Jenis Diet') ?></th>
                            <th><?= Yii::t('fe', 'Menu') ?></th>
                            <th><?= Yii::t('fe', 'Jenis Kelamin') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal Lahir') ?></th>
                            <th><?= Yii::t('fe', 'Jenis Diet') ?></th>
                            <th><?= Yii::t('fe', 'Diagnosa') ?></th>
                            <th><?= Yii::t('fe', 'Alergi') ?></th>
                            <th><?= Yii::t('fe', 'Penjamin') ?></th>
                            <th><?= Yii::t('fe', 'Jumlah') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th><?= Yii::t('fe', 'Total') ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var no = "'.(\Yii::t("fe", "No")).'";
    var tgl_permintaanmakan = "'.(\Yii::t("fe", "Tanggal Permintaan")).'";
    var jenisdiet_nama = "'.(\Yii::t("fe", "Jenis Diet")).'";
    var makanandiet_nama = "'.(\Yii::t("fe", "Menu")).'";
    var jenis_kelamin = "'.(\Yii::t("fe", "Jenis Kelamin")).'";
    var tanggal_lahir = "'.(\Yii::t("fe", "Tanggal Lahir")).'";
    var jenisdiet_nama = "'.(\Yii::t("fe", "Jenis Diet")).'";
    var diagnosa = "'.(\Yii::t("fe", "Diagnosa")).'";
    var riwayat_alergi = "'.(\Yii::t("fe", "Alergi")).'";
    var penjamin_nama = "'.(\Yii::t("fe", "Penjamin")).'";
    var jumlah = "'.(\Yii::t("fe", "Jumlah")).'";

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

    var inputTanggal = \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value='.date('d-M-Y').' /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value='.date('d-M-Y').' /><input type="text" style="display:none" class="targetDate" id="targetDate" col-index=2 readonly="true"></div>\';
', View::POS_END, 'index');

$this->registerJs($this->render('js/index.js'), View::POS_END);
?>