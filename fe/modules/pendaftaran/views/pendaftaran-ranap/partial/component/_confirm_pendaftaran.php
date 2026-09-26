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
    color: #ffffff;
    padding: 5px 0 5px 10px;
    margin-right: 20px;
}

.square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color: #ffffff;
    padding: 5px 0 5px 10px;
}

.modal_catatanpenting {
    border: 2px solid #e44f4f;
    border-radius: 5px;
    margin-top: 15px;
    margin-bottom: 15px;
    padding: 10px 0;
}

.title-catatanpenting {
    font-size: 21px;
}
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <b>
                <h3><?=Yii::t('fe','Pasien Sudah Didaftarkan')?></h3>
            </b>
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
                <table id="table-daftar-terakhir" class="table table-striped table-condensed table-hover"
                    style="width:100%">
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
    <div id="modal_catatanpenting" class="modal_catatanpenting alert-warning col-md-12" style="display:none;">
        <div class="col-md-1">
            <i class="fa fa-exclamation-circle fa-5x"></i>
        </div>
        <div class="col-md-11">
            <div class="col-md-12">
                <b class="title-catatanpenting"><?=Yii::t('fe','Catatan Penting Pasien')?></b>
            </div>
            <div id="content-catatanpenting" class="col-md-12"></div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', 'Selesai'),['id' => 'btn-confirm-selesai', 'class' => 'btn btn-light btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', 'Edit'),['style' => 'display:none;', 'id' => 'btn-confirm-edit', 'class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    <?=Html::button(\Yii::t('fe', 'Registrasi Ulang'),['class' => 'btn btn-info btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php
$this->registerJs('
    var tableDaftarTerakhir;
    var params = "'.$param.'";
    var jenis = "'.$jenis.'";
    var pendaftaran_id = "'.$pendaftaran_id.'";
    if (params != "ranap" && params != "penunjang") {
        extUrl = "pendaftaran/daftar-ranap/get-data-sepuluh-terakhir?param=&pendaftaran_id='.$pendaftaran_id.'";
    } else {
        extUrl = "pendaftaran/daftar-ranap/get-data-sepuluh-terakhir?param='.$param.'&pendaftaran_id='.$pendaftaran_id.'"
    }
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
                if (jenis == params) {
                    $("#btn-confirm-edit").show();
                }

                if(tableData.catatanpenting_pasien) {
                    $("#modal_catatanpenting").show();
                    $("#content-catatanpenting").html(tableData.catatanpenting_pasien);
                }
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
            ajax: baseUrl+extUrl,
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
                {
                    data: "catatanpenting_pasien",
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
        if (typeof tableData !== "undefined") {
            if("primaryPasien" in tableData && "primaryPendaftaran" in tableData){
                var target = $(this).attr("data-target");
                var primaryPendaftaran = tableData.primaryPendaftaran;
                var primaryPasien = tableData.primaryPasien;
                var primarySep = tableData.primarySep;
                var carabayar_nama = tableData.carabayar_nama;

                window.open(target+"?pasien_id="+primaryPasien+"&pendaftaran_id="+primaryPendaftaran+"&nosep="+primarySep); 
            }else{
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

    $(document).on("click","#btn-confirm-selesai",function(e){
        e.preventDefault();
        location.reload();
    });

    $(document).on("click","#btn-confirm-edit",function(e){
        e.preventDefault();
        $("#pendaftaran_id_hidden").val(pendaftaran_id).trigger("change");
    });

', View::POS_END, 'js-confirm-pendaftaran');

?>