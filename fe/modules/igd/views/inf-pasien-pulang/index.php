<?php

/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-08-15 17:29:55
 * @Description:
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = Yii::t('fe', 'Informasi Pasien Pulang Rawat Darurat');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat Darurat'), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$this->registerCss('
    .DTFC_LeftBodyWrapper{
        width: 0px;
    }
');
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
                    <h3 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien Pulang').' '.Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'batal-pulang' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Batal pulang'),
                            'icon' => 'fa fa-close',
                            'attributes' => [
                                'id' => 'btn-batal-pulang',
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-url' => '/igd/inf-pasien-pulang/batal-periksa?pendaftaran_id=',
                                'data-requiredchecked' => true,
                            ],
                        ],
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Lihat Pemeriksaan'),
                        'icon' => 'fa fa-stethoscope',

                        'attributes' => [
                            'data-target'=> '/igd/pemeriksaan-igd/periksa?state=pulang&id=',
                            // 'disabled'=>'true',
                        ]
                    ],
                    // 'rincian' => [
                    //     'title' => Yii::t('fe', 'Rincian Tagihan'),
                    //     'icon' => 'fa fa-file-pdf-o',
                    //     'attributes' => [
                    //         'id'=>'cetak-rincian-tagihan',
                    //         'data-options' => 'link',
                    //         'class'=>'spa',
                    //         'data-target' => '/igd/inf-pasien-pulang/export-pdf-rincian-tagihan?pendaftaran_id=',
                    //         'disabled'=>'true',
                    //         'target'=>'_blank',
                    //         'data-requiredchecked' => true,
                    //     ]
                    // ],
                    'pdf',
                    'excel',
                    'surat-kematian' => [
                        'title' => Yii::t('fe', 'Surat Kematian'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id'=>'cetak-surat-kematian',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'data-target' => '/igd/inf-pasien-pulang/export-pdf-surat-kematian?pendaftaran_id=',
                            'disabled'=>'true',
                            'target'=>'_blank',
                            'data-requiredchecked' => true,
                        ]
                    ],
                    // 're-sync'=>[
                    //     'type'=>'button',
                    //     'title' => \Yii::t('fe', 'Sinkronisasi'),
                    //     'icon' => 'fa fa-refresh',
                    //     'method' => 'not-exist',
                    //     'attributes' => [
                    //         'id'=>'btn-sync',
                    //         'data-options'=>'click',
                    //         'disabled' => 'true',
                    //         'data-target'=> '/igd/inf-pasien-pulang/resync',
                    //     ]
                    // ],
                ], '#informasi-pasien-pulang');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 advanced-filter filter-form"></div>
                </div>
                <div class="legend-index">
                        <div class="col-md-12">
                        <div class="legend-header">Keterangan</div>
                        <div class="legend-wrapper">
                            <div class="legend-information">
                                <div class="legend-information__color" style="background-color: #ffc0cb"></div>
                                <div class="legend-information__text">Belum Lunas</div>
                            </div>
                        </div>
                        </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable"
                    id="informasi-pasien-pulang"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Tanggal pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Tanggal pulang')?></th>
                            <th><?=Yii::t('fe', 'No pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No rekam medik')?></th>
                            <th><?=Yii::t('fe', 'Nomor Telepon')?></th>
                            <th><?=Yii::t('fe', 'Nama pasien')?></th>
                            <th><?=Yii::t('fe', 'Ruangan')?></th>
                            <th><?=Yii::t('fe', 'L/P')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Cara Bayar')?></th>
                            <th><?=Yii::t('fe', 'Dokter')?></th>
                            <th><?=Yii::t('fe', 'Cara pulang')?></th>
                            <th><?=Yii::t('fe', 'Dipulangkan Oleh')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="clearfix">
    <!-- Modal -->
    <?php
       /* Modal::begin([
            'header' => '<h4>'.\Yii::t("fe", "Konfirmasi Dokter").'</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
        ]);
        echo "
            <div id='modalContent'>
                <div class='row'>
                    <div class='col-md-12'>
                        ".Html::dropDownList(
                            'nama_pegawai',
                            '',
                            ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'),
                            [
                                'class' => 'form-control select2',
                                'id' => 'dd-pegawai',
                                'prompt' => \Yii::t('fe', '— pilih dokter--'),
                            ]
                        )."
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        ".Html::hiddenInput('temp-pendaftaran', null, ['id' => 'temp-pendaftaran', 'read-only' => 'read-only'])."
                        ".Html::hiddenInput('temp-pegawai', null, ['id' => 'temp-pegawai', 'read-only' => 'read-only'])."
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        ".Html::button(Yii::t('fe', 'Pilih Dokter'), ['class' => 'btn btn-primary btn-update-pegawai'])."
                    </div>
                </div>
            </div>
        ";
        Modal::end();*/
    ?>

    <style type="text/css">
        .inline-row {
          display: inline-block;
          margin: 0;
          color: red;
        }
    </style>
