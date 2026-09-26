<?php
    use app\components\DocoHelpers;
    use yii\helpers\Url;
    use yii\web\View;
    use yii\helpers\Html;

    $visible = false;
    if($param == 'ranap') {
        $visible = true;
    }
?>
<style type="text/css">
.row-nosep {
    background-color: #ff7f00 !important;
    color: #FFFFFF;
    font-weight: bold;
}
</style>
<style>

    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }

    .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
    .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-white">
                <div class="panel-body">
                    <div class="col-md-3" style="padding:0;">
                        <div class="col-md-12"> 
                            Nama Pasien
                        </div>
                        <div id="cetakan-nama_pasien" class="col-md-12"><b></b></div>
                    </div>
                    <div class="col-md-3" style="padding:0;">
                        <div class="col-md-12"> 
                            No Rekam Medik
                        </div>
                        <div id="cetakan-no_rekammedik" class="col-md-12"><b></b></div>
                    </div>
                    <div class="col-md-3" style="padding:0;">
                        <div class="col-md-12"> 
                            No Registrasi
                        </div>
                        <div id="cetakan-no_pendaftaran" class="col-md-12"><b></b></div>
                    </div>
                    <div class="col-md-3" style="padding:0;">
                        <div class="col-md-12"> 
                            Ruangan
                        </div>
                        <div id="cetakan-ruangan" class="col-md-12"><b></b></div>
                    </div>
                    <div class="hidden">
                    <table id="table-daftar-terakhir" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?=Yii::t('fe', 'Nama Pasien')?></th> 
                                <th><?=Yii::t('fe', 'No Rekam Medik')?></th>
                                <th><?=Yii::t('fe', 'No Registrasi')?></th> 
                                <?php if ($param != 'ranap'): ?>
                                    <th><?=Yii::t('fe', 'Poliklinik')?></th> 
                                <?php else: ?>
                                    <th><?=Yii::t('fe', 'Ruangan - Kamar')?></th> 
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <b><h3><?=Yii::t('fe','Cetak')?></h3></b>
            <!-- GLOBAL TOOLBAR IN DAFTAR (RJ, RD, RI) -->
            <?php
                switch ($param) {
                    case 'penunjang':
                        echo Yii::$app->controller->renderPartial('/pendaftaran-penunjang/partial/_cetakantoolbardaftar', 
                        [
                            'param' => $param,
                            'module' => $module,
                        ]);
                        break;
                    case 'rajal':
                        echo Yii::$app->controller->renderPartial('/pendaftaran-rajal/partial/component/_cetakantoolbardaftar', 
                        [
                            'param' => $param,
                            'module' => $module,
                        ]);
                        break;
                    case 'igd':
                        echo Yii::$app->controller->renderPartial('/pendaftaran-igd/partial/_cetakantoolbardaftar', 
                        [
                            'param' => $param,
                            'module' => $module,
                        ]);
                        break;
                    default:
                        echo Yii::$app->controller->renderPartial('/pendaftaran-rajal/partial/component/_globaltoolbardaftar', 
                        [
                            'param' => $param,
                            'module' => $module,
                        ]);
                        break;
                }
            ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', 'Selesai'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php
