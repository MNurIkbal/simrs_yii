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

    .text-required {
        color: red;
    }

    #btn_batal_penolakan, 
    #btn_batal_penolakan:hover, 
    #btn_batal_penolakan:focus {
        background-color: #CF212A !important;
        border-color: #CF212A !important;
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
                    'daftarkan' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Daftarkan'),
                        'icon' => 'fa fa-plus',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-daftarkan-bantaran',
                            'class' => 'btn-daftarkan-bantaran',
                            'data-options' => 'click',
                            'data-target' => '/pendaftaran/daftar/index?pendaftaranol_id=',
                        ]
                    ],
                    'batal_reservasi' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal Reservasi'),
                        'icon' => 'fa fa-ban',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-batal-reservasi-bantaran',
                            'class' => 'btn-batal-reservasi-bantaran',
                            'data-options' => 'click',
                            'data-target' => '#'
                        ]
                    ],
                    'kirim_dokumen' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Kirim Dokumen'),
                        'icon' => 'fa fa-file-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-dokumen-bantaran',
                            'class' => 'btn-dokumen-bantaran',
                            'data-toggle'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-width' => '60%',
                            'data-options' => 'modal',
                            'data-url' => '/pendaftaran/rujukan-bantaran/kirim-dokumen?id='
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
                            <th><?= Yii::t("fe", "No. Antrian") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Kunjungan") ?></th>
                            <th><?= Yii::t("fe", "Info Pasien") ?></th>
                            <th><?= Yii::t("fe", "UPT Asal") ?></th>
                            <th><?= Yii::t("fe", "Instalasi Tujuan") ?></th>
                            <th><?= Yii::t("fe", "Poli Reservasi") ?></th>
                            <th><?= Yii::t("fe", "Verifikasi") ?></th>
                            <th><?= Yii::t("fe", "Pegawai Verifikasi") ?></th>
                            <th><?= Yii::t("fe", "Status Pelayanan") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div id="modal_penolakan" class="modal">
    <div class="modal-dialog modal-xl" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    <label>Keterangan Penolakan Rujukan <span class="text-required">*</span></label>
                    <input type="hidden" id="post_bantaran_id">
                    <textarea class="form-control" id="form_keterangan_penolakan"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info btn-labeled btn-xs" id="btn_simpan_penolakan">
                    <b>
                        <i class="fa fa-check"></i>
                    </b>
                    Simpan
                </button>
                <button type="button" class="btn btn-info btn-labeled btn-xs" id="btn_batal_penolakan">
                    <b>
                        <i class="fa fa-ban"></i>
                    </b>
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs('
        var no = "'.(\Yii::t("fe", "No")).'";
        var noAntrian = "'.(\Yii::t("fe", "No. Antrian")).'";
        var tanggalKunjungan = "'.(\Yii::t("fe", "Tanggal Kunjungan")).'";
        var infoPasien = "'.(\Yii::t("fe", "Info Pasien")).'";
        var uptAsal = "'.(\Yii::t("fe", "UPT Asal")).'";
        var instalasiTujuan = "'.(\Yii::t("fe", "Instalasi Tujuan")).'";
        var poliReservasi = "'.(\Yii::t("fe", "Poli Reservasi")).'";
        var verifikasiLabel = "'.(\Yii::t("fe", "Verifikasi")).'";
        var pegawaiVerif = "'.(\Yii::t("fe", "Pegawai Verifikasi")).'";
        var statusLayanan = "'.(\Yii::t("fe", "Status Pelayanan")).'";

        $(document).ready(function() {
            $("#btn-detail-bantaran").attr(\'disabled\', true);
            $("#btn-verifikasi-bantaran").attr(\'disabled\', true);
            $("#btn-daftarkan-bantaran").attr(\'disabled\', true);
            $("#btn-batal-reservasi-bantaran").attr(\'disabled\', true);
            $("#btn-dokumen-bantaran").attr(\'disabled\', true);
            
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
                        title: noAntrian,
                        data: "no_antrian",
                        searchable: false,
                        orderable: false
                    }, // 2
                    {
                        title: tanggalKunjungan,
                        data: "tgl_kunjungan",
                        searchable: true,
                    }, // 3
                    {
                        title: infoPasien,
                        data: "nama_pasien",
                        searchable: true,
                        orderable: false
                    }, // 4
                    {
                        title: uptAsal,
                        data: "uptasal_nama",
                        searchable: true,
                        orderable: false
                    }, // 5
                    {
                        title: instalasiTujuan,
                        data: "instalasi_nama",
                        searchable: false,
                        orderable: false,
                        visible: true
                    }, // 6
                    {
                        title: poliReservasi,
                        data: "ruangan_nama",
                        searchable: false,
                        orderable: false,
                        visible: true
                    }, // 7
                    {
                        title: verifikasiLabel,
                        data: "status_verifikasi_bantaran",
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
                        title: statusLayanan,
                        data: "status_pelayanan_bantaran",
                        searchable: false,
                        orderable: false
                    }, // 10
                    {
                        title: "Instalasi",
                        data: "instalasi_id",
                        searchable: true,
                        orderable: false,
                        visible: false
                    }, // 11
                    {
                        title: \'Ruangan\',
                        data: "ruangan_id",
                        searchable: false,
                        orderable: false,
                        visible: false
                    }, // 12
                    {
                        title: \'No Reservasi\',
                        data: "no_pendaftaranol",
                        searchable: true,
                        orderable: false,
                        visible: false
                    }, // 13
                ],
                drawCallback: function(e) {

                },
            });

            $(".dataTables_filter").hide();
            
            $(".filter-form").datatableBootstrapFilter(table, [
                [
                    3,
                    "<div class=\'input-group\'><input type=\'text\' id=\'rangeDemoStart\' class=\'form-control startDate\'/><span class=\'input-group-addon\' style=\'border-left: 0; border-right: 0;\'>-</span><input type=\'text\' id=\'rangeDemoFinish\' class=\'form-control endDate\'/><input type=\'text\' style=\'display:none\' class=\'targetDate\' col-index=2></div>"
                ],
                [
                    5,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('upt_asal', '', $masterUpt, ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih UPT —')]))).'\'
                ],
                [
                    11,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_id', '', $masterInstalasi, ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Instalasi —')]))).'\'
                ],
            ],{
                3:0,
                5:1,
                4:2,
                13:3,
                11:4
            }, true);
            
            dateRangeHelper(".startDate",".endDate",".targetDate");
        });

    ', View::POS_END, 'index');

    $this->registerJs($this->render('js/index.js'), View::POS_END);
?>
