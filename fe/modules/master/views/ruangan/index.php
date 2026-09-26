<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Ruangan');
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
                            'action' => '/master/ruangan/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'id' => 'btn-edit',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/master/ruangan/update?id=',
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'data-additional' => 'data-rm'
                        ]
                    ],
                    'pdf',
                    'excel',
                ], '#table-ruangan'); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-ruangan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Nama instalasi"); ?></th>
                            <th><?= \Yii::t("fe", "Nama ruangan"); ?></th>
                            <th><?= \Yii::t("fe", "Nama ruangan singkatan"); ?></th>
                            <th><?= \Yii::t("fe", "Status"); ?></th>
                            <th><?= \Yii::t("fe", "Satu Sehat Location ID"); ?></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="17"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
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
    var tableRuangan;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/ruangan/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "' . (\Yii::t("fe", "Konfirmasi")) . '",
            confirmMessage : "' . (\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")) . '",
            success : function (data) {
                tableRuangan.draw();
            }
        });
        tableRuangan.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });


    // Event Ready
    $(document).ready(function() {
        jQuery("#btn-delete").removeClass("btn-toolbar");

        // Generate Table
        tableRuangan = $("#table-ruangan").docoTabel({
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
            ajax: baseUrl+"master/ruangan/get-data",
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
                {title: "' . (\Yii::t("fe", "Nama Instalasi")) . '",  data: "instalasi_nama"},
                {title: "' . (\Yii::t("fe", "Nama Ruangan")) . '",  data: "ruangan_nama"},
                {title: "' . (\Yii::t("fe", "Nama Ruangan Singkatan")) . '",  data: "ruangan_singkatan",searchable: false},
                {title: "' . (\Yii::t('fe', 'Status')) . '", data: "is_active"},
                {title: "' . (\Yii::t('fe', 'Satu Sehat Location ID')) . '", data: "satusehat_ruangan_id", searchable: false, orderable: false},
            ],
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 2,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableRuangan, [
            [2, \'' . (preg_replace("/[\n\t\r]/i", '', Html::textInput('instalasi_nama', '', ['class' => 'form-control', 'placeholder' => 'Nama Instalasi']))) . '\'],
            [3, \'' . (preg_replace("/[\n\t\r]/i", '', Html::textInput('ruangan_nama', '', ['class' => 'form-control', 'placeholder' => 'Nama Ruangan']))) . '\'],
            [5, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2']))) . '\']
        ]);
        
        $("#table-ruangan tbody").on("click", "tr", function(){
            try {
                primaryKey = tableRuangan.row(".selected").data().primary ? tableRuangan.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit").attr("action",$("#btn-edit").data("url")+primaryKey);
            } else {
                $("#btn-edit").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }
            
        });






    });
', View::POS_END, 'b-index');
?>