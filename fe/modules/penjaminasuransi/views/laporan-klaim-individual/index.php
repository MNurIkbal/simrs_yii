<?php

/**
 * @Author: Sigit
 * @Date:   2019-10-16 17:08:22
 */

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('fe', 'Laporan Klaim Individual');
$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                <?= Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                ], '#tb-laporan');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <table id="tb-laporan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                            <th><?=\Yii::t("fe", "No SEP");?></th>
                            <th><?=\Yii::t("fe", "Info Pasien");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Pulang");?></th>
                            <th><?=\Yii::t("fe", "Jenis Tarif");?></th>
                            <th><?=\Yii::t("fe", "Kelas Rawat");?></th>
                            <th><?=\Yii::t("fe", "Kode");?></th>
                            <th><?=\Yii::t("fe", "Tarif Total");?></th>
                            <th><?=\Yii::t("fe", "Waktu Grouping");?></th>
                            <th><?=\Yii::t("fe", "Biaya Riil");?></th>
                            <th><?=\Yii::t("fe", "Rawat");?></th>
                            <th><?=\Yii::t("fe", "Petugas");?></th>
                            <th><?=\Yii::t("fe", "Jenis Rawat");?></th>
                            <th><?=\Yii::t("fe", "Periode");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
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
        table = $("#tb-laporan").docoTabel({
            filter: true,
            sorting: [[1, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"penjamin-asuransi/laporan-klaim-individual/get-data",
            columns: [
                {data: "rowNum", name : "rowNum", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Masuk")).'", data: "tgl_masuk", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Keluar")).'", data: "tgl_keluar", searchable: false},
                {title: "'.(\Yii::t("fe", "No SEP")).'", data: "no_sep", searchable: false},
                {title: "'.(\Yii::t("fe", "Info Pasien")).'", data: "nama_pasien", searchable: false},
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", name: "penjamin_id"},
                {title: "'.(\Yii::t("fe", "Cara Pulang")).'", data: "cara_pulang"},
                {title: "'.(\Yii::t("fe", "Jenis Tarif")).'", data: "jenis_tarif"},
                {title: "'.(\Yii::t("fe", "Kelas Rawat")).'", data: "jenis_kelasrawat"},
                {title: "'.(\Yii::t("fe", "Kode")).'", data: "diagnosa_primer", searchable: false},
                {title: "'.(\Yii::t("fe", "Tarif Total")).'", data: "total_tarifrs", searchable: false},
                {title: "'.(\Yii::t("fe", "Waktu Grouping")).'", data: "tgl_group"},
                {title: "'.(\Yii::t("fe", "Biaya Riil")).'", data: "total", searchable: false},
                {title: "'.(\Yii::t("fe", "Rawat")).'", data: "tipe", searchable: false},
                {title: "'.(\Yii::t("fe", "Petugas")).'", data: "petugas"},
                {title: "'.(\Yii::t("fe", "Jenis Rawat")).'", data: "filter", visible: false},
                {title: "'.(\Yii::t("fe", "Periode")).'", data: "periode", visible: false}
            ],
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    5, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('penjamin_nama', '',
                            $filterPenjamin,
                            [
                                'id' => 'filter_penjamin',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    6, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('cara_pulang', '',
                            $filterCaraPulang,
                            [
                                'id' => 'filter_cara_pulang',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    7, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('jenis_tarif', '',
                            $filterJenisTarif,
                            [
                                'id' => 'filter_jenis_tarif',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    8, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('kelas_rawat', '',
                            $filterKelasRawat,
                            [
                                'id' => 'filter_kelas_rawat',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    11, 
                    \'<div class="input-group"><input type="text" id="tgl_grouping_start" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="tgl_grouping_end" readonly class="form-control endDate"/><input type="text" id="tgl_grouping_target" style="display:none" class="targetDate" col-index=2></div>\'
                ],
                [
                    14, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('petugas', '',
                            $filterPetugas,
                            [
                                'id' => 'filter_petugas',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    15, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('periode', '',
                            $filterPeriode,
                            [
                                'id' => 'filter_periode',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    16, 
                    \'<div class="input-group"><input type="text" id="tgl_periode_start" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="tgl_periode_end" readonly class="form-control endDate"/><input type="text" id="tgl_periode_target" style="display:none" class="targetDate" col-index=2></div>\'
                ],
            ], {
                15:1,
                16:2,
                5:3,
                8:4,
                6:5,
                6:6,
            }, true
        );
        
        dateRangeHelper("#tgl_grouping_start", "#tgl_grouping_end", "#tgl_grouping_target");
        dateRangeHelper("#tgl_periode_start", "#tgl_periode_end", "#tgl_periode_target");
    });
', View::POS_END, 'index');
?>