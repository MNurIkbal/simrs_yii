<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent'=>'.filter-form'
                        ]
                    ],
                    'lihat' => [
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'id'=>'btn-lihat',
                            'data-target' => '/gudang/inf-penerimaan-barang-manual/view?id=',
                        ]
                    ],
                    // 'lihat-detail' => [
                    //     'title' => 'Lihat',
                    //     'icon' => 'fa fa-eye',
                    //     'method' => '#',
                    //     'attributes' => [
                    //         'data-options'=>'click',
                    //         'id' => 'data-lihat',
                    //         'data-id' => '',
                    //         'class' => 'data-detail'
                    //     ]
                    // ],
                    'retur' => [
                        'title' => 'Retur',
                        'icon' => 'fa fa-reply',
                        'attributes' => [
                            'id' => 'btn-retur',
                            'data-target' => $module.'/retur-penerimaan?id=',
                            // 'data-conditions' => 'type'
                        ]
                    ],
                ],'#penerimaan-manual');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="penerimaan-manual" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Faktur");?></th>
                            <th><?=\Yii::t("fe", "Nama Supplier");?></th>
                            <th><?=\Yii::t("fe", "Status Verifikasi");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Verifikasi");?></th>
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

<div id="modalLihat" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Detail Penerimaan</h5>
            </div>
            <div class="modal-body">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="column-1">
                                <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                            </div>
                            <div class="column-2">
                                <h3 class="panel-title"><b>Detail Penerimaan Barang Supplier Manual </b></h3>
                                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                            </div>
                        </div>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="panel-toolbar clearfix">
                        <?=DocoHelpers::generateToolbar([
                            'pdf' =>[
                                'type' => 'button',
                                "attributes" => [
                                    'data-target' => "",
                                    "id" => "cetak-pdf",
                                    'data-options' => 'link',
                                    'target' => "_blank"
                                ]
                            ]
                        ]);?>
                    </div>

                    <div class="panel-body">
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tanggal Penerimaan</b></label>
                                    <div class="col-sm-7"> <b>:</b> <span class="tgl_penerimaan"></span> </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nama Supplier</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="supplier_nama"></span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>No Penerimaan</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="no_penerimaan"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>No Faktur</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="no_faktur"></span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>No Surat Jalan</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="no_surat_jalan"></span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tarif Pajak</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="tarif_pajak"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Payment Term</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="payment_term"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-striped table-condensed table-hover table-detail" style="width:100%" id="tableDetailPenerimaan">
                                    <thead>
                                    <tr class="bg-inverse">
                                        <th width="80">No</th>
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty Penerimaan");?></th>
                                        <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                        <th><?=\Yii::t("fe", "Harga Netto");?></th>
                                        <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                                        <th><?=\Yii::t("fe", "NO Batch");?></th>
                                        <th><?=\Yii::t("fe", "Keterangan");?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var table;
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#penerimaan-manual").docoTabel({
            filter: true,
            columnDefs: [ 
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                }
            ],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"gudang/inf-penerimaan-barang-manual/get-data",
            columns: [
                {
                    title: "", 
                    data: null, 
                    defaultContent: "", 
                    searchable: false, 
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'", 
                    data: "tgl_penerimaan"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Penerimaan")).'", 
                    data: "no_penerimaan"
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Faktur")).'", 
                    data: "no_faktur"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Supplier")).'", 
                    data: "supplier_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Status Verifikasi")).'", 
                    data: "status_verifikasi"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Verifikasi")).'", 
                    data: "tgl_verifikasi",
                    searchable:false
                }
            ],

        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                6,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status', '',
                        $status_verifikasi,
                        [
                            'id' => 'filter_status',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
        ]);
        
        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("click", "#penerimaan-manual tr", function(){
            var _data = table.row(".selected").data();

            if(typeof _data !== "undefined"){
                if(_data.is_verifikasi == true){
                    $("#btn-retur").attr("disabled",false);
                }else{
                    $("#btn-retur").attr("disabled",true);
                }
            }
        });
    });
    
', View::POS_END, 'b-index');

?>
