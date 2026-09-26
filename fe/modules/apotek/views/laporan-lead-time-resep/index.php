<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        "search"=> [
                            'attributes'=>[
                                'id' => 'find-data',
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'id' => 'reset-data',
                                'data-parent' => '.filter-form',
                            ]
                        ],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'apotek/laporan-lead-time-resep/show-popup-excel?',
                                'data-width' => '75%',
                                'disabled' => true,
                            ]
                        ],
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="row" id="ffBody">
                    <div class="form-group col-md-3">
                        <label>Tanggal Resep :</label>
                            <div class="form-group">
                                <div class='input-group'>
                                    <input value="<?= date('d-M-Y') ?>" type='text' id='rangeDemoStart' class='form-control startDate' />
                                    <span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span>
                                    <input value="<?= date('d-M-Y') ?>" type='text' id='rangeDemoFinish' class='form-control endDate' />
                                    <input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'>
                                </div>
                            </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Ruangan :</label>
                            <div class="form-group">
                                        <?=  Html::dropDownList(
                                            'ruangan_id',
                                            '',
                                            $list_ruangan,
                                            [
                                                'class' => 'form-control select2',
                                                'id' => 'ruangan_id',
                                                'prompt' => \Yii::t('fe', '--Pilih Ruangan--')
                                            ]
                                        )
                              ?>
                            </div>
                    </div>
                           
                </div>
                <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Lead Time Resep') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                <center><span class="populate-data-index" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                    <div class="progress-index" style="margin-left: 12px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <span class="label-persentase-index">0</span>%</div>
                    </div>
                    <span class="help-block label-progress-index" style="margin-left: 12px;"></span>
                <hr>
                <div class="row content" id="content">
                    <div class="col-md-12 content-data" style="display: none">
                        <table id="laporan" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="70">No</th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Tanggal Resep");?></th>
                                    <th><?=\Yii::t("fe", "No. Resep");?></th>
                                    <th><?=\Yii::t("fe", "Jenis Resep");?></th>
                                    <th><?=\Yii::t("fe", "Jumlah R/");?></th>
                                    <th><?=\Yii::t("fe", "Dokter");?></th>
                                    <th><?=\Yii::t("fe", "Jumlah Item");?></th>
                                    <th><?=\Yii::t("fe", "Jam Resep Masuk");?></th>
                                    <th><?=\Yii::t("fe", "Jam Resep Dibayarkan");?></th>
                                    <th><?=\Yii::t("fe", "Jam Production");?></th>
                                    <th><?=\Yii::t("fe", "Jam Resep Siap Diserahkan");?></th>
                                    <th><?=\Yii::t("fe", "Waktu Tunggu Obat");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="20"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    const progress = $(".progress-index");
    const progressBar = $(".progress-index .progress-bar");
    const labelProgress = $(".label-progress-index");
    const labelPercent = $(".label-persentase-index");
    const content = $(".content");
    const contentData = $("#content .content-data");
    var table;
    var draw = 0;
    
    $(document).on("keydown", null, "enter", function (event) {
        $("#find-data").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#find-data").click();
        }

        if (e.key == "F7") {
            $("#reset-data").click();
        }
    });
    
    
    $(document).ready(() => {
        dateRangeHelper(".startDate", ".endDate", ".targetDate");
        progress.css("display", "none")
        $("#jenis_laporan").val(1145).trigger("change")

        const showInfo = () => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve($(".populate-data-index").html(`mempersiapkan data ...`))
                }, 1000);
                setTimeout(() => {
                    resolve($(".populate-data-index").css("display", "none"))
                    resolve(progress.css("display", "block"))
                    resolve($(".label-progress-index").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
                }, 2000);
            })
        }

        const setPresentase = function(progress) {
            setTimeout(() => {
                $(".progress-index .label-persentase-index").html(progress)
                $(".progress-index .progress-bar").css("width", progress +"%")
                .attr("aria-valuenow", progress)
                .attr("aria-volume", progress);
            }, 2500);
        }

        async function updateProgressBar() {
            let config = await $.getJSON("./../../json/setup.json")
            
            if (config.origin == "true") {
                
                var socket = io.connect(window.location.origin);
            } else {
                var socket = io.connect(config.ip+":"+config.port);
            }

            const channel = `laporan-lead-time-resep-datatable:`
            await showInfo()
            var tgl = $(".startDate").val()+" - "+$(".endDate").val();
            let _data = {
                "advance_filter":{  tgl_resep: tgl,
                    ruangan_id:$("#ruangan_id").val(),
                }
            }
            
            $.ajax({
                url : "/apotek/laporan-lead-time-resep/get-data-serconn",
                data: _data,
                success : function (data) {
                    socket.on(channel + data.randString, (message) => {
                        
                        const _data = $.parseJSON(message);
                        
                        const { status , messageProcess , filename, progress} = _data
                        if(status == "update") {
                            let valProgress = 30
                            setPresentase(valProgress)
                        } else {
                            let valProgress = 100
                            setPresentase(valProgress)
                            $("#find-data").prop("disabled", false);
                            setTimeout(() => {
                                $(".label-progress-index").html(`<p style="font-size:16px;font-weight:bold;"> Berhasil menyiapkan data</p>`)
                                if(draw > 0) {
                                    table.clear().draw()
                                    table.rows.add(_data.data); 
                                    table.columns.adjust().draw(); 
                                } else {
                                    
                                    
                                    table = $("#laporan").DataTable({
                                        data:  _data.data,
                                        scrollX: true,
                                        destroy: true,
                                        searching: false,
                                        paging: false,
                                        sorting: false,
                                        displayLength: 100,
                                        processing: false,
                                        serverSide: false,
                                        ordering: false,
                                        lengthChange: false,
                                        columns: _data.header.columns,
                                    });
                                    
                                    draw++;
                                    
                                }
                                contentData.css("display", "");
                            }, 2500);
                            setTimeout(() => {
                                setPresentase(0)
                                $(".progress-index").css("display", "none");
                                $(".label-progress-index").html("")
                                table.columns.adjust().draw(); 
                            }, 4000);
                        }
                    });

                }
            });
        }

            $(document).on("click","#data-export-excel-serconn", function(e){
                e.preventDefault();
            })

            $(document).on("click", "#reset-data", function (e) {
                $(".select2").select2();
                $(".select2").val("").trigger("change");
                $(".startDate").val(moment().locale("en").format("DD-MMM-YYYY"))
                $(".endDate").val(moment().locale("en").format("DD-MMM-YYYY"))
                if(draw > 0) {
                    table.clear().draw();
                }
                setPresentase(0);
                contentData.css("display", "none");
                progress.css("display", "none");
                $(".label-progress-index").html("");
                $("#find-data").prop("disabled", false);
                $("#data-export-excel-serconn").prop("disabled", true);
                
            })
            $(document).on("click","#find-data", function (e) {
                e.preventDefault();
        
                setTimeout(() => {
                    var tgl = $(".startDate").val()+" - "+$(".endDate").val();
                    let _data = {
                      "advance_filter":{  tgl_resep: tgl,
                          ruangan_id:$("#ruangan_id").val(),
                      }
                    }
                    var _param = $.param(_data);

                    $("#data-export-excel-serconn").attr("action","/apotek/laporan-lead-time-resep/show-popup-excel?tipe=excel&"+_param);
                    $("#data-export-excel-serconn").attr("data-target","#modal_backdrop");
                    $("#data-export-excel-serconn").attr("data-width","75%");
                    $("#data-export-excel-serconn").attr("data-toggle","modal");

                    updateProgressBar()
                }, 1000);
                // $("#find-data").prop("disabled", true);
                $("#data-export-excel-serconn").prop("disabled", false);
            })
    });

    $(".daterange-basic").daterangepicker({
        startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
        endDate: "'.(date("d-M-Y")).'",
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

', View::POS_END, 'b-index');
?>
