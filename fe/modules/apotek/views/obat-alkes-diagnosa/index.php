<?php

/**
 * @author Randy Vianda Putra
 * @edite yaya
 * @date 24 Maret 2018
**/

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', $this->context->_title);
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$title); ?></b></h3>
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
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => $this->context->_module . '/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-url' => $this->context->_module . '/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional'=>'data-rm'
                            ] 
                        ],
                        'pdf',
                    ], '#table-kasus');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-kasus" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Diagnosa Pasien");?></th>

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
    // Global Var
    var table;
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#table-kasus").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"apotek/obat-alkes-diagnosa/get-data",
            columns: [
                 {
                    data : null,
                    render : function () {
                        return null;
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",
                    data: "obatalkes_nama",
                    name : "obatalkes_m.obatalkes_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Diagnosa")). '",
                    data: "diagnosa_nama",
                    name : "diagnosa_m.diagnosa_nama"

                },

            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Select2::widget([
                            'name' => 'obatalkes_nama',
                            'options' => ['placeholder' => \Yii::t('fe', 'Nama Obat Alkes'),'autocomplete'=>'off'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::home().(Yii::$app->controller->module->id).'/obat-alkes-diagnosa/get-obat-alkes',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                            ],
                        ])
                    )).'\'
                ],
                [
                    3,
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Select2::widget([
                            'name' => 'diagnosa_nama',
                            'options' => ['placeholder' => \Yii::t('fe', 'Nama Diagnosa')],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::home().(Yii::$app->controller->module->id).'/obat-alkes-diagnosa/get-diagnosa',
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                            ],
                        ])
                    )).'\'
                ],
            ]);
    });

', View::POS_END, 'b-index');
?>
