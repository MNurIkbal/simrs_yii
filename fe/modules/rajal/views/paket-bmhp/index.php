<?php

/**
 * @Author: afil
 * @Date:   2018-01-22 14:03:04
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-28 16:56:02
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Paket BMHP');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat Jalan'), 'url' => ['/rajal/dashboard']];
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
                    'reset',
                    'print',
                    'pdf',
                    'excel',
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => $default_url. '/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => $default_url .'/update?id=',
                        ]
                    ],
                    'delete',
                ], '#table-paket');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-paket" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'nama_daftartindakan')?></th>
                            <th><?=Yii::t('fe', 'nama_obatalkes')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/_paketbmhp.js'));
$this->registerJs('
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#table-paket").docoTabel({
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
            rowsGroup: [0, 1],
            filter: true,
            order: [[ 1, "asc" ]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            saveState: false,
            ajax: baseUrl+"rajal/paket-bmhp/get-data",
            columns: [
                {
                    data: "rowNum",
                    orderable: false,
                    searchable: false
                },
                {title: "'.(\Yii::t("fe", "nama_daftartindakan")).'", data: "daftartindakan_m.daftartindakan_nama"},
                {title: "'.(\Yii::t("fe", "nama_obatalkes")).'", data: "obatalkes_m.obatalkes_nama"},
            ],
            fixedColumns: {
                leftColumns: 1,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table);
    });
', View::POS_END, 'b-index');
?>
