<?php

/**
 * @Author: johndoe (Spiritual Consultant)
 * @Date:   2018-03-20
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'lihat' => [
                            'type' => 'link',
                            'title' => Yii::t('fe', 'Detail'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs',
                                'data-target' => Url::home().('rajal/inf-penerimaan/view?id='),
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm'
                            ]
                        ],
                    ]);?>
            </div>

         	<div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%" data-filter=".form-filter">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "No. Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Instalasi Pengirim");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Pengirim");?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php /* list data */ ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
$(document).on("click", ".data-reload", function() {
    table.draw();
});
$(document).ready(function() {
    $(function(){
        $(".daterange").daterangepicker({
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD MMM YYYY"
            }
        });
    });

    table = $("#example").docoTabel({
        filter: false,
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
        scrollX: true,
        ajax: baseUrl+"rajal/inf-penerimaan/get-data",
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
            {title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'", data: "tglterima"},
            {title: "'.(\Yii::t("fe", "No Penerimaan")).'", data: "noterimamutasi"},
            {title: "'.(\Yii::t("fe", "Instalasi Pengirim")).'", data: "instalasi_pengirim"},
            {title: "'.(\Yii::t("fe", "Ruangan Pengirim")).'", data: "ruangan_pengirim"},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [   2,
            \''.(preg_replace("/[\n\t\r]/i", '',
                  Html::textInput('tglterima', '', ['class' => 'form-control daterange','placeholder'=>\Yii::t('fe', 'Tanggal Penerimaan')])
                  )).'\'],
        [   4,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_pengirim', '', ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'), ['class' => 'form-control select2', 'prompt' => Yii::t('fe', '--Pilih Instalasi Pengirim--') ]))).'\' ],
        [   5,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_pengirim', '', ArrayHelper::map($api['response']['ruangan'], 'ruangan_id', 'ruangan_nama'), ['class' => 'form-control select2', 'prompt' => Yii::t('fe', '--Pilih Ruangan Pengirim--') ]))).'\' ],

    ]);
});
', View::POS_END, 'info-penerimaan-obat'); ?>
