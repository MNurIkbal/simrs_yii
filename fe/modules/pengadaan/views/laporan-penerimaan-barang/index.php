<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
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


$this->title = Dhtml::getTitleMenu();
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
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
                      <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
                        'excel' => [
                            'attributes' => [
                                'data-target' => $module.'export-excel?'
                            ]
                        ]
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar,'#laporan-penerimaan');?>
            </div>

            <div class="panel-body">
                 <div class="advanced-filter"></div>
                <table id="laporan-penerimaan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=\Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Kode Supplier");?></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "Supplier");?></th>
                            <th><?=\Yii::t("fe", "Payment Term");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Diterima Oleh");?></th>
                            <th><?=\Yii::t("fe", "Status Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Dibuat PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Validasi PO");?></th>
                            <th><?=\Yii::t("fe", "Nomor PO");?></th>
                            <th><?=\Yii::t("fe", "Kode Barang");?></th>
                            <th><?=\Yii::t("fe", "Nama Barang");?></th>
                            <th><?=\Yii::t("fe", "Qty PO");?></th>
                            <th><?=\Yii::t("fe", "Satuan PO");?></th>
                            <th><?=\Yii::t("fe", "Qty Diterima");?></th>
                            <th><?=\Yii::t("fe", "Satuan Diterima");?></th>
                            <th><?=\Yii::t("fe", "PO Balance");?></th>
                            <th><?=\Yii::t("fe", "Satuan PO Balance");?></th>
                            <th><?=\Yii::t("fe", "Nilai Konversi");?></th>
                            <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                            <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                            <th><?=\Yii::t("fe", "Harga Netto");?></th>
                            <th><?=\Yii::t("fe", "Harga");?></th>
                            <th><?=\Yii::t("fe", "Disc");?></th>
                            <th><?=\Yii::t("fe", "PPn");?></th>
                            <th><?=\Yii::t("fe", "Subtotal");?></th>
                            <th><?=\Yii::t("fe", "Total");?></th>
                            <th><?=\Yii::t("fe", "Catatan PO");?></th>
                            <th><?=\Yii::t("fe", "Tanggal dibuat PR");?></th>
                            <th><?=\Yii::t("fe", "Nomor PR");?></th>
                            <th><?=\Yii::t("fe", "Nomor Batch");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                            <th><?=\Yii::t("fe", "Nomor Surat Jalan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Faktur");?></th>
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
        table = $("#laporan-penerimaan").docoTabel({
            filter: true,
            sorting: [[3, "asc"], [5, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"pengadaan/laporan-penerimaan-barang/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Kode Supplier")).'", 
                    data: "supplier_kode", 
                    searchable: false 
                },
                { 
                    title: "' . (\Yii::t("fe", "Nama Supplier")) . '", 
                    data: "supplier_id",
                    visible: false
                },
                { 
                    title: "'.(\Yii::t("fe", "Supplier")).'", 
                    data: "supplier_nama", 
                    searchable: false  
                },
                { 
                    title: "'.(\Yii::t("fe", "Payment Term")).'", 
                    data: "payterm_nama",
                    name: "payterm_id"
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'", 
                    data: "tgl_penerimaan"
                },
                { 
                    title: "'.(\Yii::t("fe", "Nomor Penerimaan")).'", 
                    data: "no_penerimaan", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Diterima Oleh")).'", 
                    data: "diterima_oleh", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Status Penerimaan")).'", 
                    data: "status_penerimaan", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Dibuat PO")).'", 
                    data: "tgl_po",
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Validasi PO")).'", 
                    data: "tgl_validasi_po",
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nomor PO")).'", 
                    data: "nomor_po", 
                    searchable: true 
                },
                { 
                    title: "'.(\Yii::t("fe", "Kode Barang")).'", 
                    data: "kode_item", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nama Barang")).'", 
                    data: "barang_nama", 
                    searchable: true 
                },
                { 
                    title: "'.(\Yii::t("fe", "Qty PO")).'", 
                    data: "qty_po", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Satuan PO")).'", 
                    data: "satuan_po", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Qty Diterima")).'", 
                    data: "qty_diterima", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Satuan Diterima")).'", 
                    data: "satuan_terima", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "PO Balance")).'", 
                    data: "po_balance", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Satuan PO Balance")).'", 
                    data: "satuan_balance", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nilai Konversi")).'", 
                    data: "nilai_konversi", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Qty Konversi")).'", 
                    data: "qty_konversi", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Satuan Kecil")).'", 
                    data: "satuan_kecil", 
                    searchable: false 
                },
                {
                    title: "'.(\Yii::t("fe", "Harga Netto (Rp.)")).'",
                    data: "barang_harganetto",
                    searchable: false,
                    render: (data) => {
                        return docoHelper.convertToRupiah(data);
                    }
                },
                { 
                    title: "'.(\Yii::t("fe", "Harga (Rp.)")).'", 
                    data: "harga", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Discount (%)")).'", 
                    data: "discount", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "PPn (%)")).'", 
                    data: "ppn_persen", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Subtotal (Rp.)")).'", 
                    data: "sub_total", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Total (Rp.)")).'", 
                    data: "total", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Catatan PO")).'", 
                    data: "catatan_po", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Dibuat PR")).'", 
                    data: "tgl_pr", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nomor PR")).'", 
                    data: "no_pr", 
                    searchable: true 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nomor Batch")).'", 
                    data: "no_batch", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Tanggal Kadaluarsa")).'", 
                    data: "tgl_kadaluarsa", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nomor Surat Jalan")).'", 
                    data: "no_suratjalan", 
                    searchable: false 
                },
                { 
                    title: "'.(\Yii::t("fe", "Nomor Faktur")).'", 
                    data: "no_faktur", 
                    searchable: false 
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                5,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=4 readonly="true"></div>\'
            ],
            [
                2,
                \'<div class=\"form-group\">' . (preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Html::dropDownList(
                        "supplier_id",
                        "",
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
                4,
                \'<div class=\"form-group\">' . (preg_replace(
                    "/[\n\t\r]/i",
                    '',
                    Html::dropDownList(
                        "payterm_id",
                        "",
                        ArrayHelper::map($list_payterm, 'payterm_id', 'payterm_nama'),
                        [
                            "id" => "filter_status",
                            "class" => "form-control select2",
                            "prompt" => \Yii::t("fe", "-- Pilih Semua--")
                        ]
                    )
                )) . '</div>\'
            ]
        ], 
        {
            5:0,
            2:1,
            11:2,
            4:3,
            13:4,
            30:5
        }, true);

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(document).on("change", ".startDate, .endDate", function(){
            $(".data-filter").click();
        });
    });
', View::POS_END, 'b-index');
?>
