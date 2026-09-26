<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-12 15:02:51
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-24 09:23:11
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
// use app\modules\master\models\GolonganpegawaiForm;
// use Doco\master\controllers\GolonganpegawaiController;


$this->title = \Yii::t('fe', $title);
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
                        // 'add' => [
                        //     'attributes' => [
                        //         'data-toggle' => 'modal',
                        //         'data-target' => '#modal_backdrop',
                        //         'data-width' => '75%',
                        //         'action' => '/master/golongan-umur-lab/create',
                        //     ]
                        // ],
                        'edit' => [
                            'attributes' => [                                
                                'data-options'=>'modal',    
                                'data-target'=>'#modal_backdrop',
                                'data-width' => '60%',                            
                                'data-url' => '/master/golongan-umur-lab/update?id=',
                            ]
                        ],
                        // 'delete' => [
                        //     'attributes' => [
                        //         'data-additional' => 'data-rm',
                        //     ]
                        // ],
                        // 'pdf',
                        // 'excel',
                    ],'#example');?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Nama Golongan");?></th>
                            <th><?=\Yii::t("fe", "Nama Lainnya");?></th>
                            <th><?=\Yii::t("fe", "Umur Minimal");?></th>
                            <th><?=\Yii::t("fe", "Umur Maksimal");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    $(".switch").bootstrapSwitch();
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
        var dataStatus = "0";
        var dataId = $(this).attr("data-id");
        if (e.target.checked == true)
            dataStatus = "1";
        var ResData = {};

        $(this).docoForm("click",{
            url: baseUrl+"master/golongan-umur-lab/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            data: ResData,
            method: "GET",
            success: function () {
                table .draw();
            },
            error: function (res) {
                table .draw();
            }
        });
    });
    
    var table;
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
    //     var dataStatus = "0";
    //     var dataId = $(this).attr("data-id");
    //     if (e.target.checked == true)
    //         dataStatus = "1";
        
    //     $(this).docoForm("delete",{
    //         url: baseUrl+"master/golongan-umur-lab/change-status?id="+dataId+"&status="+dataStatus,
    //         confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
    //         confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
    //         success : function (data) {
    //             table.draw();
    //         }
    //     });
    //     table.draw();
    // });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            // sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/golongan-umur-lab/get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Golongan")).'",  data: "gol_umurlab_nama"},
                {title: "'.(\Yii::t("fe", "Nama Lainnya")).'", data: "gol_umurlab_namalainnya"},
                {title: "'.(\Yii::t("fe", "Umur Minimal")).'", data: "umur_minimal", searchable: false},
                {title: "'.(\Yii::t("fe", "Umur Maksimal")).'", data: "umur_maksimal", searchable: false},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "is_active", class: "text-center"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('gol_umurlab_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Golongan')]))).'\'
            ],
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('gol_umurlab_namalainnya', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Lainnya')]))).'\'
            ],
            [
                6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', [1=>Yii::t('fe', 'Aktif'), 0=>Yii::t('fe', 'Tidak aktif')], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '— Pilih Status —')]))).'\'
            ]
        ]);
        
        // $(".selectGolongan").select2({
        //     placeholder: "",
        //     minimumInputLength: 3,
        //     ajax: {
        //         url: "/master/end-point/get-data-golongan-operasi",
        //         dataType: "json",
        //         quietMillis: 250,
        //         processResults: function (data) {
        //             return {
        //                 results: data.result
        //             };
        //         }
        //     },
        //     dropdownCssClass: "bigdrop",
        //     escapeMarkup: function (m) { return m; },
        // });
    });
', View::POS_END, 'b-index');
?>