$this->registerJs('
    var tableDaftarTerakhir;
    var params = "'.$param.'";
    var pendaftaran_id = "'.$pendaftaran_id.'";
    var is_bpjs = "'.$is_bpjs.'";
    var visibleStatus = false;
    if(params == "ranap") {
        visibleStatus = true;
    }

    // Event Ready
    $(document).ready(function() {
        tableDaftarTerakhir = $("#table-daftar-terakhir").docoTabel({
            drawCallback: function(e) {
                var api = this.api();
                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    if(rowData.carabayar_id == 6 && rowData.nosep == null) {
                        $(rowNode).addClass("row-nosep");
                    }
                    else {
                        $(rowNode).removeClass("row-nosep");
                    }
                }
                tableDaftarTerakhir.row(":eq(0)").select();
                var tableData = tableDaftarTerakhir.row(":eq(0)").data();
                $("#cetakan-nama_pasien > b").html(tableData.nama_pasien);
                $("#cetakan-no_rekammedik > b").html(tableData.no_rekam_medik);
                $("#cetakan-no_pendaftaran > b").html(tableData.no_pendaftaran);
                $("#cetakan-ruangan > b").html(tableData.ruangan_nama);
            },
            filter: true,
            //add for handle checkbox
            columnDefs: [ 
            

            ],
            select: {
                style:    "os",
                selector: "tr"
            },
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            oLanguage: {
                sLengthMenu: "'.(\Yii::t('fe', 'dt_length_menu')).'",
                sZeroRecords: "'.(\Yii::t('fe', 'dt_zero_records')).'",
                sEmptyTable: "'.(\Yii::t('fe', 'dt_empty_table')).'",
                sInfoFiltered: "'.(\Yii::t('fe', 'dt_info_filtered')).'",
                sInfoEmpty: "'.(\Yii::t('fe', 'dt_info_empty')).'",
                sInfo: "'.(\Yii::t('fe', 'dt_info')).'",
                oPaginate: {
                    sFirst: "'.(\Yii::t('fe', 'dt_first_page')).'",
                    sPrevious: "'.(\Yii::t('fe', 'dt_previous_page')).'",
                    sNext: "'.(\Yii::t('fe', 'dt_next_page')).'",
                    sLast: "'.(\Yii::t('fe', 'dt_last_page')).'"
                }
            },
            ajax: baseUrl+"pendaftaran/pendaftaran-rajal/get-data-sepuluh-terakhir?param='.$param.'&pendaftaran_id='.$pendaftaran_id.'",
            columns: [
                {title: "'.Yii::t('fe', 'Nama Pasien').'", data: "nama_pasien"},
                {title: "'.Yii::t('fe', 'No Rekam Medik').'", data: "no_rekam_medik"},
                {title: "'.Yii::t('fe', 'No Pendaftaran').'",  data: "no_pendaftaran"}, 
                {title: "'.($param=='ranap' ? Yii::t('fe', 'Ruangan - kamar') : Yii::t('fe', 'Poliklinik')).'", data: "ruangan_nama"},
                {
                    data: "primaryPasien",
                    searchable: false,
                    orderable: false,
                    visible: false,
                }, 
                {
                    data: "primaryPendaftaran",
                    searchable: false,
                    orderable: false,
                    visible: false,
                },

            ]
        });

        $(".dataTables_filter").hide(); 
        $(".dataTables_length").hide(); 
        $(".dataTables_info").hide(); 
        $(".dataTables_paginate").hide();

        if (is_bpjs == "true") {
            $("#btn-print-sep").attr("disabled", false);
        }
    });

    $(document).on("click", "#table-daftar-terakhir tbody tr", function () {

        $("#btn-print-sep").attr("disabled", true);
        $("#btn-create-sep").attr("disabled", true);
        $("#btn-create-sep-manual").attr("disabled", true);
        var bpjs_id = null;
        var carabayar_id = null;
        var pendaftaran_id = null;
        var nama_pasien = null;
        var pasienadmisi_id = null;
        try {
            pendaftaran_id = tableDaftarTerakhir.row(".selected").data().pendaftaran_id;
            nama_pasien = tableDaftarTerakhir.row(".selected").data().nama_pasien;
            bpjs_id = tableDaftarTerakhir.row(".selected").data().bpjs_id ? tableDaftarTerakhir.row(".selected").data().bpjs_id : null;
            carabayar_id = tableDaftarTerakhir.row(".selected").data().carabayar_id ? tableDaftarTerakhir.row(".selected").data().carabayar_id : null;
            pasienadmisi_id = tableDaftarTerakhir.row(".selected").data().pasienadmisi_id;
        } catch (e) {
            bpjs_id = null;
        }

        if (bpjs_id != null ) {
            $("#btn-print-sep").attr("disabled", false);
        }
        
        if (carabayar_id == 6 && bpjs_id == null) {
            var url = $("#btn-create-sep-manual").attr("data-url");
            $("#btn-create-sep").attr("disabled", false);
            $("#btn-create-sep-manual").attr("disabled", false);
            $("#btn-create-sep-manual").attr("data-id", pendaftaran_id);
            $("#btn-create-sep-manual").attr("data-url", url+"&pasienadmisi_id="+pasienadmisi_id+"&pendaftaran_id=" + pendaftaran_id + "&nama_pasien=" + encodeURIComponent(tableDaftarTerakhir.row(".selected").data().nama_pasien) + "-");
        }else{
            $("#btn-create-sep").attr("disabled", true);
            $("#btn-create-sep-manual").attr("disabled", true);
        }
    });

    $(document).on("click",".btn-print-pasien-terakhir",function(e){
        e.preventDefault();
        var tableData = tableDaftarTerakhir.row(":eq(0)").data();
        var btnId = $(this).attr("id");
        if (typeof tableData !== "undefined") {
            if("primaryPasien" in tableData && "primaryPendaftaran" in tableData){
                var target = $(this).attr("data-target");
                var primaryPendaftaran = tableData.primaryPendaftaran;
                var primaryPasien = tableData.primaryPasien;
                var primarySep = tableData.primarySep;
                var carabayar_nama = tableData.carabayar_nama;

                window.open(target+"?pasien_id="+primaryPasien+"&pendaftaran_id="+primaryPendaftaran+"&nosep="+primarySep); 
                $(this).removeClass("btn-info").addClass("btn-primary");
                $("#" + btnId + "> b > i").removeClass("fa fa-print").addClass("fa fa-check");
            }else{
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

', View::POS_END, 'js-modal-cetakan');

?>