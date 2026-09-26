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

$this->title = 'Pemesanan Dokumen Rekam Medik Keluar';
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
                        'delete'=>[
                            'attributes'=>[
                                'data-additional'=>'data-rm',
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
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-dok-keluar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=Yii::t('fe', 'Tanggal Pemesanan')?></th>
                            <th><?=Yii::t('fe', 'No Pemesanan')?></th>
                            <th><?=Yii::t('fe', 'Instalasi Tujuan')?></th>
                            <th><?=Yii::t('fe', 'Ruangan Tujuan')?></th>
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
            ajax: baseUrl+"informasi/pemesanan-dokumen-keluar/get-data",
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
                {title: "'.Yii::t('fe', 'Instalasi Tujuan').'", data: "instalasi_tujuan", name: "instalasi_tujuan_id"},
                {title: "'.Yii::t('fe', 'Ruangan Tujuan').'", data: "ruangan_tujuan", name: "ruangantujuan_id"},
                {title: "Status",  data: "status", name: "status"},
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
                [
                    2, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                ], 
                [3,\'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('nomutasioa', '',[], 
                            [
                                'class' => 'form-control selectNoPesan select2',
                                'prompt' => \Yii::t('fe', 'No Pemesanan'),
                            ]
                        )
                    ))).'\'],
                [4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_id','', $list_instalasi, ['id'=>'instalasi_id','class' => 'select2', 'prompt' => \Yii::t('fe', ' ')]))).'\'],
                [5, \''.(preg_replace("/[\n\t\r]/i", '', DepDrop::widget(['name'=>'ruangan_id',
                        'options'=>['id'=>'ruangan_id','class'=>'select2'],
                        'pluginOptions'=>[
                            'depends'=>['instalasi_id'],
                            'placeholder'=>\Yii::t('fe', '--Pilih--'),
                            'url'=>Url::to(['/informasi/pemesanan-dokumen-keluar/dpd-list-ruangan'])
                        ]
                    ]))).'\'],
                [6, 
                    \'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('nomutasioa', '',$list_status, 
                            [
                                'class' => 'form-control select2',
                                'prompt' => '',
                            ]
                        )
                    ))).'\'
                ]
            ], {2:0}, true
        );
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".selectNoPesan").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "/informasi/pemesanan-dokumen-keluar/get-data-nopesan",
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
            templateSelection: function (data, container) {
                $(data.element).attr("data-instalasi", data.instalasi)
                $(data.element).attr("data-ruangan", data.ruangan)
                return data.text;
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
        $(document).on("change", ".selectNoPesan", function(){
            $("#instalasi_id").val($(".selectNoPesan").find(":selected").attr("data-instalasi")).trigger("change").trigger("depdrop:change");
            $("#ruangan_id").on("depdrop:afterChange", function (event, id, value) {
                $(this).val($(".selectNoPesan").find(":selected").attr("data-ruangan")).trigger("change").trigger("depdrop:change");
            });
        })
        tableDokKeluar.on( "select", function ( e, dt, type, indexes ) {
            if ( type === "row" ) {
                var data = tableDokKeluar.rows( indexes ).data()[0].status;
                if(data == "Sudah Dikirim"){
                    $(".data-delete").attr("disabled", true)
                }else{
                    $(".data-delete").attr("disabled", false)
                }
                // console.log(data)
                // do something with the ID of the selected items
            }
        } );

    });


', View::POS_END, 'js-dok-keluar');
?>