<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 11:36:33
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-17 16:04:02
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', 'Satuan');
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
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/satuan/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => '/master/satuan/update?id=',
                        ]
                    ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
                        ]
                    ],
                    // 'pdf',
                    'excel',
                ], '#tabel-satuan') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tabel-satuan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Kode") ?></th>
                            <th><?= Yii::t("fe", "Nama Satuan") ?></th>
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
$this->registerJs('
    var tableSatuan;
    $(document).ready(function() {
        tableSatuan = $("#tabel-satuan").docoTabel({
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
            ajax: baseUrl+"master/satuan/get-data",
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
                {
                    title: "'.(\Yii::t("fe", "Kode")).'",  
                    data: "satuanlab_kode",
                    // name : "satuanlab_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Satuan")).'", 
                    data: "satuanlab_nama",
                    // name : "satuanlab_id"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableSatuan, 
            [
                [2, "<input class=\"form-control\">" ],
                [3, "<input class=\"form-control\">" ]
            ]);
    });


', View::POS_END, 'b-index');
