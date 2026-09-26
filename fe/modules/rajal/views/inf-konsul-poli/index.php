<?php

/**
 * @Author: ayip
 * @Date:   2018-01-12 15:47:03
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-10 18:50:00
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .wraptext {
        white-space:normal;
        width:200px;
    }
</style>
<style>

    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 80px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 80px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }

</style>

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
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => $default_url .'/detail?id=',
                                'style' => (!$hasAccessUpdate ? 'display:none;' : ''),
                            ]
                        ],
                        'cancel' => [
                            'attributes' => [
                                'data-options' => 'delete',
                                'data-target' => '/rajal/inf-konsul-poli/batal?id=',
                                'data-additional' => 'data-rm',
                                'style' => (!$hasAccessUpdate ? 'display:none;' : ''),
                            ]
                        ],
                        'custom-pdf_konsul' => [
                            'title' => Yii::t('fe', 'Cetak konsul'),
                            'icon' => 'fa fa-file-pdf-o',
                            'attributes' => [
                                'data-target' => '/rajal/inf-konsul-poli/export-pdf-konsul?id='
                            ],
                        ],
                        'approve' => [
                            'title' => \Yii::t('fe', 'Setujui'),
                            'icon' => 'fa fa-send',
                            'attributes' => [
                                'action' => '/rajal/inf-konsul-poli/approve?id=',
                                'class' => 'btn-aksi',
                                'data-options' => 'click',
                                'data-type' => 'approve',
                                'id' => 'btn-approve',
                                'disabled' => true,
                                'style' => (!$hasAccessApprove ? 'display:none;' : ''),
                            ]
                        ]
                        // 'custom-pdf_antrian' => [
                        //     'title' => Yii::t('fe', 'Cetak antrian'),
                        //     'icon' => 'fa fa-file-pdf-o',
                        //     'attributes' => [
                        //         'data-options' => 'pdf',
                        //         'data-url' => '/rajal/inf-konsul-poli/export-pdf-antrian',
                        //     ],
                        // ],
                    ], '#inf-konsul-poli');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 advanced-filter">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class='my-legend'>
                        <div class='legend-title'>Keterangan</div>
                        <div class='legend-scale'>
                        <ul class='legend-labels'>
                            <li><span style='background:#05bbbe;'></span>Konsul di Setujui</li>
                        </ul>
                        </div>
                    </div>
                </div>

                <table id="inf-konsul-poli" class="table table-striped table-condensed table-hover table-framed" width="100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Konsul");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Asal");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
                            <th><?=\Yii::t("fe", "Dikonsul Ke");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Tanggal");?></th>
                            <th><?=\Yii::t("fe", "Dokter Konsul");?></th>
                            <th><?=\Yii::t("fe", "Jawaban Konsul");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php /* list data */ ?>
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

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#inf-konsul-poli').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
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
            scrollX: true,
            ajax: baseUrl+'rajal/inf-konsul-poli/get-data',
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
                {title: '".(\Yii::t('fe', 'Tanggal Konsul'))."', data: 'tgl_konsulpoli'},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."', data: 'nama_pasien'},
                {title: '".(\Yii::t('fe', 'Ruangan Asal'))."', data: 'ruangan_asal'},
                {title: '".(\Yii::t('fe', 'Catatan'))."',  data: 'catatan_dokter_konsul', className: 'wraptext',searchable:false},
                {title: '".(\Yii::t('fe', 'Dikonsul Ke'))."', data: 'ruangan_tujuan'},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'status_konsul'},
                {title: '".(\Yii::t('fe', 'Tanggal'))."', data: 'tgl_selesaikonsul',searchable:false},
                {title: '".(\Yii::t('fe', 'Dokter Konsul'))."', data: 'nama_dokter'},
                {title: '".(\Yii::t('fe', 'Jawaban Konsul'))."', data: 'jawaban_konsul',searchable:false},
            ],
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData.status_approve_id == 565 && aData.status_periksa != 411){
                    $('td', nRow).css('background-color', '#05bbbe');
                }
            },          
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                4,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('ruangan_asal', '', 
                        ArrayHelper::map($ruangan, "ruangan_id", "ruangan_nama"),
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Semua'),
                        ]
                    )
                )))."\"
            ],
            [
                6,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('ruangan', '', 
                        ArrayHelper::map($ruangan, "ruangan_id", "ruangan_nama"),
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Semua'),
                        ]
                    )
                )))."\"
            ],
            [
                7,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('status_konsul', '', 
                        ArrayHelper::map($statusKonsul, "lookup_name", "lookup_name"),
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Semua'),
                        ]
                    )
                )))."\"
            ],
            [
                9,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('dokter', '', 
                        ArrayHelper::map($dokter, "pegawai_id", "nama_pegawai"),
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', 'Semua'),
                        ]
                    )
                )))."\"
            ],
        ], {
                2:0,
                3:1,
                4:2,
                6:3,
                7:4,
                9:5,
            }, true
        );
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

        $('.ddl_kelas_pelayanan').select2({
            minimumInputLength: 3,  
            ajax: {
                url: '/master/kelas/get-kelas-pelayanan',
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
        $(document).on('click', '#inf-konsul-poli tr', function(){
            var tbl = table.row('.selected').data();
            console.log(tbl);
            if (typeof tbl == 'undefined') {
                $('.data-cancel').show();
                $('#btn-approve').prop('disabled', true);
                return true;
            }
            if(tbl.status_periksa == 1 || tbl.status_periksa == 339){
                $('.data-cancel').show();
            }else{
                $('.data-cancel').hide();
            }

            if((tbl.is_dokter_dirujuk == 1 && tbl.status_approve_id == 564 && tbl.status_periksa != 411) || tbl.is_not_dokter == 1){
                $('#btn-approve').prop('disabled', false);
            }else{
                $('#btn-approve').prop('disabled', true);
            }
        });

        $(document).on('click', '#btn-approve', function(){
            var data = table.row('.selected').data();
            if(typeof data !== 'undefined'){
                var primary = data.primary;
                    target = $(this).attr('action');
                    type = $(this).attr('data-type');
                $(this).docoForm('click', {
                    url: target+primary,
                    confirmMessage: 'Apakah anda yakin untuk menerima konsul pasien ini ?',
                    success: function(res){
                        if(type != 'setujui'){
                            table.draw();
                        }
                    }
                })
            }
        });


        var primaryKey;
    });", View::POS_END, 'js-kuning');
?>