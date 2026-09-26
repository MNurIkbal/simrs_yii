<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-03 11:12:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-06 14:23:08
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace('instalasi_name')), 'url' => []];
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
                    'reset',
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',
                            'data-target' => Url::home().($module.'view?id='),
                        ] 
                    ]
                ]);?>
            </div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12 filter-form">
						
					</div>
				</div>
				<div class="row">
					<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "tanggal pemakaian");?></th>
                            <th><?=\Yii::t('fe','nama penginput')?></th>
                            <th><?=\Yii::t("fe", "Nomor pemakaian");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 

$this->registerJs("
	// Global Var
	var table;

	$(function(){
		table = $('#example').docoTabel({
	        filter: true,
	        columnDefs: [{
	            orderable: false,
	            className: 'select-checkbox',
	            targets:   0
	        }],
	        select: {
	            style:    'os',
	            selector: 'tr'
	        },
	        sorting: [[2, 'asc']], 
	        displayLength: 10,
	        processing: true,
	        serverSide: true,
	        stateSave: true,
	        scrollX: true,
	        ajax: baseUrl+'".$module."get-data',
	        columns: [
	            {
	                data : null,
	                render : function ( data, type, full, meta ) {
	                    return null;
	                },
	                searchable: false,
	                orderable: false
	            },
	            {
	                title: 'No',
	                data: 'rowNum',
	                searchable: false,
	                orderable: false
	            },
	            {
	                title: '".(\Yii::t('fe', 'tanggal pemakaian'))."', 
	                data: 'tgl_pemakaianbarang'
	            },
	            {
	            	title: '".(\Yii::t('fe','nama penginput'))."',
	            	data: 'nama_pegawai',
	            	searchable:false
	            },
	            {
	                title: '".(\Yii::t('fe', 'Nomor pemakaian'))."', 
	                data: 'no_pemakaianbarang'
	            },            
	        ],
	    });
	    $('.dataTables_filter').hide();
	    $('.filter-form').datatableBootstrapFilter(table, [
	    	[
	    		2,
	    		'<div class=\"input-group\"><input type=\"text\" id=\"rangeDemoStart\" class=\"form-control startDate\"/><span class=\"input-group-addon\" style=\"border-left: 0; border-right: 0;\">-</span><input type=\"text\" id=\"rangeDemoFinish\" class=\"form-control endDate\"/><input type=\"text\" style=\"display:none\" class=\"targetDate\" col-index=2></div>'
	    	],      
	    ]);
	    dateRangeHelper('.startDate','.endDate','.targetDate');
	    $('.no_pemakaianbarang').select2({
            placeholder: '',
            minimumInputLength: 3,  
            ajax: {
                url: '/gudang/laporan-pemakaian-barang/get-nopemakaian',
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

        // Order by the grouping
        $('#example tbody').on( 'click', 'tr.group', function () {
            var currentOrder = table.order()[0];
            if ( currentOrder[0] === 8 && currentOrder[1] === 'asc' ) {
                table.order( [ 8, 'desc' ] ).draw();
            }
            else {
                table.order( [ 8, 'asc' ] ).draw();
            }
        } );

    });
	", View::POS_END, 'js-kuning');
?>
