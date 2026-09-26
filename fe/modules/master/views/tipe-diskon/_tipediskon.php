<?php
/**
 * 
 * Author: Dede Herdiana 
 * 
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\DocoHelpers;
use app\components\DHtml;

$this->title = DHtml::getTitleMenu('Master Tipe Diskon');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search'=>[
                            'attributes'=>[
                                'data-parent'=>'.filter-td'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-td'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'sp',
                                'data-options' => 'click',
                                'data-render' => 'create?stat=add',
                                'data-content' => 'content-td',
                                'data-type' => 'add',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'class' => 'sp',
                                'data-options' => 'click',
                                'data-render' => 'edit?stat=edit&id=',
                                'data-content' => 'content-td',
                                'data-type' => 'edit',
                                'data-table' => 'table-td',
                                // 'data-url' => '/master/tarif-tindakan/kontrak-manajemen-form?id=',
                            ]
                        ],
                    ], '#table-td');?>
                </div>
            </div>
            
            <div class="panel-body">
                <div class="tab-td">
                    </div>
                <table id="table-td" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    $(document).on("click", ".data-reload", function() {
        tabletD.draw();
    });
    var tabletD;
    $(document).ready(function() {
        generateFilter("tab-td", "filter-td");
        // Generate Table
        tabletD = $("#table-td").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/tipe-diskon/get-tipe-diskon",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                    width:"5%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Tipe Diskon")).'", 
                    data: "tipediskon_nama", 
                    name: "tipediskon_nama",
                    searchable: true,
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "status", 
                    searchable:false,
                    visible:true
                },
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-td").datatableBootstrapFilter(tabletD, [
        ], {
            2:1,
        }, true);
        
        $(".flex-1").hide();
        
        //dateRangeHelper(".startDate",".endDate",".targetDate");
        
        $("#table-td tbody").on("click", "tr", function(){
            try {
                primaryKey = tabletD.row(".selected").data().primary ? tabletD.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit").attr("action",$("#btn-edit").data("url")+primaryKey);
            } else {
                $("#btn-edit").removeAttr("action");
            }
        });
    });
', View::POS_END, 'e-index');

?>