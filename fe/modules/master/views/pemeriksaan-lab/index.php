<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 17:10:27
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-26 09:16:31
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', 'Pemeriksaan Laboratorium');
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
                            'action' => '/master/pemeriksaan-lab/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'id' => 'btn-edit',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'disabled' => 'disabled'
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'id' => 'btn-delete',
                            'disabled' => 'disabled'
                        ]
                    ],
                    'pdf',
                    'excel',
                ], '#tb-pemeriksaan-lab') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-pemeriksaan-lab" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'Nomor') ?></th>
                            <th><?= Yii::t("fe", "Kode") ?></th>
                            <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                            <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                            <th><?= Yii::t("fe", "Kelompok Pemeriksaan") ?></th>
                            <th><?= Yii::t("fe", "Exception") ?></th>
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
    localStorage.setItem("jenis", \''.json_encode($data['jenis_pemeriksaan']).'\');
    // Global vars
    var no = "'.(\Yii::t("fe", "Nomor")).'";
    var kode = "'.(\Yii::t("fe", "Kode")).'";
    var namaPemeriksaan = "'.(\Yii::t("fe", "Nama Pemeriksaan")).'";
    var jenisPemeriksaan = "'.(\Yii::t("fe", "Jenis Pemeriksaan")).'";
    var kelompokPemeriksaan = "'.(\Yii::t("fe", "Kelompok Pemeriksaan")).'";
    var exception = "'.(\Yii::t("fe", "Exception")).'";
    var updateUrl = "/master/pemeriksaan-lab/update?id=";

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

    // Custom dropdown
    var dropdownLab = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pemeriksaanlab_nama', '', $daftarTindakan, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\';
    var dropdownJenis = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenispemeriksaanlab_id', '', $jenisPemeriksaanLab, ['id' => 'filter_jenis', 'class' => 'form-control select2 dep-to-parent', 'prompt' => \Yii::t('fe', 'Pilih'),'data-url' => '/master/jenis-pemeriksaan-lab/get-kelompok-pemeriksaan-lab', 'data-depend_id' => 'filter_kelompok']))).'\';
    var dropdownKelompok = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelompokpemerisaanlab_id', '', $kelompokPemeriksaanLab, ['id' => 'filter_kelompok', 'class' => 'form-control select2 dep-to-child', 'prompt' => \Yii::t('fe', 'Pilih'),'data-url' => '/master/jenis-pemeriksaan-lab/get-jenis-pemeriksaan-lab', 'data-depend_id' => 'filter_jenis','data-depend_prompt' => \Yii::t('fe', 'Pilih'),'data-storage' => 'jenis','data-key' => 'jenispemeriksaanlab_id', 'data-val' => 'jenispemeriksaanlab_nama']))).'\';
    var dropdownException = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_exception', '', ['1' => 'Ya', '0' => 'Tidak'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\';

    ', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
