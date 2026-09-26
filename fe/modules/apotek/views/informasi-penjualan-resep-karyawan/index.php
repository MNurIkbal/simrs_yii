<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-05 10:56:37
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-11-12 13:33:07
 */


use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;


$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',
                            'data-target' => Url::home().('apotek/informasi-penjualan-resep-karyawan/view?id='),
                        ] 
                    ],
                    'retur' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Retur'),
                        'icon' => 'fa fa-repeat',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-retur',
                            'data-target' => Url::home().('apotek/transaksi-retur/index?id='),
                            'data-conditions'=>'noresep,jenispenjualan',       
                        ] 
                    ],
                    'delete' => [
                        'title' => \Yii::t('fe', 'Batal'),
                        'attributes'=>['data-additional'=>'data-rm']
                    ]
                ]);?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Penjualan");?></th>
                            <th><?=\Yii::t("fe", "No Resep");?></th>
                            <th><?=\Yii::t("fe", "Nama Karyawan");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Total tagihan");?></th>
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
<?php 
    
    $this->registerJs("
    var table;
    // Event Reload
    $(document).on('click', '.data-reset', function() {
        $('.penjamin_nama').val(null).trigger('change');
        $('.penjamin_nama').prop('disabled','disabled');
        table.draw();
    });

    $(document).ready(function(){
        table = $('#example').docoTabel({
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
            sorting: [[3, 'desc']], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,     
            // fixedColumns: {
            //     leftColumns: 1
            // },       
            ajax: baseUrl+'apotek/informasi-penjualan-resep-karyawan/get-data',
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
                {title: '".(\Yii::t('fe', 'Tanggal penjualan'))."', data: 'tglpenjualan'},
                {title: '".(\Yii::t('fe', 'No resep'))."', data: 'noresep'},
                {title: '".(\Yii::t('fe', 'Nama karyawan'))."',  data: 'nama_karyawan',orderable: false},
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."',  data: 'carabayar_nama'},
                {title: '".(\Yii::t('fe', 'Penjamin'))."',  data: 'penjamin_nama'},
                {title: '".(\Yii::t('fe', 'Total tagihan')).' '.'(Rp.)'."',  data: 'totaltagihan',searchable: false,orderable: false,class: 'text-right'},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value='".date('d-M-Y')."' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' value='".date('d-M-Y')."' readonly='readonly' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
                3,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::textInput('noresep', '', 
                            [
                                'class' => 'form-control noresep', 
                                'prompt' => 'Nomor Resep', 
                                'col-index'=>3,
                                'placeholder'=> 'Nomor Resep'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                4,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::textInput('nama_karyawan', '', 
                            [
                                'class' => 'form-control nama_karyawan', 
                                'prompt' => 'Nama Karyawan', 
                                'col-index'=>3,
                                'placeholder'=> 'Nama Karyawan'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                5, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('carabayar_nama', '', $carabayar, 
                            [
                                'class' => 'form-control select2 carabayar_nama', 
                                'id'=>'carabayar_select',
                                'prompt' => '', 
                                'col-index'=>6
                            ]
                        )
                        )
            )."<div>\"
            ],
            [
                6, 
                \"".preg_replace("/[\n\t\r]/i", '',  preg_replace("/[\"]/i", '\'', 
                     DepDrop::widget([
                            'data'=>[''=>'-'],
                            'name' => 'penjamin_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2 penjamin_nama',
                            ],
                            'pluginOptions' => [
                               'depends'  => ['carabayar_select'],
                               'placeholder' => '',
                               'url' => Url::to(['informasi-penjualan-resep-karyawan/get-penjamin'])
                            ]
                        ])
            ))."\"
            ],
            
        ], {2:0}, true);
        dateRangeHelper('.startDate','.endDate','.targetDate');
        $('.carabayar_nama').select2({
            placeholder: '— Pilih Cara Bayar —',
        });
        $('.penjamin_nama').select2({
            placeholder: '— Pilih Penjamin —',
        });

        $('#example tbody').on('click', 'tr', function(){
            try {
                statusPembayaran = table.row('.selected').data().status_pembayaran ? table.row('.selected').data().status_pembayaran : null;
            } catch (e) {
                statusPembayaran = false;
            }
            if (statusPembayaran) {
                if(statusPembayaran == 'LUNAS'){
                    $('.data-delete').prop('disabled',true);
                }else{
                    $('.data-delete').prop('disabled',false);
                }
            } else {
                $('.data-delete').prop('disabled',false);
            }
        });

    });
    

    ", VIEW::POS_END, 'js-kunings');
?>