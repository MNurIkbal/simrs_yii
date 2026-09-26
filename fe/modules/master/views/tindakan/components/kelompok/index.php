<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kelompok
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
                                'data-parent'=>'.filter-kelompok'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-kelompok',
                                'data-tab' => 'tab-kelompok',
                                'data-target' => '#view-kelompok',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-edit',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options'=>'click',
                                'data-render' => 'update-kelompok?id=',
                                'data-tab' => 'tab-kelompok',
                                'data-target' => '#view-kelompok',
                                'data-type' => 'wp'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'class' => 'hidden',
                                'data-additional' => 'data-rm',
                                'data-target' => '/master/tindakan/delete-kelompok?id='
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-pdf-kelompok?'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-excel-kelompok?'
                            ]
                        ],
                    ],'#table-kelompok');?>    
            
            </div>
            <div class="panel-body">
              
                <div class="tab-kelompok">
                </div>

                <table id="table-kelompok" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=\Yii::t("fe", "Kode kelompok");?></th>
                            <th><?=\Yii::t("fe", "Nama kelompok");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Cyto (%)");?></th>
                            <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tableKelompok;

    // Event Ready
    $(document).ready(function() {
        generateFilter("tab-kelompok","filter-kelompok");
        // Generate Table
        tableKelompok = $("#table-kelompok").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0,
                width: "5%"
            }],
            select: {
                style:    "os",
                selector: "td:first-child"
            },
            sorting: [[4, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "/master/tindakan/get-data-kelompok",
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
                {title: "'.(\Yii::t("fe", "Kode kelompok")).'", data: "kelompoktindakan_kode"},
                {title: "'.(\Yii::t("fe", "Nama kelompok")).'", data: "kelompoktindakan_nama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")).'",  data: "kelompoktindakan_namalainnya"},
                {title: "'.(\Yii::t("fe", "Cyto (%)")).'",  data: "kelompoktindakan_persencyto", searchable: false},
                {title: "'.(\Yii::t("fe", "Diskon (%)")).'",  data: "kelompoktindakan_persendiskon", searchable: false},
                {title: "'.(\Yii::t("fe", "Status")).'",  data: "status"},
                {title: "'.(\Yii::t("fe", "Catatan")).'", data: "catatan"},
            ],
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 2,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-kelompok").datatableBootstrapFilter(tableKelompok, [
            [
                8, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\'
            ],
        ],
        {
            3:0,
            4:1,
            5:2,
            8:4,
            9:3
        }, true);
    });
    ',VIEW::POS_END);
?>
