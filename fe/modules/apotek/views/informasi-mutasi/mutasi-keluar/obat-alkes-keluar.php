<?php

/**
 * @author Randy Vianda Putra
 * @copyright 17 January 2018 aweutist
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
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
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
                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/detail?id=',
                            'data-conditions'=>'type'
                        ]
                    ],
                    'kirim' => [
                        'type' => 'button',
                        'title' => 'Kirim',
                        'icon' => 'fa fa-plus',
                        'attributes'=>[
                            'data-target'=>'/apotek/mutasi-obat',
                            'data-options'=>'link'
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/delete-mutasi?id=',
                            'data-additional' => 'data-rm'
                        ]
                    ]
                ],'#table_mutasi_keluar');?>
                <div class="pull-right">
                    <?=DocoHelpers::generateToolbar([
                    'reset'
                ]);?>
                </div>
            </div>
            <div class="panel-body">
                <input type="hidden" value="<?=Yii::$app->docoVars->workspace("instalasi_name")?>" class="hiddenInstalasi">
                <input type="hidden" value="<?=Yii::$app->docoVars->workspace("ruangan_name")?>" class="hiddenRuangan">
                <div class="col-md-12 filter-form"></div>
                <table id="table_mutasi_keluar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="80">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Mutasi");?></th>
                            <th><?=\Yii::t("fe", "No Mutasi");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan Tujuan");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Terima");?></th>
                            <th><?=\Yii::t("fe", "Reference");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- <tr>
                            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src=""></script>
<?php
    $this->registerCss($this->render('../../assets/css/apotek.css'));


    $this->registerJs("
        // Global Var
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });
    $(document).on('click', '#table_mutasi_keluar tbody tr', function () {
        try{
            status_verif = table.row('.selected').data().status_verif ? table.row('.selected').data().status_verifikasi : null;
        } catch(e){
            status_verif = false
        }

        if(status_verif == 550 || status_verif == 556 || status_verif == 551){
            $('.btn-proses').attr('disabled', false)
        }else{
            $('.btn-proses').attr('disabled', true)
        }
    })
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#table_mutasi_keluar').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }, {
                targets: 4,
                render: function(data, type, row) {
                    return data + ' - ' + row['ruangan_nama'];
                }
            }],
            select: {
                style:    'os',
                selector: 'tr'
            },
            rowCallback: function(row, data, index){
                var hiddenInstalasi = $('.hiddenInstalasi').val();
                var hiddenRuangan = $('.hiddenRuangan').val();
                if(hiddenInstalasi == data['instalasi_nama'] && hiddenRuangan == data['ruangan_nama']){
                    $('td:first-child').addClass('select-checkbox');
                }
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'apotek/informasi-mutasi/get-data-keluar',
            // fixedColumns: {
            //     leftColumns: 1
            // },
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
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Mutasi'))."', data: 'tglmutasioa'},
                {title: '".(\Yii::t('fe', 'No Mutasi'))."', data: 'nomutasioa'},
                {title: '".(\Yii::t('fe', 'Instalasi - Ruangan Tujuan'))."',  data: 'instalasi_nama'},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'statusmutasi', searchable: false},
                {title: '".(\Yii::t('fe', 'Tanggal Terima'))."', data: 'tgl_terima', searchable: false},
                {title: '".(\Yii::t('fe', 'Reference'))."', data: 'reference', searchable: false},
                {
                    data: 'primary',
                    searchable: false,
                    orderable: false,
                    visible: false,
                },
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                    2,
                    \"<div class='input-group'><input type='text' id='rangeDemoStart' value='".date('d-M-Y')."' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' value='".date('d-M-Y')."' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
                ],
                [
                    4,
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('instalasi_nama', '',
                            ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
                            [

                                'class' => 'form-control select2 selectInstalasi',
                                'id'=>'filter_instalasi',
                                'prompt' => \Yii::t('fe', 'Instalasi akhir')
                            ]
                        )
                    )))."\"
                ],
                [
                    5,
                    \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2 selectRuangan'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               'placeholder' => '',
                               'url' => Url::to(['/apotek/end-point/get-ruangan'])
                            ]
                        ])
                        )
                    )
                )."\"
                ],

            ], {
                // 1:0,
                // 2:4,
                // 3:1,
                // 4:3
            }, true
        );
        dateRangeHelper('.startDate','.endDate','.targetDate');
        $('.selectNomutasi').select2({
                placeholder: '',
                minimumInputLength: 3,
                ajax: {
                    url: '/apotek/informasi-mutasi/get-data-nomutasi2',
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
        $('.selectInstalasi').select2({
            placeholder: '',
        });
        $('.selectRuangan').select2({
            placeholder: '',
        });
        $(document).on('click', '#table_mutasi_keluar tr', function(){
            var tbl = table.row('.selected').data();
            if(tbl.status_mutasi == 400){
                $('.data-delete').hide();
            }else{
                $('.data-delete').show();
            }
        });

    });

        ", View::POS_END, 'js-kuning-2');

?>