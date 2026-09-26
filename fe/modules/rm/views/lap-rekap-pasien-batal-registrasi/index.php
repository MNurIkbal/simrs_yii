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
                'excel' => [
                    'title' => Yii::t('fe', 'Unduh Excel'),
                    'attributes'=>[
                        'data-target'=>Url::home().'rm/lap-rekap-pasien-batal-registrasi/export-excel?'
                  ]
                ],
                'pdf'=>[
                    'type'=>'button',
                    'title' => \Yii::t('fe', 'Cetak PDF'),
                    'icon' => 'fa fa-file-pdf-o',
                    'attributes' => [
                        'data-target'=>Url::home().'rm/lap-rekap-pasien-batal-registrasi/export-pdf?',
                    ]
                ],
        ], '#lap-rekap-pasien-batal-registrasi');?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!--<div class="col-md-12 filter-form"></div>-->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-rekap-pasien-batal-registrasi" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th>No</th>
                                <th><?=\Yii::t("fe", "No Registrasi");?></th>
                                <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                                <th><?=\Yii::t("fe", "Instalasi");?></th>
                                <th><?=\Yii::t("fe", "Ruangan");?></th>
                                <th><?=\Yii::t("fe", "Alasan Batal");?></th>
                                <th><?=\Yii::t("fe", "Petugas");?></th>
                                <th><?=\Yii::t("fe", "Tanggal Registrasi");?></th>
                                <th><?=\Yii::t("fe", "Instalasi");?></th>
                                <th><?=\Yii::t("fe", "Ruangan");?></th>
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
        $('.flex-1').addClass('hidden')
        // Generate Table
        table = $('#lap-rekap-pasien-batal-registrasi').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                targets: 0,
            }],
            sorting: [[8, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-rekap-pasien-batal-registrasi/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                }, //0
                {title: '".(\Yii::t('fe', "No Registrasi"))."', data: 'no_registrasi', searchable: false}, //1
                {title: '".(\Yii::t('fe', "No Rekam Medik"))."', data: 'no_rekam_medik', searchable: false}, //2
                {title: '".(\Yii::t('fe', "Nama Pasien"))."', data: 'nama_pasien', searchable: false}, //3
                {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_nama', searchable: false}, //4
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_nama', searchable: false}, //5
                {title: '".(\Yii::t('fe', "Alasan Batal"))."', data: 'alasan_batal', searchable: false}, //6
                {title: '".(\Yii::t('fe', "Petugas"))."', data: 'petugas', searchable: false}, //7
                {title: '".(\Yii::t('fe', "Tanggal Registrasi"))."', data: 'tgl_registrasi', visible: false, searchable: true}, //8
                {title: '".(\Yii::t('fe', "Instalasi"))."', data: 'instalasi_id', visible: false, searchable: true}, //9
                {title: '".(\Yii::t('fe', "Ruangan"))."', data: 'ruangan_id', visible: false, searchable: true}, //10
            ],
        });
        
        $('.dataTables_filter').hide();

        //Filter berdasarkan Tanggal Registrasi, Instalasi, Ruangan
        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                8,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                9,
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
                10,
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
                               'url' => Url::to(['/rm/lap-rekap-pasien-batal-registrasi/get-ruangan'])
                            ]
                        ])
                ))."<div>\"
            ],
        ],
        {
            //posisi kolom dan grid
            8:0,
            9:1,
            10:2
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

    });
",View::POS_END) ?>