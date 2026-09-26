<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;

$this->title = \Yii::t('fe', 'Cara bayar');
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
                        // 'add' => [
                        //     'attributes' => [
                        //         'data-toggle' => 'modal',
                        //         'data-target' => '#modal_backdrop',
                        //         'action' => '/master/cara-bayar/create',
                        //     ]
                        // ],
                        // 'edit' => [
                        //     'attributes' => [
                        //         'data-toggle' => 'modal',
                        //         'data-target' => '#modal_backdrop',
                        //         'data-url' => '/master/cara-bayar/update?id=',
                        //     ]
                        // ],
                        // 'delete' => [
                        //     'attributes' => [
                        //     ]
                        // ],
                        // 'pdf',
                        'excel',
                    ],'#table-carabayar');?>

            </div>

            <div class="panel-body">
                <div class="panel panel-white">
                    <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12 filter-form">
                        <div class="row docofilter">
                        </div>
                    </div>
                </div>
                <table id="table-carabayar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th><?=\Yii::t("fe", "Nama");?></th>
                            <th><?=\Yii::t("fe", "Nama Lainnya");?></th>
                            <th width="1">Status</th>
                            <th><?=\Yii::t("fe", "Tampil di mobile");?></th>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
$this->registerJs('
    // Global Var
    var tableCaraBayar;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/cara-bayar/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tableCaraBayar.draw();
            }
        });
        tableCaraBayar.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableCaraBayar.draw();
    });

    // Event Delete


    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableCaraBayar = $("#table-carabayar").docoTabel({
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
            // stateSave: true,
            // scrollX: true,
            ajax: baseUrl+"master/cara-bayar/get-data",
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
                {title: "'.(\Yii::t("fe", "Nama")).'", data: "carabayar_nama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")).'", data: "carabayar_namalainnya"},
                {title: "Status", data: "is_active"},
                {title: "'.(\Yii::t("fe", "Tampil di mobile")).'", data: "is_online"},

            ],
            // scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 2,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableCaraBayar, [

            [4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\'],
            [5, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_online', '', $options['confirm'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\']

        ]);
    });



', View::POS_END, 'b-index');
?>
