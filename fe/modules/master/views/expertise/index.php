<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-25 11:02:32
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-25 15:05:35
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;


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
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'action' => '/master/expertise/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options'=>'modal',    
                                'data-target'=>'#modal_backdrop',
                                // 'data-width' => '75%',
                                'data-url' => '/master/expertise/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                            ]
                        ],
                        'pdf',
                        'excel',
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
                            <th><?=\Yii::t("fe", "Nama expertise");?></th>
                            <th><?=\Yii::t("fe", "Nama pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Dokter");?></th>
                            <th><?=\Yii::t("fe", "Hasil expertise");?></th>
                            <th><?=\Yii::t("fe", "Kesan");?></th>
                            <th><?=\Yii::t("fe", "Kesimpulan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$url = Url::to(['get-pemeriksaan-rad']);
$this->registerJs('
    // Global Var
    var table;

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
            ajax: baseUrl+"master/expertise/get-data",
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
                {title: "'.(\Yii::t("fe", "Nama expertise")).'",  data: "nama_expertise"},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_pegawai", name: "pegawai_id",
                    render: (data) => {
                        return data == "" || data == null ? "-" : data
                     }
                },
                {title: "'.(\Yii::t("fe", "Nama pemeriksaan")).'", data: "pemeriksaanrad_nama", name: "pemeriksaanrad_id"},
                {title: "'.(\Yii::t("fe", "Hasil expertise")).'", data: "hasil_expertise", searchable: false},
                {title: "'.(\Yii::t("fe", "Kesan")).'", data: "kesan", searchable: false},
                {title: "'.(\Yii::t("fe", "Kesimpulan")).'", data: "kesimpulan", searchable: false},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('dokter', '', [], ['class' => 'form-control select2 selectDokter', 'prompt' => '']))).'\'],
            [4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pemeriksaan', '', [], ['class' => 'form-control select2 selectPemeriksaan', 'prompt' => '']))).'\']
        ]);
        $(".selectPemeriksaan").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "'.$url.'",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
        $(".selectDokter").docoPaginationSelec2(
            // dapat disesuaikan dengan kebutuhan data / customize
            config = {
                placeholder : "-- Pilih Dokter --",      // custom placeholder (optional) default null
                _api : "/igd/master-api/list-all-new-dokter",   // get data
            }
        );
    });
', View::POS_END, 'b-index');
?>