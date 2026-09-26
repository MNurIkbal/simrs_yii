<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
// use app\components\DocoHelpers;
// use app\components\DocoController;

$this->title = Yii::t('fe', 'Master Footer');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
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
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'tambah' => [
                            'type' => 'link',
                            'title' => Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs',
                                'data-target' => Url::home().('master/footer/create'),
                                'data-options' => 'link'
                            ]
                        ],
                        'update' => [
                            'type' => 'link',
                            'title' => Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs',
                                'data-target' => Url::home().('master/footer/update?id='),
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Kode Footer Kertas");?></th>
                            <th><?=\Yii::t("fe", "Nama Footer Kertas");?></th>
                            <th><?=\Yii::t("fe", "Gambar Kiri Bawah");?></th>
                            <th><?=\Yii::t("fe", "Gambar Kanan Bawah");?></th>
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

<?php
$this->registerJs('
var table;

    // Event Reload
    $(document).on("click", ".data-reset", function() {
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
                selector: "td:first-child"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/footer/get-data",
            columns: [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Kode Footer")).'", data: "kode_footer"},
                {title: "'.(\Yii::t("fe", "Nama Footer")).'", data: "nama_footer"},
                {title: "'.(\Yii::t("fe", "Gambar Kiri Bawah")).'", data: "gambarkiri", searchable: false},
                {title: "'.(\Yii::t("fe", "Gambar Kanan Bawah")).'", data: "gambarkanan", searchable: false},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table);
    });
', View::POS_END, 'b-index');
?>
