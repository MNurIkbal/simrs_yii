<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Unit kerja');
$this->title = Yii::t('fe', 'Kelas pelayanan');
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
                                'data-parent'=>'.filter-form-kelaspelayanan'
                            ]
                        ],
                        'reset' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-kelaspelayanan'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'type' => 'link',
                                'action' => '/master/kelas/create-pelayanan',
                                'data-target' => '/master/kelas/create-pelayanan',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/kelas/update-pelayanan?id=',
                                'data-url' => '/master/kelas/update-pelayanan?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],"#tableKelasPelayanan");?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-kelaspelayanan"></div>
                </div>
                <table id="tableKelasPelayanan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Jenis kelas");?></th>
                            <th><?=\Yii::t("fe", "Kelas pelayanan");?></th>
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
        table = $("#tableKelasPelayanan").docoTabel({
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
            ajax: baseUrl+"master/kelas/get-data-kelas-pelayanan",
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
                {
                    title: "<?= (\Yii::t("fe", "Jenis kelas")); ?>",
                    data: "jeniskelas_nama",
                    name : "jeniskelas_m.jeniskelas_nama"
                },
                {
                    title: "<?= (\Yii::t("fe", "Kelas pelayanan")); ?>", 
                    data: "kelaspelayanan_nama"
                },
                {
                    title: "<?= (\Yii::t("fe", "Nama lainnya")); ?>", 
                    data: "kelaspelayanan_namalainnya"
                },
                {
                    title: "<?= (\Yii::t("fe", "Status")); ?>", 
                    data: "is_active"
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-kelaspelayanan").datatableBootstrapFilter(
            table, 
            [
                [
                    2, 
                    '<?=
                    preg_replace(
                        "/[\n\t\r]/i", 
                        '', 
                        Html::dropDownList(
                            'jeniskelas_id',
                            '',
                            $listJenisKelas,
                            [
                                'class' => 'select2',
                                'prompt' => \Yii::t('fe', 'Pilih')
                            ]
                        )
                    );
                    ?>'
                ],
                [
                    5, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>'
                ]
            ]
        );
    });
</script>