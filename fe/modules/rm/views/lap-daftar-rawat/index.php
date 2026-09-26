<?php
/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rekam Medik'), 'url' => ['/rm/lap-daftar-rawat']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'id' => 'button-cari',
                        ]
                    ],
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'rm/lap-daftar-rawat/export-excel?'
                        ]
                    ],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed"
                    id="example"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {

        // Generate Table
        table = $('#example').docoTabel({
            filter: true,
            ordering:false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl + 'rm/lap-daftar-rawat/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                	title: '".(\Yii::t("fe", "Tanggal Masuk"))."', 
                	data: 'Tanggal Masuk',
                	searchable:false
                },
                {
                	title: '".(\Yii::t("fe", "Nomor Pendaftaran"))."', 
                	data: 'pendaftaran',
                	searchable:false
                },
                {
                	title: '".(\Yii::t("fe", "Nomor Rekam Medik"))."', 
                	data: 'r_medik',
                	searchable:false
                },
                {
                	title: '".(\Yii::t("fe", "Nama Pasien"))."', 
                	data: 'pasien',
                	searchable:false
                },
                {
                	title: '".(\Yii::t("fe", "Penanggung Jawab"))."', 
                	data: 'penanggungjawab_nama',
                	searchable:false
                },
                {
                	title: '".(\Yii::t("fe", "Cara Bayar / Penjamin"))."', 
                	data: 'carabayar_penjamin', 
                	searchable: false
                },
                {
                	title: '".(\Yii::t("fe", "Kamar"))."', 
                	data: 'kamar',
                	searchable:false
                },
                {
                    title: '".(\Yii::t("fe", "TT"))."', 
                    data: 'no_tempattidur',
                    searchable:false
                },
                {
                	title: '".(\Yii::t("fe", "Kelas Pelayanan"))."', 
                	data: 'kp', 
                	searchable: false
                },
                {
                	title: '".(\Yii::t("fe", "DPJP"))."', 
                	data: 'Dokter',
                	searchable:false
                },
                {
                	title: '".(\Yii::t("fe", "Tanggal Pasien Pulang (Tanggal Keluar)"))."', 
                	data: 'Tanggal Keluar'
                },
            ],
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                11,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ]
        ],{
        	11:0
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate',true);

    });
    
    ", View::POS_END, 'b-index');
?>
