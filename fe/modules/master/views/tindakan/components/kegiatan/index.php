<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kategori
 * @copyright 26 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-kegiatan'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-kegiatan',
                                'data-tab' => 'tab-kegiatan',
                                'data-target' => '#view-kegiatan',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-edit',
                            'attributes'=>[
                                'id' => 'btn-update-kegiatan',
                                'class' => 'spa',
                                'data-options'=>'click',
                                'data-render' => 'update-kegiatan?id=',
                                'data-tab' => 'tab-kegiatan',
                                'data-target' => '#view-kegiatan',
                                'data-type' => 'wp'
                            ]
                        ],
                        // 'delete' => [
                        //     'attributes' => [
                        //         'id' => 'btn-delete-kegiatan',
                        //         'data-additional' => 'data-rm',
                        //         'data-target' => '/master/tindakan/delete-kegiatan?id='
                        //     ]
                        // ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-pdf-kegiatan?'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-excel-kegiatan?'
                            ]
                        ],
                ],'#table-kegiatan');?>    
            
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-kegiatan"></div>
                </div>
                <table id="table-kegiatan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=\Yii::t("fe", "Kode kegiatan");?></th>
                            <th><?=\Yii::t("fe", "Nama kegiatan");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Keterangan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tableKegiatan;

    // Event Ready
    $(document).ready(function() {
        tableKegiatan = $("#table-kegiatan").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            sorting: [[3, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: "/master/tindakan/get-data-kegiatan",
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
                    title: "Detail",
                    data: "detail",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Kode kegiatan")).'", data: "jeniskegiatantindakan_kode"},
                {title: "'.(\Yii::t("fe", "Nama kegiatan")).'", data: "jeniskegiatantindakan_nama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")).'",  data: "jeniskegiatan_namalainnya"},
                {title: "'.(\Yii::t("fe", "Keterangan")).'",  data: "jeniskegiatan_keterangan"},
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-kegiatan").datatableBootstrapFilter(tableKegiatan, []);

        tableKegiatan.on("select", function(e, dt, type, indexes) {
            if (type === "row") {
                var id = tableKegiatan.rows(indexes).data()[0].primary;

                if (id) {
                    $.ajax({
                        type: "GET",
                        url: "/master/tindakan/cek-transaksi-kegiatan?id="+id,
                        success: function(response) {
                            if (response) {
                                $("#btn-delete-kegiatan").attr("disabled", true);
                                $("#btn-update-kegiatan").attr("disabled", true);
                            } else {
                                $("#btn-delete-kegiatan").attr("disabled", false);
                                $("#btn-update-kegiatan").attr("disabled", false);
                            }
                        }
                    });
                }
            }
        });
    });
    ',VIEW::POS_END);
?>

