<?php

/**
 * @author Randy Vianda Putra
 * @copyright 17 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => []];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
             <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'print',
                        'excel'
                    ]);?>
                </div>
            </div>
            <div class="panel-body">	                	
                <div class="filter-form">
                    
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Mutasi");?></th>
                            <th><?=\Yii::t("fe", "No Stok Opname");?></th>
                            <th><?=\Yii::t("fe", "Harga Netto Sistem");?></th>
                            <th><?=\Yii::t("fe", "Harga Netto Fisik");?></th>
                            <th><?=\Yii::t("fe", "Selisih");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src=""></script>
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));

    $this->registerJs("
        // Global Var
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#example').docoTabel({
            filter: true,
            sorting: [[1, 'asc']], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'apotek/laporan-stok/get-data-stokopname',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Mutasi'))."', data: 'tglstokopname'},
                {title: '".(\Yii::t('fe', 'No Stok Opname'))."', data: 'nostokopname'},
                {title: '".(\Yii::t('fe', 'Harga Netto Sistem'))."',  data: 'totalharga_sistem', searchable: false},
                {title: '".(\Yii::t('fe', 'Harga Netto Fisik'))."', data: 'totalharga_fisik', searchable: false},
                {title: '".(\Yii::t('fe', 'Selisih'))."', data: 'selisih', searchable: false},                
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            
            [
                1,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                2,
                \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',                                                 
                        Html::dropDownList('nostokopname', '', array(), 
                            [
                                'class' => 'form-control select2 nostokopname', 
                                'prompt' => \Yii::t('fe', 'No Stok Opname'), 
                            ]
                        )

                        )
                    )
                )."\"
            ]
        ] );    

        dateRangeHelper('.startDate','.endDate','.targetDate');

        $('.nostokopname').select2({
            allowClear: true,
            placeholder: '".\Yii::t("fe", "No Mutasi")."',
            minimumInputLength: 3,              
            ajax: {
                url: '/apotek/laporan-stok/get-data-nostokopname',
                dataType: 'json',
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
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });        

    });

    $(document).on('click', '.data-excel', function(e){
        e.preventDefault();
        window.open(baseUrl+'apotek/laporan-stok/export-excel?'+$.param(table.ajax.params()));
        return false;
    });

        ");
?>

<script>
</script>
