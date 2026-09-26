<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-17 11:30:26
 */

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('fe', 'Master Daftar Fasilitas');
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
                    "search",
                    "add" => [
                        "attributes" => [
                            "data-toggle" => "modal",
                            "data-target" => "#modal_backdrop",
                            "action" => "/master/fasilitas-rs/create",
                        ]
                    ],
                    "edit" => [
                        "attributes" => [
                            "id" => "btn-edit",
                            "data-toggle" => "modal",
                            "data-target" => "#modal_backdrop",
                            "disabled" => "disabled"
                        ]
                    ],
                    "delete" => [
                        "attributes" => [
                            "id" => "btn-delete",
                            "data-additional" => "data-rm",
                            "disabled" => "disabled"
                        ]
                    ],
                    // "pdf",
                    // "excel",
                ], "#tb-fasilitas-rs") ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-fasilitas-rs" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Jenis Fasilitas") ?></th>
                            <th><?= Yii::t("fe", "Nama Fasilitas") ?></th>
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
    // Global vars
    var no = "'.(\Yii::t("fe", "No")).'";
    var jenisFasilitas = "'.(\Yii::t("fe", "Jenis Fasilitas")).'";
    var namaFasilitas = "'.(\Yii::t("fe", "Nama Fasilitas")).'";
    var updateUrl = "/master/fasilitas-rs/update?id=";

    // Datatable language
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

    // Custom dropdown
    var dropdownJenisFasilitas = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_fasilitas', '', $listJenisFasilitas, [
        'id' => 'filter_jenis_fasilitas',
        'class' => 'form-control select2',
        'prompt' => \Yii::t('fe', '-- Pilih --'),
    ]))).'\';

', View::POS_END, 'index');

$this->registerJs($this->render('js/index.js'), View::POS_END);
?>