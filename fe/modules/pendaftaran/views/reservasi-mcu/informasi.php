<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .no-width{
        width: 19px
    }
</style>
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
              
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    // 'print',
                    'pdf'=>[
                        'attributes'=>[
                            'data-target'=>Url::home().'pendaftaran/reservasi-mcu/export-pdf?'
                        ],
                    ],
                    'excel'=>[
                        'attributes'=>[
                            'data-target'=>Url::home().'pendaftaran/reservasi-mcu/export-excel?'
                        ]
                    ],
                    'setujui'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Setujui'),
                        'icon' => 'fa fa-check',
                        'attributes'=>[
                            'id'=>'data-setujui',
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url'=>'/pendaftaran/reservasi-mcu/setujui?reservasimcu_id=',
                            'disabled'=>false
                        ]
                    ],
                    'tolak' => [
                        'title' => \Yii::t('fe', 'Tolak'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'action' => '/pendaftaran/reservasi-mcu/tolak?reservasimcu_id=',
                            'class' => 'btn-aksi',
                            'data-options' => 'click',
                            'data-type' => 'tolak',
                            'id' => 'btn-tolak',
                            'disabled' => false
                        ]
                    ]
                ], '#table-reservasi-mcu')?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="row"></div>
                    <table id="table-reservasi-mcu" class="table table-striped table-condensed table-hover" style="width:100%;">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1" style="width: 19px"></th>
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Tanggal Reservasi");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pemeriksaan");?></th>
                                <th>Info Pasien</th>
                                <th><?=\Yii::t("fe", "No. Rekam Medik");?></th>
                                <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                <th><?=\Yii::t("fe", "No. Asuransi");?></th>
                                <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                                <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                                <th><?=\Yii::t("fe", "Penjamin");?></th>
                                <th><?=\Yii::t("fe", "Nama Perusahaan");?></th>
                                <th><?=\Yii::t("fe", "Status");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
    </div>

    <div id="modal_poli" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            </div>
        </div>
    </div>

</div>
<script src=""></script>
<?php
    $this->registerJs('
    //Globar var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function () {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        $(function(){
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });
        })

        // Generate Table
        table = $("#table-reservasi-mcu").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox no-width",
                targets: 0
            },
            ],
            select: {
                style: "os",
                selector: "tr"
            },
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/reservasi-mcu/get-data-informasi",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal Reservasi")).'", data: "tgl_reservasi"},
                {title: "'.(\Yii::t("fe", "Tanggal Pemeriksaan")).'", data: "tgl_pemeriksaan"},
                {
                    title: "'.(\Yii::t('fe', 'Info Pasien')).'",
                    data: "info_pasien",
                    searchable: false,
                    orderable: false,
                },
                {title: "'.(\Yii::t("fe", "No. Rekam Medik")).'", data: "no_rekam_medik", visible: false},
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'",  data: "nama_lengkap", visible: false},
                {title: "'.(\Yii::t("fe", "No. Asuransi")).'", data: "no_asuransi", searchable: false},
                {title: "'.(\Yii::t("fe", "Cara bayar / Penjamin")).'", data: "carabayar_penjamin", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama",name: "carabayar_id", visible: false},
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", name: "penjamin_id", visible: false},
                {title: "'.(\Yii::t("fe", "Nama Perusahaan")).'", data: "nama_perusahaan"},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status_reservasi_nama"},
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                "<div class=\'input-group\' style=\"margin-bottom: 0px !important;\"><input type=\'text\' value=\''.date('d-M-Y').'\' readonly=\'true\' id=\'rangeDemoStart\' class=\'form-control startDate\'/><span class=\'input-group-addon\' style=\'border-left: 0; border-right: 0;\'>-</span><input type=\'text\'  id=\'rangeDemoFinish\' readonly=\'true\' value=\''.date('d-M-Y').'\' class=\'form-control endDate\'/><input type=\'text\' style=\'display:none\' class=\'targetDate\'></div>",
            ],
            [
                3,
                "<div class=\'input-group\' style=\"margin-bottom: 0px !important;\"><input type=\'text\' value=\''.date('d-M-Y').'\' readonly=\'true\' id=\'rangeDemoStart2\' class=\'form-control startDate2\'/><span class=\'input-group-addon\' style=\'border-left: 0; border-right: 0;\'>-</span><input type=\'text\'  id=\'rangeDemoFinish2\' readonly=\'true\' value=\''.date('d-M-Y').'\' class=\'form-control endDate2\'/><input type=\'text\' style=\'display:none\' class=\'targetDate2\'></div>",
            ],
            [
                9,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'carabayar_id',
                            '',
                            $result['carabayar'],
                            [
                                'class' => 'form-control select2',
                                'id' => 'carabayar_id',
                                'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--')
                            ]
                        )
                    )
                ).'\'
            ],
            [
                10,
                \''.(
                    preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        DepDrop::widget(
                            [
                                'name'=>'penjamin_id',
                                'options'=>['id'=>'penjamin_id', 'class'=>'form-control select2'],
                                'pluginOptions'=>[
                                    'depends'=>['carabayar_id'],
                                    'placeholder'=>\Yii::t('fe', '--Pilih Penjamin--'),
                                    'url'=>Url::to(['/pendaftaran/end-point/list-penjamin'])
                                ]
                            ]
                        )
                    )
                ).'\'
            ],
            [
                12,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'status_reservasi',
                                '',
                                isset($result['status_reservasi']) ? $result['status_reservasi'] : [],
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'status_reservasi',
                                    'prompt' => \Yii::t('fe', '--Pilih Status Reservasi--')
                                ]
                            )
                        )
                    ).'\'
            ],
        ], {
            2:0,
            3:1,
            5:2,
            6:3,
        }, true);
        dateRangeHelper(\'.startDate\',\'.endDate\',\'.targetDate\');
        dateRangeHelper(\'.startDate2\',\'.endDate2\',\'.targetDate2\');
    });
    // Options
    var oneDay = 24*60*60*1000;
    var rangeDemoFormat = "%e-%b-%Y";
    var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});

    var primaryKey;
    //add for handle checkbox click
    $(document).on("click", "#table-reservasi-mcu tbody tr", function () {
        var data = table.rows(".selected").data();
        if(typeof data !== \'undefined\'){
            $("#btn-tolak").prop("disabled", false);
            $("#data-setujui").prop("disabled", false);
                
            for(var i = 0; i < data.length; i++) {
                if(data[i].status_reservasi == "565" || data[i].status_reservasi == "566") {
                    $("#btn-tolak").prop("disabled", true);
                    $("#data-setujui").prop("disabled", true);
                }
            }
        }else{
            $("#btn-tolak").prop("disabled", true);
            $("#data-setujui").prop("disabled", true);
        }
    });

    $(document).on("click", "#btn-tolak", function(){
        var data = table.rows(".selected").data();
        var reservasimcu_id = [];
        for (var i = 0; i < data.length; i++) {
            if(data[i]["reservasimcu_id"] !== \'undefined\'){
                reservasimcu_id.push(data[i]["reservasimcu_id"]);
            }
        }
        
        if(typeof data !== \'undefined\'){
                target = $(this).attr("action");
                type = $(this).attr("data-type");
            $(this).docoForm("delete", {
                url: target+reservasimcu_id.join(),
                confirmMessage: (type == "setujui") ? "Apakah anda yakin untuk menerima reservasi pasien ini ?" : "Apakah anda yakin untuk menolak reservasi pasien ini ?",
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
    
', View::POS_END, 'js-kuning');

?>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>