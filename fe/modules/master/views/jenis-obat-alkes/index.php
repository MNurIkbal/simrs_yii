<?php

/**
 * @Author: Budi
 * @Date:   2018-04-24 11:36:33
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = $title;
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
                            'action' => '/master/jenis-obat-alkes/create-jenis-obat',
                        ]
                    ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
                        ]
                    ],
                ], '#example') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 advanced-filter"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Kode") ?></th>
                            <th><?= Yii::t("fe", "Nama Jenis Obat Alkes") ?></th>
                            <th><?= Yii::t("fe", "Nama Lain Jenis Obat Alkes") ?></th>
                            <th><?= Yii::t("fe", "Grup") ?></th>
                            <th><?= Yii::t("fe", "Service Group") ?></th>
                            <th><?= Yii::t("fe", "Service Category") ?></th>
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
    var table;
    $(document).ready(function() {
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
            ajax: baseUrl+"master/jenis-obat-alkes/get-data",
            columns: [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Kode")).'",
                    data: "jenisobatalkes_kode",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Jenis Obat Alkes")).'",
                    data: "jenisobatalkes_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Lain Jenis Obat Alkes")).'",
                    data: "jenisobatalkes_namalain",
                },
                {
                    title: "'.(\Yii::t("fe", "Grup")).'",
                    data: "group_jenisobat_nama",
                    name: "group_jenisobat"
                },
                {
                    title: "'.(\Yii::t("fe", "Service Group")).'",
                    data: "servicegroup_nama",
                    name: "servicegroup_id"
                },
                {
                    title: "'.(\Yii::t("fe", "Service Category")).'",
                    data: "servicecategory_nama",
                    name: "servicecategory_id"
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,[
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('jenisobatalkes_kode', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Kode Jenis Obat Alkes')]))).'\'
            ],
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('jenisobatalkes_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Jenis Obat Alkes')]))).'\'
            ],
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('jenisobatalkes_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Lain Jenis Obat Alkes')]))).'\'
            ],
            [
                5,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('group_jenisobat_nama', '',
                        $group_jenisobat,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '— Pilih Group —'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                6,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('servicegroup_nama', '',
                        $service_group,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '— Pilih Group —'),
                        ]
                    )
                )).'</div>\'
            ],
            [
                7,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('servicecategory_nama', '',
                        $service_category,
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '— Pilih Group —'),
                        ]
                    )
                )).'</div>\'
            ],
        ],{2:0, 3:1, 4:2, 5:3, 6:4, 7:5});
    });

', View::POS_END, 'b-index');
