<?php

// Author: Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/tarif-tindakan/create-jenis-tarif',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'edit_komponen',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/tarif-tindakan/update-jenis-tarif?id=',
                                'data-options'=>'modal'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'data-delete-komponen',
                                'data-target' => '/master/tarif-tindakan/delete-jenis-tarif?id=',
                                'action' => 'null_id'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tarif-tindakan/cetak-jenis-tarif?',
                            ]
                        ],
                        'excel' => ['attributes' => ['data-target'=>'/master/tarif-tindakan/export-excel-jenis-tarif?type=1&']],
                    ], '#table-jenis-tarif');?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">                        
                    </div>
                </div>
                <table id="table-jenis-tarif" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed table-jenis-tarif">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?=Yii::t('fe', 'Kode Jenis Tarif')?></th>
                            <th><?=Yii::t('fe', 'Nama Jenis Tarif')?></th>
                            <th><?=Yii::t('fe', 'Nama Lainnya')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
                            <th><?=Yii::t('fe', 'Catatan')?></th>
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
$this->registerJs("
    $(document).on('click', '.data-reset', function() {
        tabel_jenis_tarif.draw();
    });

    $(document).ready(function() {
        tabel_jenis_tarif = $('.table-jenis-tarif').docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            oLanguage: {
                sLengthMenu: '".(\Yii::t('fe', 'dt_length_menu'))."',
                sZeroRecords: '".(\Yii::t('fe', 'dt_zero_records'))."',
                sEmptyTable: '".(\Yii::t('fe', 'dt_empty_table'))."',
                sInfoFiltered: '".(\Yii::t('fe', 'dt_info_filtered'))."',
                sInfoEmpty: '".(\Yii::t('fe', 'dt_info_empty'))."',
                sInfo: '".(\Yii::t('fe', 'dt_info'))."',
                oPaginate: {
                    sFirst: '".(\Yii::t('fe', 'dt_first_page'))."',
                    sPrevious: '".(\Yii::t('fe', 'dt_previous_page'))."',
                    sNext: '".(\Yii::t('fe', 'dt_next_page'))."',
                    sLast: '".(\Yii::t('fe', 'dt_last_page'))."'
                }
            },
            sorting: [[1, 'asc']], 
            processing: true,
            serverSide: true,
            ajax: baseUrl+'master/tarif-tindakan/get-data-jenis-tarif',
            columns:[     
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                {title: '" . (\Yii::t("fe", "Kode Jenis Tarif")) . "', data: 'jenistarif_m.jenistarif_kode'},
                {title: '" . (\Yii::t("fe", "Nama Jenis Tarif")) . "', data: 'jenistarif_m.jenistarif_nama'},
                {title: '" . (\Yii::t("fe", "Nama Lainnya")) . "', data: 'jenistarif_m.jenistarif_namalainnya'},
                {title: '" . (\Yii::t("fe", "Penjamin")) . "', data: 'penjamin_m.penjamin_nama'},
                {title: '" . (\Yii::t("fe", "Status")) . "', data: 'is_active'},        
                {title: '" . (\Yii::t("fe", "Catatan")) . "', data: 'jenistarif_m.catatan'},        
                    {                    
                        data: 'primary',
                        searchable: false,
                        orderable: false,
                        visible: false,
                    },    
                    
            ]
        });

         $('.dataTables_filter').hide();
         $('.filter-form').datatableBootstrapFilter(tabel_jenis_tarif, 
            [
                
                [
                    5,
                     \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                            Html::dropDownList('is_active', '',$status, 
                                [
                                    'class' => 'form-control select2', 
                                    'prompt' => \Yii::t('fe', ''),
                                    'col-index'=>1,
                                ]
                            )
                        )))."\"
                ],
            ]
        );
     });
")
?>