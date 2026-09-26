<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-18 11:44:38
 */

use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'History Master Tempat Tidur');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rekam Medik'), 'url' => ['/rm']];
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
                ], '#tb-history-tempat-tidur');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table class="table table-striped table-hover dataTable" id="tb-history-tempat-tidur" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan') ?></th>
                            <th><?= Yii::t('fe', 'Kamar') ?></th>
                            <th><?= Yii::t('fe', 'No. Tempat Tidur') ?></th>
                            <th><?= Yii::t('fe', 'Keterangan') ?></th>
                            <!-- <th><?= Yii::t('fe', 'Status') ?></th> -->
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
    var tgl_tthistory = '".(\Yii::t("fe", "Tanggal"))."';
    var ruangan_nama = '".(\Yii::t("fe", "Ruangan"))."';
    var kamarruangan_nokamar = '".(\Yii::t("fe", "Kamar"))."';
    var no_tempattidur = '".(\Yii::t("fe", "No. Tempat Tidur"))."';
    var keterangan = '".(\Yii::t("fe", "Keterangan"))."';
    var status = '".(\Yii::t("fe", "Status"))."';

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

    var dropdownRuangan = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('ruangan_nama', '',
            $listRuangan,
            [
                'id' => 'filter_ruangan',
                'class' => 'form-control select2 dep-to-child',
                'style' => 'width:100%;',
                'prompt' => \Yii::t('fe', '-- Pilih --'),
                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/history-tempat-tidur/filter-kamar',
                'data-depend_id' => 'filter_kamar',
                'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
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
                'prompt' => \Yii::t('fe', '-- Pilih --'),
                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/history-tempat-tidur/filter-ruangan',
                'data-depend_id' => 'filter_ruangan',
            ]
        )
    ))."<div>\";

    var dropdownKeterangan = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('keterangan', '',
            $listKeterangan,
            [
                'id' => 'filter_kategori',
                'class' => 'form-control select2',
                'prompt' => \Yii::t('fe', '-- Pilih --'),
            ]
        )
    ))."<div>\";

    var dropdownStatus = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('status', '',
            $listStatus,
            [
                'id' => 'filter_status',
                'class' => 'form-control select2',
                'prompt' => \Yii::t('fe', '-- Pilih --'),
            ]
        )
    ))."<div>\";

    var inputTanggal = \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' value=".date('d-M-Y')." /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' value=".date('d-M-Y')." /><input type='text' style='display:none' class='targetDate' id='targetDate' col-index=2 readonly='true'></div>\";

    var ruangan = '".json_encode($listRuangan)."';
    var kamar = '".json_encode($listKamar)."';
", View::POS_END, 'index');

$this->registerJs($this->render('js/index.js'), View::POS_END);
?>