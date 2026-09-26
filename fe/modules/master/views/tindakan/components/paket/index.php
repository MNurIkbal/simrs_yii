<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 11:35:09
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-22 15:08:48
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;

$this->title = $title;

?>
<style type="text/css">
.row-mcu {
    background-color: #FCF3CF !important;
    color: #000000;
    /*font-weight: bold;*/
}
</style>
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
    width: 75px;
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
    width: 75px;
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
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-paket'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-paket'
                            ]
                        ],
                        'tambah' => [
                            'title'=>'Tambah',
                            'icon' => 'fa fa-plus',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-paket-a',
                                'data-tab' => 'tab-paket',
                                'data-target' => '#view-paket',
                            ]
                        ],
                        'update' => [
                            'title' => 'Ubah',
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'id' => 'btn-update-paket',
                                // 'data-toggle' => 'modal',
                                // 'data-target' => '#modal_backdrop',
                                // 'data-url' => '/master/cara-bayar/update?id=',
                                'class' => 'spa btn btn-info btn-labeled btn-xs btn-toolbar btn-update-paket',
                                'data-options' => 'click',
                                'data-render' => 'view-paket-a?id=',
                                'data-tab' => 'tab-paket',
                                'data-target' => '#view-paket',
                                'data-type' => 'wp'

                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'btn-delete-paket',
                                'data-additional'=>'data-rm'
                            ]
                        ],
                        'pdf'=>[
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-pdf-tipe-paket?'
                            ]
                        ],
                        'excel'=>[
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-excel-tipe-paket?'
                            ]
                        ],
                    ],'#table-paket');?>    
            
            </div>
            <div class="panel-body">
                <div class="tab-paket"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:#FCF3CF;'></span>Paket MCU</li>
                                    <li><span style='background:#ffffff;'></span>Paket Non MCU</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="table-paket" class="table table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Detail"); ?></th>
                            <th><?=\Yii::t("fe", "Kode paket");?></th>
                            <th><?=\Yii::t("fe", "Nama paket");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                            <th><?=\Yii::t("fe", "Paket MCU");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tablePaket;
    // Event Ready
    $(document).ready(function() {
        generateFilter("tab-paket", "filter-paket");
        // Generate Table
        tablePaket = $("#table-paket").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[4, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "/master/tindakan/get-data-paket",
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
                    data:"detail",
                    searchable: false,
                    orderable: false,
                    
                },
                
                {title: "'.(\Yii::t("fe", "Kode paket")). '", data: "tipepaket_kode"},
                {title: "'.(\Yii::t("fe", "Nama paket")). '", data: "tipepaket_nama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")). '",  data: "tipepaket_namalainnya"},
                {title: "'.(\Yii::t("fe", "Status")). '", data: "status_label",searchable: false},
                {title: "'.(\Yii::t("fe", "Catatan")). '", data: "keterangan_tipepaket"},
                {
                    title: "Status",
                    data: "is_active",
                    searchable: true,
                    orderable: false,
                    visible:false
                },
                {title: "'.(\Yii::t("fe", "Jenis Paket")). '", data: "is_mcu", visible: false},
            ],
            drawCallback: function(e) {
                var api = this.api();
                for (var i = 0; api.rows().count() > i; i++) {
                    var rowData = api.row(i).data();
                    var rowNode = api.row(i).node();
                    if(rowData.is_mcu == true) {
                        $(rowNode).addClass("row-mcu");
                    }
                    else {
                        $(rowNode).removeClass("row-mcu");
                    }
                }
            },
        });
        $(".dataTables_filter").hide();
        $(".filter-paket").datatableBootstrapFilter(tablePaket, [
            [
                8, 
                \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\'
            ],
            [
                9, 
                \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_mcu', '', [1 => 'Paket MCU', 0 => 'Paket Non MCU'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\'
            ],
        ],
        {
            3:0,
            4:1,
            5:2,
            9:3,
            8:4,
            7:5
        }
        );

        tablePaket.on("select", function(e, dt, type, indexes) {
            if (type === "row") {
                var id = tablePaket.rows(indexes).data()[0].primary;
                var is_mcu = tablePaket.rows(indexes).data()[0].is_mcu;
                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "/master/tindakan/cek-transaksi-paket?id="+id,
                        success: function(response) {
                            if (response) {
                                $("#btn-delete-paket").attr("disabled", true);
                            } else {
                                $("#btn-delete-paket").attr("disabled", false);
                            }
                        }
                    });
                }
                
            }
        });
    });
    ',VIEW::POS_END, 'index');
?>