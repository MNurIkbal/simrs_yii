<?php
use app\components\DocoHelpers;
use yii\helpers\Url;
use yii\web\View;
?>


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
            <div class="panel-toolbar clearfix">
                <?php
                $param1 = $param == 'ranap' ? 'hidden' : '';
                $param2 = $param != 'ranap' ? 'hidden' : '';
                ?>
                <?=DocoHelpers::generateToolbar([
                        'print-karcis'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Karcis'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-karcis',
                                'class'=>'btn-print-pasien-terakhir ' . $param1,
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-karcis'
                            ]
                        ],
                        'print-status-pasien'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Tracer'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-status-pasien',
                                'class'=>'btn-print-pasien-terakhir',
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-status-pasien'
                            ]
                        ],
                        'print-kartu-pasien'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Kartu Pasien'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-kartu-pasien',
                                'class'=>'btn-print-pasien-terakhir',
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-kartu-pasien'
                            ]
                        ],
                        'print-gelang-pasien'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Gelang Pasien'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-gelang-pasien',
                                'class'=>'btn-print-pasien-terakhir ' . $param2,
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-gelang-pasien'
                            ]
                        ],
                        'print-label-pasien'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Label Pasien'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-label-pasien',
                                'class'=>'btn-print-pasien-terakhir ' . $param2,
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-pasien'
                            ]
                        ],
                        'print-label'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print Label'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-label',
                                'class'=>'btn-print-pasien-terakhir ' . $param1,
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-pasien'
                            ]
                        ],
                        'print-sep'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Print sep'),
                            'icon' => 'fa fa-print',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'btn-print-sep',
                                'class'=>'btn-print-pasien-terakhir',
                                'data-options'=>'click',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/end-point/print-sep',
                                'disabled'=>true
                            ]
                        ],
                    ], "#table-daftar-terakhir");?>
            </div>
            <div class="panel-body">

                <table id="table-daftar-terakhir" class="table table-striped table-condensed table-hover" style="width:100%">
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
    var tableDaftarTerakhir;

    // Event Ready
    $(document).ready(function() {
        tableDaftarTerakhir = $("#table-daftar-terakhir").docoTabel({
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
                {title: "'.Yii::t('fe', 'Tanggal Pendaftaran').'",  data: "tgl_pendaftaran"},
                {title: "'.Yii::t('fe', 'No Pendaftaran').'",  data: "no_pendaftaran"},
                {title: "'.Yii::t('fe', 'No Rekam Medik').'", data: "no_rekam_medik"},
                {title: "'.Yii::t('fe', 'Nama Pasien').'", data: "nama_pasien"},
                {title: "'.Yii::t('fe', 'Umur').'", data: "umur"},
                {title: "'.Yii::t('fe', 'Jenis Kelamin').'", data: "jenis_kelamin"},
                {title: "'.($param=='ranap' ? Yii::t('fe', 'Ruangan - kamar') : Yii::t('fe', 'Poliklinik')).'", data: "ruangan_nama"},
                {title: "'.Yii::t('fe', 'Dokter').'", data: "nama_pegawai"},
                {title: "'.Yii::t('fe', 'Cara Bayar').'", data: "carabayar_nama"},
                {title: "'.Yii::t('fe', 'Penjamin').'", data: "penjamin_nama"},
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

    $(document).on("click", "#table-daftar-terakhir tbody tr", function () {

        $("#btn-print-sep").attr("disabled", true);
        var bpjs_id = null;
        try {
            bpjs_id = tableDaftarTerakhir.row(".selected").data().bpjs_id ? tableDaftarTerakhir.row(".selected").data().bpjs_id : null;
        } catch (e) {
            bpjs_id = null;
        }

        if (bpjs_id != null ) {
            $("#btn-print-sep").attr("disabled", false);
        }
    });

    $(document).on("click",".btn-print-pasien-terakhir",function(e){
        e.preventDefault();
        var tableData = tableDaftarTerakhir.row(".selected").data();
        //console.log(tableData);
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