<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Mappingan Klasifikasi Kamar');
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
                                'data-parent'=>'.filter-form-klasifikasi-kamar'
                            ]
                        ],
                        'reset' => [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-klasifikasi-kamar'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/kamar/create-klasifikasi-kamar',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/kamar/update-klasifikasi-kamar?id=',
                                'data-url' => '/master/kamar/update-klasifikasi-kamar?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'url' => '/master/kamar/delete-klasifikasi-kamar?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'url' => '/master/kamar/export-pdf-klasifikasi-kamar?',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'url' => '/master/kamar/export-excel-klasifikasi-kamar?',
                            ]
                        ],
                    ],"#tableKlasifikasiKamar");?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-klasifikasi-kamar"></div>
                </div>
                <table id="tableKlasifikasiKamar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Nama Kamar");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Klasifikasi");?></th>
                            <th><?=\Yii::t("fe", "Applicare");?></th>
                            <th><?=\Yii::t("fe", "RS Online");?></th>
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
        table = $("#tableKlasifikasiKamar").docoTabel({
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
            sorting: [[5, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/kamar/get-data-klasifikasi-kamar",
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
                {title: "<?= (\Yii::t("fe", "Nama Kamar")); ?>", data: "kamarruangan_nokamar"},
                {title: "<?= (\Yii::t("fe", "Ruangan")); ?>", data: "ruangan_nama"},
                {title: "<?= (\Yii::t("fe", "Kelas Pelayanan")); ?>", data: "kelaspelayanan_nama"},
                {title: "<?= (\Yii::t("fe", "Klasifikasi")); ?>", data: "klasifikasikamar_nama"},
                {title: "<?= (\Yii::t("fe", "Applicare")); ?>", data: "namakelas_aplicare"},
                {title: "<?= (\Yii::t("fe", "RS Online")); ?>", data: "namatt_rsonline"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-klasifikasi-kamar").datatableBootstrapFilter(
            table, 
            [
                [
                3,
                '<div class="form-group"> <?=(preg_replace("/[\n\t\r]/i", '', preg_replace("/[\']/i", '\'',Html::dropDownList('ruangan_nama', '', $data_ruangan, 
                    [
                        'id' => 'ruangan_id',
                        'class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '--Pilih--'),
                        'col-index' => 3
                    ]
                ))));?>'
            ],
            [
                4,
                '<?= (preg_replace("/[\n\t\r]/i", '', preg_replace("/[\']/i", '\'',
                    Html::dropDownList('kelaspelayanan_nama', '', $data_pelayanan,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                'id' => 'kelaspelayanan_nama',
                                'col-index' => 3
                            ]
                        )
                    )
                )); ?>'
            ],
            [
                5,
                '<?= (preg_replace("/[\n\t\r]/i", '', preg_replace("/[\']/i", '\'',
                    Html::dropDownList('klasifikasi_id', '', $data_klasifikasi,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                'id' => 'klasifikasi_id',
                                'col-index' => 3
                            ]
                        )
                    )
                )); ?>'
            ],
            ],
            {
                3:0,
                2:1,
                4:2,
                5:3
            }
        );
    });
</script>