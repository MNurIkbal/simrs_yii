<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-13 14:26:33
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Informasi Permintaan Makan Pasien');
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
                    'edit' => [
                        'attributes' => [
                            'id' => 'btn-edit-permintaan-makan',
                            'url' => Url::home().('gizi/permintaan-makan/update?id=')
                        ],
                    ],
                    'detail' => [
                        'attributes' => [
                            'id' => 'btn-detail-permintaan-makan',
                            'data-options' => 'modal',
                            'data-target' => '#modal_detail_permintaan_makan',
                            'data-url' => Url::home().('gizi/inf-permintaan-makan/detail?id=')
                        ],
                    ],
                    'print' => [
                        'title' => 'Label Makanan',
                        'attributes' => ($konfig_print_gizi ?
                        [
                            'id' => 'btn-label-makanan',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '30%',
                            'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/pilih-jumlah-label-makanan?&id=',
                            'data-conditions' => 'pendaftaran_id'
                        ] :
                        [
                            'id' => 'btn-label-makanan',
                            'data-options' => false,
                            'data-pages' => '_blank',
                            'data-target' => '/gizi/inf-permintaan-makan/print-label-makanan?id=',
                        ])
                    ],
                    'print-label-makanan-multiple' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print Label Makanan Multiple'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-label-makanan',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '30%',
                            'data-url' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/pilih-jumlah-label-makanan?with_jumlah=true&id=',
                            'data-conditions' => 'pendaftaran_id'
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-table' => '#tb-inf-permintaan-makan'
                        ]
                    ],
                    'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Unduh Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'gizi/inf-permintaan-makan/show-popup-excel?',
                                'data-width' => '75%',
                            ]
                        ],
                    'reset',
                ], '#tb-inf-permintaan-makan');?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <div class="legend-index">
                    <div class="col-md-6">
                        <div class="legend-header">Keterangan</div>
                        <div class="legend-wrapper">
                            <div class="legend-information">
                                <div class="legend-information__color" style="background-color: #7efff5"></div>
                                <div class="legend-information__text">Stop Akomodasi</div>
                            </div>
                            <div class="legend-information">
                                <div class="legend-information__color" style="background-color: #d64541"></div>
                                <div class="legend-information__text">Pasien Pulang</div>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-hover dataTable" id="tb-inf-permintaan-makan" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal Permintaan') ?></th>
                            <th><?= Yii::t('fe', 'No Permintaan') ?></th>
                            <th><?= Yii::t('fe', 'Pembayaran') ?></th>
                            <th><?= Yii::t('fe', 'Catatan Diet') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan/Kamar') ?></th>
                            <th><?= Yii::t('fe', 'No Pendaftaran') ?></th>
                            <th><?= Yii::t('fe', 'No Rekam Medik') ?></th>
                            <th><?= Yii::t('fe', 'Nama Pasien') ?></th>
                            <th><?= Yii::t('fe', 'Jenis Kelamin') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal Lahir') ?></th>
                            <th><?= Yii::t('fe', 'Jenis Diet') ?></th>
                            <th><?= Yii::t('fe', 'Diagnosa') ?></th>
                            <th><?= Yii::t('fe', 'Alergi') ?></th>
                            <th><?= Yii::t('fe', 'Penjamin') ?></th>
                            <th><?= Yii::t('fe', 'Info Cara Bayar') ?></th>
                            <th><?= Yii::t('fe', 'Status') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_detail_permintaan_makan" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$this->registerJsFile(
    '/js/app/dcms/date-range-filter.js',
    [
        'depends' => [
            'app\assets\AppAsset',
        ]
    ]
);

$this->registerJs("
    var no = '".(\Yii::t("fe", "No"))."';
    var tgl_permintaanmakan = '".(\Yii::t("fe", "Tanggal Permintaan"))."';
    var no_permintaanmakan = '".(\Yii::t("fe", "No Permintaan"))."';
    var pembayaran = '".(\Yii::t("fe", "Pembayaran"))."';
    var catatan_diet = '".(\Yii::t("fe", "Catatan Diet"))."';
    var ruangan_kamar = '".(\Yii::t("fe", "Ruangan/Kamar"))."';
    var no_pendaftaran = '".(\Yii::t("fe", "No Pendaftaran"))."';
    var no_rekam_medik = '".(\Yii::t("fe", "No Rekam Medik"))."';
    var nama_pasien = '".(\Yii::t("fe", "Nama Pasien"))."';
    var jenis_kelamin = '".(\Yii::t("fe", "Jenis Kelamin"))."';
    var tanggal_lahir = '".(\Yii::t("fe", "Tanggal Lahir"))."';
    var jenisdiet_nama = '".(\Yii::t("fe", "Jenis Diet"))."';
    var diagnosa = '".(\Yii::t("fe", "Diagnosa"))."';
    var riwayat_alergi = '".(\Yii::t("fe", "Alergi"))."';
    var penjamin_nama = '".(\Yii::t("fe", "Penjamin"))."';
    var status_permintaan = '".(\Yii::t("fe", "Status"))."';
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
    var konfigPrintGizi = ".json_encode($konfig_print_gizi).";

    var dropdownStatus = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('status_permintaan_makan', '',
            $listStatus,
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
    var dropdownCaraBayar = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('carabayar_id', '',
            $listCaraBayar,
            [
                'id' => 'filter_carabayar',
                'class' => 'form-control select2',
                'style' => 'width:100%;',
                'prompt' => \Yii::t('fe', '-- Pilih Cara Bayar --')
            ]
        )
    ))."<div>\";

    var dropdownPembayaran = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::dropDownList('status_pembayaran', '',
            $listPembayaran,
            [
                'id' => 'filter_status_pembayaran_permintaan_makan',
                'class' => 'form-control select2',
                'prompt' => \Yii::t('fe', '-- Pilih --'),
            ]
        )
    ))."<div>\";

    var inputTanggal = \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' value=".date('d-m-Y')."  data-value=" . date('d-m-Y') . " /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' value=".date('d-m-Y')." data-value=".date('d-m-Y')." /><input type='text' style='display:none' class='targetDate' id='targetDate' col-index=2 readonly='true'></div>\";

    var ruangan = '".json_encode($listRuangan)."';
    var kamar = '".json_encode($listKamar)."';
", View::POS_END, 'index');

$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
