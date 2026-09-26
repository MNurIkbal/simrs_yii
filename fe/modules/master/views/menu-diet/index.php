<?php

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoTableHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;

$this->title = \Yii::t('fe', 'Menu diet');
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
                                'action' => '/master/menu-diet/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'btn-edit',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/menu-diet/update?jenis_id=',
                            ]
                        ],
                        /*'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm'
                            ]
                        ],*/
                        'pdf',
                        'excel',
                    ],'#table-menudiet');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-menudiet" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama jenis");?></th>
                            <th><?=\Yii::t("fe", "Nama makanan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var tableMenuDiet;
    $(document).on("click", ".data-reload", function() {
        tableMenuDiet.draw();
    });

    $(document).ready(function() {
        tableMenuDiet = $("#table-menudiet").docoTabel({
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
            // rowsGroup: [2, 1, 0],
            ajax: baseUrl+"master/menu-diet/get-data",
            columns: [
                {
                    title: "",
                    data: "dataCheckBox",
                    searchable: false,
                    orderable: false,
                    visible:true
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama jenis diet")).'",  data: "jenisdiet_nama", name: "jenisdiet_id"},
                {title: "'.(\Yii::t("fe", "Nama makanan")).'",  data: "makanandiet_nama", name:"makanandiet_nama"},
            ],
            // scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 2,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableMenuDiet, [
            [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_jenisdiet', '', $listJenisDiet, ['class' => 'form-control select2', 'prompt' => '-- Pilih '.\Yii::t('fe', 'Nama jenis diet').' --']))).'\'],
            [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_makanan', '', ['class' => 'form-control','placeholder'=>'Nama makanan']))).'\']
        ]);

        $("#table-menudiet tbody").on("click", "tr", function(){
            try {
                primary = tableMenuDiet.row(".selected").data().primary ? tableMenuDiet.row(".selected").data().primary : null;
            } catch (e) {
                primary = false;
            }
            console.log(primary);
            if (primary) {
                 $("#btn-edit").attr("action",$("#btn-edit").data("url")+primary);
            } else {
                $("#btn-edit").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }
            
        });        
    });


', View::POS_END, 'b-index');
?>