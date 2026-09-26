<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', 'Jenis Pemeriksaan Radiologi');
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
                            'action' => '/master/jenis-pemeriksaan-rad/create',
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
                    // 'pdf',
                    'excel',
                ], '#tb-jenis-pemeriksaan-rad') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-jenis-pemeriksaan-rad" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'Nomor') ?></th>
                            <th><?= Yii::t("fe", "Kode") ?></th>
                            <th><?= Yii::t("fe", "Kelompok Pemeriksaan") ?></th>
                            <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
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
    const no = "'.(\Yii::t("fe", "Nomor")).'";
    const kode = "'.(\Yii::t("fe", "Kode")).'";
    const jenisPemeriksaan = "'.(\Yii::t("fe", "Jenis Pemeriksaan")).'";
    const kelompokPemeriksaan = "'.(\Yii::t("fe", "Kelompok Pemeriksaan")).'";
    const updateUrl = "/master/jenis-pemeriksaan-rad/update?id=";

    // Datatable language
    const emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    const info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    const infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    const infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    const lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    const loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    const processing = "'.(\Yii::t("fe", "Memproses...")).'";
    const search = "'.(\Yii::t("fe", "Cari:")).'";
    const zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    const first = "'.(\Yii::t("fe", "Pertama")).'";
    const last = "'.(\Yii::t("fe", "Terakhir")).'";
    const next = "'.(\Yii::t("fe", "Selanjutnya")).'";
    const previous = "'.(\Yii::t("fe", "Sebelumnya")).'";
    const sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    const sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";

    // Custom dropdown
    var dropdownJenis = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenispemeriksaanrad_id', '', $jenisPemeriksaanRad, ['id' => 'filter_jenis', 'class' => 'form-control select2 dep-to-parent', 'prompt' => \Yii::t('fe', 'Pilih'),'data-url' => '/master/jenis-pemeriksaan-rad/get-kelompok-pemeriksaan-rad', 'data-depend_id' => 'filter_kelompok']))).'\';
    var dropdown = \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelompokpemerisaanrad_id', '', $kelompokPemeriksaanRad, ['id' => 'filter_kelompok', 'class' => 'form-control select2 dep-to-child', 'prompt' => \Yii::t('fe', 'Pilih'),'data-url' => '/master/jenis-pemeriksaan-rad/get-jenis-pemeriksaan-rad', 'data-depend_id' => 'filter_jenis','data-depend_prompt' => \Yii::t('fe', 'Pilih'),'data-storage' => 'jenis','data-key' => 'jenispemeriksaanrad_id', 'data-val' => 'jenispemeriksaanrad_nama']))).'\';
', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
