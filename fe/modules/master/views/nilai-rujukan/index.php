<?php

/**
* @author Sunarko
* @todo Master Nilai Rujukan
* @copyright 03-07-2018
*/

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\DisplayAntrianForm;
use Doco\master\controllers\DisplayAntrianController;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;


$this->title = \Yii::t('fe', 'Nilai Rujukan Pemeriksaan Laboratorium');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master']];
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
                        // 'add' => [
                        //     'attributes' => [
                        //         'data-options' => 'link',
                        //         'data-target' => '/master/nilai-rujukan/create',
                        //     ]
                        // ],
                        'edit' => [
                            'attributes' => [
                                'data-target' => '/master/nilai-rujukan/update?id=',
                            ]
                        ],
                        // 'delete' => [
                        //     'attributes' => [
                        //     'data-additional' => 'data-rm'
                        //     ]
                        // ],
                        // 'pdf' => [
                        //     'title' => Yii::t('fe', 'Cetak'),
                        //     'attributes'=>[
                        //         'data-target'=>Url::home().'ranap/inf-pasien-ranap/export-pdf?jenis=ranap&'
                        //     ],
                        //   ],
                        //   'excel' => [
                        //     'title' => Yii::t('fe', 'Excel'),
                        //     'attributes'=>[
                        //         'data-target'=>Url::home().'ranap/inf-pasien-ranap/export-excel?jenis=ranap&'
                        //     ]
                        //   ],
                    ],'#example');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>                                  
                            <th width="80">No</th>
                            <th><?=\Yii::t("fe", "Kelompok Pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Nama Pemeriksaan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-nilairujukan"
                    data-source="<?//=Url::home();?>master/nilai-rujukan/get-data"
                    data-filter=".form-filter"
                    data-test="true"
                    style="width: 100%"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <?//php for ($i=0; $i < count($myHeader) ; $i++) { ?>
                                <th></th>
                            <?//php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table> -->
            </div>
        </div>
    </div>
</div>

<?php
// var datas = ".json_encode($myHeader).";

$this->registerJs("
    var table;
    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    $(document).ready(function(){
        table = $('#example').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }],
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'master/nilai-rujukan/get-data',
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
                {title: '".(\Yii::t('fe', 'Kelompok Pemeriksaan'))."',  data: 'nama_kelompok'},
                {title: '".(\Yii::t('fe', 'Nama Pemeriksaan'))."', data: 'nama_pemeriksaan'},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                2, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('nama_kelompok', '', array(), 
                            [
                                'class' => 'form-control select2 nama_kelompok', 
                                'prompt' => '', 
                                'col-index' => 2
                            ]
                        )
                        )
                )."<div>\"
                ],

                [
                3, 
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::dropDownList('nama_pemeriksaan', '', array(), 
                            [
                                'class' => 'form-control select2 nama_pemeriksaan', 
                                'prompt' => '', 
                                'col-index' => 2
                            ]
                        )
                        )
                )."<div>\"
                ],
            ]
        );

        $('.nama_kelompok').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/master/nilai-rujukan/get-kelompok-periksa',
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

        $('.nama_pemeriksaan').select2({
            placeholder: '',
            minimumInputLength: 2,
            ajax: {
                url: '/master/nilai-rujukan/get-pemeriksaan-lab',
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


    ", VIEW::POS_END, 'js-kunings');
?>
