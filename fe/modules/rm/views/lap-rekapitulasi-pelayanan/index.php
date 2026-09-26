<?php
    use yii\web\View;
    use yii\helpers\Html;
    use app\components\DHtml;
    use yii\widgets\Breadcrumbs;
    use yii\helpers\ArrayHelper;
    use app\components\DocoHelpers;

    $this->title = DHtml::getTitleMenu();
    $this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['/']];
    $this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['/']];
    $this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .card {
      box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
      transition: 0.3s;
      width: 100%;
      border: 1px solid #34bfa3;
    }

    .card:hover {
      box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .container {
      padding: 2px 16px;
    }

    .img-rekap {
        margin-left: -10px !important;
        height: 35px;
        margin: 4px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'detail' => [
                        'title' => \Yii::t('fe', 'Detail'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-url' => '/rm/lap-rekapitulasi-pelayanan/detail?id=',
                            'data-conditions' => 'instalasi_id,ruangan_id,status_bayar,status_periksa,tgl_pendaftaran',
                            'id' => 'btn-detail'
                        ]
                    ],
                    'excel'
                ], '#example'); ?>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
                        <div class="col-md-2">
                            <div class="card">
                              <div class="container" style="display: flex;">
                                <img src="/media/img/icon-rm/pasien_lama.svg" class="img-rekap">
                                <div style="margin-left: 5px; margin-top: 2px;">
                                    <p style="font-size:1vw; margin-bottom: -3px"><b>Pasien Lama</b></p>
                                    <p style="font-size:1vw; margin-bottom: -3px""><b id="pasien_lama">0 </b></p>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card">
                              <div class="container" style="display: flex;">
                                <img src="/media/img/icon-rm/pasien_baru.svg" class="img-rekap">
                                <div style="margin-left: 5px; margin-top: 2px;">
                                    <p style="font-size:1vw; margin-bottom: -3px"><b>Pasien Baru</b></p>
                                    <p style="font-size:1vw; margin-bottom: -3px""><b id="pasien_baru">0 </b></p>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card">
                              <div class="container" style="display: flex;">
                                <img src="/media/img/icon-rm/jumlah_pasien.svg" class="img-rekap">
                                <div style="margin-left: 5px; margin-top: 2px;">
                                    <p style="font-size:1vw; margin-bottom: -3px"><b>Jumlah Pasien</b></p>
                                    <p style="font-size:1vw; margin-bottom: -3px""><b id="jumlah_pasien">0 </b></p>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="advanced-filter"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Instalasi"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter"); ?></th>
                            <th><?= \Yii::t("fe", "Pasien Lama"); ?></th>
                            <th><?= \Yii::t("fe", "Pasien Baru"); ?></th>
                            <th><?= \Yii::t("fe", "Jumlah Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Instalasi ID"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan ID"); ?></th>
                            <th><?= \Yii::t("fe", "Pegawai ID"); ?></th>
                            <th><?= \Yii::t("fe", "Status Pembayaran"); ?></th>
                            <th><?= \Yii::t("fe", "Status Pemeriksaan"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var data;

    $(document).on("click", ".data-reset", function() {
        table.draw();
    });

    $(document).ready(function() {
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[4, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/lap-rekapitulasi-pelayanan/get-data",
            columnDefs: [{
                className: "select-checkbox",
                targets: 0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            columns: [
                {title: "", data: null, defaultContent: "", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "No")).'",data: "rowNum", name : "rowNum", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_dokter", searchable: false},
                {title: "'.(\Yii::t("fe", "Pasien Baru")).'", data: "jumlah_pasien_baru", searchable: false},
                {title: "'.(\Yii::t("fe", "Pasien Lama")).'", data: "jumlah_pasien_lama", searchable: false},
                {title: "'.(\Yii::t("fe", "Jumlah Pasien")).'", data: "jumlah_pasien", className: "text-center", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", data: "instalasi_id", name: "tgl_pendaftaran", visible: false},
                {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_id", visible: false},
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_id", visible: false},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "pegawai_id", visible: false},
                {title: "'.(\Yii::t("fe", "Status Pembayaran")).'", data: "instalasi_id", name: "status_bayar", visible: false},
                {title: "'.(\Yii::t("fe", "Status Pemeriksaan")).'", data: "instalasi_id", name: "status_periksa", visible: false}
            ],
            "drawCallback": function (setting) {
                var response = setting.json;
                var count = response.summary;
                $("#pasien_lama").html(`<b>${Number(count.pasien_lama)}</b>`)
                $("#pasien_baru").html(`<b>${Number(count.pasien_baru)}</b>`)
                $("#jumlah_pasien").html(`<b>${Number(count.jumlah_pasien)}</b>`)
            },
            "preDrawCallback": function (setting) {
                $("#pasien_lama").html(`Loading . . .`)
                $("#pasien_baru").html(`Loading . . .`)
                $("#jumlah_pasien").html(`Loading . . .`)
            },
        });

        table.on( \'xhr\', function () {
            data = table.ajax.params();
            // alert( \'Search term was: \'+data.search.value );
        });

        $(".dataTables_filter").hide();
        
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    8,
                    \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    9,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'instalasi_id',
                        '',
                        ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '2'
                        ]
                    ))).'\'
                ],
                [
                    10,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'ruangan_id',
                        '',
                        ArrayHelper::map($api['response']['ruangan'], 'ruangan_id', 'ruangan_nama'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '2'
                        ]
                    ))).'\'
                ],
                [
                    11,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'pegawai_id',
                        null,
                        ArrayHelper::map($api['response']['dokter'], 'pegawai_id', 'nama_pegawai'),
                        [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '4'
                        ]
                    ))).'\'
                ],
                [
                    12,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'status_bayar',
                        null,
                        ArrayHelper::map($api['response']['statusBayar'], 'lookup_id', 'lookup_name'),
                        [
                            'class' => 'form-control select2',
                            'id' => 'status_bayar',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '4'
                        ]
                    ))).'\'
                ],
                [
                    13,
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                        'status_periksa',
                        null,
                        ArrayHelper::map($api['response']['statusPeriksa'], 'lookup_id', 'lookup_name'),
                        [
                            'class' => 'form-control select2',
                            'id' => 'status_periksa',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                            'col-index' => '4'
                        ]
                    ))).'\'
                ]
            ], {
                8:0,
                9:1,
                10:2,
                11:3,
                12:4,
                13:5
            }, true
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate", true);
    });
', View::POS_END, 'b-index');
?>