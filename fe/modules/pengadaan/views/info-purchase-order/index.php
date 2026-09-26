<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\bootstrap\Modal;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
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
    .legend-information__color {
        background-color:#ffffff !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="Modul Icon Pengadaan">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><strong><?= $title; ?></strong></h3>
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
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=> [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'edit' => [
                        'title' => 'Lihat',
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'id' => 'btn-lihat',
                            'data-target' => $module.'detail?id=',
                            'data-conditions' => 'type_po',
                            'disabled' => true
                        ]
                    ],
                    'custom-delete' => [
                        'type' => 'button',
                        'title' => 'Batal',
                        'icon' => 'fa fa-trash',
                        'attributes' => [
                            'id' => 'delete-po',
                            'data-options' => 'click',
                            'disabled' => true
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => $module.'export-pdf?'
                        ]
                    ],
                    'print-rincian-ppo' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Cetak Rincian'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'btn-print-rincian-po',
                            'class' => 'btn-print-kop',
                            'data-options' => 'click',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-table-id' => 'tbl-info-purchase-order',
                            'data-url' => $module.'show-popup-rincian?',
                            'data-conditions' => 'no_transaksi,type_po',
                            'disabled' => true,
                        ],
                    ],
                    'print-rincian-kop' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Cetak Rincian (Kop)'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'btn-print-rincian-kop',
                            'class' => 'btn-print-kop',
                            'data-options' => 'click',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-table-id' => 'tbl-info-purchase-order',
                            'data-url' => $module.'show-popup-rincian?is_kop=true&',
                            'data-conditions' => 'no_transaksi,type_po',
                            'disabled' => true,
                        ],
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => $module.'export-excel?'
                        ]
                    ],
                    'merge-po' => [
                        'type' => 'button',
                        'title' => 'Gabung PO',
                        'icon' => 'fa fa-random',
                        'attributes' => [
                            'id' => 'merge-po',
                            'data-options' => 'click',
                            'disabled' => true
                        ]
                    ],
                ],'#tbl-info-purchase-order');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="advanced-filter">
                    </div>
                    
                    <div class="col-md-3" style="width: 23%;">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:rgb(255, 250, 187);'></span>Belum Tervalidasi</li>
                                    <li><span style='background:#fff;'></span>Sudah Tervalidasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9" style="width: 77%;">
                        <?php echo $this->render('_filter_selected', [
                            'tableName' => 'tbl-info-purchase-order',
                            'url' => '/pengadaan/info-purchase-order/get-data?'
                        ]); ?>
                    </div>
                </div>
                <table id="tbl-info-purchase-order" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Validasi PO");?></th>
                            <th><?=\Yii::t("fe", "Nomor Purchase Order");?></th>
                            <th><?=\Yii::t("fe", "Asal Transaksi");?></th>
                            <th><?=\Yii::t("fe", "No RO Permintaan Pembelian");?></th>
                            <th><?=\Yii::t("fe", "Supplier");?></th>
                            <th><?=\Yii::t("fe", "Payment Term");?></th>
                            <th><?=\Yii::t("fe", "Total Harga PO");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Status Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Pegawai Validasi");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan PO");?></th>
                            <th><?=\Yii::t("fe", "Qty PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Batal PO");?></th>
                            <th><?=\Yii::t("fe", "Catatan Batal PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Cetak PO");?></th>
                            <th><?=\Yii::t("fe", "Cito");?></th>
                            <th><?=\Yii::t("fe", "Admin");?></th>
                            <th><?=\Yii::t("fe", "Consigment");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="15"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
    Modal::begin([
        'header' => '<h5>Catatan Batal PO</h5>',
        'id' => 'modal',
        'size' => 'modal-md',
    ]);
?>
<?php
$form = ActiveForm::begin([
        'id' => 'form-batal-po',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'validateOnSubmit' => false,
        'formConfig' => [
            'labelSpan' => 4,
            'deviceSize' => ActiveForm::SIZE_MEDIUM
        ],
        'options' => [
            'class' => 'form-horizontal',
            'role' => 'form',
        ]
    ]);
?>
<div class="row">
    <div class="col-sm-12">
        <textarea
            autofocus
            class="form-control"
            id="catatan"
            name="catatan"
            rows="3"
            placeholder="Catatan Batal PO"></textarea>
    </div>
</div>
<?php ActiveForm::end(); ?>
<hr>
<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
        'class' => 'btn btn-info btn-labeled btn-xs',
        'data-dismiss' => 'modal'
    ]); ?>
    <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => 'btn-submit-batal'
    ]) ?>
</div>
<?php Modal::end(); ?>

