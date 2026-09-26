<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 10:05:45
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-22 15:00:28
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-tindakan'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-tindakan',
                                'data-tab' => 'tab-tindakan',
                                'data-target' => '#view-tindakan',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-edit',
                            'attributes'=>[
                                'id' => 'btn-update-tindakan',
                                'class' => 'spa',
                                'data-options'=>'click',
                                'data-render' => 'update-tindakan?id=',
                                'data-tab' => 'tab-tindakan',
                                'data-target' => '#view-tindakan',
                                'data-type' => 'wp'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'btn-delete-tindakan',
                                'data-additional' => 'data-rm',
                                'data-target' => '/master/tindakan/delete-tindakan?id='
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-pdf-tindakan?'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-excel-tindakan?'
                            ]
                        ],
                    ],'#table-tindakan');?>    
            
            </div>
            <div class="panel-body">
                <div class="tab-tindakan"></div>
           
                <table id="table-tindakan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Kode tindakan");?></th>
                            <th><?=\Yii::t("fe", "Nama tindakan");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Nama kategori");?></th>
                            <th><?=\Yii::t("fe", "Nama kelompok");?></th>
                            <th><?=\Yii::t("fe", "Nama kegiatan");?></th>
                            <th><?=\Yii::t("fe", "Grup ina cbgs");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
var tableTindakan;

// Event Ready
$(document).ready(function() {
    generateFilter("tab-tindakan", "filter-tindakan");
    // Generate Table
    tableTindakan = $("#table-tindakan").docoTabel({
        filter: true,
        //add for handle checkbox
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "os",
            selector: "tr"
        },
        sorting: [[3, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: "/master/tindakan/get-data-tindakan",
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
            {title: "'.(\Yii::t("fe", "Kode tindakan")).'", data: "daftartindakan_kode"},
            {title: "'.(\Yii::t("fe", "Nama tindakan")).'", data: "daftartindakan_nama"},
            {title: "'.(\Yii::t("fe", "Nama lainnya")).'",  data: "daftartindakan_namalainnya"},
            {title: "'.(\Yii::t("fe", "Nama kategori")).'", data: "kategoritindakan_nama"},
            {title: "'.(\Yii::t("fe", "Nama kelompok")).'", data: "kelompoktindakan_nama"},
            {title: "'.(\Yii::t("fe", "Nama kegiatan")).'", data: "jeniskegiatantindakan_nama"},
            {title: "'.(\Yii::t("fe", "Group ina cbgs")).'", data: "groupinacbg_nama"},
            {title: "'.(\Yii::t("fe", "Status")).'", data: "status"},
            {title: "'.(\Yii::t("fe", "Catatan")).'", data: "catatan"},
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-tindakan").datatableBootstrapFilter(tableTindakan, [
        [
            9, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\'
        ],
        [
            5,
            \'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('kategoritindakan_nama', '',[], 
                    [
                        'class' => 'form-control select2 selectKategori',                                 
                        'prompt' => \Yii::t('fe', 'Nama Kategori'),                                
                    ]
                )                        
            ))).'\'
        ],
        [
            6,
            \'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('kelompoktindakan_nama', '',[], 
                    [
                        'class' => 'form-control select2 selectKelompok',                                 
                        'prompt' => \Yii::t('fe', 'Nama Kelompok'),                                
                    ]
                )                        
            ))).'\'
        ],
        [
            7,
            \'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('jeniskegiatantindakan_nama', '',[], 
                    [
                        'class' => 'form-control select2 selectKegiatan',
                        'prompt' => \Yii::t('fe', 'Nama Kegiatan'),
                    ]
                )                        
            ))).'\'
        ],
        [
            8,
            \'<div class="form-group">'.(preg_replace('/[\n\t\r]/i', "", preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('groupinacbg_nama', '',[], 
                    [
                        'class' => 'form-control select2 selectCbgs',
                        'prompt' => \Yii::t('fe', 'Group ina cbgs'),
                    ]
                )                        
            ))).'\'
        ],
    ],
    {
        2:0,
        3:1,
        4:2,
        5:3,
        7:4,
        6:5,
        8:6,
        10:7,
        9:8
    }, true);

    $(".selectKategori").select2({
        placeholder: "",
        minimumInputLength: 3,                  
        ajax: {
            url: "/master/tindakan/get-kategori",
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
    $(".selectKegiatan").select2({
        placeholder: "",
        minimumInputLength: 3,                  
        ajax: {
            url: "/master/tindakan/get-kegiatan",
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
    $(".selectKelompok").select2({
        placeholder: "",
        minimumInputLength: 3,                  
        ajax: {
            url: "/master/tindakan/get-kelompok",
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
    $(".selectCbgs").select2({
        placeholder: "",
        minimumInputLength: 3,                  
        ajax: {
            url: "/master/tindakan/get-cbg",
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

    // tableTindakan.on("select", function(e, dt, type, indexes) {
    //     if (type === "row") {
    //         var id = tableTindakan.rows(indexes).data()[0].primary;

    //         if (id) {
    //             $.ajax({
    //                 type: "GET",
    //                 url: "/master/tindakan/cek-transaksi-tindakan?id="+id,
    //                 success: function(response) {
    //                     if (response) {
    //                         $("#btn-delete-tindakan").attr("disabled", true);
    //                         $("#btn-update-tindakan").attr("disabled", true);
    //                     } else {
    //                         $("#btn-delete-tindakan").attr("disabled", false);
    //                         $("#btn-update-tindakan").attr("disabled", false);
    //                     }
    //                 }
    //             });
    //         }
    //     }
    // });
});

        
',VIEW::POS_END);
?>