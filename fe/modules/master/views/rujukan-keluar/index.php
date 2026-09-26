<?php
// Modify : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Rujukan Keluar');
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
                                'action' => '/master/rujukan-keluar/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/rujukan-keluar/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-rujukan-keluar');?>
            </div>


            <div class="panel-body">
                <div class="form-group">
                 <div class="row">
                    <div class="col-md-12 filter-form-rujukan-keluar"></div>
                </div>
                <table id="table-rujukan-keluar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th >No</th>
                            <th><?=Yii::t('fe', 'Asal Rujukan')?></th>
                            <th><?=Yii::t('fe', 'Rumah Sakit Rujukan')?></th>
                            <th><?=Yii::t('fe', 'Alamat RS Rujukan')?></th>
                            <th><?=Yii::t('fe', 'Telp')?></th>
                            <th>Status</th>
                            <th ></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_rujukankeluar" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global Var
    var tableRujukanKeluar ;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status-rujukan-keluar", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/rujukan-keluar/change-status-rujukan-keluar?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tableRujukanKeluar.draw();
            }
        });
        tableRujukanKeluar.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableRujukanKeluar.draw();
    });



    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableRujukanKeluar  = $("#table-rujukan-keluar").docoTabel({
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
            ajax: baseUrl+"master/rujukan-keluar/get-data-rujukan-keluar",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Asal Rujukan")).'", data: "asalrujukan_m.asalrujukan_nama"},
                {title: "'.(\Yii::t("fe", "Rumah Sakit Rujukan")).'", data: "rumahsakit_rujukan"},
                {title: "'.(\Yii::t("fe", "Alamat Lengkap")).'",  data: "alamat_rsrujukan"},
                {title: "'.(\Yii::t("fe", "No Telp")).'",  data: "telp_fax"},
                {title: "Status", data: "is_active"},

            ],
            scrollCollapse: true,
        });
        $(".dataTables_filter").hide();
        $(".filter-form-rujukan-keluar ").datatableBootstrapFilter(tableRujukanKeluar , [[5, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\']]);
    });



', View::POS_END, 'b-index');
?>
