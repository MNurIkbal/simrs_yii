<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-08 18:16:17
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-09 11:42:44
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
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Apotek'), 'url' => []];
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
            <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],                                       
                    // 'print',
                    'pdf',
                    'excel'
                ]);?>     
            </div>
            <div class="panel-body">				
                <div class="col-md-12 filter-form"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead style="display: none;">
                        <tr class="bg-inverse">                                        
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal retur");?></th>
                            <th><?=\Yii::t("fe", "No retur");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Resep");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>      
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
<!-- <script src=""></script> -->
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));

    $this->registerJs("     
        
        //global variable
        var table;
        $(document).ready(function(){
            table = $('#example').docoTabel({
                filter: true,
                sorting: [[7,'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                // fixedColumns: {
                //     leftColumns: 1
                // },  
                ajax: baseUrl+'apotek/laporan-retur/get-data',
                drawCallback: function ( settings ) {

                    var api = this.api();
                    console.log(api);
                    var rows = api.rows( {page:'current'} ).nodes();
                    var number = api.column(0, {page:'current'} ).nodes();
                    var last=null;
                    var rownum = 1;
                    var total_all = 0;
                    api.column(7, {page:'current'} ).data().each( function ( group, i ) {

                        rownum++;
                        if ( last !== group ) {
                            
                            $(rows).eq( i ).before(
                                '<tr class=\"group\">'+
                                    '<th>".(\Yii::t('fe', 'Tanggal retur')).":</th>'+
                                    '<th>'+api.column(6, {page:'current'} ).data()[i]+'</th>'+
                                    '<th>".(\Yii::t('fe', 'No resep')).":</th>'+                                    
                                    '<th>'+api.column(9, {page:'current'} ).data()[i]+'</th>'+
                                    '<th>".(\Yii::t('fe', 'Total Retur')).":</th>'+                                    
                                    '<th class=\"total-retur\">'+total_all+'</th>'+
                                '</tr>'+
                                '<tr class=\"group\">'+
                                    '<th>".(\Yii::t('fe', 'Nomor retur')).":</th>'+
                                    '<th>'+api.column(7, {page:'current'} ).data()[i]+'</th>'+                                                                        
                                    '<th>".(\Yii::t('fe', 'Nama pasien')).":</th>'+
                                    '<th>'+api.column(8, {page:'current'} ).data()[i]+'</th>'+
                                    '<th></th>'+
                                    '<th></th>'+
                                '</tr>'+
                                '<tr class=\"group'+api.column(7, {page:'current'} ).data()[i]+'\">'+
                                    '<th>".(\Yii::t('fe', 'No'))."</th>'+
                                    '<th>".(\Yii::t('fe', 'nama obat alkes'))."</th>'+
                                    '<th>".(\Yii::t('fe', 'Tglkadaluarsa'))."</th>'+
                                    '<th>".(\Yii::t('fe', 'Qty retur'))."</th>'+
                                    '<th>".(\Yii::t('fe', 'Harga satuan'))."</th>'+
                                    '<th>".(\Yii::t('fe', 'Jumlah'))."</th>'+
                                '</tr>'
                            );
                            total_all += api.column(5, {page:'current'} ).data()[i];
                            last = group;
                            rownum = 1;                            
                        }
                        total_all = 0;
                        $(number).eq(i).html(rownum);
                    } );                    
                },
                columns: [                    
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "nama obat alkes")."',
                        data: 'obatalkes_nama',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Tanggal kadaluarsa")."',
                        data: 'tglkadaluarsa',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Qty retur")."',
                        data: 'qty_retur',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Harga satuan")."',
                        data: 'hargasatuan',
                        searchable: false,
                        orderable: false,
                        render: function (data, type) {
                            return docoHelper.convertToRupiah(data);
                        }
                    },
                    {
                        title: '".\Yii::t("fe", "Jumlah")."',
                        data: 'jumlah',
                        searchable: false,
                        orderable: false,
                        render: function (data, type) {
                            return docoHelper.convertToRupiah(data);
                        }
                    },
                    {
                        title: '".\Yii::t("fe", "Tanggal retur")."',
                        data: 'tgl_retur',       
                        visible: false                 
                    },
                    {
                        title: '".\Yii::t("fe", "Nomor retur")."',
                        data: 'no_returresep',                        
                        visible: false
                    },
                    {
                        title: '".\Yii::t("fe", "Nama Pasien")."',
                        data: 'nama_pasien',                
                        searchable: false,
                        visible: false
                    },
                    {
                        title: '".\Yii::t("fe", "No resep")."',
                        data: 'noresep',                        
                        visible: false
                    },                    
                    {
                        title: '".\Yii::t("fe", "Total retur")."',
                        data: 'total_retur',
                        searchable: false,
                        orderable: false,
                        visible: false,
                    },
                ],
                preDrawCallback: function (settings) {
                    let thisTable = new $.fn.dataTable.Api( settings );
                    if (thisTable.column(6).search() == '' || thisTable.column(6).search() == null) { // if empty set today
                        let todayFormated = convertTanggaldMY(new Date());
                        thisTable.column(6).search(todayFormated+' - '+todayFormated);
                    }
                },
            });

            $('.dataTables_filter').hide();
            $('.filter-form').datatableBootstrapFilter(table, [
                    [
                       6,
                        \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
                    ], 
                    [
                        7,
                        \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                            Html::dropDownList('no_returresep', '', array(), [
                                            'class' => 'form-control select2 selectNoRetur',
                                            'col-index'=>3,
                                ])
                        )
                        )."</div>\"
                    ],                   
                    [
                        9,
                        \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                            Html::dropDownList('noresep', '', array(), [
                                            'class' => 'form-control select2 selectResep',
                                ])))."</div>\"
                    ],   

                ],
            );         
            dateRangeHelper('.startDate','.endDate','.targetDate');   
            $('.selectNoRetur').select2({
                placeholder: '-',
                minimumInputLength: 4,  
                ajax: {
                    url: '/apotek/informasi-retur/get-no-retur',
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
            $('.selectResep').select2({
                placeholder: '-',
                minimumInputLength: 2,  
                ajax: {
                    url: '/apotek/informasi-retur/get-no-resep',
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
        $('.carabayar').select2({
            placeholder: '-',
        })

    ", VIEW::POS_END, 'js-kuning'
);

?>
