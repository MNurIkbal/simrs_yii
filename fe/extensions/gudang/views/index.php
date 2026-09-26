<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>
<style type="text/css">
	#no_header .dataTables_wrapper table thead{
    display:none;
}
</style>
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
                    'pdf' => [
                        'title' => Yii::t('fe', 'Cetak Pdf'),
                        'attributes'=>[
                            'data-target'=>Url::home().'gudang/laporan-pemakaian-barang/export-pdf?'
                        ],
                    ],
                    'excel',
                ]);?>                
            </div>
			<div class="panel-body">				
                <div class="row">
                <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">                                             
                            <th width="80"></th>
                            <th></th>
                            <th></th>
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
            sorting: [[7, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.$module.'get-data",
            drawCallback: function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:"current"} ).nodes();
                var number = api.column(0, {page:"current"} ).nodes();
                var last=null;
                var rownum = 1;
                api.column(8, {page:"current"} ).data().each( function ( group, i ) {
                    rownum++;
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            "<tr class=\'group\'>"+
                                "<th>'.(\Yii::t("fe", "Nomor pemakaian")).':</th>"+
                                "<th>"+api.column(8, {page:"current"} ).data()[i]+"</th>"+                                
                                "<th>'.(\Yii::t("fe", "Nama penginput")).':</th>"+
                                "<th>"+api.column(9, {page:"current"} ).data()[i]+"</th>"+
                                "<th>'.(\Yii::t("fe", "Tanggal pemakaian")).':</th>"+
                                "<th>"+api.column(7, {page:"current"} ).data()[i]+"</th>"+
                                "<th></th>"+
                            "</tr>"+                            
                            "<tr class=\'group\'>"+
                                "<th>'.(\Yii::t("fe", "No")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Nama barang")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Qty")).'</th>"+
                                "<th>'.(\Yii::t("fe", "satuan_besar")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Qty")).'</th>"+
                                "<th>'.(\Yii::t("fe", "satuan_kecil")).'</th>"+
                                "<th>'.(\Yii::t("fe", "Keterangan")).'</th>"+
                            "</tr>"
                        );
     
                        last = group;
                        rownum = 1;
                    }
                    $(number).eq(i).html(rownum);
                } );
            },
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama barang")).'", data: "barang_nama"},
                {title: "'.(\Yii::t("fe", "Qty")).'", data: "jumlah_input", searchable: false},
                {title: "'.(\Yii::t("fe", "satuan_besar")).'", data: "satuan_besar", searchable: false},
                {title: "'.(\Yii::t("fe", "Qty")).'", data: "jumlah_pakai", searchable: false},
                {title: "'.(\Yii::t("fe", "satuan_kecil")).'", data: "satuan_kecil", searchable: false},
                {title: "'.(\Yii::t("fe", "Keterangan")).'", data: "keteranganpakai", searchable: false},
                {
                    title: "'.(\Yii::t("fe", "Tanggal pemakaian barang")).'", 
                    visible: false,
                    data: "tgl_pemakaianbarang"
                },
                {
                    title: "'.(\Yii::t("fe", "No pemakaian barang")).'", 
                    visible: false,
                    data: "no_pemakaianbarang",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama penginput")).'",  
                    visible: false,
                    data: "nama_pegawai",
                    orderable: false
                },                
            ]
        });
    	
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    7, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ],                
                [
                    8, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('no_pemakaianbarang', '', [], 
                            [                                
                                'class' => 'form-control select2 nopemakaianbarang',                                                               
                            ]
                        )
                    )
                ).'</div>\'
                ]
            ], {
                7:0,
                1:1,
                8:2,
                9:3
            }, true
        );
        dateRangeHelper(".startDate",".endDate",".targetDate");        
         
        $(".nopemakaianbarang").select2({
            placeholder: "-",
            minimumInputLength: 3,  
            ajax: {
                url: "/gudang/laporan-pemakaian-barang/get-nopemakaian",
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

        // Order by the grouping
        $("#example tbody").on( "click", "tr.group", function () {
            var currentOrder = table.order()[0];
            if ( currentOrder[0] === 8 && currentOrder[1] === "asc" ) {
                table.order( [ 8, "desc" ] ).draw();
            }
            else {
                table.order( [ 8, "asc" ] ).draw();
            }
        } );

    });
    
	', View::POS_END, 'js-kuning')

?>
