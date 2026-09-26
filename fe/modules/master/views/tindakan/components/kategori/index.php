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
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-kategori'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-kategori',
                                'data-tab' => 'tab-kategori',
                                'data-target' => '#view-kategori',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-edit',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options'=>'click',
                                'data-render' => 'update-kategori?id=',
                                'data-tab' => 'tab-kategori',
                                'data-target' => '#view-kategori',
                                'data-type' => 'wp'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'class' => 'hidden',
                                'data-additional' => 'data-rm',
                                'data-target' => '/master/tindakan/delete-kategori?id='
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-pdf-kategori?'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-excel-kategori?'
                            ]
                        ],
                    ],'#table-kategori');?>    
            
            </div>
            <div class="panel-body">
                
                    <div class="tab-kategori">

                    </div>
                <table id="table-kategori" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=\Yii::t("fe", "Kode kategori");?></th>
                            <th><?=\Yii::t("fe", "Nama kategori");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
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
    var tableKategori;

    // Event Ready
    $(document).ready(function() {
        generateFilter("tab-kategori", "filter-kategori");
        // Generate Table
        tableKategori = $("#table-kategori").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "td:first-child"
            },
            sorting: [[4, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "/master/tindakan/get-data-kategori",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                    width: "7%",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'", 
                    data: "detail", 
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Kode kategori")).'", data: "kategori_kode"},
                {title: "'.(\Yii::t("fe", "Nama kategori")).'", data: "kategoritindakan_nama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")).'",  data: "kategoritindakan_namalainnya"},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status", name: "is_active"},
                {title: "'.(\Yii::t("fe", "Catatan")).'", data: "catatan"},
            ],
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 2,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-kategori").datatableBootstrapFilter(tableKategori, [
            [
                6, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\'
            ]
        ],
        {
            3:0,
            4:1,
            5:2,
            7:3,
            6:4
        }, true);
    });
    ',VIEW::POS_END);
?>

