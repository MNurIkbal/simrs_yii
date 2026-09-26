<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-12 15:02:51
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-19 13:08:34
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'lihat'=>[
	                        'title' => \Yii::t('fe', 'Lihat'),
	                        'icon' => 'fa fa-list-ul',
	                        'method' => 'not-exist',
	                        'attributes' => [
	                            'data-options'=>'modal',    
                                'data-target'=>'#modal_backdrop',
	                            'data-url' => '/laboratorium/informasi-tarif-penunjang/detail?id=',
	                        ] 
	                    ],
                        //'pdf',
	                    // 'pdf' => [
                     //        'type' => 'link',
                     //        'attributes' => [
                     //            'data-options' => 'link',
                     //            'class' => 'btn btn-info btn-labeled btn-xs data-print',
                     //            'id' => 'cetak-pdf',
                     //            'url' => '/laboratorium/informasi-tarif-penunjang/export-pdf'
                     //        ]
                     //    ],
                        'export-pdf-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Cetak PDF',
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id' => 'data-export-pdf-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'laboratorium/informasi-tarif-penunjang/show-popup-pdf?',
                                'data-width' => '75%'
                            ]
                        ],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Export Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-table-id' => 'example',
                                'data-url' => Url::home() . 'laboratorium/informasi-tarif-penunjang/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                    ],'#example');?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Kelompok Pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Jenis Pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Nama Pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Tarif Total");?></th>
                            <th><?=\Yii::t("fe", "Cyto Tindakan (%)");?></th>
                            <th><?=\Yii::t("fe", "Diskon Tindakan (%)");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    $("#cetak-pdf").on("click",function (event) {
        event.preventDefault();
        window.open(baseUrl+"'.(Yii::$app->controller->module->id).'/informasi-tarif-penunjang/export-pdf?"+$.param(table.ajax.params()));
        return false;
    });
    

    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[6, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"laboratorium/informasi-tarif-penunjang/get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",       
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Ruangan")).'",  data: "ruangan_nama"},
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama"},
                {title: "'.(\Yii::t("fe", "Kelompok Pemeriksaan")).'", data: "nama_kelompok"},
                {title: "'.(\Yii::t("fe", "Jenis Pemeriksaan")).'", data: "jenispemeriksaanlab_nama"},
                {title: "'.(\Yii::t("fe", "Nama Pemeriksaan")).'", data: "pemeriksaanlab_nama"},
                {title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", data: "kelaspelayanan_nama"},
                {title: "'.(\Yii::t("fe", "Tarif Total")).'", data: "harga_tariftindakan", searchable: false},
                {title: "'.(\Yii::t("fe", "Cyto Tindakan (%)")).'", data: "persencyto_tindakan", searchable: false},
                {title: "'.(\Yii::t("fe", "Diskon Tindakan (%)")).'", data: "persendiskon_tindakan", searchable: false},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    2, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('ruangan_nama', '', 
                            ArrayHelper::map($response['ruangan'], 'ruangan_id', 'ruangan_nama'), 
                            [
                                'id' => 'ruangan_nama', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Ruangan')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    3, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('penjamin_nama', '', 
                            ArrayHelper::map($response['penjamin'], 'penjamin_id', 'penjamin_nama'), 
                            [
                                'id' => 'penjamin_nama', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Penjamin')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    4, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('nama_kelompok', '',
                            ArrayHelper::map($response['kelompok'], $response['kelompok_id'], 'nama_kelompok'),
                            [
                                'id' => 'nama_kelompok', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Kelompok Pemeriksaan')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    5, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList($response['jenispemeriksaan_nama'], '',
                            ArrayHelper::map($response['jenis'], $response['jenispemeriksaan_id'], $response['jenispemeriksaan_nama']),
                            [
                                'id' => 'jenispemeriksaanlab_nama', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Jenis Pemeriksaan')
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    7, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('kelaspelayanan_nama', '', 
                            ArrayHelper::map($response['kelas_pelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama'), 
                            [
                                'id' => 'kelaspelayanan_nama', 
                                'class' => 'form-control select2', 
                                'prompt' => \Yii::t('fe', 'Kelas Pelayanan')
                            ]
                        )
                    )).'</div>\'
                ],
            ]
        );
    });
', View::POS_END, 'b-index');
?>