<?php
$this->registerJs('
    // Global Var
    var table;
    var state_key = window.location.pathname;
    var select=0;
    var url = `'.$module.'`

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // $(document).on("click",".select-checkbox",function(){
    //     if($(this).prop("checked") == true){
    //         console.log("Checkbox is checked.");
    //     }else if($(this).prop("checked") == false){
    //         console.log("Checkbox is unchecked.");
    //     }

    //     console.log(table.rows(".selected").data());
    // });

    $(document).ready(function() {
        // Generate Table
        table = $("#tbl-info-purchase-order").docoTabel({

            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "multi",
                selector: "tr"
            },
            sorting: [[2,"desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            stateSave: true,
            stateDuration: -1,
            ajax: baseUrl+"pengadaan/info-purchase-order/get-data",
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
                    title: "'.(\Yii::t("fe", "Tanggal PO")).'",
                    data: "tanggal_buat_po"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Validasi PO")).'",
                    data: "tanggal_po"
                },
                {
                    title: "'.(\Yii::t("fe", "No Transaksi")).'",
                    data: "nomor"
                },
                {
                    title: "'.(\Yii::t("fe", "Asal Transaksi")).'",
                    data: "asal_transaksi",
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor PO")).'",
                    data: "no_transaksi"
                },
                {
                    title: "'.(\Yii::t("fe", "Supplier")).'",
                    data: "supplier_nama",
                    name: "supplier_id",
                },
                {
                    title: "'.(\Yii::t("fe", "Payment Term")).'",
                    data: "payment_term",
                    name: "payterm_id",
                },
                {
                    title: "'.(\Yii::t("fe", "Total Harga PO")).'",
                    data: "total_harga_po",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Ruangan")).'",
                    data: "ruangan_nama",
                    name: "ruangan_id",
                },
                {
                    title: "'.(\Yii::t("fe", "Status Penerimaan")).'",
                    data: "stat_penerimaan",
                    name: "lookup_id",
                },
                {
                    title: "'.(\Yii::t("fe", "Pegawai Validasi")).'",
                    data: "pegawai_validasi",
                    name: "pegawai_validasi",
                },
                {
                    title: `Tanggal Penerimaan PO`,
                    data: "tgl_penerimaan",
                    name: "tgl_penerimaan",
                    searchable: false,
                    visible: false
                },
                {
                    title: `Qty PO`,
                    data: "qty_po",
                    name: "qty_po",
                    searchable: false,
                    visible: false
                },
                {
                    title: `Tanggal Batal PO`,
                    data: "tgl_batal_po",
                    name: "tgl_batal_po",
                    searchable: false,
                    visible: false
                },
                {
                    title: `Catatan Batal PO`,
                    data: "catatan_batal_po",
                    name: "catatan_batal_po",
                    searchable: false,
                    visible: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Cetak PO")).'",
                    data: "tgl_cetak_po",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Cito ")).'",
                    data: "po_cito",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Admin")).'",
                    data: "po_admin",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Consigment")).'",
                    data: "po_consigment",
                    searchable: false,
                },
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData.is_validasi) {
                    $(nRow).css("background", "fff");
                } else {
                    $(nRow).css("background", "rgb(255, 250, 187)");
                }
            },
            stateSaveCallback: function(settings,data) {
                localStorage.setItem( "DataTables_" + settings.sInstance, JSON.stringify(data) )
            },
            stateLoadCallback: function(settings) {
                return JSON.parse( localStorage.getItem( "DataTables_" + settings.sInstance ) )
            },
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStartValidasi" value="'.date('d-M-Y').'" class="form-control startDateValidasi" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishValidasi" value="'.date('d-M-Y').'" class="form-control endDateValidasi" readonly /><input type="text" style="display:none" class="validasiDate" col-index=2 readonly="true"></div>\'
            ],
            [
                5,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('asal_transaksi', '',
                        DocoConstants::$statusAsal,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
            [
                7,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('supplier_id', '',
                        [],
                        [
                            'id' => 'filter_supplier',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
            [
                8,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('payterm_id', '',
                        ArrayHelper::map($payterm,'payterm_id','payterm_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
            [
                10,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('ruangan_id', '',
                        [],
                        [
                            'id' => 'filter_ruangan',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
            [
                11,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('stat_penerimaan', '',
                        ArrayHelper::map($status_po,'lookup_id','lookup_name'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Semua--')
                        ]
                    )
                )).'</div>\'
            ],
        ],{
            0:2,
            1:3,
            2:5,
            3:7,
            4:8,
            5:10,
            6:11,
        },true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
        dateRangeHelper(".startDateValidasi",".endDateValidasi",".validasiDate");
        $("#filter_ruangan").select2({
            minimumInputLength: 3,
            ajax : {
                url: "/pengadaan/info-purchase-order/get-ruangan",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        });

        $("#filter_supplier").select2({
            minimumInputLength: 3,
            ajax : {
                url: "/pengadaan/info-purchase-order/get-supplier",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        });

        $(".data-reset").on("click", function(){
            location.reload();
            var table = $("#tbl-info-purchase-order").DataTable();
            table.state.clear();
            table.draw();
            table = $("#tbl-info-purchase-order").dataTable();
            table.fnDraw();
            return false;
        });

        var detail = "";
        $("#delete-po").on("click", function (e) {
            $("#modal").modal("show")
                .find("#modal_backdrop")
                .load(detail);
        });

        $("#btn-submit-batal").on("click", function(){
            var catatan = $("#catatan").val();
            var id = table.row(".selected").data().primary;
            var type_po = table.row(".selected").data().type_po;
            var target = "'.$module.'delete?id="+id+"&type_po="+type_po+"&catatan="+catatan;
            var messageText = "Apakah Anda yakin ingin membatalkan PO ini?";
            $("#modal").removeAttr("tabindex");

            $(this).attr("action", target);
            $(this).docoForm("delete", {
                additional: {catatan: catatan},
                confirmMessage: messageText,
                success: function (data) {
                    $("#modal").modal("hide");
                    $("#catatan").val("");
                    table.draw();
                }
            });
        });
    });


', View::POS_END);

$this->registerJs($this->render("assets/js/index.js"));
?>
