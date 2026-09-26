<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-04 12:20:57
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-10 10:11:55
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\GolonganpegawaiForm;
use Doco\master\controllers\GolonganpegawaiController;;


$this->title = \Yii::t('fe', 'Golongan Operasi');
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
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/golongan-operasi/create',
                            ]
                        ],
                        // 'edit' => [
                        //     'attributes' => [                                
                        //         'data-options'=>'modal',    
                        //         'data-target'=>'#modal_backdrop',                            
                        //         'data-url' => '/master/golongan-operasi/update?id=',
                        //     ]
                        // ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                            ]
                        ],
                        // 'pdf',
                        // 'excel',
                    ],'#example');?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Nama Golongan Operasi");?></th>
                            <th><?=\Yii::t("fe", "Kode Golongan Operasi");?></th>
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
            sorting: [[4, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/golongan-operasi/get-data",
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
                {title: "'.(\Yii::t("fe", "Nama Golongan Operasi")).'",  data: "golonganoperasi_nama"},
                {title: "'.(\Yii::t("fe", "Kode Golongan Operasi")).'", data: "golonganoperasi_kode"},
                {
                    title: "id",
                    data: "golonganoperasi_id",
                    searchable: false,
                    orderable: false,
                    visible : false
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [[2, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('golonganoperasi_nama', '', [], ['class' => 'form-control selectGolongan select2', 'prompt' => \Yii::t('fe', 'Nama Golongan Operasi')]))).'\']]);
        
        $(".selectGolongan").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "/master/end-point/get-data-golongan-operasi",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
    });
', View::POS_END, 'b-index');
?>
