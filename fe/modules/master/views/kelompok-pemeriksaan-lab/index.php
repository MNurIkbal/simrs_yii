<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 13:04:23
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-23 16:48:49
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', 'Kelompok Pemeriksaan Laboratorium');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/kelompok-pemeriksaan-lab/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'id' => 'btn-edit',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            // 'disabled' => 'disabled'
                        ]
                    ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
                        ]
                    ],
                    // 'pdf',
                    'excel',
                ], '#tb-kelompok-pemeriksaan-lab') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-kelompok-pemeriksaan-lab" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'Nomor') ?></th>
                            <th><?= Yii::t("fe", "Kode") ?></th>
                            <th><?= Yii::t("fe", "Kelompok Pemeriksaan") ?></th>
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
    // Global vars
    var no = "'.(\Yii::t("fe", "No")).'";
    var kode = "'.(\Yii::t("fe", "Kode")).'";
    var kelompokPemeriksaan = "'.(\Yii::t("fe", "Kelompok Pemeriksaan")).'";
    var updateUrl = "/master/kelompok-pemeriksaan-lab/update?id=";

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
    var first = "'.(\Yii::t("fe", "Pertama")).'";
    var last = "'.(\Yii::t("fe", "Terakhir")).'";
    var next = "'.(\Yii::t("fe", "Selanjutnya")).'";
    var previous = "'.(\Yii::t("fe", "Sebelumnya")).'";
    var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";

    var Filterkode = \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('kode_kelompok', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Kode')]))).'\';
    var FilterkelompokPemeriksaan = \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_kelompok', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Kelompok Pemeriksaan')]))).'\';


', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
