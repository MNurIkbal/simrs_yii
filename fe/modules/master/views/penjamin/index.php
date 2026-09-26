<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\PenjaminForm;
use Doco\master\controllers\PenjaminController;


$this->title = 'Penjamin';
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/penjamin/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/master/penjamin/update?id=',
                        ]
                    ],
                    // 'edit' => [
                    //     'attributes' => [
                    //         'data-toggle' => 'modal',
                    //         'data-target' => '#modal_backdrop',
                    //         'data-url' => '/master/penjamin/update?id=',
                    //     ]
                    // ],
                    'delete' => [
                        'attributes' => []
                    ],
                    'pdf',
                    'excel',
                ], '#table-penjamin'); ?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-penjamin" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th style="width:5%"></th>
                            <th>No</th>
                            <th><?= \Yii::t("fe", "Nama"); ?></th>
                            <th><?= \Yii::t("fe", "Cara bayar"); ?></th>
                            <th><?= \Yii::t("fe", "Nama lainnya"); ?></th>
                            <th><?= \Yii::t("fe", "Alaman penjamin"); ?></th>
                            <th style="width:5%">Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
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
    var tablePenjamin;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/penjamin/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "' . (\Yii::t("fe", "Konfirmasi")) . '",
            confirmMessage : "' . (\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")) . '",
            success : function (data) {
                tablePenjamin.draw();
            }
        });
        tablePenjamin.draw();
    });

    $("#table-penjamin tbody").on("click", "tr", function(){
        try {
             var primaryKey = tablePenjamin.row(".selected").data().primary ? tablePenjamin.row(".selected").data().primary : null;
        } catch (e) {
            var primaryKey = false;
        }
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tablePenjamin.draw();
    });


    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tablePenjamin = $("#table-penjamin").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"master/penjamin/get-data-penjamin",
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
                {title: "' . (\Yii::t("fe", "Nama")) . '", data: "penjamin_nama"},
                {title: "' . (\Yii::t("fe", "Cara bayar")) . '",  data: "carabayar_m.carabayar_nama"},
                {title: "' . (\Yii::t("fe", "Nama lainnya")) . '",  data: "penjamin_namalainnya"},
                {title: "' . (\Yii::t("fe", "Alaman penjamin")) . '", data: "alamat_penjamin"},
                {title: "' . (\Yii::t("fe", "Status")) . '", data: "is_active"},

            ],
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 0,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tablePenjamin, [[6, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\']]);
    });


', View::POS_END, 'b-index');
?>