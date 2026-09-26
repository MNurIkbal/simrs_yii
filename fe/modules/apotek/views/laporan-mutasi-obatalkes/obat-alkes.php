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
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
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
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'print',
                    'excel'
                ]);?>
            </div>
            <div class="panel-body">				
                <input type="hidden" value="<?=Yii::$app->docoVars->workspace("instalasi_name")?>" class="hiddenInstalasi">
                <input type="hidden" value="<?=Yii::$app->docoVars->workspace("ruangan_name")?>" class="hiddenRuangan">
                <div class="col-md-12 filter-form"></div>     
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">      
                            <th width="80"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <!-- <tr>
                            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr> -->
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
            sorting: [[7, 'asc']], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'apotek/laporan-mutasi-obatalkes/get-data',
            drawCallback: function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var number = api.column(0, {page:'current'} ).nodes();
                var last=null;
                var rownum = 1;
                api.column(7, {page:'current'} ).data().each( function ( group, i ) {
                    rownum++;
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class=\"group\">'+
                                '<th>".(\Yii::t('fe', 'tglmutasioa')).":</th>'+
                                '<th>'+api.column(6, {page:'current'} ).data()[i]+'</th>'+
                                '<th></th>'+
                                '<th></th>'+
                                '<th>".(\Yii::t('fe', 'instalasi_nama')).":</th>'+
                                '<th>'+api.column(8, {page:'current'} ).data()[i]+'</th>'+
                            '</tr>'+
                            '<tr class=\"group\">'+
                                '<th>".(\Yii::t('fe', 'nomutasioa')).":</th>'+
                                '<th>'+api.column(7, {page:'current'} ).data()[i]+'</th>'+
                                '<th></th>'+
                                '<th></th>'+
                                '<th>".(\Yii::t('fe', 'ruangan_nama')).":</th>'+
                                '<th>'+api.column(10, {page:'current'} ).data()[i]+'</th>'+
                            '</tr>'+
                            '<tr class=\"group\">'+
                                '<th>".(\Yii::t('fe', 'no'))."</th>'+
                                '<th>".(\Yii::t('fe', 'obatalkes_namalain'))."</th>'+
                                '<th>".(\Yii::t('fe', 'qty_satuan_besar'))."</th>'+
                                '<th>".(\Yii::t('fe', 'satuanbesar_nama'))."</th>'+
                                '<th>".(\Yii::t('fe', 'jumlah_mutasi'))."</th>'+
                                '<th>".(\Yii::t('fe', 'satuankecil_nama'))."</th>'+
                            '</tr>'
                        );
     
                        last = group;
                        rownum = 1;
                    }
                    $(number).eq(i).html(rownum);
                } );
            },
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'obatalkes_namalain'))."', data: 'obatalkes_namalain', searchable: false},
                {title: '".(\Yii::t('fe', 'qty_satuan_besar'))."', data: 'qty_satuan_besar', searchable: false},
                {title: '".(\Yii::t('fe', 'satuanbesar_nama'))."', data: 'satuanbesar_nama', searchable: false},
                {title: '".(\Yii::t('fe', 'jumlah_mutasi'))."', data: 'jumlah_mutasi', searchable: false},
                {title: '".(\Yii::t('fe', 'satuankecil_nama'))."', data: 'satuankecil_nama', name: 'ruangan_nama', searchable: false},
                {
                    title: '".(\Yii::t('fe', 'tglmutasioa'))."', 
                    visible: false,
                    data: 'tglmutasioa'
                },
                {
                    title: '".(\Yii::t('fe', 'nomutasioa'))."', 
                    visible: false,
                    data: 'nomutasioa'
                },
                {
                    title: '".(\Yii::t('fe', 'instalasi_nama'))."',  
                    visible: false,
                    data: 'instalasi_nama',
                    searchable: false
                },
                {
                    title: '".(\Yii::t('fe', 'instalasi tujuan'))."',  
                    visible: false,
                    data: 'instalasi_tujuan_id'
                },
                {
                    title: '".(\Yii::t('fe', 'ruangan tujuan'))."', 
                    visible: false,
                    data: 'ruangan_nama'
                },
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, 
            [
                [
                    6, 
                    \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
                ], 
                                 
                [
                    9, 
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('instalasi_tujuan_id', '', 
                            ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), 
                            [

                                'class' => 'form-control select2 selectInstalasi', 
                                'id'=>'filter_instalasi',
                                'prompt' => Yii::t('fe','--Pilih--'),
                            ]
                        )                        
                    )))."\"
                ],                 
                [
                    10, 
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',                         
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2 selectRuangan'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               'placeholder' => '',
                               'url' => '/apotek/end-point/get-ruangan',
                            ]
                        ])
                        )
                    )
                    )."\"
                ], 
            ], {
            }, true
        );
        dateRangeHelper('.startDate','.endDate','.targetDate');                
        $('.selectNomutasi').select2({
                placeholder: '',
                minimumInputLength: 3,                  
                ajax: {
                    url: '/apotek/laporan-mutasi-obatalkes/get-data-nomutasi2',
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
        $('.selectInstalasi').select2({
            placeholder: '',            
        });          
        $('.selectRuangan').select2({
            placeholder: '',            
        });      

        // Order by the grouping
        $('#example tbody').on( 'click', 'tr.group', function () {
            var currentOrder = table.order()[0];
            if ( currentOrder[0] === 7 && currentOrder[1] === 'asc' ) {
                table.order( [ 7, 'desc' ] ).draw();
            }
            else {
                table.order( [ 7, 'asc' ] ).draw();
            }
        } );
    });

        $(document).on('click', '.data-reset', function(){            
            $('.advancedFilter [type=reset]').click();     
                               
            $('.selectInstalasi').val('').trigger('change');       
            $('.selectRuangan').val('').trigger('change');
            $('.selectNomutasi').val('').trigger('change');

            $('.advancedFilterDo').click();     
        });        
        ", View::POS_END, 'js-kuning');

?>

<script>
</script>
