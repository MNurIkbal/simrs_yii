<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Kelompok remunerasi');
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
                                'action' => '/master/kelompok-remunerasi/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/kelompok-remunerasi/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-kelompokremunerasi');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-kelompokremunerasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Kode");?></th>
                            <th><?=\Yii::t("fe", "Nama");?></th>
                            <th><?=\Yii::t("fe", "Urutan");?></th>
                            <th><?=\Yii::t("fe", "Deskripsi");?></th>
                            <th><?=\Yii::t("fe", "Singkatan");?></th>
                            <th><?=\Yii::t("fe", "Rate");?></th>
                            <th width="1">Status</th>

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
<?php
$this->registerJs('
    // Global Var
    var tableKelompokRemunerasi;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/kelompok-remunerasi/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tableKelompokRemunerasi.draw();
            }
        });
        tableKelompokRemunerasi.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableKelompokRemunerasi.draw();
    });



    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableKelompokRemunerasi = $("#table-kelompokremunerasi").docoTabel({
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
            ajax: baseUrl+"master/kelompok-remunerasi/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Kode")).'", data: "kelompokremunerasi_kode"},
                {title: "'.(\Yii::t("fe", "Nama")).'", data: "kelompokremunerasi_nama"},
                {title: "'.(\Yii::t("fe", "Urutan")).'",  data: "kelompokremunerasi_urutan"},
                {title: "'.(\Yii::t("fe", "Deskripsi")).'", data: "kelompokremunerasi_desc"},
                {title: "'.(\Yii::t("fe", "Singkatan")).'", data: "kelompokremunerasi_singkatan"},
                {title: "'.(\Yii::t("fe", "Rate")).'", data: "kelompokremunerasi_rate"},
                {title: "Status", data: "is_active", class: "text-center"},
            ],
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 3,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableKelompokRemunerasi, [[7, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Status')]))).'\']]);
    });


', View::POS_END, 'b-index');
?>
