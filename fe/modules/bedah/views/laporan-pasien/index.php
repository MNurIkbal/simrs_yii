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
                        'excel-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Unduh excel',
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'excel-bgprocess',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'data-url' => '/bedah/laporan-pasien/show-popup?id=',
                                'data-conditions' => 'pendaftaran_id,instalasi_id'
                            ]
                        ],
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
                            <?php foreach($header as $val): ?>
                                <th>
                            <?php endforeach; ?>
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

$this->registerJs('
var columns = ' . json_encode($header) . ';
var indexOf = {};
    // Event Ready
    $(document).ready(function() {
        generateColumn()
        // Generate Table
        table = $("#tabel-pasien-sentral").docoTabel({
            filter: true,
            scrollX: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax:baseUrl+"bedah/laporan-pasien/get-data",
            columns: columns
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                indexOf["tanggal_operasi"],
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="' . date('d-M-Y') . '"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="' . date('d-M-Y') . '"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ], 
            [
                6, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('kelaspelayanan_nama', '', 
                        ArrayHelper::map($list_ruangan, 'ruangan_id', 'ruangan_nama'), 
                        [
                            'id' => 'kelaspelayanan_nama', 
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Semua --')
                        ]
                    )
                )).'</div>\'
            ],
            [
                7, 
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('kelaspelayanan_nama', '', 
                        ArrayHelper::map($list_penjamin, 'penjamin_id', 'penjamin_nama'), 
                        [
                            'id' => 'kelaspelayanan_nama', 
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Semua --')
                        ]
                    )
                )).'</div>\'
            ],
        ], [
            indexOf["tanggal_operasi"],
            indexOf["no_pendaftaran"],
        ], true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });

    function generateColumn() {
        columns.forEach(function(val,key){
            indexOf[val.data] = key;
            if(val.data == "tanggal_operasi" || val.data == "no_pendaftaran"){
                columns[key].searchable = true;
            }
        })
    }
    ', View::POS_END, 'js');

?>