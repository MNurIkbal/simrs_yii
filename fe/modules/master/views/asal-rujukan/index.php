<?php
// Author : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\AsalRujukanForm;
use Doco\master\controllers\AsalRujukanController;

$this->title = Yii::t('fe', 'Asal Rujukan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
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
                        <li><a data-action="reload"></a></li>
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
                                'action' => '/master/asal-rujukan/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/asal-rujukan/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-asalrujukan');?>


                    </div>
                    <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-asalrujukan"></div>
                </div>
                <table id="table-asalrujukan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'Asal Rujukan')?></th>
                            <th><?=Yii::t('fe', 'Institusi Asal Rujukan')?></th>
                            <th><?=Yii::t('fe', 'Nama Lainnya')?></th>
                            <th>Status</th>

                            <th ></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<div id="modal_asalrujukan" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

</div>
<?php
$this->registerJs('
    // Global Var
    var tableAsalRujukan ;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/asal-rujukan/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tableAsalRujukan .draw();
            }
        });
        tableAsalRujukan .draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableAsalRujukan .draw();
    });



    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableAsalRujukan  = $("#table-asalrujukan").docoTabel({
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
            ajax: baseUrl+"master/asal-rujukan/get-data-asal-rujukan",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },

                {title: "'.(\Yii::t("fe", "Asal Rujukan")).'", data: "asalrujukan_nama"},
                {title: "'.(\Yii::t("fe", "Institusi Asal Rujukan")).'", data: "asalrujukan_institusi"},
                {title: "'.(\Yii::t("fe", "Nama Lainnya")).'",  data: "asalrujukan_namalainnya"},
                {title: "Status", data: "is_active"},

            ],
            scrollCollapse: true,
        });
        $(".dataTables_filter").hide();
        $(".filter-form-asalrujukan ").datatableBootstrapFilter(tableAsalRujukan , [[10, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\']]);
    });



', View::POS_END, 'b-index');
?>
