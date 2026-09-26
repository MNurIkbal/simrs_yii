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
                        'search'=>[
                            'attributes'=>[
                                'data-parent'=>'.filter-tarif'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-tarif'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-content'=>'content-tarif',
                                'data-url' => '/master/tarif-tindakan/form-tarif',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'class'=>'spa',
                                'data-options'=>'click',
                                'data-type'=>'edit',
                                'data-content'=>'content-tarif',
                                'data-url' => '/master/tarif-tindakan/form-tarif-edit?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'class'=>'hidden',
                                'data-additional'=>'data-rm',
                                'data-target' => '/master/tarif-tindakan/delete-tarif?id=',
                            ]
                        ],
                        // 'excel' => [
                        //     'attributes' => [
                        //         'data-target'=>'/master/tarif-tindakan/export-excel?type=tarif&',
                        //     ],
                        // ],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'master/tarif-tindakan/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tarif-tindakan/export-pdf?type=tarif&',
                            ]
                        ],
                        // 'set-default' => [
                        //     'title' => \Yii::t('fe', 'Salin Tarif'),
                        //     'icon' => 'fa fa-gear',
                        //     'attributes' => [
                        //         'class'=>'spa',
                        //         //'class'=>'hidden',
                        //         'data-options'=>'click',
                        //         'data-content'=>'content-tarif',
                        //         'data-url' => '/master/tarif-tindakan/set-default',
                        //     ]
                        // ],
                    ], '#table-tarif');?>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-tarif">
                </div>
                <table id="table-tarif" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th></th> <!-- filter jenis tindakan 2-->
                            <th></th> <!-- filter nama tindakan 3-->
                            <th><?=Yii::t('fe', 'Jenis Paket/Tindakan'.'<br>'.'Nama Paket/Tindakan')?></th>
                            <th><?=Yii::t('fe', 'Kamar')?></th>
                            <th><?=Yii::t('fe', 'Kelas Pelayanan')?></th> <!-- 5 -->
                            <th></th> <!-- filter jenis tindakan 6-->
                            <th></th> <!-- filter nama tindakan 7-->
                            <th><?=Yii::t('fe', 'Cara Bayar'.'<br>'.'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Dokter')?></th>
                            <th><?=Yii::t('fe', 'Perda/SK')?></th> <!-- 5 -->
                            <th><?=Yii::t('fe', 'Persen Cyto')?></th>
                            <th><?=Yii::t('fe', 'Persen Penyulit')?></th>
                            <th><?=Yii::t('fe', 'Persen Diskon')?></th>
                            <th><?=Yii::t('fe', 'Harga')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th> <!-- 9 -->
                            <th></th> <!-- filter jenis tarif-->
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
    $(document).on("click", ".data-reload", function() {
        tableTarif.draw();
    });
    var tableTarif;
    $(document).ready(function() {
        generateFilter("tab-tarif", "filter-tarif");
        // Generate Table
        tableTarif = $("#table-tarif").docoTabel({
            filter: true,
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
            ajax: baseUrl+"master/tarif-tindakan/get-data-tarif",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                    width:"5%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Kamar")).'", 
                    data: "kamar",
                    name: "kamarruangan_id",
                    // visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Paket/Tindakan")).'", 
                    data: "jenis_tindakan_paket", 
                    searchable: true,
                    visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Paket/Tindakan")).'", 
                    data: "nama_tindakan_paket", 
                    searchable: true,
                    visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Paket/Tindakan"."<br>"."Nama Paket/Tindakan")).'", 
                    data: "jenis_nama_paket_tindakan", 
                    searchable: false,
                    width:"20%",
                    name: "jenis_tindakan_paket"
                },
                {
                    title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", 
                    data: "kelaspelayanan_nama", 
                    name: "kelaspelayanan_id", 
                    searchable: true,
                    visible: true,
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar")).'", 
                    data: "carabayar_nama", 
                    name : "carabayar_id",
                    searchable: true,
                    visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Penjamin")).'", 
                    name : "penjamin_id",
                    data: "penjamin_nama", 
                    searchable: true,
                    visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar"."<br>"."Penjamin")).'", 
                    data: "cara_bayar_penjamin", 
                    searchable: false,
                    width:"15%",
                    name: "carabayar_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Dokter")).'", 
                    name : "dokter_id",
                    data: "dokter", 
                },
                {
                    title: "'.(\Yii::t("fe", "Perda/SK")).'", 
                    data: "perdanama_sk", 
                    name: "perdatarif_id",
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Persen Cito")).' (%) '.'", 
                    data: "persencyto_tindakan",
                    searchable: false, 
                    class: "text-right",
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Persen Penyulit")).' (%) '.'", 
                    data: "persen_penyulit",
                    searchable: false, 
                    class: "text-right",
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Persen Diskon")).' (%) '.'", 
                    data: "persendiskon_tindakan",
                    searchable: false, 
                    class: "text-right",
                    visible: false,
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Harga")).' (Rp.) '.'", 
                    data: "harga_tariftindakan", 
                    searchable: false,
                    class: "text-right",
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'", 
                    data: "is_active", 
                    orderable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Tarif")).'", 
                    data: "jenis_tarif", 
                    visible: false,
                    orderable: false,
                },
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-tarif").datatableBootstrapFilter(tableTarif, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kamarruangan_id', '', [], ['class' => 'select2 selectKamar', 'prompt' => \Yii::t('fe', '— Pilih Kamar —')]))). '\'
            ],
            [   
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_tindakan_paket', '', @$additional_data['jenis_tindakan_paket'], 
                    ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Jenis —'),
                    'name' => "jenis_tindakan_paket",
                    ]))).'\'
            ],
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_tindakan_paket', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Tindakan')]))).'\'
            ],
            [   
                6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelaspelayanan_nama', '', [], 
                    ['class' => 'form-control select2 selectKelas', 
                    'prompt' => \Yii::t('fe', '— Pilih Kelas Pelayanan —'),
                    'name' => "kelaspelayanan_id",
                    ]))).'\'
            ],
            [
                7, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_nama', '', [], 
                    [
                        'class' => 'form-control select2', 
                        'id' => 'filter_carabayar',
                        'style'=>'width:100%;',
                        'prompt' => \Yii::t('fe', '— Pilih Cara bayar —'),
                    ]))).'\'
            ],
            [
                8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('penjamin_nama', '', [], 
                    [
                        'id' => 'filter_penjamin',
                        'class' => 'form-control select2',
                        'style'=>'width:100%;',
                        'prompt' => \Yii::t('fe', '— Pilih Penjamin —'),
                    ]))).'\'
            ],
            [
                10, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('dokter', '', [], 
                    [
                        'id' => 'filter_dokter',
                        'class' => 'form-control select2',
                        'style'=>'width:100%;',
                    ]))).'\'
            ],
            [   
                11, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('perdanama_sk', '', @$additional_data['perdatarif'], 
                    ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Perda —'),
                    'name' => "kelaspelayanan_id",
                    ]))).'\'
            ],
            [
                16, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '— Pilih Status —')]))). '\'
            ],
            [
                17, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_tarif', '', $jenis_tarif, ['class' => 'select2', 'prompt' => \Yii::t('fe', '— Pilih Jenis Tarif —')]))). '\'
            ],
        ], {
            2:0,
            4:1,
            6:2,
            7:3,
            8:4,
            10:5,
            12:6,
        }, true);
        
        $("#table-perda tbody").on("click", "tr", function(){
            try {
                primaryKey = tableTarif.row(".selected").data().primary ? tableTarif.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit").attr("action",$("#btn-edit").data("url")+primaryKey);
            } else {
                $("#btn-edit").removeAttr("action");
            }
        });
        $("#filter_dokter").docoPaginationSelec2(
            config = {
                placeholder : "-- Semua Dokter --",
                allowClear: true,  
                _api : "/master/master-api/get-list-dokter",
            }
        );
        $(".selectKamar").docoPaginationSelec2(
            config = {
                placeholder : "-- Pilih Kamar --",  
                _api : "/master/master-api/get-list-kamar",
            }
        );
        $(".selectKelas").docoPaginationSelec2(
            config = {
                placeholder : "-- Pilih Kelas Pelayanan --",  
                _api : "/master/master-api/get-list-kelas",
            }
        );
        $("#filter_carabayar").docoPaginationSelec2(
            config = {
                placeholder : "-- Pilih Cara Bayar --",  
                _api : "/master/master-api/get-list-carabayar",
            }
        );
        $(document).on("change", "#filter_carabayar", function(){
            var data = $("#filter_carabayar").select2("data")
            var _caraBayar = data[0].id;
            $("#filter_penjamin").docoPaginationSelec2(
                config = {
                    placeholder : "-- Pilih Penjamin --",  
                    _api : "/master/master-api/get-list-penjamin?carabayar_id=" + _caraBayar,
                }
            );
        });
    });
', View::POS_END, 'e-index');
?>