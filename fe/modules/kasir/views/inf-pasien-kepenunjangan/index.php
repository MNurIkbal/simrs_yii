<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Informasi Pasien Penunjang');
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                    <h3 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien Penunjang'); ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        // 'rincian' => [
                        //     'type' => 'button',
                        //     'title' => \Yii::t('fe', 'Lihat'),
                        //     'icon' => 'fa fa-eye',
                        //     'method' => 'not exist',
                        //     'attributes' => [
                        //         'class' => 'data-payment',
                        //         'data-target' => Url::home().(Yii::$app->controller->module->id."/".Yii::$app->controller->id)."/detail?id=",
                        //         'data-conditions' => 'ruangan'
                        //     ]
                        // ],
                        'payment' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Pembayaran'),
                            'icon' => 'fa fa-shopping-cart',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-payment',
                                'data-target' => Url::home().(Yii::$app->controller->module->id."/".Yii::$app->controller->id)."/view?id=",
                            ]
                        ]
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>

                <div class="col-md-6">
                    <!-- <div class='legend-index'>
                        <div class='legend-header'>Keterangan</div>
                        <div class="legend-wrapper">
                            <div class="legend-information">
                                <div class="legend-information__color" style="background-color: #7efff5"></div>
                                <div class="legend-information__text">Stop Akomodasi</div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="col-md-6">
                    <div class='legend-index'>
                        <div class='legend-header'>Keterangan Cara Bayar</div>
                        <div class="legend-wrapper">
                            <?php foreach($getCaraBayar as $key=>$value): ?>
                                <div class="legend-information">
                                    <div class="legend-information__color" style="background-color: <?= $value['carabayar_kode_warna']; ?>"></div>
                                    <div class="legend-information__text"><?= $value['carabayar_nama']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>


                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="70">No</th>
                            <th><?=\Yii::t("fe", "Tanggal masuk penunjang");?></th>
                            <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Instalasi akhir");?></th>
                            <th><?=\Yii::t("fe", "Ruangan akhir");?></th>
                            <th><?=\Yii::t("fe", "No rekam medik");?></th>
                            <th><?=\Yii::t("fe", "Nama pasien");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jumlah tagihan");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
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
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

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
        // save into localStorage

        // Generate Table
        table = $("#example").docoTabel({
            info: false,
            columnDefs: [ {
                sortable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pasien-kepenunjangan/get-data",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal masuk penunjang")).'", data: "tglmasukpenunjang"},
                {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "Instalasi akhir")).'",  data: "instalasi_nama"},
                {title: "'.(\Yii::t("fe", "Ruangan akhir")).'", data: "ruangan_nama"},
                {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien"},
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'", 
                    data: "carabayar_nama",
                    render: (col, type, data) => {
                        return data.carabayar_nama+" / "+data.penjamin_nama
                    },
                    searchable: false,
                },
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", className: "hidden"},
                {title: "'.(\Yii::t("fe", "Jumlah tagihan (Rp.)")).'", data: "jumlah_tagihan", searchable: false, class: "text-right"},
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama", className: "hidden"},
            ],
            rowCallback: function(row, data, index) {
                if (data.carabayar_kode_warna != null) {
                    $($(row).find("td")[8]).css("background-color", data.carabayar_kode_warna);
                    $($(row).find("td")[8]).css("color", invertColor(data.carabayar_kode_warna, true));
                }
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    2,
                    \'<div class="input-group"><input type="text" value="'.date('d-M-Y').'" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly="true" class="form-control endDate" value="'.date('d-M-Y').'"/><input type="text" style="display:none" class="targetDate"></div>\'
                ],
                [
                    4,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi_nama', '',
                            ArrayHelper::map($resMaster['instalasi'], 'instalasi_nama', 'instalasi_nama'),
                            [
                                'id' => 'filter_instalasi',
                                'class' => 'form-control select2 dep-to-child',
                                'prompt' => \Yii::t('fe', '--Instalasi akhir--'),
                                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-ruangan',
                                'data-depend_id' => 'filter_ruangan',
                                'data-depend_prompt' => \Yii::t('fe', '--Ruangan akhir--'),
                                'data-storage' => 'ruangan',
                                'data-key' => 'ruangan_nama',
                            ]
                        )
                    )).'\'
                ],
                [
                    5,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('ruangan_nama', '',
                            ArrayHelper::map($resMaster['ruangan'], 'ruangan_nama', 'ruangan_nama'),
                            [
                                'id' => 'filter_ruangan',
                                'class' => 'form-control select2 dep-to-parent',
                                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-instalasi',
                                'data-depend_id' => 'filter_instalasi',
                                'prompt' => \Yii::t('fe', '--Ruangan akhir--')
                            ]
                        )
                    )).'\'
                ], 
                [
                    11,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('carabayar_nama', '',
                            ArrayHelper::map($resMaster['carabayar'], 'carabayar_nama', 'carabayar_nama'),
                            [
                                'id' => 'filter_carabayar',
                                'class' => 'form-control select2 dep-to-child',
                                'prompt' => \Yii::t('fe', '--Cara bayar--'),
                                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-penjamin',
                                'data-depend_id' => 'filter_penjamin',
                                'data-depend_prompt' => \Yii::t('fe', '--Penjamin--'),
                                'data-storage' => 'penjamin',
                                'data-key' => 'penjamin_nama',
                            ]
                        )
                    )).'\'
                ],
                [
                    9,
                    \''.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('penjamin_nama', '',
                            ArrayHelper::map($resMaster['penjamin'], 'penjamin_nama', 'penjamin_nama'),
                            [
                                'id' => 'filter_penjamin',
                                'class' => 'form-control select2 dep-to-parent',
                                'prompt' => \Yii::t('fe', '--Penjamin--'),
                                'data-url' =>  Url::home().(Yii::$app->controller->module->id).'/end-point/get-carabayar',
                                'data-depend_id' => 'filter_carabayar',
                            ]
                        )
                    )).'\'
                ],
            ], {
                2:0,
                6:1,
                7:2,
                3:3,
                11:7
            }, true
        );

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(".daterange-basic").daterangepicker({
            startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });

        $(document).on("click", "#example tbody tr", function(){
            var pendaftaran_id = null;
            try {
                pendaftaran_id = table.row(".selected").data().pendaftaran_id;
            }
            catch(e) {
                pendaftaran_id = null;
            }
        });
    });


', View::POS_END, 'b-index');
?>
