<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
// use app\components\DocoHelpers;
// use app\components\DocoController;

$this->title = Yii::t('fe', 'Warna Dokumen');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
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
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/warna-dokumen/create',
                            ]
                        ],
                        // 'edit' => [
                        //     'attributes' => [
                        //         'data-toggle' => 'modal',
                        //         'data-target' => '#modal_backdrop',
                        //         'data-url' => '/master/warna-dokumen/update?id=',
                        //     ]
                        // ],

                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm'
                            ]
                        ],
                    ], '#table-warnadokumen');?>
                    <div class="pull-right">
                    <?=DocoHelpers::generateToolbar([
                        'reset'
                    ], '#table-warnadokumen');?>
                    </div>
            </div>
            <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 filter-form-warnadokumen"></div>
                    </div>
                        <div class="form-group">
                    </div>
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                            id="table-warnadokumen" style="width: 100%;" >
                        <thead>
                            <tr>
                                <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'No'); ?></th>
                                <th><?= Yii::t('fe', 'Digit Pertama Nomor Primer'); ?></th>
                                <th><?= Yii::t('fe', 'Warna Dokumen'); ?></th>

                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var tableWarnaDokumen;
    $(document).ready(function() {
        tableWarnaDokumen = $('#table-warnadokumen').docoTabel({
                filter: true,
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
                ajax: baseUrl+"master/warna-dokumen/get-data",
                columns: [
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {title: "<?=Yii::t('fe', 'Digit Pertama Nomor Primer')?>",  data: "warnadokrm_kodewarna"},
                    {title: "<?=Yii::t('fe', 'Warna Dokumen')?>",  data: "warnadokrm_namawarna"}
                ]
            });
        $(".dataTables_filter").hide();
        $(".filter-form-warnadokumen").datatableBootstrapFilter(tableWarnaDokumen,
                [
                    [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('warnadokrm_namawarna', '', $warnaDokrm, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
                ]
        );

         var primaryKey;

        $("#table-warnadokumen tbody").on("click", "tr", function(){
            try {
                primaryKey = tableWarnaDokumen.row(".selected").data().primary ? tableWarnaDokumen.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                $(".data-edit").attr("action",$(".data-edit").data("url")+primaryKey);
                $(".data-delete").attr("action",$(".data-delete").data("target")+primaryKey);
            } else {
                $(".data-edit").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }
        });
    });

    // Event Delete
    $(document).on("click", "#data-delete", function(e) {
        e.preventDefault();
        if($(this).attr("action") == "null_id"){
            new PNotify({
                title: "Terjadi Kesalahan",
                text: "Tidak ada data yang dipilih",
                addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                type: 'error'
            });
        }else{
            $(this).docoForm("delete",{
                success : function (data) {
                    tableWarnaDokumen.draw()
                }
            });
        }
        return false;
    });

    // Event Reload
    $(document).on("click", ".data-reset", function() {
        tableWarnaDokumen.draw();
    });
</script>
