<?php
// Modify : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\PerujukForm;
use Doco\master\controllers\PerujukController;


$this->title = Yii::t('fe', 'Perujuk');
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
                                'action' => '/master/perujuk/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/perujuk/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-perujuk');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-perujuk"></div>
                </div>
                <table id="table-perujuk" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th >No</th>
                            <th><?=Yii::t('fe', 'Asal Rujukan')?></th>
                            <th><?=Yii::t('fe', 'Nama Perujuk')?></th>
                            <th><?=Yii::t('fe', 'Spesialis')?></th>
                            <th><?=Yii::t('fe', 'Alamat')?></th>
                            <th><?=Yii::t('fe', 'Handphone')?></th>
                            <th>Status</th>
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
<div id="modal_perujuk" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global Var
    var table2 ;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status-perujuk", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/perujuk/change-status-perujuk?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                table2.draw();
            }
        });
        table2.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table2.draw();
    });


    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table2  = $("#table-perujuk").docoTabel({
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
            ajax: baseUrl+"master/perujuk/get-data-perujuk",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },

                {title: "'.(\Yii::t("fe", "Asal Rujukan")).'", data: "asalrujukan_m.asalrujukan_nama"},
                {title: "'.(\Yii::t("fe", "Nama Perujuk")).'", data: "namaperujuk"},
                {title: "'.(\Yii::t("fe", "Spesialis")).'",  data: "spesialis"},
                  {title: "'.(\Yii::t("fe", "Alamat Lengkap")).'",  data: "alamatlengkap"},
                    {title: "'.(\Yii::t("fe", "No Telp")).'",  data: "notelp"},
                {title: "Status", data: "is_active"},

            ],
            scrollCollapse: true,
        });
        $(".dataTables_filter").hide();
        $(".filter-form-perujuk ").datatableBootstrapFilter(table2 , [[6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\']]);
    });

', View::POS_END, 'b-index');
?>
