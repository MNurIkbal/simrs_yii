<?php
    use app\components\DocoHelpers;
    use yii\helpers\Url;
    use yii\web\View;
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
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="panel-title">
                    <b><h3><?=Yii::t('fe','10 Pasien Terakhir Yang Mendaftar')?></h3></b>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>


            <!-- GLOBAL TOOLBAR IN DAFTAR (RJ, RD, RI) -->
            <?php
            echo Yii::$app->controller->renderPartial('/daftar/partial/component/_globaltoolbardaftar', 
            [
                'param' => $param,
                'module' => $module,
            ]);
            ?>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#ff7f00;'></span>No SEP Belum di Buat</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="table-daftar-terakhir-ranap" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead> 
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=Yii::t('fe', 'Tanggal Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No Rekam Medik')?></th>
                            <th><?=Yii::t('fe', 'Nama Pasien')?></th>
                            <th><?=Yii::t('fe', 'Umur')?></th>
                            <th><?=Yii::t('fe', 'Jenis Kelamin')?></th>
                            <?php if ($param != 'ranap'): ?>
                                <th><?=Yii::t('fe', 'Poliklinik')?></th>
                            <?php else: ?>
                                <th><?=Yii::t('fe', 'Ruangan - Kamar')?></th>
                            <?php endif; ?>
                            <th><?=Yii::t('fe', 'Dokter')?></th>
                            <th><?=Yii::t('fe', 'Cara Bayar')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'No SEP')?> </th>
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
</div>

<?php
$this->registerJs('
    var tableDaftarTerakhirRanap;
    // Event Ready
    $(document).ready(function() {
        tableDaftarTerakhirRanap = $("#table-daftar-terakhir-ranap").docoTabel({
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
            },
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
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
            ajax: baseUrl+"pendaftaran/daftar/get-data-sepuluh-terakhir?param="+$(".params-header").val(),
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.Yii::t('fe', 'Tanggal Pendaftaran').'",
                    data: "tgl_pendaftaran"
                },
                {
                    title: "'.Yii::t('fe', 'No Pendaftaran').'",
                    data: "no_pendaftaran"

                },
                {
                    title: "'.Yii::t('fe', 'No Rekam Medik').'", 
                    data: "no_rekam_medik"
                },
                {
                    title: "'.Yii::t('fe', 'Nama Pasien').'", 
                    data: "nama_pasien"
                },
                {
                    title: "'.Yii::t('fe', 'Umur').'", 
                    data: "umur"
                },
                {
                    title: "'.Yii::t('fe', 'Jenis Kelamin').'", 
                    data: "jenis_kelamin"
                },
                {
                    title: "'.($param=='ranap' ? Yii::t('fe', 'Ruangan - kamar') : Yii::t('fe', 'Poliklinik')).'", 
                    data: "ruangan_nama"
                },
                {
                    title: "'.Yii::t('fe', 'Dokter').'", 
                    data: "nama_pegawai"
                },
                {
                    title: "'.Yii::t('fe', 'Cara Bayar').'", 
                    data: "carabayar_nama"
                },
                {
                    title: "'.Yii::t('fe', 'Penjamin').'", 
                    data: "penjamin_nama"
                },
                {
                    title: "'.Yii::t('fe', 'No SEP').'", 
                    data: "nosep"
                },
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
    });

    $(document).on("click", "#table-daftar-terakhir-ranap tbody tr", function () {
        $("#btn-print-sep").attr("disabled", true);
        $("#btn-create-sep").attr("disabled", true);
        $("#btn-create-sep-manual").attr("disabled", true);
        var bpjs_id = null;
        var carabayar_id = null;
        var pendaftaran_id = null;
        var nama_pasien = null;
        var pasienadmisi_id = null;

        try {
            bpjs_id = tableDaftarTerakhirRanap.row(".selected").data().bpjs_id ? tableDaftarTerakhirRanap.row(".selected").data().bpjs_id : null;
            pendaftaran_id = tableDaftarTerakhirRanap.row(".selected").data().pendaftaran_id;
            nama_pasien = tableDaftarTerakhirRanap.row(".selected").data().nama_pasien;
            carabayar_id = tableDaftarTerakhirRanap.row(".selected").data().carabayar_id ? tableDaftarTerakhirRanap.row(".selected").data().carabayar_id : null;
            pasienadmisi_id = tableDaftarTerakhirRanap.row(".selected").data().pasienadmisi_id;
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
            
            $("#btn-create-sep-manual").attr("data-url", url+"&pasienadmisi_id="+pasienadmisi_id+"&pendaftaran_id=" + pendaftaran_id + "&nama_pasien=" + encodeURIComponent(tableDaftarTerakhirRanap.row(".selected").data().nama_pasien) + "-");
        }else{
            $("#btn-create-sep").attr("disabled", true);
            $("#btn-create-sep-manual").attr("disabled", true);
        }
    });

    $(document).on("click",".btn-print-pasien-terakhir",function(e){
        e.preventDefault();
        var tableData = tableDaftarTerakhirRanap.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if("primaryPasien" in tableData && "primaryPendaftaran" in tableData){
                var target = $(this).attr("data-target");
                var primaryPendaftaran = tableData.primaryPendaftaran;
                var primaryPasien = tableData.primaryPasien;
                var carabayar_nama = tableData.carabayar_nama;

                window.open(target+"?pasien_id="+primaryPasien+"&pendaftaran_id="+primaryPendaftaran);
            }else{
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

', View::POS_END, 'js-daftar-terakhir');

?>
