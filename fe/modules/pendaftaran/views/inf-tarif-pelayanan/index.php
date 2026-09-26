<?php

/**
 * @Author: ayip
 * @Date:   2018-01-12 15:47:03
 * @Last Modified by: ayip
 * @Last Modified time: 2018-01-26 16:33:35
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .r-align{
        text-align: right;
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
                      <h3 class="panel-title"><b><?= $this->title ; ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'detail' => [
                        'title' => \Yii::t('fe', 'Komponen Tarif'),
                        'attributes' => [
                            'id' => 'btn-komponen-detail-tarif',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home().('pendaftaran/inf-tarif-pelayanan/detail?id=')
                        ]
                    ],
                    /*'komponen_tarif' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Komponen Tarif'),
                        'icon' => 'fa fa-reorder',
                        // 'method' => 'not exist',
                        'attributes' => [
                            'id' => 'btn-komponen-detail-tarif',
                            'class' => 'btn spa data-detail',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home().('pendaftaran/inf-tarif-pelayanan/detail?id=')
                        ],
                    ],*/
                    // 'pdf',
                    // 'excel',
                    ], '#inf-tarif-pelayanan');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>

                <table id="inf-tarif-pelayanan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t("fe", "Instalasi Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Instalasi");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Kelompok/Kategori Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kelompok Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kategori Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Nama Tindakan/Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Tarif Total");?></th>
                            <th><?=\Yii::t("fe", "Cyto Tindakan");?> (%)</th>
                            <th><?=\Yii::t("fe", "Diskon Tindakan");?> (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<div id="detail" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            ...
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

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#inf-tarif-pelayanan').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'pendaftaran/inf-tarif-pelayanan/get-data',
            columns: [
                {
                    title: '',
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Instalasi'.'<br>'.' Ruangan'))."', data: 'instalasi_ruangan',searchable: false}, 
                {title: '".(\Yii::t('fe', 'Instalasi'))."', data: 'instalasi_nama',name:'instalasi_id',visible: false}, //3
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama',name:'ruangan_id',visible: false}, //4
                {title: '".(\Yii::t('fe', 'Penjamin'))."', data: 'penjamin_nama',name:'penjamin_id'}, //5
                {title: '".(\Yii::t('fe', 'Kelompok'.'<br>'.'Kategori Tindakan'))."', data: 'kelompok_kategori_tindakan',searchable: false},
                {title: '".(\Yii::t('fe', 'Kelompok Tindakan'))."',  data: 'kelompoktindakan_nama',searchable: false,visible: false}, //7
                {title: '".(\Yii::t('fe', 'Kategori Tindakan'))."', data: 'kategoritindakan_nama',name:'kategoritindakan_id',visible: false}, //8
                {title: '".(\Yii::t('fe', 'Nama Tindakan'.'<br>'.'Kelas Pelayanan'))."', data: 'daftartindakan_nama_kelaspelayanan_nama',searchable: false},
                {title: '".(\Yii::t('fe', 'Nama Tindakan'))."', data: 'daftartindakan_nama',visible: false}, //10
                {title: '".(\Yii::t('fe', 'Kelas Pelayanan'))."', data: 'kelaspelayanan_nama',visible: false,name:'kelaspelayanan_id'}, //11
                {title: '".(\Yii::t('fe', 'Tarif Total')).' (Rp)'."', data: 'harga_tariftindakan',searchable: false,className: 'r-align' },
                {title: '".(\Yii::t('fe', 'Cyto Tindakan (%)'))."', data: 'persencyto_tindakan',searchable: false},//13
                {title: '".(\Yii::t('fe', 'Diskon Tindakan (%)'))."', data: 'persendiskon_tindakan',searchable: false},
            ],
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table,[
            [
                3,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('instalasi_nama', '', $dataFilter['instalasi'],
                            [
                                'class' => 'form-control select2 ddl_instalasi',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
            [
                4,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('ruangan_id', '', $dataFilter['ruangan'],
                            [
                                'class' => 'form-control select2 ddl_ruangan',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
            [
                5,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('jenistarif_id', '', $dataFilter['penjamin'],
                            [
                                'class' => 'form-control select2 ddl_penjamin',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
            [
                8,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kategoritindakan_nama', '', $dataFilter['kategori_tindakan'],
                            [
                                'class' => 'form-control select2 ddl_kategori_tindakan',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
            [
                10,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::textInput('daftartindakan_nama', '',
                        [
                            'id' => 'filter_daftartindakan_nama',
                            'class' => 'form-control daftartindakan_nama',
                            'placeholder'=> 'Nama Tindakan'
                        ]
                    )
                )). "<div>\"
            ],
            [
                11,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kelaspelayanan_nama', '', $dataFilter['kelas_pelayanan'],
                            [
                                'class' => 'form-control select2 ddl_kelas_pelayanan',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
        ]);

        dateRangeHelper('.startDate','.endDate','.targetDate');

        // $('.ddl_kelas_pelayanan').select2({
        //     minimumInputLength: 3,
        //     ajax: {
        //         url: '/master/kelas/get-kelas-pelayanan',
        //         dataType: 'json',
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
        //     dropdownCssClass: 'bigdrop',
        //     escapeMarkup: function (m) { return m; },
        // });

        var primaryKey;
        $(document).ready(function() {
            
            $('#inf-tarif-pelayanan tbody').on('click', 'tr', function(){

                try {
                    primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
                } catch (e) {
                    primaryKey = false;
                }


                if (primaryKey) {
                    $('#btn-komponen-detail-tarif').attr('action',$('#btn-komponen-detail-tarif').data('url')+primaryKey);
                } else {
                    $('#btn-komponen-detail-tarif').removeAttr('action');
                }
            });
        });


        // $('#inf-tarif-pelayanan tbody').on('click', 'tr', function(){
        //     // console.log(pri)
        //     primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
        //     if(primaryKey){
        //         $('#btn-komponen-detail-tarif').attr('url',$('#btn-komponen-detail-tarif').data('url')+primaryKey);
        //     }else{
        //         $('#btn-komponen-detail-tarif').removeAttr('url');
        //     }

        // });

        // $(document).on('click', '.data-detail', function(){
        //     window.location = $(this).attr('href');
        // })

        // $(document).on('click', '.data-add', function(){
        //     window.location = $(this).data('target');
        // })

    });", View::POS_END, 'js-kuning');
?>