</div>




<?php
$this->registerJs('
    // Event Ready
    // Generate Table
    const cetakRincian = "/igd/inf-pasien-pulang/export-pdf-rincian-tagihan?pendaftaran_id=";
    const cetakSuratKematian = "/igd/inf-pasien-pulang/export-pdf-surat-kematian?pendaftaran_id=";

    var table = $("#informasi-pasien-pulang").docoTabel({
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        sorting: [[3, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,

        ajax: baseUrl+"igd/inf-pasien-pulang/get-data",
        columns: [
            {
                title: "",
                data: null,
                defaultContent: "",
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", data: "tgl_pendaftaran"},
            {title: "'.(\Yii::t("fe", "Tanggal Pulang")).'", data: "tglpasienpulang"},
            {title: "'.(\Yii::t("fe", "Nomor Pendaftaran")).'", data: "no_pendaftaran"},
            {title: "'.(\Yii::t("fe", "Nomor Rekam Medik")).'", data: "no_rekam_medik"},
            {title: "'.(\Yii::t("fe", "Nomor Telepon")).'", data: "no_telepon_pasien"},
            {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien"},
            {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama"},
            {title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", data: "jenis_kelamin"},
            {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama"},
            {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama",searchable:true}, //11
            {title: "'.(\Yii::t("fe", "Dokter")).'", data: "dokter"},
            {title: "'.(\Yii::t("fe", "Cara Pulang")).'", data: "carakeluar_nama"},
            {title: "'.(\Yii::t("fe", "Dipulangkan Oleh")).'", data: "petugas_pemulang_nama"},
        ],
        fixedColumns:   {
            rightColumns: 0,
        },
        language: {
            lengthMenu: "'.(\Yii::t("fe", "Menampilkan")).' _MENU_ '.(\Yii::t("fe", "data perhalaman")).'",
        },
        fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            if(aData.status_bayar === '.DocoConstants::STAT_BAYAR_BLM_LUNAS.'){
                $("td", nRow).css("background-color", "#ffc0cb");
            }
            if(aData.is_sync === false){
                $("td", nRow).css("background-color", "#fdfd96");
            }
        }
    });
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            2,
            \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
        ],
        [
            3,
            \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart2" class="form-control startDate2" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish2" class="form-control endDate2" /><input type="text" style="display:none" class="targetDate2" col-index=2 readonly="true"></div>\'
        ],
        [4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_pendaftaran', '', ['class' => 'form-control','placeholder'=>'No Pendaftaran']))).'\'],
        [5, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_rekam_medik', '', ['class' => 'form-control','placeholder'=>'No Rekam Medik']))).'\'],
        [6, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_telepon_pasien', '', ['class' => 'form-control','placeholder'=>'No Telepon']))).'\'],
        [7, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_pasien', '', ['class' => 'form-control','placeholder'=>'Nama Pasien']))).'\'],
        [8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', $dataRuangan, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '— Pilih Ruangan —')]))).'\'],
        [9, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_kelamin', '', $dataJenisKelamin, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '— pilih Jenis Kelamin —')]))).'\'],
        [10, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('penjamin_nama', '', $dataPenjamin, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '— Pilih Penjamin —')]))).'\'],
        [11, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_nama', '', $dataCaraBayar, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--')]))).'\'],
        [12, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('dokter', '', $dataDokter, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '— Pilih Dokter —')]))).'\'],
        [13, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carakeluar_nama', '', $dataCaraKeluar, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '— Pilih Cara Pulang —')]))).'\'],
    ],{
        2:0,
        3:1,
        4:2,
        5:3,
        6:4,
        7:5,
        8:6,
        9:7,
        10:8,
        11:9,
        13:10,
        14:11,
    });
    $(".dataTables_filter").hide();
    dateRangeHelper(".startDate",".endDate",".targetDate");
    dateRangeHelper(".startDate2",".endDate2",".targetDate2");

    $(".select2", $("form.form-filter")).change(function (event) {
        event.preventDefault();
        table.reload();
    });

    $("form.form-filter").on("submit", function (e) {
        e.preventDefault();
        table.reload();
    });

    // Tooltip
    $("table").tooltip({selector: "[data-tooltip=tooltip]"});

    $(document).ready(function() {
        $("input[name=tgl_pendaftaran]").val(" - ");

        $("#informasi-pasien-pulang").on("draw.dt", function () {
            resetDefaultToolbar();
        });

        $("#informasi-pasien-pulang tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
                caraKeluar = table.row(".selected").data().carakeluar_nama ? table.row(".selected").data().carakeluar_id : null;
                status_bayar = table.row(".selected").data().carabayar_id ? table.row(".selected").data().status_bayar : null;
                stat_bayar = table.row(".selected").data().carabayar_id ? table.row(".selected").data().stat_bayar : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                $("#btn-batal-pulang").attr("action",$("#btn-batal-pulang").data("url")+primaryKey);
                if (status_bayar === '.DocoConstants::STAT_BAYAR_BLM_LUNAS.'){
                    $("#btn-batal-pulang").attr("disabled", false);
                }else {
                    $("#btn-batal-pulang").attr("disabled", true);
                }
                $("#cetak-rincian-tagihan").attr("data-target",cetakRincian+primaryKey);
                $("#cetak-rincian-tagihan").attr("disabled", false);

                if (caraKeluar === '.DocoConstants::CARA_KELUAR_MENINGGAL.'){
                    $("#cetak-surat-kematian").attr("data-target",cetakSuratKematian+primaryKey);
                    $("#cetak-surat-kematian").attr("disabled", false);
                } else {
                    $("#cetak-surat-kematian").attr("disabled", true);
                }
            } else {
                $("#btn-batal-pulang").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }

        });

    });

    $("#informasi-pasien-pulang tbody").on("click", "tr", function(e){
        e.preventDefault();
        var tableData = table.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if("primary" in tableData ){
                $("#cetak-rincian-tagihan").attr("disabled", false);

                if(tableData.carakeluar_nama == "DIRAWAT" && tableData.stat_bayar == "Belum Lunas" && (tableData.status_ranap == '.DocoConstants::STATUS_RANAP_BATAL_RAWAT.' || tableData.status_ranap == null)) {
                    $("#btn-batal-pulang").addClass("btn-toolbar");
                } else if (tableData.carakeluar_nama != "DIRAWAT" && tableData.stat_bayar == "Belum Lunas"){
                    $("#btn-batal-pulang").addClass("btn-toolbar");
                } else {
                    $("#btn-batal-pulang").removeClass("btn-toolbar");
                }
            }else{
                $("#btn-batal-pulang").removeClass("btn-toolbar");
            }
        }else{
            $("#btn-batal-pulang").addClass("btn-toolbar");

            resetDefaultToolbar();
        }

        // case resync

        if(tableData.is_sync == false){
            $("#btn-sync").prop("disabled", false);
        } else {
            $("#btn-sync").prop("disabled", true);
        }
    });


    $("#btn-batal-pulang").on("click", function(e){
        e.preventDefault();
        if(!$("#btn-batal-pulang").hasClass("btn-toolbar")) {
            var tableData = table.row(".selected").data();
            var message = "";
            if(tableData.carakeluar_nama == "DIRAWAT") {
                if (tableData.status_ranap != '.DocoConstants::STATUS_RANAP_BATAL_RAWAT.' && tableData.status_ranap != null) {
                    message += "Pasien sudah melakukan pendaftaran, silahkan melakukan batal kunjungan terlebih dahulu. ";
                }

                if(tableData.stat_bayar == "Lunas") {
                    message += "Pasien ini sudah melakukan pembayaran dan lunas. ";
                }
            } else {
                message += "Pasien ini sudah melakukan pembayaran dan lunas. ";
            }
            docoNotification("warning", "Peringatan", message);
        }
    });

    $(".daterange").daterangepicker({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    $(".date").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    function resetDefaultToolbar(){
        // semua button yang memiliki data required checked akan didisabled
        $(".panel-toolbar button[data-requiredchecked]").attr("disabled", true);
    }


', View::POS_END, 'b-index');
?>
