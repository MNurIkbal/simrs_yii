<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\InstalasiForm;
use Doco\master\controllers\InstalasiController;


$this->title = \Yii::t('fe', 'Instalasi');
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
                                'action' => '/master/instalasi/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'btn-edit',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/instalasi/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm'
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-instalasi');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-instalasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama rumah sakit");?></th>
                            <th><?=\Yii::t("fe", "Nama instalasi");?></th>
                            <th><?=\Yii::t("fe", "Nama singkatan instalasi");?></th>
                            <th><?=\Yii::t("fe", "SatuSehat Organization ID");?></th>
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
    // Global Var
    var tableInstalasi;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/instalasi/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tableInstalasi.draw();
            }
        });
        tableInstalasi.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableInstalasi.draw();
    });



    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableInstalasi = $("#table-instalasi").docoTabel({
            filter: true,
            //add for handle checkbox
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
            scrollX: true,
            ajax: baseUrl+"master/instalasi/get-data",
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
                {title: "'.(\Yii::t("fe", "Nama Rumah Sakit")).'",  data: "nama_rumahsakit"},
                {title: "'.(\Yii::t("fe", "Nama Instalasi")).'",  data: "instalasi_nama"},
                {title: "'.(\Yii::t("fe", "Nama Singkatan Instalasi")).'", data: "instalasi_singkatan",searchable: false},
                {title: "'.(\Yii::t("fe", "Satu Sehat Organization ID")).'", data: "satusehat_instalasi_id", searchable: false, orderable: false},
            ],
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 3,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableInstalasi, [
            [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_rumahsakit', '', ['class' => 'form-control','placeholder'=>'Nama Rumah Sakit']))).'\'],
            [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('instalasi_nama', '', ['class' => 'form-control','placeholder'=>'Nama Instalasi']))).'\'],
        ]);
    });


', View::POS_END, 'b-index');
?>
