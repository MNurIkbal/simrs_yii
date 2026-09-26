<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Unit kerja');
$this->title = Yii::t('fe', 'Spesialis');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-spesialis'
                            ]
                        ],
                        'reset' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-spesialis'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/spesialis/create-spesialis',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/spesialis/update-spesialis?id=',
                                'data-url' => '/master/spesialis/update-spesialis?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        // 'pdf',
                        // 'excel',
                    ],"#tableSpesialis");?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-spesialis"></div>
                </div>
                <table id="tableSpesialis" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Kode Spesialis");?></th>
                            <th><?=\Yii::t("fe", "Nama Spesialis");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th width="20"><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var table;
    // Event Ready
    $(document).ready(function() {

        // Generate Table
        table = $("#tableSpesialis").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'tr'
            },
            sorting: [[2, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/spesialis/get-data-spesialis",
            columns: [
                {
                    title: '',
                    data: null,
                    render: function () {
                        return null;
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?= (\Yii::t("fe", "Kode Spesialis")); ?>", data: "spesialis_kode"},
                {title: "<?= (\Yii::t("fe", "Nama Spesialis")); ?>", data: "spesialis_nama"},
                {title: "<?= (\Yii::t("fe", "Nama lainnya")); ?>", data: "spesialis_namalainnya", searchable: false},
                {title: "<?= (\Yii::t("fe", "Status")); ?>", data: "is_active", searchable: false, orderable: false},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-spesialis").datatableBootstrapFilter(
            table, 
            [],
            {
                2:0,
                3:1,
            }
        );
    });
</script>