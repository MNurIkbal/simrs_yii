<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\JabatanForm;
use Doco\master\controllers\JabatanController;

$this->title = \Yii::t('fe', 'Jabatan');
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
                                'data-target' => '#modal_backdrop', 'action' => '/master/jabatan/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/jabatan/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-jabatan');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-jabatan"></div>
                </div>
                <table id="table-jabatan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama jabatan");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Kelompok jabatan");?></th>
                            <th><?=\Yii::t("fe", "Indexing");?></th>
                            <th><?=\Yii::t("fe", "Urutan");?></th>
                            <th width="1">Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tablejabatan;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/jabatan/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tablejabatan.draw();
            }
        });
        tablejabatan.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tablejabatan.draw();
    });



    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tablejabatan = $("#table-jabatan").docoTabel({
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
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"master/jabatan/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Kelompok jabatan")).'",  data: "kelompokjabatan_m.kelompokjabatan_nama"},
                {title: "'.(\Yii::t("fe", "Indexing")).'",  data: "indexing_m.indexing_nama"},
                {title: "'.(\Yii::t("fe", "Urutan")).'",  data: "jabatan_urutan"},
                {title: "'.(\Yii::t("fe", "Nama jabatan")).'",  data: "jabatan_nama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")).'", data: "jabatan_lainnya"},
                {title: "Status", data: "is_active", class: "text-center"},

            ],
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 2,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form-jabatan").datatableBootstrapFilter(tablejabatan, [[6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Status')]))).'\']]);
    });


', View::POS_END, 'b-index');
?>
