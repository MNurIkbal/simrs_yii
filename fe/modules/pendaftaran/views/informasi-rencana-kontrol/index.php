<?php

/**
 * @Author: Naufal
 * @Date:   2018-01-18 16:11:07
 * @Description:  View Informasi Rencana Kontrol
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', 'Rencana Kontrol Pasien');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/pendaftaran/dashboard']];
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'approve' => [
                            'title' => \Yii::t('fe', 'Setujui'),
                            'icon' => 'fa fa-send',
                            'attributes' => [
                                'action' => '/pendaftaran/informasi-rencana-kontrol/approve?id=',
                                'class' => 'btn-aksi',
                                'data-options' => 'click',
                                'data-type' => 'approve',
                                'id' => 'btn-approve',
                            ]
                        ],
                    'proses' => [
                        'title' => \Yii::t('fe', 'Daftarkan'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'id'   => 'btn-proses',
                            'data-target' => Url::to(['proses', 'id' => '']),
                            'data-conditions' => 'state',
                            'data-options' => 'click'
                        ]
                    ],
                    'export-excel' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Unduh Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'pendaftaran/informasi-rencana-kontrol/show-popup-excel?',
                            'data-width' => '75%',
                        ]
                    ],
                ], '#table-rencanakontrol'); ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">

                </div>
                <div class="panel-body">
                    <table id="table-rencanakontrol" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="5%"></th>
                                <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                                <th><?=Yii::t('fe', 'Tanggal pendaftaran')?></th>
                                <th><?=Yii::t('fe', 'Tanggal Rencana Kontrol')?></th>
                                <th><?=Yii::t('fe', 'Pasien')?></th>
                                <th><?=Yii::t('fe', 'No Telepon')?></th>
                                <th><?=Yii::t('fe', 'Poliklinik Asal')?></th>
                                <th><?=Yii::t('fe', 'Poliklnik Tujuan')?></th>
                                <th><?=Yii::t('fe', 'Cara Bayar / Penjamin')?></th>
                                <th><?=Yii::t('fe', 'Jenis')?></th>
                                <th><?=Yii::t('fe', 'Status')?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/_rencanakontrol.js'));
$this->registerJs('
    let id = ""
     // Event Disetujui
    $(document).on("click", ".data-setuju", function (e) {
        e.preventDefault();
        var action = $(this).attr("action");

        $(this).docoForm("delete",{
            url: action,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                console.log(data)
                table.draw();
                window.location.replace("/pendaftaran/daftar-rajal/update?id="+data.response.id);
            }
        });
        return false;
    });

    // Event Ditolak
    $(document).on("click", ".data-ditolak", function (e) {
        e.preventDefault();
        var action = $(this).attr("action");

        $(this).docoForm("delete",{
            url: action,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                table.draw();
            }
        });
        return false;
    });

    // Event Batal
    $(document).on("click", ".data-batal", function (e) {
        e.preventDefault();
        var action = $(this).attr("action");

        $(this).docoForm("delete",{
            url: action,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                table.draw();
            }
        });
        return false;
    });

    $(document).on("click", "#btn-proses", function(e){
        e.preventDefault();
        window.location.replace(baseUrl+`pendaftaran/daftar-rajal?janji_id=${id}`);
        return false;
    });

    $(document).on("click", "#btn-approve", function(){
            var data = table.row(".selected").data();
            if(typeof data !== "undefined"){
                var primary = data.primary;
                    target = $(this).attr("action");
                    type = $(this).attr("data-type");
                $(this).docoForm("click", {
                    url: target+primary,
                    confirmMessage: "Apakah anda yakin untuk menerima konsul pasien ini ?",
                    success: function(res){
                        if(type == "setujui"){
                            console.log("setujui");
                        }else{
                            table.draw();
                        }
                    }
                })
            }
        });

    $(document).on("click", "#table-rencanakontrol tbody tr", function () {
        let status_konsul = null;
        if(table.row(".selected").length){
            id = table.row(".selected").data().primary;
            status_konsul = table.row(".selected").data().status_approve;
        }
        if (status_konsul == 564) {
            $("#btn-proses").prop("disabled", false);
            $("#btn-approve").prop("disabled", false);
        }else{
            $("#btn-proses").prop("disabled", true)
            $("#btn-approve").prop("disabled", true);
        }    
    })

    // Event Ready
    $(document).ready(function() {
        $("#btn-proses").prop("disabled", true);
        $("#btn-approve").prop("disabled", true);
        // Generate Table
        table = $("#table-rencanakontrol").docoTabel({
            filter: true,
            columnDefs: [{
                orderable: false,
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            /*stateSave: true,*/
            scrollX: true,
            ajax: baseUrl+"pendaftaran/informasi-rencana-kontrol/get-data",
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
                    title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", 
                    data: "tgl_pendaftaran"
                }, //2
                {
                    title: "'.(\Yii::t("fe", "Tanggal Rencana Kontrol")).'", 
                    data: "tgl_jadwal"
                }, //3 
                {
                    title: "'.(\Yii::t("fe", "Pasien")).'",  
                    data: "pasien", 
                    searchable: false, 
                    orderable:false
                }, //4
                {
                    title: "'.(\Yii::t("fe", "No Telepon")).'",  
                    data: "no_telepon_pasien", 
                    searchable: false, 
                    orderable:false
                }, //5
                {
                    title: "'.(\Yii::t("fe", "Poliklink Asal")).'", 
                    data: "poli_asal", 
                    searchable: false, 
                    orderable:false
                }, //6
                {
                    title: "'.(\Yii::t("fe", "Poliklink Tujuan")).'", 
                    data: "poli_tujuan" , 
                    searchable: false, 
                    orderable:false
                }, //7
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'", 
                    data: "pembayaran", 
                    searchable: false
                }, //8
                {
                    title: "'.(\Yii::t("fe", "Jenis")).'", 
                    data: "is_konsul", 
                    searchable: false
                }, //9
                {
                    title: "'.(\Yii::t("fe", "Status Konsul")).'", 
                    data: "approval",
                }, //10
                {
                    title: "'.(\Yii::t("fe", "Poliklinik Tujuan")).'", 
                    data: "ruangan_id", 
                    visible: false
                }, //11
                {
                    title: "'.(\Yii::t("fe", "No Pendaftaran")).'", 
                    data: "no_pendaftaran", 
                    visible: false
                }, //12
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medis")).'", 
                    data: "no_rekam_medik", visible: false
                }, //13
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien")).'", 
                    data: "nama_pasien", 
                    visible: false
                }, //14
                {
                    title: "'.(\Yii::t("fe", "Poliklinik Asal")).'", 
                    data: "asalpoliklinikkonsul_id", 
                    visible: false
                }, //15
                {
                    title: "'.(\Yii::t("fe", "Jenis")).'", 
                    data: "transaksi_konsul", 
                    visible: false
                }, //16
                {
                    title: "'.(\Yii::t("fe", "Dokter Asal")).'", 
                    data: "doktermengkonsul_id", 
                    visible: false
                }, //17
                {
                    title: "'.(\Yii::t("fe", "Dokter Tujuan")).'", 
                    data: "pegawai_id", 
                    visible: false
                }, //18
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
                [2, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate dateStart1" value="' . date('d-M-Y') . '" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate dateEnd1" value="' . date('d-M-Y') . '" /><input type="text" style="display:none" class="targetDate dateTarget1" col-index=2></div>\'
                ],
                [3, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStartOp" class="form-control startDateOp"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishOp" readonly="true" class="form-control endDateOp"/><input type="text" style="display:none" class="targetDateOp" col-index=2></div>\'
                ],
                [13, 
                \'<input type="text" id="no_rekam_medik" class="form-control" value="" maxlength="6" />\'
                ],
                [
                   10,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'status_approve',
                                '',
                                $status,
                                [
                                    'class' => 'form-control select2',
                                    'prompt' => \Yii::t('fe', '— Pilih —'),
                                ]
                            )
                        )
                    ).'\'
                ], 
                [
                   16,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'transaksi_konsul',
                                '',
                                $jenis,
                                [
                                    'class' => 'form-control select2',
                                    'prompt' => \Yii::t('fe', '— Pilih —'),
                                ]
                            )
                        )
                    ).'\'
                ], 
                [
                   15,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'asalpoliklinikkonsul_id',
                                '',
                                $ruangan,
                                [
                                    'class' => 'form-control select2',
                                    'prompt' => \Yii::t('fe', '— Pilih —'),
                                ]
                            )
                        )
                    ).'\'
                ], 
                [
                   11,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'ruangan_id',
                                '',
                                $ruangan,
                                [
                                    'class' => 'form-control select2',
                                    'prompt' => \Yii::t('fe', '— Pilih —'),
                                ]
                            )
                        )
                    ).'\'
                ], 
                [
                   17,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'doktermengkonsul_id',
                                '',
                                $ruangan,
                                [
                                    'class' => 'form-control select2 dokterasal',
                                    'prompt' => \Yii::t('fe', '— Pilih —'),
                                ]
                            )
                        )
                    ).'\'
                ], 
                [
                   18,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'pegawai_id',
                                '',
                                $ruangan,
                                [
                                    'class' => 'form-control select2 dokterasal',
                                    'prompt' => \Yii::t('fe', '— Pilih —'),
                                ]
                            )
                        )
                    ).'\'
                ], 
            ],{2:0, 12:1, 13:2, 14:3, 3:4, 15:5, 11:6, 10:7, 16:8, 17:9}, true)
            dateRangeHelper(".dateStart1",".dateEnd1",".dateTarget1");
            dateRangeHelper(".startDateOp",".endDateOp",".targetDateOp");    

            $(".dokterasal").select2({
                placeholder: "-- Pilih Dokter --",
                minimumInputLength: 3,  
                ajax: {
                    url: "informasi-rencana-kontrol/search-dokter",
                    quietMillis: 250,
                    data: function(term, page){
                        var query = {
                            search: term,
                        }

                        return query;
                    },           
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            });
    });

', View::POS_END, 'b-index');
?>
