<?php
/**
 * @Author: Sigit
 * @Date:   2018-09-26 10:37:09
 * Extension View for SIRS Only
 */

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .btn-batal-reservasi-bantaran, 
    .btn-batal-reservasi-bantaran:hover, 
    .btn-batal-reservasi-bantaran:focus {
        background-color: #CF212A !important;
        border-color: #CF212A !important;
    }

    .modal-dialog {
        width: 90%;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
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
                    'search',
                    'reset',
                    'proses_verifikasi' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Proses Verifikasi'),
                        'icon' => 'fa fa-check',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-verifikasi-bantaran',
                            'class' => 'btn-verifikasi-bantaran',
                            'data-popup'=>'tooltip',
							'data-toggle'=>'modal',
							'data-target'=>'#modal_backdrop',
                        ]
                    ],
                    'bantaran_detail' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Detail'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'btn-detail-bantaran',
                            'class' => 'btn-detail-bantaran',
                            'data-popup'=>'tooltip',
							'data-toggle'=>'modal',
							'data-target'=>'#modal_backdrop',
                        ]
                    ],
                    'periksa' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Periksa'),
                        'icon' => 'fa fa-stethoscope',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-periksa-bantaran',
                            'class' => 'btn-periksa-bantaran',
                            'data-options' => 'click',
                            'data-popup'=>'tooltip',
                        ]
                    ],
                    'batal_reservasi' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal Verifikasi'),
                        'icon' => 'fa fa-ban',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-batal-reservasi-bantaran',
                            'class' => 'btn-batal-reservasi-bantaran',
                            'data-options' => 'click',
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-rujukan'
                        ]
                    ],
                    'print-qr' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print QR Pembantaran'),
                        'icon' => 'fa fa-qrcode',
                        'method' => '',
                        'attributes' => [
                            'id' => 'btn-print-qr',
                            'class' => 'btn-print-qr',
                            'data-options' => 'click',
                            'data-popup' => 'tooltip',
                            'href' => 'javascript:void(0);',
                        ]
                    ],
                ], '#tb-rujukan-bantaran') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="advanced-filter"></div>
                    </div>
                </div>
                <table id="tb-rujukan-bantaran" class="table table-striped" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Kunjungan") ?></th>
                            <th><?= Yii::t("fe", "Info Pasien") ?></th>
                            <th><?= Yii::t("fe", "UPT Asal") ?></th>
                            <th><?= Yii::t("fe", "Instalasi Tujuan") ?></th>
                            <th><?= Yii::t("fe", "Poli") ?></th>
                            <th><?= Yii::t("fe", "Verifikasi") ?></th>
                            <th><?= Yii::t("fe", "Status Pelayanan") ?></th>
                            <th><?= Yii::t("fe", "Pegawai Verifikasi") ?></th>
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
        var tanggalKunjungan = "'.(\Yii::t("fe", "Tanggal Kunjungan")).'";
        var infoPasien = "'.(\Yii::t("fe", "Info Pasien")).'";
        var uptAsal = "'.(\Yii::t("fe", "UPT Asal")).'";
        var instalasiTujuan = "'.(\Yii::t("fe", "Instalasi Tujuan")).'";
        var poliReservasi = "'.(\Yii::t("fe", "Poli")).'";
        var verifikasiLabel = "'.(\Yii::t("fe", "Verifikasi")).'";
        var statusLayanan = "'.(\Yii::t("fe", "Status Pelayanan")).'";
        var pegawaiVerif = "'.(\Yii::t("fe", "Pegawai Verifikasi")).'";

        $(document).ready(function() {
            $("#btn-detail-bantaran").attr("disabled", true);
            $("#btn-verifikasi-bantaran").attr("disabled", true);
            $("#btn-dokumen-bantaran").attr("disabled", true);
            $("#btn-batal-reservasi-bantaran").attr("disabled", true);
            $("#btn-print-qr").attr("disabled", true).removeAttr("href");
            
            // Generate Table
            table = $("#tb-rujukan-bantaran").docoTabel({
                filter: true,
                columnDefs: [{
                    orderable: false,
                    className: "select-checkbox",
                    targets: 0
                }],
                select: {
                    style: "single",
                    selector: "tr"
                },
                ordering: false,
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+"pendaftaran/rujukan-bantaran/get-data",
                columns: [
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        defaultContent: "",
                    }, // 0
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    }, // 1
                    {
                        title: tanggalKunjungan,
                        data: "tgl_kunjungan",
                        searchable: true,
                    }, // 2
                    {
                        title: infoPasien,
                        data: "nama_pasien",
                        searchable: true,
                        orderable: false
                    }, // 3
                    {
                        title: uptAsal,
                        data: "uptasal_nama",
                        searchable: true,
                        orderable: false
                    }, // 4
                    {
                        title: instalasiTujuan,
                        data: "instalasi_nama",
                        searchable: false,
                        orderable: false,
                        visible: true
                    }, // 5
                    {
                        title: poliReservasi,
                        data: "ruangan_nama",
                        searchable: false,
                        orderable: false,
                        visible: true
                    }, // 6
                    {
                        title: verifikasiLabel,
                        data: "status_verifikasi_bantaran",
                        searchable: false,
                        orderable: false
                    }, // 7
                    {
                        title: statusLayanan,
                        data: "status_pelayanan_bantaran",
                        searchable: false,
                        orderable: false
                    }, // 8
                    {
                        title: pegawaiVerif,
                        data: "pegawaiverifikasi_id",
                        searchable: false,
                        orderable: false
                    }, // 9
                    {
                        title: "Instalasi",
                        data: "instalasi_id",
                        searchable: true,
                        orderable: false,
                        visible: false
                    }, // 10
                    {
                        title: \'No Rujukan Bantaran\',
                        data: "no_rujukanbantaran",
                        searchable: true,
                        orderable: false,
                        visible: false
                    }, // 11
                ],
                drawCallback: function(e) {

                },
            });

            $(".dataTables_filter").hide();
            
            $(".filter-form").datatableBootstrapFilter(table, [
                [
                    2,
                    "<div class=\'input-group\'><input type=\'text\' id=\'rangeDemoStart\' class=\'form-control startDate\'/><span class=\'input-group-addon\' style=\'border-left: 0; border-right: 0;\'>-</span><input type=\'text\' id=\'rangeDemoFinish\' class=\'form-control endDate\'/><input type=\'text\' style=\'display:none\' class=\'targetDate\' col-index=2></div>"
                ],
                [
                    4,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('upt_asal', '', $masterUpt, ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih UPT —')]))).'\'
                ],
                [
                    10,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_id', '', $masterInstalasi, ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Instalasi —')]))).'\'
                ],
            ],{
                2:0,
                4:1,
                3:2,
                11:3,
                10:4
            }, true);
            
            dateRangeHelper(".startDate",".endDate",".targetDate");
        });

    ', View::POS_END, 'index');
    $this->registerJs("
        const notifications = " . json_encode($notifications) . "
    ", View::POS_END, 'site-page');
    $this->registerJs($this->render('js/index.js'), View::POS_END);
?>
