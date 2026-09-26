<?php

use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
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
            	<?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ]
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <div class="container-fluid">
                <table id="rekap-penerimaan-barang" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'No.'); ?></th>
                            <th><?=\Yii::t('fe', 'Kode Supplier'); ?></th>
                            <th><?=\Yii::t("fe", 'Nama Supplier'); ?></th>
                            <th><?=\Yii::t("fe", 'Tanggal Penerimaan'); ?></th>
                            <th><?=\Yii::t("fe", 'Nomor Penerimaan'); ?></th>
                            <th><?=\Yii::t("fe", 'Diterima Oleh'); ?></th>
                            <th><?=\Yii::t("fe", 'Status Penerimaan'); ?></th>
                            <th><?=\Yii::t("fe", 'Tanggal PO'); ?></th>
                            <th><?=\Yii::t("fe", 'Tanggal Validasi PO'); ?></th>
                            <th><?=\Yii::t("fe", 'No. PO'); ?></th>
                            <th><?=\Yii::t("fe", 'No. Surat Jalan'); ?></th>
                            <th><?=\Yii::t("fe", 'No. Faktur'); ?></th>
                            <th><?=\Yii::t("fe", 'Total Harga (Rp.)'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
                </div>
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
    table = $("#rekap-penerimaan-barang").docoTabel({
        filter: true,
        sorting: [[2, "asc"],[4, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        scrollY : true,
        ajax: baseUrl+"gudang/laporan-rekap-penerimaan-barang/get-data",
        columns: [
            {
                title: "No.",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Kode Supplier",
                data: "supplier_kode",
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Nama Supplier")).'",
                data: "supplier_nama"
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'",
                data: "tgl_penerimaan",
            },
            {
                title: "'.(\Yii::t("fe", "Nomor Penerimaan")).'",
                data: "no_penerimaan"
            },
            {
                title: "'.(\Yii::t("fe", "Diterima Oleh")).'",
                data: "diterima_oleh",
                searchable:false
            },
            {
                title: "'.(\Yii::t("fe", "Status Penerimaan")).'",
                data: "status_penerimaan",
                searchable:false,
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal PO")).'",
                data: "tgl_po",
                searchable:false,
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Validasi PO")).'",
                data: "tgl_validasi_po",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "No. PO")).'",
                data: "nomor_po"
            },
            {
                title: "'.(\Yii::t("fe", "No. Surat Jalan")).'",
                data: "no_suratjalan",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "No. Faktur")).'",
                data: "no_faktur",
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Total Harga (Rp.)")).'",
                data: "total",
                searchable: false,
            }
        ]
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
     
        [
            3,
            \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'. date('d-M-Y') .'" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y') .'" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
        ],
    ],{
    	3:0,
    	2:1,
    	4:2,
    	9:3,
    	1:4
    });

    dateRangeHelper(".startDate", ".endDate", ".targetDate");
});
', View::POS_END, 'b-index');
?>