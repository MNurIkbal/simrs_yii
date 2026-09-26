<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-04 17:49:33
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-08-08 16:17:11
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\GolonganpegawaiForm;
use Doco\master\controllers\GolonganpegawaiController;;


$this->title = \Yii::t('fe', 'Tindakan Operasi');
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
                                'action' => '/master/operasi/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [                                
                                'data-options'=>'modal',    
                                'data-target'=>'#modal_backdrop',                            
                                'data-url' => '/master/operasi/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                            ]
                        ],
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
                            <th width="1"></th>                                  
                            <th width="80">No</th>
                            <th><?=\Yii::t("fe", "Nama Kegiatan Operasi");?></th>
                            <th><?=\Yii::t("fe", "Golongan Operasi");?></th>
                            <th><?=\Yii::t("fe", "Operasi");?></th>
                            <th><?=\Yii::t("fe", "Kode Operasi");?></th>
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
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });
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
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/operasi/get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                    width: "7%",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Kegiatan Operasi")).'",  data: "kegiatanoperasi_nama"},
                {title: "'.(\Yii::t("fe", "Golongan Operasi")).'", data: "golonganoperasi_nama"},
                {title: "'.(\Yii::t("fe", "Nama Daftar Tindakan")).'", data: "daftartindakan_nama"},
                {title: "'.(\Yii::t("fe", "Kode Operasi")).'", data: "operasi_kode"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kegiatanoperasi_nama', '', [], 
                    ['class' => 'form-control selectKegiatan select2', 'prompt' => \Yii::t('fe', 'Nama Kegiatan Operasi')]))).'\'
            ],
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('golonganoperasi_nama', '', [], 
                    ['class' => 'form-control selectGolongan select2', 'prompt' => \Yii::t('fe', 'Golongan Operasi')]))).'\'
            ],
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('operasi_nama', '', [], 
                    ['class' => 'form-control selectOperasi select2', 'prompt' => \Yii::t('fe', 'Nama Daftar Tindakan')]))).'\'
            ],
        ]);
        $(".selectKegiatan").select2InfinityScroll({
            url: "/master/operasi/filters?type=kegiatan",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
        $(".selectGolongan").select2InfinityScroll({
            url: "/master/operasi/filters?type=golongan",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
        $(".selectOperasi").select2InfinityScroll({
            url: "/master/operasi/filters?type=tindakan",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
    });
', View::POS_END, 'b-index');
?>
