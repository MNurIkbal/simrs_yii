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
use kartik\color\ColorInput;

$this->title = \Yii::t('fe', 'Status Tempat Tidur');
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
                                'action' => '/master/warna-tempat-tidur/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'btn-edit',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/warna-tempat-tidur/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm'
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-warnatempattidur');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-warnatempattidur" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Keterangan Tempat Tidur");?></th>
                            <th><?=\Yii::t("fe", "Warna tempat tidur");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
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
    var tableWarnatempattidur;

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
                tableWarnatempattidur.draw();
            }
        });
        tableWarnatempattidur.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableWarnatempattidur.draw();
    });



    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableWarnatempattidur = $("#table-warnatempattidur").docoTabel({
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
            scrollX: true,
            ajax: baseUrl+"master/warna-tempat-tidur/get-data",
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
                {title: "'.(\Yii::t("fe", "Keterangan Tempat Tidur")).'",  data: "kettempattidur_nama"},
                // {title: "'.(\Yii::t("fe", "Jenis Kamar")).'",  data: "jenis_kamar"},
                {title: "'.(\Yii::t("fe", "Warna Tempat Tidur")).'", data: "kettempattidur_warna",searchable: false},
                // {title: "'.(\Yii::t("fe", "Status Kamar")).'",  data: "is_kosong"},
                {title: "'.(\Yii::t("fe", "Status")).'",  data: "is_active"},
            ],
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 3,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableWarnatempattidur, [
            [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('kettempattidur_nama', '', ['class' => 'form-control','placeholder'=>'Keterangan Tempat Tidur']))).'\'],
            // [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('kettempattidur_warna', '', ['class' => 'form-control','placeholder'=>'Jenis Kamar']))).'\'],
            // [5, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_kosong', '', $kosong, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Status Kamar')]))).'\'],
            [4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Status')]))).'\']
        ]);

        $("#table-warnatempattidur tbody").on("click", "tr", function(){
            try {
                primaryKey = tableWarnatempattidur.row(".selected").data().primary ? tableWarnatempattidur.row(".selected").data().primary : null;
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
