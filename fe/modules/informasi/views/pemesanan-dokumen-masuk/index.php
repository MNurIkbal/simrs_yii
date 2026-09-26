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

$this->title = 'Pemesanan Dokumen Rekam Medik Masuk';
$this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->

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
                        'kirim'=>[
                            'type'=>'link',
                            'title' => \Yii::t('fe', 'Kirim'),
                            'icon' => 'fa fa-list-ul',
                            'method' => 'not-exist',
                            'attributes' => [
                                'class' => 'data-kirim',                            
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/kirim?id='
                            ] 
                        ],
                    ], "#table-dok-masuk");?>  
                <div class="pull-right">
	                <?=Html::button('<b><i class="fa fa-refresh"></i></b> Muat Ulang', [
	                    'class' => 'btn btn-info btn-labeled btn-xs data-reset',
	                ]);?>
	            </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-dok-masuk" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=Yii::t('fe', 'Tanggal Pemesanan')?></th>
                            <th><?=Yii::t('fe', 'No Pemesanan')?></th>
                            <th><?=Yii::t('fe', 'Instalasi Asal')?></th>
                            <th><?=Yii::t('fe', 'Ruangan Asal')?></th>
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
    localStorage.clear();
    localStorage.setItem("ruangan", \''.json_encode($ruanganReq['response']['data']).'\');
    // Global Var
    var tableDokmasuk;
    
    // Event Ready
    $(document).ready(function() {
        
        // Generate Table
        tableDokmasuk = $("#table-dok-masuk").docoTabel({
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
            ajax: baseUrl+"informasi/pemesanan-dokumen-masuk/get-data",
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
                {title: "'.Yii::t('fe', 'Tanggal Pemesanan').'",  data: "tgl_pesandokrm"},
                {title: "'.Yii::t('fe', 'No Pemesanan').'",  data: "no_pesandokrm"},
                {title: "'.Yii::t('fe', 'Instalasi Asal').'", data: "instalasi_pemesan"},
                {title: "'.Yii::t('fe', 'Ruangan Asal').'", data: "ruangan_pemesan"},
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
        $(".filter-form").datatableBootstrapFilter(tableDokmasuk, 
            [
            	[
                    2, 
                    \'<div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span><input type="text" class="form-control daterange-basic" value="" placeholder="'.(\Yii::t('fe', 'Tanggal Pemesanan')).'" col-index="2"></div>\'
                ], 
            	[3,\'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('no_pesandokrm', '',[], 
                            [
                                'class' => 'form-control select2 selectNoPesan',                                 
                                'prompt' => \Yii::t('fe', 'No Pemesanan'),                                
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
                
            ], {
                2:0,
                3:1,
                4:2,
                5:3,
                6:4
            }
        );

        $(".daterange-basic").daterangepicker({
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
        
        var $eventSelect = $(".selectNoPesan");
        $("#ruangan_id").prop("disabled", true);
        $eventSelect.select2({
            placeholder: "",
            minimumInputLength: 3,                  
            ajax: {
                url: "/informasi/pemesanan-dokumen-masuk/get-data-nopesan",
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
        
        $eventSelect.on("select2:select", function(e) {
            var response = e.params.data;
            $("#instalasi_id").val(response.instalasi_pemesan_id).trigger("change");
            $("#ruangan_id").prop("disabled", false);
            $("#ruangan_id").val(response.ruanganpemesan_id).trigger("change");
        });

        $("#instalasi_id").on("change", function() {
            var instalasi_id = $(this).val();
            $.ajax({
                type: "POST",
                url: "/informasi/pemesanan-dokumen-masuk/get-list-ruangan",
                data: "instalasi_id=" + instalasi_id,
                success: function(data) {
                    console.log(data);
                    $("#ruangan_id").prop("disabled", false);
                    $("#ruangan_id").html(data);
                }
            });
        });

        $(document).on("click", "#table-dok-masuk tr", function(){
            var tbl = tableDokmasuk.row(".selected").data();
            console.log(tbl);
            if (typeof tbl == "undefined") {
                $(".data-kirim").show();
                return true;
            }
            if(tbl.status_pesan == 399){
                $(".data-kirim").hide();
            }else{
                $(".data-kirim").show();
            }
        }); 
    });


', View::POS_END, 'js-dok-masuk');
?>