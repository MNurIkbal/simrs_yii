<?php

/**
 * @author Randy Vianda Putra
 * @copyright 17 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'detail2'=>[
                        'type'=>'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-list-ul',
                        'method' => 'not-exist',
                        'attributes' => [
                            'class' => 'data-detail',
                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/detail?id=',
                            'data-conditions'=>'type'
                        ]
                    ],
                    'penerimaan' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Penerimaan Obat Alkes'),
                        'icon' => 'fa fa-check-square-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'class' => 'data-penerimaan',
                            'data-target' => Url::home().('apotek/informasi-mutasi/penerimaan?id='),
                            'data-conditions' => 'nomutasioa'
                        ]
                    ],
                ]);?>
                <div class="pull-right">
                    <?=DocoHelpers::generateToolbar([
                    'reset'
                ]);?>
                </div>
            </div>
            <div class="panel-body">
                <input type="hidden" value="<?=Yii::$app->docoVars->workspace("instalasi_name")?>" class="hiddenInstalasi">
                <input type="hidden" value="<?=Yii::$app->docoVars->workspace("ruangan_name")?>" class="hiddenRuangan">
                <div class="col-md-12 filter-form"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="80">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Mutasi");?></th>
                            <th><?=\Yii::t("fe", "No Mutasi");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan Asal");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Terima");?></th>
                            <th><?=\Yii::t("fe", "Reference");?></th>
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
    $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
    $status = DocoConstants::STATUS_TERIMA;
    $this->registerCss($this->render('../assets/css/apotek.css'));

    $this->registerJs("
    localStorage.clear();
    localStorage.setItem('ruangan', '".json_encode(ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'))."');

    // Global Var
    var table;
    var _ruanganId = $ruangan_id;
    var _status = $status;
    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {


        // Generate Table
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
            rowCallback: function(row, data, index){
                var hiddenInstalasi = $('.hiddenInstalasi').val();
                var hiddenRuangan = $('.hiddenRuangan').val();
                if(hiddenInstalasi == data['instalasi_nama'] && hiddenRuangan == data['ruangan_nama']){
                    $('td:first-child').addClass('select-checkbox');
                }
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'apotek/informasi-mutasi/get-data',

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
                    title: '".(\Yii::t('fe', 'Tanggal Mutasi'))."',
                    data: 'tglmutasioa'
                },
                {
                    title: '".(\Yii::t('fe', 'No Mutasi'))."',
                    data: 'nomutasioa'
                },
                {
                    title: '".(\Yii::t('fe', 'Instalasi - Ruangan Asal'))."',
                    data: 'instalasi_ruangan',
                    name: 'ruangan_asal_id',
                },
                {
                    title: '".(\Yii::t('fe', 'Status'))."',
                    data: 'statusmutasi',
                    searchable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Tanggal Terima'))."',
                    data: 'tgl_terima',
                },
                {
                    title: '".(\Yii::t('fe', 'Reference'))."',
                    data: 'reference',
                },
                {
                    data: 'primary',
                    searchable: false,
                    orderable: false,
                    visible: false,
                },
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                    2,
                    \"<div class='input-group'><input type='text' id='rangeDemoStart' value=".date('d-M-Y')." class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' value=".date('d-M-Y')." class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
                ],
            ], {}, true
        );
        dateRangeHelper('.startDate','.endDate','.targetDate');

        $('.selectNomutasi').select2({
                placeholder: '',
                minimumInputLength: 3,
                ajax: {
                    url: '/apotek/informasi-mutasi/get-data-nomutasi2',
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(term, page){
                        return{
                            q: term,
                            page: page
                        }
                    },
                    processResults: function (data) {
                      return {
                        results: data.result
                      };
                    }
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            });

    });

    $(document).on('click','#example  tr', function (event) {
        event.preventDefault();
        var tbl = table.row('.selected').data();
        if (typeof tbl == 'undefined') {
            $('.data-penerimaan').show();
            return false;
        }

        if (_status == tbl.status_mutasi) {
            $('.data-penerimaan').hide();
            return false;
        }
        $('.data-penerimaan').show();

    });
    ", View::POS_END, 'js-kuning');

?>

<script>
</script>
