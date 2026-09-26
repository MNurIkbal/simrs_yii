<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = 'Pengiriman Dokumen Rekam Medik Keluar';
$this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['index']];
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
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
	                    'lihat'=>[
	                        'type'=>'link',
	                        'title' => \Yii::t('fe', 'Lihat'),
	                        'icon' => 'fa fa-list-ul',
	                        'method' => 'not-exist',
	                        'attributes' => [
	                            'class' => 'data-detail',                            
	                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/detail?id='
	                        ] 
	                    ],
	                    'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm'
                            ]
                        ]
                    ], "#table-dok-keluar");?>  
                <div class="pull-right">
	                <?=Html::button('<b><i class="fa fa-refresh"></i></b> Muat Ulang', [
	                    'class' => 'btn btn-info btn-labeled btn-xs data-reset',
	                ]);?>
	            </div>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="table-dok-keluar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=Yii::t('fe', 'Tanggal Pengiriman')?></th>
                            <th><?=Yii::t('fe', 'Nomer Pengiriman')?></th>
                            <th><?=Yii::t('fe', 'Instalasi Tujuan Pengiriman')?></th>
                            <th><?=Yii::t('fe', 'Ruangan Tujuan Pengiriman')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
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
    localStorage.clear();
    localStorage.setItem("ruangan", \''.json_encode($ruanganReq['response']['data']).'\');
    var tableDokKeluar;
    
    // Event Ready
    $(document).ready(function() {
        
        // Generate Table
        tableDokKeluar = $("#table-dok-keluar").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "td:first-child"
            },
            sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            oLanguage: {
                sLengthMenu: "'.(\Yii::t('fe', 'dt_length_menu')).'",
                sZeroRecords: "'.(\Yii::t('fe', 'dt_zero_records')).'",
                sEmptyTable: "'.(\Yii::t('fe', 'dt_empty_table')).'",
                sInfoFiltered: "'.(\Yii::t('fe', 'dt_info_filtered')).'",
                sInfoEmpty: "'.(\Yii::t('fe', 'dt_info_empty')).'",
                sInfo: "'.(\Yii::t('fe', 'dt_info')).'",
                oPaginate: {
                    sFirst: "'.(\Yii::t('fe', 'dt_first_page')).'",
                    sPrevious: "'.(\Yii::t('fe', 'dt_previous_page')).'",
                    sNext: "'.(\Yii::t('fe', 'dt_next_page')).'",
                    sLast: "'.(\Yii::t('fe', 'dt_last_page')).'"
                }
            },
            ajax: baseUrl+"informasi/pengiriman-dokumen-keluar/get-data",
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
                {title: "'.Yii::t('fe', 'Tanggal Pengiriman').'",  data: "tgl_kirim"},
                {title: "'.Yii::t('fe', 'Nomer Pengiriman').'",  data: "no_kirimdokrm"},
                {title: "'.Yii::t('fe', 'Instalasi Tujuan Pengiriman').'", data: "instalasi_pemesan"},
                {title: "'.Yii::t('fe', 'Ruangan Tujuan Pengiriman').'", data: "ruangan_pemesan"},
                {title: "Status",  data: "status"},           
                {                    
                    data: "primary",
                    searchable: false,
                    orderable: false,
                    visible: false,
                },
                
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableDokKeluar, 
            [
            	[2, \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ],
            	[3,\'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('no_kirimdokrm', '',[], 
                            [
                                'class' => 'form-control selectNoKirim select2',                                 
                                'prompt' => \Yii::t('fe', 'No Pengiriman'),                                
                            ]
                        )                        
                    ))).'\'],       
                [
                    4,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi_pemesan', '',
                            ArrayHelper::map($instalasiReq['response']['data'], 'instalasi_id', 'instalasi_nama'),
                            [
                                'id' => 'filter_instalasi',
                                'class' => 'form-control select2 dep-to-child',
                                'prompt' => \Yii::t('fe', 'Pilih Instalasi'),
                                'data-url' => '/rm/tra-pemesanan-dok-rekam-medik/get-ruangan',
                                'data-depend_id' => 'filter_ruangan',
                                'data-depend_prompt' => \Yii::t('fe', ''),
                                'data-storage' => 'ruangan',
                                'data-key' => 'ruangan_id',
                            ]
                        )
                    )).'\'
                ],
                [
                    5,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_pemesan', '',
                            ArrayHelper::map($ruanganReq['response']['data'], 'ruangan_id', 'ruangan_nama'),
                            [
                                'id' => 'filter_ruangan',
                                'class' => 'form-control select2 dep-to-parent',
                                'data-url' => '/rm/tra-pemesanan-dok-rekam-medik/get-instalasi',
                                'data-depend_id' => 'filter_instalasi',
                                'prompt' => \Yii::t('fe', '--Pilih Ruangan--')
                            ]
                        )
                    )).'\'
                ],
                [6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status','', $list_status, ['id'=>'status','class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')]))).'\'],
            ]
        );

        // $(".daterange-basic").daterangepicker({
        //     applyClass: "bg-slate-600",
        //     cancelClass: "btn-default",
        //     locale: {
        //         format: "DD-MMMM-YYYY"
        //     }
        // });
        
        dateRangeHelper(\'.startDate\',\'.endDate\',\'.targetDate\');
        
        $(".selectNoKirim").select2({
            placeholder: "'. \Yii::t("fe", "Pilih") .'",
            minimumInputLength: 3, 
            ajax : {
                url: baseUrl+"informasi/pengiriman-dokumen-keluar/search-nomor",
                dataType: \'json\',
                quietMillis: 250,
                data: function (params) {
                  params.tanggal = $(".targetDate").val();
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: \'bigdrop\',
                escapeMarkup: function (m) { return m; },
            },
            cache: true
        });

        // $(".selectNoKirim").select2({
        //     placeholder: "",
        //     minimumInputLength: 3,                  
        //     ajax: {
        //         url: "/informasi/pengiriman-dokumen-keluar/get-data-nokirim",
        //         dataType: "json",
        //         quietMillis: 250,
        //         data: function(term, page){
        //             return{
        //                 q: term,
        //                 page: page
        //             }
        //         },
        //         processResults: function (data) {                
        //           return {
        //             results: data.result
        //           };
        //         }                   
        //     },
        //     dropdownCssClass: "bigdrop",
        //     escapeMarkup: function (m) { return m; },
        // });
    });


', View::POS_END, 'js-dok-keluar');
?>