<?php

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title">
                        <b>
                            <?php
                            // echo Yii::$app->docoVars->workspace("modul_alias");
                            echo $this->title;
                            ?>
                        </b>
                    </h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                </div>
            </div>
            <!-- end -->
        </div>

        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                // 'excel' => [
                //     'title' => Yii::t('fe', 'Unduh Excel'),
                //     'attributes'=>[
                //         'data-target'=>Url::home().'rm/laporan-cara-pulang-pasien/export-excel?'
                //   ]
                // ],
                // 'pdf'=>[
                //     'type'=>'button',
                //     'title' => \Yii::t('fe', 'Cetak PDF'),
                //     'icon' => 'fa fa-file-pdf-o',
                //     'attributes' => [
                //         'data-target'=>Url::home().'rm/laporan-cara-pulang-pasien/export-pdf?',
                //     ]
                // ],
                'export-excel-serconn' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Excel'),
                    'icon' => 'fa fa-file-excel-o',
                    'attributes' => [
                        'id' => 'data-export-excel-serconn',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'rm/laporan-cara-pulang-pasien/show-popup-excel?',
                        'data-width' => '75%'
                    ]
                ],
                'export-pdf-serconn' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Cetak PDF'),
                    'icon' => 'fa fa-file-pdf-o',
                    'attributes' => [
                        'id' => 'data-export-pdf-serconn',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'rm/laporan-cara-pulang-pasien/show-popup?',
                        'data-width' => '75%'
                    ]
                ],
        ], '#lap-cara-pulang-pasien');?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!--<div class="col-md-12 filter-form"></div>-->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-cara-pulang-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th>No</th>
                                <th><?=\Yii::t("fe", "No Registrasi");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                                <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                <th><?=\Yii::t("fe", "Instalasi");?></th>
                                <th><?=\Yii::t("fe", "Ruangan");?></th>
                                <th><?=\Yii::t("fe", "Cara Pulang");?></th>
                                <th><?=\Yii::t("fe", "Rumah Sakit Dirujuk");?></th>
                                <th><?=\Yii::t("fe", "Kondisi Pulang");?></th>
                                <th><?=\Yii::t("fe", "Instalasi");?></th>
                                <th><?=\Yii::t("fe", "Ruangan");?></th>
                                <th><?=\Yii::t("fe", "Cara Pulang");?></th>
                                <th><?=\Yii::t("fe", "Kondisi Pulang");?></th>
                                <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php

$this->registerJs("
    var table;

    $(document).ready(function(){
        // Generate Table
        table = $('#lap-cara-pulang-pasien').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                targets: 0,
            }],
            sorting: [[1, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/laporan-cara-pulang-pasien/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                }, //0
                {title: '".(\Yii::t('fe', "No Registrasi"))."', data: 'no_registrasi', searchable: true}, //1
                {title: '".(\Yii::t('fe', "Tanggal Pendaftaran"))."', data: 'tgl_pendaftaran', searchable: false}, //2
                {title: '".(\Yii::t('fe', "Tanggal Pulang"))."', data: 'tgl_pulang', searchable: true}, //3
                {title: '".(\Yii::t('fe', "No Rekam Medik"))."', data: 'no_rekam_medik', searchable: true}, //4
                {title: '".(\Yii::t('fe', "Nama Pasien"))."', data: 'nama_pasien', searchable: true}, //5
                {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_nama', searchable: false}, //6
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_nama', searchable: false}, //7
                {title: '".(\Yii::t('fe', "Cara Pulang"))."', data: 'cara_pulang', searchable: false}, //8
                {title: '".(\Yii::t('fe', "Rumah Sakit Dirujuk"))."', data: 'rumahsakit_rujukan', render: (data) => { return data ? data : '-' }, searchable: true}, //9
                {title: '".(\Yii::t('fe', "Kondisi Pulang"))."', data: 'kondisi_pulang', render: (data) => { return data ? data : '-' }, searchable: false,}, //10
                {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_id', visible: false, searchable: true}, //11
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false, searchable: true}, //12
                {title: '".(\Yii::t('fe', "Cara Pulang"))."', data: 'carapulang_id', visible: false, searchable: true}, //13
                {title: '".(\Yii::t('fe', "Kondisi Pulang"))."', data: 'kondisipulang_id', visible: false, searchable: true}, //14
                {title: '".(\Yii::t('fe', "Cara Bayar"))."', data: 'cara_bayar', searchable: false}, //15
                {title: '".(\Yii::t('fe', "Cara Bayar"))."', data: 'carabayar_id', visible: false, searchable: true}, //16
            ],
        });
        
        $('.dataTables_filter').hide();

        //Filter berdasarkan Tanggal Pulang, Instalasi, Ruangan
        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                3,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                11,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('instalasi_nama', '',
                    ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'id' => 'filter_instalasi_nama',
                            'class' => 'form-control select2 selectInstalasi',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                12,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            //'data'=> [
                            //    Yii::$app->docoVars->workspace("ruangan_id") => Yii::$app->docoVars->workspace("ruangan_name")
                            //],
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2 selectRuangan'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi_nama'],
                               'placeholder' => '--Pilih Semua--',
                               'url' => Url::to(['/rm/laporan-cara-pulang-pasien/get-ruangan'])
                            ]
                        ])
                ))."<div>\"
            ],
            [
                13,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carapulang_id', '',
                    ArrayHelper::map($api['response']['cara_pulang'], 'carakeluar_id', 'carakeluar_nama'),
                        [
                            'id' => 'filter_cara_pulang',
                            'class' => 'form-control select2 selectCaraPulang',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                14,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kondisipulang_id', '',
                    ArrayHelper::map($api['response']['kondisi_pulang'], 'kondisikeluar_id', 'kondisikeluar_nama'),
                        [
                            'id' => 'filter_kondisi_pulang',
                            'class' => 'form-control select2 selectKondisiPulang',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                16,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('carabayar_id', '',
                    ArrayHelper::map($api['response']['cara_bayar'], 'carabayar_id', 'carabayar_nama'),
                        [
                            'id' => 'filter_cara_bayar',
                            'class' => 'form-control select2 selectCaraBayar',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
        ],
        {
            //posisi kolom dan grid
            3:0,
            11:1,
            12:2,
            13:3,
            14:4,
            5:5,
            4:6,
            1:7,
            9:8,
            16:9,
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

    });
",View::POS_END) ?>



