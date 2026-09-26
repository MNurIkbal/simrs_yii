 <?php
/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
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
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
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
                <?=
                    DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Unduh Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'gudang/laporan-mutasi-obat-alkes/show-popup-excel?',
                                'data-width' => '75%',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="advanced-filter"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "User Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Kode Obat");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
                            <th><?=\Yii::t("fe", "Jenis Obat");?></th>
                            <th><?=\Yii::t("fe", "Qty Pesan");?></th>
                            <th><?=\Yii::t("fe", "Uom Pesan");?></th>
                            <th><?=\Yii::t("fe", "Qty Terima");?></th>
                            <th><?=\Yii::t("fe", "Uom Terima");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pengiriman");?></th>
                            <th><?=\Yii::t("fe", "Nomor Pengiriman");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Pengirim");?></th>
                            <th><?=\Yii::t("fe", "User Pengirim");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Penerimaan");?></th>
                            <th><?=\Yii::t("fe", "User Penerima");?></th>
                            <th><?=\Yii::t("fe", "Catatan Penerima");?></th>
                            <th style="text-align: right"><?=\Yii::t("fe", "Harga Satuan (Rp)");?></th>
                            <th style="text-align: right"><?=\Yii::t("fe", "Total Harga (Rp)");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global Var
    var isDisabled = "'.$is_disabled.'";
    var table;

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            scrollX: true,
            filter: true,
            sorting: [[1, "asc"], [3, "asc"], [5, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"gudang/laporan-mutasi-obat-alkes/get-list-data",
            columns: [
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'",
                    data: "tgl_pemesanan",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Pemesanan")).'",
                    data: "no_pemesanan",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "User Pemesanan")).'",
                    data: "pegawai_pemesan",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Kode Obat")).'",
                    data: "kode_obat",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Obat")).'",
                    data: "nama_obat",
                },
                {
                    title: "'.(\Yii::t("fe", "Jenis Obat")).'",
                    data: "jenisobatalkes_nama",
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Pesan")).'",
                    data: "qty_pesan",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Uom Pesan")).'",
                    data: "uom_input",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Terima")).'",
                    data: "qty_terima",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Uom Terima")).'",
                    data: "uom_konversi",
                    searchable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pengiriman")).'",
                    data: "tgl_pengiriman",
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Pengiriman")).'",
                    data: "no_pengiriman",
                    searchable: false,
                },                
                {
                    title: "'.(\Yii::t("fe", "Ruangan Pengirim")).'",
                    data: "ruangan_pengirim",
                },
                {
                    title: "'.(\Yii::t("fe", "User Pengirim")).'",
                    data: "pegawai_pengirim",
                    searchable: false,
                },                
                {
                    title: "'.(\Yii::t("fe", "Tanggal Penerimaan")).'",
                    data: "tgl_penerimaan",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Nomor Penerimaan")).'",
                    data: "no_penerimaan",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Ruangan Penerima")).'",
                    data: "ruangan_penerima",
                },
                {
                    title: "'.(\Yii::t("fe", "User Penerima")).'",
                    data: "pegawai_penerima",
                    searchable: false,
                },                                
                {
                    title: "'.(\Yii::t("fe", "Catatan Penerima")).'",
                    data: "catatan_penerima",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Harga Satuan (Rp)")).'",
                    data: "harga_satuan",
                    searchable: false,
                    class : "text-right"
                },                                                
                {
                    title: "'.(\Yii::t("fe", "Total Harga (Rp)")).'",
                    data: "harga_total",
                    searchable: false,
                    class : "text-right"
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                5,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('obatalkes_id', '',
                        [],
                        [
                            'class' => 'form-control select2',
                            'prompt' => '-',
                            'id'=>'filter_obatalkes'
                        ]
                    )
                )).'\'
            ],
            [
                6,
                \''.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('jenisobatalkes_id', '',
                        [],
                        [
                            'class' => 'form-control select2',
                            'prompt' => '-',
                            'id'=>'filter_jenisobatalkes'
                        ]
                    )
                )).'\'
            ],
            [
                11,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" readonly /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            [
                13,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('ruangan_pengirim', $ruangan_aktif, 
                        $daftar_ruangan, 
                        [
                            'class' => 'form-control select2 ruangan_pengirim', 
                            'id' => 'filter_ruangan_pengirim',
                            'prompt' => $ruangan_aktif == '' ? \Yii::t('fe', 'Ruangan Pengirim') : $daftar_ruangan[$ruangan_aktif]
                        ]
                    )
                )).'</div>\'
            ],
            [
                17,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                    Html::dropDownList('ruangan_penerima', '', 
                        $daftar_ruangan, 
                        [
                            'class' => 'form-control select2 ruangan_penerima', 
                            'id' => 'filter_ruangan_penerima',
                            'prompt' => \Yii::t('fe', 'Ruangan Penerima')
                        ]
                    )
                )).'</div>\'
            ],
        ],{
            11:0,
            13:1,
            17:2,
            6:3,
            5:4,
        });

        dateRangeHelper(".startDate", ".endDate", ".targetDate");

        $(document).on("change", ".startDate, .endDate", function(){
            $(".data-filter").click();
        });

        $("#filter_jenisobatalkes").select2InfinityScroll({
            url: "/master/jenis-obat-alkes/get-data-select2",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                    }
                }
            }
        })
 
        $("#filter_jenisobatalkes").on("change.select2", function() {
            $("#filter_obatalkes").val(null).trigger("change"); // reset filter penjamin
        });

        $("#filter_obatalkes").select2InfinityScroll({
            url: "/master/obat-alkes/get-data-select2",
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        jenisobatalkes_id: $("#filter_jenisobatalkes").val()
                    }
                }
            }
        })

        if(isDisabled) {
            $(".ruangan_pengirim").prop("disabled", true);
        }
    });

', View::POS_END, 'b-index');
?>

