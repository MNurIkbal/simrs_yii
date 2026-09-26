<?php

use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.dataTables_scroll {
    max-height: 99999em !important
    }
.dataTables_wrapper {
    margin-bottom: 30px;
}
</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?=Yii::$app->docoVars->workspace("modul_icon");?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title">
                        <b>
                            <?php
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
                'search' => [
                    'attributes' => [
                        'id' => 'search'
                    ]
                ],
                'reset' => [
                    'attributes' => [
                        'data-parent' => '.filter-form', 'id' => 'reset']],
                'print-excel-all'=>[
                    'type'=>'button',
                    'title' => \Yii::t('fe', 'Unduh Excel'),
                    'icon' => 'fa fa-file-excel-o',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id'=>'btn-print-excel-all',
                        'data-pages'=>'_blank',
                        'data-options' => 'custom-print',
                        'data-target'=>Url::home().'rm/lap-carabayar-pasien/export-excel?',
                    ]
                ],
                // 'pdf',
                // 'excel',
            ], '#lap-carabayar-pasien-rajal');?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!-- <div class="col-md-12 filter-form"></div> -->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-carabayar-pasien-rajal" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="<?=count($listCarabayar)+2;?>" class="text-center">
                                <?=\Yii::t("fe", "RAWAT JALAN");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class='text-center' ><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <?php
                                foreach ($listCarabayar as $key => $value) {
                                    echo "<th class='text-center' >".\Yii::t("fe", $value['carabayar_nama'])."</th>";
                                }
                            ?>
                            <th class='text-center' ><?=\Yii::t("fe", "Jumlah");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="<?=count($listCarabayar)+2;?>"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-carabayar-pasien-igd" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="<?=count($listCarabayar)+2;?>" class="text-center">
                                <?=\Yii::t("fe", "RAWAT DARURAT");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class='text-center' ><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <?php
                                foreach ($listCarabayar as $key => $value) {
                                    echo "<th class='text-center' >".\Yii::t("fe", $value['carabayar_nama'])."</th>";
                                }
                            ?>
                            <th class='text-center' ><?=\Yii::t("fe", "Jumlah");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="<?=count($listCarabayar)+2;?>"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-carabayar-pasien-ranap" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="<?=count($listCarabayar)+2;?>" class="text-center">
                                <?=\Yii::t("fe", "RAWAT INAP");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class='text-center' ><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <?php
                                foreach ($listCarabayar as $key => $value) {
                                    echo "<th class='text-center' >".\Yii::t("fe", $value['carabayar_nama'])."</th>";
                                }
                            ?>
                            <th class='text-center' ><?=\Yii::t("fe", "Jumlah");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="<?=count($listCarabayar)+2;?>"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-carabayar-pasien-mcu" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="<?=count($listCarabayar)+2;?>" class="text-center">
                                <?=\Yii::t("fe", "MCU");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class='text-center' ><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <?php
                                foreach ($listCarabayar as $key => $value) {
                                    echo "<th class='text-center' >".\Yii::t("fe", $value['carabayar_nama'])."</th>";
                                }
                            ?>
                            <th class='text-center' ><?=\Yii::t("fe", "Jumlah");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="<?=count($listCarabayar)+2;?>"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="lap-carabayar-pasien-penunjang" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th colspan="<?=count($listCarabayar)+2;?>" class="text-center">
                                <?=\Yii::t("fe", "PENUNJANG");?>
                            </th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class='text-center' ><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <?php
                                foreach ($listCarabayar as $key => $value) {
                                    echo "<th class='text-center' >".\Yii::t("fe", $value['carabayar_nama'])."</th>";
                                }
                            ?>
                            <th class='text-center' ><?=\Yii::t("fe", "Jumlah");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="<?=count($listCarabayar)+2;?>"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var tabRajal, tabIGD, tabRanap, tabMCU, tabPenunjang;
    var listCarabayar = ".json_encode($listCarabayar).";
    var columnKeys = [];
    
    columnKeys[0] = {title: '".(\Yii::t('fe', "Tanggal Pendaftaran"))."', data: 'tgl_pendaftaran', visible: false};
    for (i = 0; i < listCarabayar.length; i++) {
        columnKeys[i+1] = {title: listCarabayar[i].carabayar_nama, data: listCarabayar[i].carabayar_nama, searchable: false};
    }
    columnKeys[listCarabayar.length+1] = {title: '".(\Yii::t('fe', "Jumlah"))."', data: 'jumlah', searchable: false};
    
    $(document).ready(function(){
        $('.flex-1').addClass('hidden')
        tabRajal = $('#lap-carabayar-pasien-rajal').docoTabel({
            filter: true,
            columnDefs: [
                { targets: '_all', className: 'text-center', orderable: false }
            ],
            displayLength: 10,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-carabayar-pasien/get-data-rajal',
            columns: columnKeys,
        });

        tabIGD = $('#lap-carabayar-pasien-igd').docoTabel({
            filter: true,
            columnDefs: [
                { targets: '_all', className: 'text-center', orderable: false }
            ],
            displayLength: 10,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-carabayar-pasien/get-data-igd',
            columns: columnKeys,
        });

        tabRanap = $('#lap-carabayar-pasien-ranap').docoTabel({
            filter: true,
            columnDefs: [
                { targets: '_all', className: 'text-center', orderable: false }
            ],
            displayLength: 10,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-carabayar-pasien/get-data-ranap',
            columns: columnKeys,
        });

        tabMCU = $('#lap-carabayar-pasien-mcu').docoTabel({
            filter: true,
            columnDefs: [
                { targets: '_all', className: 'text-center', orderable: false }
            ],
            displayLength: 10,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-carabayar-pasien/get-data-mcu',
            columns: columnKeys,
        });

        tabPenunjang = $('#lap-carabayar-pasien-penunjang').docoTabel({
            filter: true,
            columnDefs: [
                { targets: '_all', className: 'text-center', orderable: false }
            ],
            displayLength: 10,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/lap-carabayar-pasien/get-data-penunjang',
            columns: columnKeys,
        });
        
        $('.dataTables_filter').hide();
        generateFillter(tabRajal)
    });

function generateFillter(targetTab) {
    $('.filter-form').datatableBootstrapFilter(targetTab, 
    [
        [
            0,
            \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate date' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate date' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
        ],
    ],
    {
        //posisi kolom dan grid
        0:0,
    });

    dateRangeHelper('.startDate','.endDate','.targetDate');
}

function resetTable() {
    //tabRajal.columns().search('').draw()
    tabIGD.columns().search('').draw()
    tabRanap.columns().search('').draw()
    tabMCU.columns().search('').draw()
    tabPenunjang.columns().search('').draw()
}

$(document).on('change keyup click', '.date', function(){
    let start = $('#rangeDemoStart').val()
    let end = $('#rangeDemoFinish').val()
    periode = start +' - '+ end
})

$(document).on('click', '#search', function() {
    applyFillter()
})

$(document).on('click', '#reset', function() {
    periode = ''
    resetTable()
})

function searchFilter(targetTab, pos1, val1=periode){
    targetTab.column(pos1).search(val1).draw()
}

function applyFillter() {
    //searchFilter(tabRajal, 0)
    searchFilter(tabIGD, 0)
    searchFilter(tabRanap, 0)
    searchFilter(tabMCU, 0)
    searchFilter(tabPenunjang, 0)
}

",View::POS_END)

?>

