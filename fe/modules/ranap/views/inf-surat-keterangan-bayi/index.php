<?php

/**
 * @Author: aris
 * @Date:   2019-06-24 15:00:00
 * @Description:
 */

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

$this->title = isset($title) ? $title : Yii::t('fe', 'Informasi Surat Keterangan Bayi');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")) , 'url' => ['/ranap']];
//$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/inf-pasien-ranap']];
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
                'periksa' => [
                    'title' => \Yii::t('fe', 'Buat SK Lahir'),
                    'icon' => 'fa fa-plus',

                    'attributes' => [
                        'data-target'=> '/ranap/inf-surat-keterangan-bayi/buat-sk?id=',
                    ]
                ],
                'excel' => [
                    'title' => Yii::t('fe', 'Excel'),
                    'attributes'=>[
                        'data-target'=>Url::home().'ranap/inf-surat-keterangan-bayi/export-excel?'
                  ]
                ],
                
        ], '#surat-bayi');?>
        </div>

      <div class="panel-body">
        <div class="row">
            <!--<div class="col-md-12 filter-form"></div>-->
        </div>
        <div class="advanced-filter">
        </div>
        <div class="row">
            <div class="col-md-12">
                <table id="surat-bayi" class="table table-striped table-condensed table-hover" style="width:100%">
                   <thead>
                    <tr class="bg-inverse">
                        <th></th>
                        <th>No</th>
                        <th><?=Yii::t('fe', 'Tanggal Pendaftaran')?></th>
                        <th><?=Yii::t('fe', 'No RM/Nama Bayi')?></th>
                        <th><?=Yii::t('fe', 'Tanggal Lahir')?></th>
                        <th><?=Yii::t('fe', 'Ruangan/Kamar/TT')?></th>
                        <th><?=Yii::t('fe', 'No RM/Nama Ibu')?></th>
                        <th><?=Yii::t('fe', 'Status')?></th>
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
        table = $('#surat-bayi').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[3, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'ranap/inf-surat-keterangan-bayi/get-data-bayi?',
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

                {
                    title: '".(\Yii::t('fe', 'Tanggal Pendaftaran'))."', 
                    data: 'tgl_pendaftaran'
                },
                {
                    title: '".(\Yii::t('fe', 'No RM/Nama Bayi'))."',
                    data: 'rm_namabayi',
                    name: 'nama_bayi'
                },
                {
                    title: '".(\Yii::t('fe', 'Tanggal Lahir'))."',
                    data: 'tgl_lahir',
                    name: 'tanggal_lahir',
                    searchable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Ruangan/Kamar/TT'))."',
                    data: 'nama_ruangan',
                    name: 'ruangan'
                },
                {
                    title: '".(\Yii::t('fe', 'No RM/Nama Ibu'))."',
                    data: 'rm_namaibu',
                    name: 'nama_ibu'
                },
                {
                    title: '".(\Yii::t('fe', 'Status'))."',
                    data: 'status_skl'
                },
            ],

            
        });
        
        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                2,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('status_skl', '',[
                        'Belum Buat'=>'Belum Buat',
                        'Sudah Buat'=>'Sudah Buat'
                    ],
                        [
                            'id' => 'filter_status_skl',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Semua--'),
                        ]
                    )
                ))."<div>\"
            ],
            
        ],
        {
            //posisi kolom dan grid
            2:0,
            3:1,
            5:2,
            6:3,
            7:4,
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

        });
        ")

        ?>



