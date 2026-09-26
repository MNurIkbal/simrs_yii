<?php

/**
 * @author Randy Vianda Putra
 * @todo Laporan Detail Stok Opname Barang
 * @copyright 17 April 2018 aweutist
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


$this->title = Yii::t('fe', 'Laporan Detail Stok Opname Barang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Laporan'), 'url' => ['index']];
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
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'pdf',
                    'excel'
                ],'#example');
                ?>
            </div>
            <div class="panel-body">
                <div class="panel-body filter-form">
                    
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Tanggal stok opname"); ?></th>
                            <th><?= \Yii::t("fe", "Nomor stok opname"); ?></th>
                            <th><?= \Yii::t("fe", "Nama barang"); ?></th>
                            <th><?= \Yii::t("fe", "Jumlah sistem"); ?></th>
                            <th><?= \Yii::t("fe", "Jumlah fisik"); ?></th>
                            <th><?= \Yii::t("fe", "Harga netto sistem"); ?></th>
                            <th><?= \Yii::t("fe", "Harga netto fisik"); ?></th>
                            <th><?= \Yii::t("fe", "Selisih jumlah"); ?></th>
                            <th><?= \Yii::t("fe", "Selisih harga netto"); ?></th>
                        </tr>
                    </thead>
                    <tbody>                                    
                        <!-- <tr>
                            <td class="text-center" colspan="3"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 

$this->registerJs("
	var table;
	// Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    $(document).ready(function(){
    	table = $('#example').docoTabel({
            filter: true,
            sorting: [[1, 'asc']], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,     
            ajax: baseUrl+'gudang/laporan-detail-stok-opname/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '" . (\Yii::t('fe', 'Tanggal stok opname')) . "', data: 'tglstokopname'},
                {title: '" . (\Yii::t('fe', 'Nomor stok opname')) . "', data: 'nostokopname'},
                {title: '" . (\Yii::t('fe', 'Nama barang')) . "', data: 'barang_nama', searchable: false},
                {title: '" . (\Yii::t('fe', 'Jumlah sistem')) . "',  data: 'volume_sistem', searchable: false},    
                {title: '" . (\Yii::t('fe', 'Jumlah fisik')) . "',  data: 'volume_fisik', searchable: false, orderable: false},                                    
                {title: '" . (\Yii::t('fe', 'Harga netto sistem')) . "',  data: 'harga_sistem', searchable: false, orderable: false},    
                {title: '" . (\Yii::t('fe', 'Harga netto fisik')) . "',  data: 'harga_fisik', searchable: false, orderable: false},  
                {title: '" . (\Yii::t('fe', 'Selisih jumlah')) . "',  data: 'selisih_jumlah', searchable: false, orderable: false},    
                {title: '" . (\Yii::t('fe', 'Selisih harga netto')) . "',  data: 'selisih_harga', searchable: false, orderable: false},  
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
        	[
                1,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
            	2, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('nama_obat', '', array(), 
                            [
                                'class' => 'form-control select2 nama_obat', 
                                'prompt' => '', 
                                'col-index' => 3
                            ]
                        )
                        )
                )."<div>\"
            ],
            
        	
        ]);
        dateRangeHelper('.startDate','.endDate','.targetDate');
        dateRangeHelper('.startDate1','.endDate2','.targetDate1');
        $('.nama_obat').select2({
            language: {
                errorLoading: function () { return 'Searching...' } 
            },
            placeholder: '',
            minimumInputLength: 3,  
            ajax: {
                url: '/gudang/laporan-detail-stok-opname/get-no-so',
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
    

	", VIEW::POS_END, 'js-kunings');
?>