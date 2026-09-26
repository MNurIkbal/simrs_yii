<?php

/**
 * @author Randy Vianda Putra
 * @copyright 19 January 2018 aweutist
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
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => []];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .wrap-detail { 
        overflow-wrap: break-word;
    }
    .dataTables_scrollFoot {
        overflow: initial !important;
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    "search"=> [
                        'attributes'=>[
                            'id' => 'find-data',
                        ]
                    ],
                    'reset'=>[
                        'attributes'=>[
                            'data-parent'=>'.filter-form',
                            'id' => 'reset-data',
                            'disabled' => true
                        ]
                    ],
                    'export-excel' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Export Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-table-id' => 'example',
                            'data-url' => Url::home() . 'apotek/informasi-stok-obatalkes/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <!-- <div class="col-md-12 filter-form"></div> -->
                <div class="advanced-filter" style="display: none;">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "ruangan_nama");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "obatalkes_namalain");?></th>
                            <th><?=\Yii::t("fe", "Ven");?></th> <!-- 5 -->                            
                            <th style="display: none;"><?=\Yii::t("fe", "instalasi_nama");?></th>
                            <th><?=\Yii::t("fe", "Stok minimal");?></th>
                            <th><?=\Yii::t("fe", "Stok maksimal");?></th>
                            <th><?=\Yii::t("fe", "qty_dipesan");?></th>
                            <th><?=\Yii::t("fe", "qty_tersedia");?></th>
                            <th><?=\Yii::t("fe", "Stok Total");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                    <tfoot>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1" class="text-right"><strong><?= Yii::t('fe', 'Total Stok') ?></strong></th>
                        <th style="background-color: #EAFAF1"></th>
                        <th style="background-color: #EAFAF1"></th>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-dipesan" style="z-index:1064;" tabindex="-1" role="dialog" aria-labelledby="detailDipesan">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header bg-inverse">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Detail Pesan</h5>
        </div>
        <div class="modal-body">
            <dl>
                <dt><strong>Mutasi:</strong></dt>
                <dd><p class="detail-mutasi wrap-detail"></p></dd>
                <dt><strong>Penjualan Farmasi:</strong></dt>
                <dd><p class="detail-penjualan wrap-detail"></p></dd>
                <dt><strong>Resep Dokter:</strong></dt>
                <dd class="detail-dokter wrap-detail"></dd>
            </dl>
        </div>
    </div>
  </div>
</div>
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));

    $this->registerJs("
    var isDisabled = '.$is_disabled.';
    var obatalkesNama = '".$obatalkesNama."';
    var ruanganIds = ".json_encode($ruanganIds).";
    
    $(document).on('keydown', null, 'enter', function(event) {
        $('#find-data').click();
    });
    
    $(document).on('keydown', null, function (e) {
        // if (e.key == 'Enter') {
        //     $('#find-data').click();
        // }

        if (e.key == 'F7') {
            $('#reset-data').click();
        }

        // if (e.key == 'F8') {
        //     $('#pdf-data').click();
        // }

        // if (e.key == 'F9') {
        //     $('#excel-data').click();
        // }
    });
    
    // Global Var
    var table;
    var optionsTable = {
            filter: true,
            sorting: [[1, 'asc'],[4, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            stateSave: false,
            ajax: baseUrl+'apotek/informasi-stok-obatalkes/get-data',
            deferLoading: 0,
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'ruangan_nama'))."', 
                    data: 'ruangan_nama', 
                    name: 'ruangan_id'
                },
                {
                    title: '".(\Yii::t('fe', 'Jenis Obat'))."', 
                    data: 'jenisobatalkes_nama',
                    name: 'jenisobatalkes_id',
                    
                },
                {
                    title: '".(\Yii::t('fe', 'Kode Obat'))."', 
                    data: 'obatalkes_kode', 
                    name: 'obatalkes_kode',
                },
                {
                    title: '".(\Yii::t('fe', 'obatalkes_namalain'))."', 
                    data: 'obatalkes_nama',
                    name: 'obatalkes_nama', 
                },

                {
                    title: '".(\Yii::t('fe', 'Ven'))."', 
                    data: 'ven_name',
                    name: 'ven_name', 
                },
                {
                    title: '".(\Yii::t('fe', 'instalasi_nama'))."',  
                    data: 'instalasi_nama',
                    name: 'instalasi_id',
                    searchable: false, 
                    visible: false
                },
                {
                    title: '".(\Yii::t('fe', 'Stok minimal'))."', 
                    data: 'min_stok', 
                    searchable: false, 
                    class :'text-right'
                },
                {
                    title: '".(\Yii::t('fe', 'Stok maksimal'))."', 
                    data: 'max_stok', 
                    searchable: false, 
                    class :'text-right'
                },
                {
                    title: '".(\Yii::t('fe', 'qty_dipesan'))."', 
                    data: 'qty_dipesan', 
                    searchable: false, 
                    class :'text-right'
                },
                {
                    title: '".(\Yii::t('fe', 'qty_tersedia'))."', 
                    data: 'qty_tersedia', 
                    searchable: false, 
                    class :'text-right'
                },
                {
                    title: '".(\Yii::t('fe', 'Stok Total'))."', 
                    data: 'qty_stok', 
                    searchable: false, 
                    class :'text-right'
                }
            ],
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();
                let res = this.api().ajax.json();
                let jmlStokTersedia = 0;
                let jmlStokTotal = 0;
                if(res) {
                    jmlStokTersedia = res.total_qty_tersedia
                    jmlStokTotal = res.total_qty_stok
                }

                $(api.column(10).footer()).html(
                    jmlStokTersedia
                );

                $(api.column(11).footer()).html(
                    jmlStokTotal 
                );
            },
        };

    // Event Ready
    $(document).ready(function() {
        $('#download-data').prop('disabled', true);
        $('#reset-data').prop('disabled', true);
        // Generate Table
        table = $('#example').docoTabel(optionsTable);
        $('.filter-form').datatableBootstrapFilter(table, 
            [
                [
                     1,
                    '".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_ids[]', '',
                            $daftar_ruangan,
                            [
                                'class' => 'form-control select2 word-wrapper ruangan_ids',
                                'id' => 'filter_ruangan_ids',
                                'multiple' => 'multiple'
                            ]
                        )
                    ))."'
                ],
                [
                    2,
                    '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenisobatalkes_id', '', $data_obat,
                            [
                                'class' => 'form-control select2',
                                'id' => 'filter_jenis_obat',
                                'prompt' => Yii::t('fe', '— Pilih Jenis Obat Alkes —'),
                            ]
                        )
                    ))."</div>'
                ],
                [
                    4,
                        \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::textInput('obatalkes_nama', '',
                                [
                                    'class' => 'form-control obatalkes_nama',
                                    'col-index'=>3,
                                    'placeholder'=> 'Nama Obat Alkes'
                                ]
                            )
                        )
                    )."<div>\"
                ],
            ], {
                1:0,
                2:2,
                4:1,
                3:3,
            }, true
        );
        $('.dataTables_filter').hide();
        $('#obatalkes_id').select2({
            placeholder: '". \Yii::t("fe", "— Pilih Obat Alkes —") ."',
            minimumInputLength: 3,
            ajax: {
                url: '/apotek/informasi-pemakaian-obatalkes/search-obat-alkes',
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
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
        $('.advanced-filter').show();

        $('.modal-dipesan').on('show.bs.modal', function(event){
            $('.detail-mutasi').html($(document.activeElement).attr('data-referencemutasi'));
            $('.detail-penjualan').html($(document.activeElement).attr('data-referencefarmasi'));
            $('.detail-dokter').html($(document.activeElement).attr('data-referencedokter'));
        });

        $('.modal-dipesan').on('hide.bs.modal', function(event){
            $('.detail-mutasi').empty();
            $('.detail-penjualan').empty();
            $('.detail-dokter').empty();
        });

        $('#example tbody').on( 'click', '.link-dipesan', function () {
            $('.modal-dipesan').modal('show');
        });

        $('.obatalkes_nama').val(obatalkesNama).trigger('change');
        $('.ruangan_ids').val(ruanganIds).trigger('change')
        if (obatalkesNama !== '' ||  ruanganIds != null) $('#find-data').trigger('click');
    });

    $(document).on('click', '.data-resetfilter', function(){
        let tgl = new Date()
        $('.advancedFilter [type=reset]').click();

        $('.advancedFilterDo').click();
        $('#obatalkes_id').val('').trigger('change');
    });
    
    $(document).on('click', '#find-data', function(){
        $('#download-data').prop('disabled', false);
        $('#reset-data').prop('disabled', false);
    });

        ",View::POS_END, 'js-kuning');

?>
<!-- <span class='input-group-addon'><span class='cursor-pointer' action='".Url::home()."kasir/modal-search/modal-stok-obat-alkes' data-toggle='modal' data-target='#modal_backdrop_search'><i class='fa fa-list'></i> <i class='fa fa-search'></i></span></span> -->
<script>
</script>
