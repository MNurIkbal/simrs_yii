<?php
    /**
     * @author Chacha Nurholis
     * A product of PT Citra Raya Nusatama
     * Powered by Sirs
     */

    use yii\web\View;
    use yii\helpers\Html;
    use app\components\DHtml;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use app\components\DocoConstants;

    $this->title = DHtml::getTitleMenu();
    $this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
    $this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['/']];
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= 
                    DocoHelpers::generateToolbar([
                        'search',
                        'reset' => [
                            'attributes' => [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'edit-penerimaan' => [
                            'title'      => \Yii::t('fe', 'Detail'),
                            'icon'       => 'fa fa-eye',
                            'attributes' => [
                                'data-target' => '/penjamin-asuransi/informasi-penerimaan-pembayaran/edit-penerimaan?id=',
                            ]
                        ],
                        'delete' => [
                            'title'      => \Yii::t('fe', 'Batal'),
                            'icon'       => 'fa fa-close',
                            'attributes' => [
                                'data-target'          => '/penjamin-asuransi/informasi-penerimaan-pembayaran/batal-penerimaan?id=',
                                'data-confirm-message' => 'Apakah anda yakin untuk membatalkan data ini?'
                            ]
                        ],
                        'pdf-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Cetak PDF',
                            'icon' => 'fa fa-file-pdf-o',
                            'method' => 'not-exist',
                            'attributes' => [
                               'id' => 'pdf-bgprocess',
                               'data-options' => 'click',
                               'data-options' => 'excel-serconn',
                               'data-target' => '#modal_backdrop',
                               'data-width' => '75%',
                               'data-url' => '/penjamin-asuransi/informasi-penerimaan-pembayaran/show-popup?type=1&id=',
                            ]
                        ],
                        'excel-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Unduh Excel',
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                               'id' => 'excel-bgprocess',
                               'data-options' => 'click',
                               'data-options' => 'excel-serconn',
                               'data-target' => '#modal_backdrop',
                               'data-width' => '75%',
                               'data-url' => '/penjamin-asuransi/informasi-penerimaan-pembayaran/show-popup?type=2&id=',
                            ]
                        ],
                    ])
                ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1"><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Penerimaan") ?></th>
                            <th><?= Yii::t("fe", "No Pembayaran") ?></th>
                            <th><?= Yii::t("fe", "Cara bayar / Penjamin") ?></th>
                            <th><?= Yii::t("fe", "Cara bayar") ?></th>
                            <th><?= Yii::t("fe", "Penjamin") ?></th>
                            <th><?= Yii::t("fe", "Total Pembayaran") ?></th>
                            <th><?= Yii::t("fe", "Status Alokasi") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 
    $this->registerJs('
        var table;
        localStorage.clear();
        localStorage.setItem("penjamin", \''.json_encode($penjamin).'\');

        // Event Ready
        $(document).on("click", "#example tr", function (e) {
            e.preventDefault();
            try {
                status = table.row(".selected").data().status_alokasi ? 1 : 0;
            } catch (e) {
                status = 0;
            }

            if (status == 1) {
                $(".data-delete").prop("disabled",true);
            } else {
                $(".data-delete").prop("disabled",false);
            }
        });

        $(document).ready(function() {
            table = $("#example").docoTabel({
                filter: true,
                columnDefs: [{
                    orderable: false,
                    className: "select-checkbox",
                    targets: 0
                }],
                select: {
                    style: "os",
                    selector: "tr"
                },
                sorting: [2, "desc"], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+"penjamin-asuransi/informasi-penerimaan-pembayaran/get-data",
                columns: [
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        defaultContent: "",
                    },
                    {
                        data: "rowNum",
                        name : "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'", 
                        data: "tgl_terimabayarklaim"
                    },
                    {
                        title: "'.(\Yii::t("fe", "No Pembayaran")).'", 
                        data: "no_terimabayarklaim"
                    },
                    {
                        title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'", 
                        data: "carabayar_nama", 
                        searchable: false,
                        orderable: false,
                        render: (data, type, row, meta) => {
                            let caraBayarNama = row.carabayar_nama
                            let penjaminNama = row.penjamin_nama
                            return `<b>` + caraBayarNama + `</b>` + `<br>` + penjaminNama
                        }
                    },
                    {
                        title: "'.(\Yii::t("fe", "Cara Bayar")).'", 
                        data: "carabayar_nama", 
                        name : "carabayar_id",
                        visible: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Penjamin")).'", 
                        data: "penjamin_nama", 
                        name: "penjamin_id",
                        visible: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Total Pembayaran")).'", 
                        data: "total_terimabayar_label", 
                        name: "total_terimabayar", 
                        searchable: false
                    },
                    {
                        title: "'.(\Yii::t("fe", "Status Alokasi")).'", 
                        data: "final_alokasi", 
                        name: "final_alokasi", 
                    },
                ],
            });

            $(".dataTables_filter").hide();
            
            $(".filter-form").datatableBootstrapFilter(table, 
                [
                    [
                        2, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'. date('d-M-Y') .'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="'. date('d-M-Y') .'"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
                    ],
                    [
                        5, 
                        \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                            Html::dropDownList('carabayar_nama', '', 
                                $cara_bayar, 
                                [
                                    'id' => 'filter_carabayar', 
                                    'class' => 'form-control select2 dep-to-child', 
                                    'prompt' => \Yii::t('fe', '-- Pilih Cara Bayar --'),
                                    'data-url' =>  '/penjamin-asuransi/informasi-pasien-non-bpjs/get-penjamin',
                                    'data-depend_id' => 'filter_penjamin',
                                    'data-depend_prompt' => \Yii::t('fe', '-- Pilih Penjamin --'),
                                    'data-storage' => 'penjamin',
                                    'data-key' => 'penjamin_id',
                                ]
                            )
                        )).'</div>\'
                    ],
                    [
                        6, 
                        \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                            Html::dropDownList('penjamin_nama', '',
                                $penjamin,
                                [
                                    'id' => 'filter_penjamin',
                                    'class' => 'form-control select2',
                                    'prompt' => \Yii::t('fe', '-- Pilih Penjamin --'),
                                    'data-url' =>  '/penjamin-asuransi/informasi-pasien-non-bpjs/get-carabayar',
                                    'data-depend_id' => 'filter_carabayar',
                                ]
                            )
                        )).'</div>\'
                    ],
                    [
                        8, 
                        \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                            Html::dropDownList('penjamin_nama', '',
                                DocoConstants::$statusAlokasi,
                                [
                                    'class' => 'form-control select2',
                                    'prompt' => \Yii::t('fe', '-- Pilih Status Alokasi --'),
                                ]
                            )
                        )).'</div>\'
                    ],
                ], {
                    2:0,
                    3:1,
                    5:2,
                    6:3,
                    8:4
                }, true
            );

            dateRangeHelper(".startDate",".endDate",".targetDate");
        });
    ', View::POS_END, 'js');
?>