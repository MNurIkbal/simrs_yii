<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-12 15:11:55
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-12 16:20:46
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('fe', 'Informasi Stok Opname');
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=Yii::t('fe', 'Pencarian');?></legend>
                    <div class="row">
                        <div class="col-md-12 filter-form"></div>
                    </div>
                </fieldset>
                <br />

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=$this->title;?></legend>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Tanggal stok opname");?></th>
                                <th><?=\Yii::t("fe", "No stok opname");?></th>
                                <th width="1"><?=\Yii::t("fe", "Detail");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="4"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<div id="modal_backdrop_full" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
<?php 

$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"apotek/informasi-stok/get-data-stokopname",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal stok opname")).'", data: "tglstokopname"},
                {title: "'.(\Yii::t("fe", "No stok opname")).'", data: "nostokopname"},
                {
                    title: "'.(\Yii::t("fe", "Detail")).'",
                    data: "detail",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span><input type="text" class="form-control daterange-basic" value="" placeholder="'.(\Yii::t('fe', 'Tanggal stok opname')).'" col-index="1"></div>\'
                ], [
                    2, 
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('nostokopname', '', array(), 
                            [
                                'class' => 'form-control select2 nostokopname', 
                                'prompt' => \Yii::t('fe', 'No stok opname'), 
                                "col-index" => "2"
                            ]
                        )
                    )).'<span class="input-group-addon"><span class="cursor-pointer" action="'.Url::home().'kasir/modal-search/modal-stok-opname" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>\'
                ]
            ], {
                1:0,
                2:4
            }, true
        );

        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("01-m-Y")).'", autoUpdateInput: false,
            endDate: "'.(date("d-m-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
        $(".nostokopname ").select2({
                placeholder: "'.\Yii::t('fe', 'No stok opname').'",
                minimumInputLength: 2,  
                ajax: {
                    url: "/apotek/informasi-stok/get-nostok",
                    dataType: "json",
                    quietMillis: 250,
                    data: function(term, page){
                        return{
                            q: term,
                            page: page
                        }
                    },
                    processResults: function (data) {                
                      return {
                        results: data.result
                      };
                    }                   
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            });
    	
    });
', View::POS_END, 'b-index');
?>