<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

if($this->context->action->id == 'index'){
    $title = $title." Obat";
}else{
    $title = $title." Barang";
}

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
.redClass {
    background-color: #ffd2d2!important;
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
                        'reset' => [
                            'attributes'=> [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Export Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-table-id' => 'laporan-pr',
                                'class' => 'data-excel',
                                'data-url' => '/pengadaan/laporan-purchase-order/show-popup-excel?actionId='.$this->context->action->id.'&',
                                'data-width' => '75%'
                            ]
                        ]
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-pr');?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="legend-index">
                            <div hidden>
                            <?php 
                                if($this->context->action->id == 'index'){
                                    echo $data = 'obat';
                                }else{
                                    echo $data = 'barang';
                                }
                            ?>
                            </div>
                            <?php echo $this->render('@app/modules/pengadaan/views/assets/js/filter/filter.php', [
                                'tableName' => 'laporan-pr',
                                'class' => 'data-filter',
                                'url' => '/pengadaan/laporan-purchase-order',
                                'type' => $data,
                                'get' => '/get-data?actionId='.$this->context->action->id.'&',
                                'excel' => '/show-popup-excel?actionId='.$this->context->action->id.'&',
                            ]); ?>
                        </div>
                    </div>
                </div>
                <table id="laporan-pr" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "PR No.");?></th>
                            <th><?=\Yii::t("fe", "PR Approval Date");?></th>
                            <th><?=\Yii::t("fe", "PO No.");?></th>
                            <th><?=\Yii::t("fe", "PO Create Date");?></th>
                            <th><?=\Yii::t("fe", "PO Approval Date");?></th>
                            <th><?=\Yii::t("fe", "Cito ");?></th>
                            <th><?=\Yii::t("fe", "Admin");?></th>
                            <?php if($this->context->action->id == 'index'){ echo"<th>".\Yii::t("fe", "Consignment")."</th>";}?>
                            <th></th>
                            <th><?=\Yii::t("fe", "Supplier Code");?></th>
                            <th><?=\Yii::t("fe", "Supplier Name");?></th>
                            <th><?=\Yii::t("fe", "Manufacturer");?></th>
                            <th><?=\Yii::t("fe", "Item Code");?></th>
                            <th><?=\Yii::t("fe", "Item Name");?></th>
                            <th><?=\Yii::t("fe", "Qty PO");?></th>
                            <th><?=\Yii::t("fe", "Qty GRN");?></th>
                            <th><?=\Yii::t("fe", "Qty Outstanding");?></th>
                            <th><?=\Yii::t("fe", "UOM");?></th>
                            <th><?=\Yii::t("fe", "From UOM");?></th>
                            <th><?=\Yii::t("fe", "Factor");?></th>
                            <th><?=\Yii::t("fe", "To UOM");?></th>
                            <th><?=\Yii::t("fe", "Price");?></th>
                            <th><?=\Yii::t("fe", "Discount (%)");?></th>
                            <th><?=\Yii::t("fe", "Discount (Rp.)");?></th>
                            <th><?=\Yii::t("fe", "Tax (%)");?></th>
                            <th><?=\Yii::t("fe", "Gross Amount");?></th>
                            <th><?=\Yii::t("fe", "Nett Amount");?></th>
                            <th><?=\Yii::t("fe", "PO Remarks");?></th>
                            <th><?=\Yii::t("fe", "Catatan Internal");?></th>
                            <th><?=\Yii::t("fe", "Catatan Eksternal");?></th>
                            <th><?=\Yii::t("fe", "Status PO");?></th>
                            <th><?=\Yii::t("fe", "Reject Date");?></th>
                            <th><?=\Yii::t("fe", "Reject Remarks");?></th>
                            <th><?=\Yii::t("fe", "Reception Date");?></th>
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
    var belum_po = '.DocoConstants::VAR_BELUM_PO.';
    var actionId = "'.$this->context->action->id.'";
    let colTargets = {};
    
    let list_tabel = [
        {
            title: "No.",
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        { title: "'.(\Yii::t("fe", "PR No.")).'", data: "no_pr", searchable: false  },
        { title: "'.(\Yii::t("fe", "PR Approval Date")).'", data: "tanggal_verifikasi_pr", searchable: false  },
        { title: "'.(\Yii::t("fe", "PO No.")).'", data: "no_po" },
        { title: "'.(\Yii::t("fe", "PO Create Date")).'", data: "tanggal_po" },
        { title: "'.(\Yii::t("fe", "PO Approval Date")).'", data: "tgl_verifikasi_po", searchable: false },
        { title: "'.(\Yii::t("fe", "Cito ")).'", data: "is_cito", searchable: false },
        { title: "'.(\Yii::t("fe", "Admin")).'", data: "is_admin", searchable: false },
        { title: "'.(\Yii::t("fe", "Supplier Name")).'", data: "supplier_id", visible: false },
        { title: "'.(\Yii::t("fe", "Supplier Code")).'", data: "supplier_code", searchable: false },
        { title: "'.(\Yii::t("fe", "Supplier Name")).'", data: "supplier_name" ,searchable:false},
        { title: "'.(\Yii::t("fe", "Manufacturer")).'", data: "manufacturer", searchable: false },
        { title: "'.(\Yii::t("fe", "Item Code")).'", data: "item_code", searchable: false },
        { title: "'.(\Yii::t("fe", "Item Name")).'", data: "item_name", searchable: true },
        { title: "'.(\Yii::t("fe", "Qty PO")).'", data: "qty_po", searchable: false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "Qty GRN")).'", data: "po_balance", searchable: false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "Qty Outstanding")).'", data: "qty_outstanding", searchable: false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "UOM")).'", data: "uom", searchable: false },
        { title: "'.(\Yii::t("fe", "From UOM")).'", data: "from_uom", searchable: false },
        { title: "'.(\Yii::t("fe", "Factor")).'", data: "factor", searchable: false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "To UOM")).'", data: "to_uom", searchable: false },
        { title: "'.(\Yii::t("fe", "Price")).'", data: "price", searchable: false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "Discount (%)")).'", data: "deduction_percent", searchable: false, class: "text-center" },
        { title: "'.(\Yii::t("fe", "Discount (Rp.)")).'", data: "deduction_rupiah", searchable: false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "Tax (%)")).'", data: "addition_percent", searchable: false, class: "text-center" },
        { title: "'.(\Yii::t("fe", "Gross Amount")).'", data: "gross_amount" ,searchable:false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "Nett Amount")).'", data: "nett_amount", searchable: false, class: "text-right" },
        { title: "'.(\Yii::t("fe", "PO Remarks")).'", data: "remarks", searchable: false },
        { title: "'.(\Yii::t("fe", "Catatan Internal")).'", data: "catatan_internal", searchable: false, orderable: false }, 
        { title: "'.(\Yii::t("fe", "Catatan Eksternal")).'", data: "catatan_eksternal", searchable: false, orderable: false },
        { title: "'.(\Yii::t("fe", "Status")).'", data: "status_po" },
        { title: "'.(\Yii::t("fe", "Reject Date")).'", data: "reject_date", searchable: false },
        { title: "'.(\Yii::t("fe", "Reject Remarks")).'", data: "reject_remarks", searchable: false },
        { title: "'.(\Yii::t("fe", "Reception Date")).'", data: "tanggal_penerimaan", searchable: false },
    ];

    if("'.$this->context->action->id.'"=="index"){
        list_tabel.splice(8,0,{title: "'.(\Yii::t("fe", "Consignment")).'", data: "is_consigment", searchable:false})
        mappingfilter = {
                0:4,
                1:3,
                2:9,
                3:31,
                4:14
        }
        postFilterSupplier = 9
        postFilterStatus = 31
        colTargets = {
            po_remarks: 27,
            catatan_internal: 28,
            catatan_external: 29
        }
    }else{
        mappingfilter = {
                0:4,
                1:3,
                2:8,
                3:30,
                4:13
        }
        postFilterSupplier = 8
        postFilterStatus = 30
        colTargets = {
            po_remarks: 26,
            catatan_internal: 27,
            catatan_external: 28
        }
    }
    

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#laporan-pr").docoTabel({
            filter: true,
            sorting: [[4, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pengadaan/laporan-purchase-order/get-data?actionId="+actionId,
            columns: list_tabel,
            createdRow: function(row, data, index) {
                $("td", row).eq(colTargets.po_remarks).attr("style", "max-width:350px; overflow-x:auto; white-space:pre; text-align: justify;");
                $("td", row).eq(colTargets.catatan_internal).attr("style", "max-width:350px; overflow-x:auto; white-space:pre; text-align: justify;");
                $("td", row).eq(colTargets.catatan_external).attr("style", "max-width:350px; overflow-x:auto; white-space:pre; text-align: justify;");
            },
            columnDefs: [
                {
                    render: (data, type, row, meta) => {
                        let remarksPO = data ? data : "-";
                        let wrapper = `${remarksPO}`;
                        return wrapper;
                    },
                    targets: colTargets.po_remarks,
                },
                {
                    render: (data, type, row, meta) => {
                        let catatanInternal = data ? data : "-";
                        let wrapper = `${catatanInternal}`;
                        return wrapper;
                    },
                    targets: colTargets.catatan_internal,
                },
                {
                    render: (data, type, row, meta) => {
                        let catatanEksternal = data ? data : "-";
                        let wrapper = `${catatanEksternal}`;
                        return wrapper;
                    },
                    targets: colTargets.catatan_external,
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                4,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                postFilterSupplier,
                \'<div class=\"form-group\">' . (preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList("supplier_id", "",
                        ArrayHelper::map($list_supplier, 'supplier_id', 'supplier_nama'),
                        [
                            "id" => "filter_status",
                            "class" => "form-control select2",
                            "prompt" => \Yii::t("fe", "-- Pilih Semua--")
                        ]
                    )
                )) . '</div>\'
            ],
            [
                postFilterStatus,
                \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status_po', '', $status_po,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            "col-index" => "2"
                        ]
                    )
                )).'</div>\'
            ]
        ], 
            mappingfilter
        );

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("change", "select[name=\'status_po\']", function(){
            $(".data-filter").click();
        });
    });

', View::POS_END, 'b-index');
?>
