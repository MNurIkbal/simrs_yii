<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Bedah Sentral', 'url' => ['/bedah']];
$this->params['breadcrumbs'][] = $title;

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
                <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'pdf',
                        'excel'
                    ],'#tabel-pasien-sentral');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                    
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="tabel-pasien-sentral" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=Yii::t('fe', 'No'); ?></th>
                            <th><?=Yii::t('fe', 'Tanggal Operasi'); ?></th>
                            <th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Pasien'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Operasi'); ?></th>
                            <th><?=Yii::t('fe', 'Golongan Operasi'); ?></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 

$this->registerJs('
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#tabel-pasien-sentral").docoTabel({
            filter: true,
            scrollX: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax:baseUrl+"bedah/laporan-jenis-operasi/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Operasi")).'",  
                    data: "tgl_tindakan"
                },
                {
                    title: "'.(\Yii::t("fe", "No Pendaftaran")).'", 
                    data: "no_pendaftaran"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien")).'", 
                    data: "nama_pasien",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Operasi")).'", 
                    data: "operasi_nama",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Golongan Operasi")).'", 
                    data: "golonganoperasi_nama",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Operasi")).'", 
                    data: "operasi_id",
                    visible : false,
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="' . date('d-M-Y') . '"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="' . date('d-M-Y') . '"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ], 
            [
                6, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('kelaspelayanan_nama', '', 
                        ArrayHelper::map($list_operasi, 'operasi_id', 'operasi_nama'), 
                        [
                            'id' => 'kelaspelayanan_nama', 
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Semua --')
                        ]
                    )
                )).'</div>\'
            ],
        ], {
            1:0
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });
    ', View::POS_END, 'js');

?>