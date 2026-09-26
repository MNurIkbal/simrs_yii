<?php
// Author : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\KlasifikasipasienForm;
use Doco\master\controllers\KlasifikasipasienController;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div  class="panel-heading">
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
                                'action' => '/master/klasifikasi-pasien/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/klasifikasi-pasien/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm'
                            ]
                        ],
                        // 'pdf',
                        'excel',
                    ],'#table-klasifikasipasien');?>
            </div>


            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>
                <table width="100%" class="table table-striped table-condensed table-hover" id="table-klasifikasipasien">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=Yii::t('fe', 'klasifikasipasien_nama'); ?></th>
                            <th><?=Yii::t('fe', 'klasifikasipasien_kode'); ?></th>
                           <th width='20'><?=\Yii::t('fe', 'Status');?></th>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global Var
    var tableklasifikasipasien;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/klasifikasi-pasien/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tableklasifikasipasien.draw();
            }
        });
        tableklasifikasipasien.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableklasifikasipasien.draw();
    });


    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableklasifikasipasien = $("#table-klasifikasipasien").docoTabel({
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
            // scrollX: true,
            ajax: baseUrl+"master/klasifikasi-pasien/get-data",
            columns: [
                {
                    title: "",
                    data: null,
                    render: function () {
                        return null;
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Klasifikasi Pasien")).'",  data: "klasifikasipasien_nama"},
                {title: "'.(\Yii::t("fe", "Kode Klasifikasi Pasien")).'", data: "klasifikasipasien_kode"},
                {title: "Status", data: "is_active", class: "text-center"},

            ],
            // scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 2,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableklasifikasipasien, [
            [
                4,
                 \''.(preg_replace("/[\n\t\r]/i", "", preg_replace('/[\']/i', "\"",
                    Html::dropDownList("is_active", "",
                        $dataDropdown,
                        [
                            "class" => "form-control select2",
                            "prompt" => \Yii::t("fe", "--Pilih--"),
                            "col-index" => 1,
                        ]
                    )
                ))).'\'
            ]]);
    });


', View::POS_END);
?>
