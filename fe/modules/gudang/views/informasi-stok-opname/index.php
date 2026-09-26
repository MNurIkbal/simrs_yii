<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Stok Opname Barang
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


$this->title = Yii::t('fe', 'Informasi Stok Opname Barang');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Gudang'), 'url' => ['index']];
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
                        <h3 class="panel-title"><b>Informasi Stok Opname</b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix"> 
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    // 'lihat' => [
                    //     'type' => 'link',
                    //     'title' => \Yii::t('fe', 'Lihat'),
                    //     'icon' => 'fa fa-eye',
                    //     'method' => '#',
                    //     'attributes' => [
                    //         'class' => 'data-lihat',
                    //         'data-target' => Url::home().('gudang/informasi-stok-opname/view?id='),
                    //     ] 
                    // ],
                    'lihat-detail' => [
                        'title' => 'Detail',
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'data-options'=>'click',
                            'id' => 'data-lihat',
                            'data-id' => '',
                            'class' => 'data-detail'
                        ]
                    ],
                ],'#example');
                ?>
            </div>
            <div class="panel-body">
                <div class="filter-form">
                    
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Tanggal stok opname"); ?></th>
                            <th><?= \Yii::t("fe", "Nomor formulir stok opname"); ?></th>
                            <th><?= \Yii::t("fe", "Nomor stok opname"); ?></th>
                            <!-- <th><?= \Yii::t("fe", "Harga netto fisik"); ?></th> -->
                            <!-- <th><?= \Yii::t("fe", "Harga netto sistem"); ?></th> -->
                            <!-- <th><?= \Yii::t("fe", "Selisih"); ?></th> -->
                        </tr>
                    </thead>
                    <tbody>                                    
                        <tr>
                            <td class="text-center" colspan="5"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ////////////////////////////////////////////////////////////////////////// -->
<div id="modalLihat" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Detail Penerimaan</h5>
            </div>
            <div class="modal-body">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="column-1">
                                <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                            </div>
                            <div class="column-2">
                                <h3 class="panel-title"><b>Detail Informasi Stok Opname </b></h3>
                                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                            </div>
                        </div>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="panel-toolbar clearfix">
                        <?=DocoHelpers::generateToolbar([
                            'pdf' =>[
                                'type' => 'button',
                                "attributes" => [
                                    'data-target' => "",
                                    "id" => "cetak-pdf"
,                                    'data-options' => 'link',
                                    'target' => "_blank"
                                ]
                            ]
                        ]);?>
                    </div>

                    <div class="panel-body">
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>Tanggal SO</b></label>
                                    <div class="col-sm-7"> <b>:</b> <span class="tglstokopname"></span> </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>Tanggal Formulir</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="tglformulir"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>Nomor SO</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="nostokopname"></span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>No Formulir</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="noformulir"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>Jenis Stok</b></label>
                                    <div class="col-sm-7 "> <b>:</b> <span class="jenis_stokopname"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-striped table-condensed table-hover table-detail" style="width:100%" id="tableDetailPenerimaan">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="80">No</th>
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Kelompok Barang");?></th>
                                        <th><?=\Yii::t("fe", "Sub Kelompok Barang");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                        <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                                        <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                                        <th><?=\Yii::t("fe", "Selisih");?></th>
                                        <th><?=\Yii::t("fe", "Kondisi");?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>Total Stok Sistem</b></label>
                                    <div class="col-sm-7"> <b>:</b> <span class="stok_sistem"></span> </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>Total Stok Fisik</b></label>
                                    <div class="col-sm-7"> <b>:</b> <span class="stok_fisik"></span> </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom: 15px">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b>Selisih</b></label>
                                    <div class="col-sm-7"> <b>:</b> <span class="stok_selisih"></span> </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
            </div>
        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////// -->

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
	        // stateSave: true,
	        scrollX: true,   
            ajax: baseUrl+'gudang/informasi-stok-opname/get-data',
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
                {title: '" . (\Yii::t('fe', 'Tanggal Formulir')) . "', data: 'tglstokopname'},
                {title: '" . (\Yii::t('fe', 'No Formulir')) . "', data: 'noformulir'},
                {title: '" . (\Yii::t('fe', 'No Stok Opname')) . "', data: 'nostokopname'},
                // {title: '" . (\Yii::t('fe', 'Harga netto fisik')) . "',  data: 'totalharga_fisik', searchable: false, orderable: false},  
                // {title: '" . (\Yii::t('fe', 'Harga netto sistem')) . "',  data: 'totalharga_sistem', searchable: false, orderable: false},    
                // {title: '" . (\Yii::t('fe', 'Selisih')) . "',  data: 'selisih', searchable: false, orderable: false},    
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
        	[
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value='".date('d-M-Y')."' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' value='".date('d-M-Y')."' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
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
                url: '/gudang/informasi-stok-opname/get-no-formulir',
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
    
    let stokTotal = {
        fisik: 0,
        sistem: 0,
        selisih: 0,
    }

	", VIEW::POS_END, 'js-kunings');

$this->registerJs($this->render('js/index.js'));
?>