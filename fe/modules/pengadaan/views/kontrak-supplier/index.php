<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

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
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
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
                <?php
                    $btn_toolbar = [
                        'search',
                        'reset'=> [
                            'attributes'=> [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'add',
                        'custom-edit' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Edit Data'),
                            'icon' => 'fa fa-edit',
                            'method' => '#',
                            'attributes' => [
                                'disabled' => true,
                                'id' => 'edit',
                                'data-target' => $module.'edit?id='
                            ]
                        ],
                        'custom-detail' => [
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon'  => 'fa fa-eye',
                            'attributes' => [
                                'disabled' => true,
                                'id' => 'detail',
                                'data-options' => 'click',
                                'data-target' => '#modal_backdrop',
                                'data-toggle' => 'modal',
                                'data-width' => '85%',
                                'action' => '/pengadaan/kontrak-supplier/detail?id=12',
                            ]
                        ],
                        "export" => [
                            'title' => \Yii::t('fe', 'Export Template Data'),
                            'attributes'=>[
                                'id' => 'export-download-data',
                                'data-options' => 'click',
                            ]
                        ],
                        'import' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Import Data'),
                            'icon' => 'fa fa-upload"',
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/pengadaan/kontrak-supplier/import-data',
                                'data-width' => '90%',
                            ]
                        ],
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#info-ks');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>

                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:rgb(182, 215, 168);'></span>Kontrak Aktif</li>
                                    <li><span style='background:#fff;'></span>Kontrak Tidak Aktif</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="info-ks" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "No. Kontrak Supplier");?></th>
                            <th><?=\Yii::t("fe", "Nama Supplier");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Mulai Berlaku");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global Var
    var table;

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#info-ks").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            drawCallback: function(settings) {
                $(".switch").bootstrapSwitch();
            }, 
            ajax: baseUrl+"pengadaan/kontrak-supplier/get-list-data",
            columns: [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "5%"
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width: "5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Kontrak Supplier")).'",
                    data: "kontraksupplier_no"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Supplier")).'",
                    data: "supplier_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Berlaku")).'",
                    data: "tgl_berlaku"
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'",
                    data: "is_active",
                    orderable: false, 
                    class: "text-center"
                }
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData.is_active) {
                    $(nRow).css("background", "rgb(182, 215, 168)");
                } else {
                    $(nRow).css("background", "fff");
                }
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                4,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                5, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', [
                    1 => Yii::t('fe', 'Aktif'), 0 => Yii::t('fe', 'Tidak Aktif')
                ], ['class' => 'form-control select2', 
                'prompt' => \Yii::t('fe', 'Pilih Status')]))).'\'
            ]
        ]);

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("change", ".startDate, .endDate", function(){
            $(".data-filter").click();
        });
    });

    $(document).on("click", "#info-ks tr", function(){
        let data = table.row(".selected").data();
        if(data) {
            const primaryKey = data.primary;
            $(`#detail`).attr(`disabled`, false);
            $(`#detail`).attr("action", `/pengadaan/kontrak-supplier/detail?id=${primaryKey}`);
            $(`#edit`).attr(`disabled`, false)
        } else { 
            $(`#detail`).attr(`disabled`, true)
            $(`#edit`).attr(`disabled`, true)
        }
    });

    $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
        $(this).attr("data-state", state);
        var dataId = $(this).attr("data-id");
        var that = $(this);
        var header = "'.(\Yii::t("fe", "Konfirmasi")).'";
        var message = "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'";
        var label = {buttons: { Yes: "button-yes", No: "button-no"}, hidden: true};
        $.showQuestionDialog(header, message, label, function (reaction) {
            showReaction(reaction, that, function(str) {
                hideIt();
            });
        });
    });
    
    function showReaction(str, that, callback) {
        var dataId = that.attr("data-id");
        var dataState = that.attr("data-state");
        var dataStatus = "1";
        if (dataState == "false") {
            dataStatus = "0";
        }
        //jika pilih No
        if(dataState == "false") {
            dataState = true;
        } else {
            dataState = false;
        }
    
        if (str == "Yes") {
            $.ajax({
                url: "/pengadaan/kontrak-supplier/change-status?id="+dataId+"&status="+dataStatus,
                type: "POST",
                dataType: "json",
                success : function(data) {
                    docoNotification("success", "Proses Berhasil !", data.data.message);
                    hideIt();
                },
                error : function(data) {
                    docoNotification("error", "Proses Gagal !", data.responseJSON.meta.message);
                    that.bootstrapSwitch("state", dataState);
                    hideIt();
                }
            });
        } else {
            that.bootstrapSwitch("state", dataState);
        }
        callback(str);
    }
        
    function hideIt() {
        $("#confirm-dialog-overlay").remove();
        $("#confirm-dialog").remove();
        $("#confirm-dialog-overlay").remove();
        $("#confirm-dialog").remove();            
    }

    $(document).on("click", ".data-export-download", function(e){
        e.preventDefault();
        window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/kontrak-supplier/export-download-excel?"+$.param(table.ajax.params()));
        return false;
    });

', View::POS_END, 'b-index');
?>
