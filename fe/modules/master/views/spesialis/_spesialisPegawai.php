<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Dokter Spesialis');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
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
                        'search' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-dokterSpesialis'
                            ]
                        ],
                        'reset' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-dokterSpesialis'
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/spesialis/update-spesialis-pegawai?id=',
                                'data-url' => '/master/spesialis/update-spesialis-pegawai?id=',
                            ]
                        ],
                        // 'pdf' => [
                        //     'attributes' => [
                        //         'data-target' => '/master/spesialis/export-pdf-spesialis-pegawai?',
                        //     ]
                        // ],
                        // 'excel' => [
                        //     'attributes' => [
                        //         'data-target' => '/master/spesialis/export-excel-spesialis-pegawai?',
                        //     ]
                        // ],
                    ],'#tableDokterSpesialis');?>    
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-dokterSpesialis"></div>
                </div>
                <table id="tableDokterSpesialis" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "NIK");?></th>
                            <th><?=\Yii::t("fe", "Nama Dokter");?></th>
                            <th><?=\Yii::t("fe", "Nama Spesialis");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var tablePegawai;
$(document).ready(function() {
    // Generate Table
    tablePegawai = $('#tableDokterSpesialis').docoTabel({
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        sorting: [[3, 'asc']], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+'master/spesialis/get-data-spesialis-pegawai',
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
            {title: "<?= (\Yii::t("fe", "NIK")); ?>", data: 'nomorindukpegawai', searchable: false},
            {title: "<?= (\Yii::t("fe", "Nama Dokter")); ?>", data: 'nama_pegawai'},
            {title: "<?= (\Yii::t("fe", "Nama Spesialis")); ?>", data: 'spesialis_nama'},
        ],
    });
    $('.dataTables_filter').hide();
    $('.filter-form-dokterSpesialis').datatableBootstrapFilter(
        tablePegawai, 
            [],
            {
                3:0,
                4:1,
            }
    );
});
</script>