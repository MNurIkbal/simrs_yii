<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-07 11:03:58
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 10:30:58
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
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'proses'=>[
                        'title'=>\Yii::t('fe', 'Proses'),
                        'icon'=>'fa fa-stethoscope',
                        'attributes'=>[
                            'data-target'=>Url::to(['detail', 'id'=>'']),
                            'data-conditions'=>'periksa'
                        ]
                    ],
                    'detail'=>[
                        'title'=>\Yii::t('fe', 'Lihat'),
                        'icon'=>'fa fa-eye',
                        'attributes'=>[
                            'data-target'=>Url::to(['detail', 'id'=>'']),
                            'data-conditions'=>'periksa'
                        ]
                    ],
                    'batal'=>[
                        'title'=>\Yii::t('fe', 'Batal'),
                        'icon'=>'fa fa-close',
                        'attributes'=>[
                            // 'data-toggle' => 'modal',
                            // 'data-target' => '#modal_backdrop',
                            // 'data-width' => '50%',
                            'data-target' => Url::to(['batal-operasi', 'id'=>''])
                        ]
                    ]
                ]);?>
            </div>
            <div class="panel-body">
                <div class="filter-form">
                    
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "Tanggal rujukan");?></th>
                            <th><?=\Yii::t("fe", "Nomor operasi");?></th>
                            <th><?=\Yii::t("fe", "Tanggal operasi");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran").' / '. \Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Instalasi").' / '. \Yii::t("fe", "Ruang perujuk");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="12"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
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
    $(document).on('click', '#example tbody tr', function () {
    // $(document).on('click','.select-checkbox', function () {
        // console.log($('.select-checkbox').is(':checked'))
        // if($('.select-checkbox').is(':checked')){
            try{
                primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
                status_periksa = table.row('.selected').data().status_periksa ? table.row('.selected').data().status_periksa : null;
            } catch(e){
                primaryKey = false
                status_periksa = false
            }
            if(status_periksa == 488){
                $('.btn-batal').attr('disabled', false)
                $('.btn-proses').attr('disabled', false)
                $('.data-detail').attr('disabled', true)
            }else if(status_periksa == 482 ){
                $('.btn-batal').attr('disabled', true)
                $('.btn-proses').attr('disabled', false)
                $('.data-detail').attr('disabled', true)
            }else if(status_periksa == 483 ){
                $('.btn-batal').attr('disabled', true)
                $('.btn-proses').attr('disabled', true)
                $('.data-detail').attr('disabled', false)
            }else if(status_periksa != false){
                $('.btn-batal').attr('disabled', true)
                $('.btn-proses').attr('disabled', true)
                $('.data-detail').attr('disabled', true)
            }else{
                $('.btn-batal').attr('disabled', true)
                $('.btn-proses').attr('disabled', true)
                $('.data-detail').attr('disabled', true)
            }
        // }else{
        //     $('.btn-batal').attr('disabled', true)
        //     $('.btn-proses').attr('disabled', true)
        // }
        
    });
    $(document).ready(function(){
        localStorage.clear();
        localStorage.setItem('ruangan', '".json_encode($ruangan)."');
        localStorage.setItem('penjamin', '".json_encode($instalasi)."');
        $('.btn-proses').attr('disabled', true)
        $('.btn-batal').attr('disabled', true)
        $('.data-detail').attr('disabled', true)
        table = $('#example').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
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
            // fixedColumns: {
            //     leftColumns: 1
            // },
            ajax: baseUrl+'bedah/informasi-pasien-operasi/get-data',
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
                {
                    title: '".(\Yii::t("fe", "Instalasi perujuk"))."',
                    data: 'asalrujukan_nama',
                    name: 'instalasiasal_id',
                    visible: false
                },
                {
                    title: '".(\Yii::t("fe", "Ruangan perujuk"))."',
                    data: 'ruangan_nama',
                    name: 'ruanganasal_id',
                    visible: false
                },
                {
                    title: '".(\Yii::t("fe", "No Rekam Medik"))."',
                    data: 'no_rekam_medik',
                    visible: false
                },
                {
                    title: '".(\Yii::t("fe", "No Pendaftaran"))."',
                    data: 'no_pendaftaran',
                    visible: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal rujukan'))."', data: 'tgl_rujukan'},
                {title: '".(\Yii::t('fe', 'Nomor operasi'))."', data: 'no_masukpenunjang', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Tanggal operasi'))."', data: 'tgl_operasi'},
                {title: '".(\Yii::t("fe", "No Pendaftaran").' / '. \Yii::t("fe", "No Rekam Medik"))."',  data: 'no_pendaftaran_medik', searchable: false},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."',  data: 'nama_pasien'},
                {title: '".(\Yii::t("fe", "Instalasi").' / '. \Yii::t("fe", "Ruang perujuk"))."',  data: 'instalasi_ruangan', searchable: false},
                {title: '".(\Yii::t('fe', 'Status'))."',  data: 'status', name: 'status_periksa'},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                6,
                \"<div class='input-group'><input type='text' value='".date('d-M-Y')."' id='rangeDemoStart' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' value='".date('d-M-Y')."'  id='rangeDemoFinish' readonly='true' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
                8,
                \"<div class='input-group'><input type='text' id='rangeDemoStartOp' class='form-control startDateOp'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinishOp' readonly='true' class='form-control endDateOp'/><input type='text' style='display:none' class='targetDateOp' col-index=2></div>\"
            ],
            [
                2, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('noresep', '', $instalasi, 
                            [
                                'id' => 'filter_instalasi',
                                'class' => 'form-control select2 dep-to-child', 
                                'prompt' => \Yii::t('fe', '-- Instalasi perujuk --'),
                                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/informasi-pasien-operasi/get-ruangan',
                                'data-depend_id' => 'filter_ruangan',
                                'data-depend_prompt' => \Yii::t('fe', '-- Ruangan perujuk --'),
                                'data-storage' => 'ruangan',
                                'data-key' => 'ruangan_nama',
                            ]
                        )
                        )
                )."<div>\"
            ],
            [
                3, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('noresep', '', $ruangan, 
                            [
                                'id' => 'filter_ruangan',
                                'class' => 'form-control select2 dep-to-parent', 
                                'data-depend_id' => 'filter_instalasi',
                                'prompt' => \Yii::t('fe', '-- Ruangan perujuk --')
                            ]
                        )
                        )
                )."<div>\"
            ],
            [
                12, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('noresep', '', $status, 
                            [
                                'class' => 'form-control select2', 
                                'prompt' => '', 
                            ]
                        )
                        )
                )."<div>\"
            ],
        ], {6:2, 3:10, 8:6, 5:4, 5:3}, true);
        dateRangeHelper('.startDate','.endDate','.targetDate');
        dateRangeHelper('.startDateOp','.endDateOp','.targetDateOp');
    });


    ", VIEW::POS_END, 'js-kunings');
?>