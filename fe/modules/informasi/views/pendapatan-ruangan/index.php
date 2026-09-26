<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
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

    .img-pendapatan {
        height: 35px;
        margin: 4px;
    }
</style>

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
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
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
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent'=>'.filter-form'
                        ]
                    ],
                    // 'pdf' => [
                    //     'attributes' => [
                    //         'data-target' => '/informasi/pendapatan-ruangan/export-pdf?'
                    //     ]
                    // ],
                    'export-excel-serconn' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'data-export-excel-serconn',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'informasi/pendapatan-ruangan/show-popup-excel?',
                            'data-width' => '75%'
                        ]
                    ],
                ],'#table_lap_pendapatan_ruangan');?>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="container">
                                    <img src="/media/img/icon-app/penjamin/legend_penjamin_total.png" class="img-pendapatan">
                                    <label style="font-size:1vw;bottom: 8px;position: relative;"><b>Total Pendapatan Ruangan</b></label>
                                    <span style="font-size:1vw;position: relative;top: 10px;right: 166px;"><b class="total_pendapatan">Rp. 0 </b></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="advanced-filter">
                </div>
                <table id="table_lap_pendapatan_ruangan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th rowspan="1" width="1">No</th>
                            <th rowspan="1"><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "No. Pendaftaran");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Nama Pemeriksaan");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Dokter Pengirim/Perujuk");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Instalasi/Ruangan");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th rowspan="1"><?=\Yii::t("fe", "Total");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$list_penjamin = json_encode($response['penjamin']);
$this->registerJs('
    var listPenjamin = JSON.stringify('.$list_penjamin.');
    localStorage.clear();
    localStorage.setItem("penjamin", listPenjamin);

    // Global Var
    var table;
    let totalPendapatan = 0;
    
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        $(function(){
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });
        })
        // Generate Table
        table = $("#table_lap_pendapatan_ruangan").docoTabel({
            filter: true,
            order: [[ 1, "desc" ]],
            sorting: [[1, "desc"]],
            displayLength: 50,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"informasi/pendapatan-ruangan/get-data",
            columns: [
                {
                    title: "Nomor",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", 
                    data: "tgl_pendaftaran"
                },
                {
                    title: "'.(\Yii::t("fe", "No. Pendaftaran")).'", 
                    data: "no_pendaftaran"
                },
                {
                    title: "'.(\Yii::t("fe", "No Rekam Medik")).'", 
                    data: "no_rekam_medik"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien")).'",
                    data: "nama_pasien"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pemeriksaan")).'",
                    data: "daftartindakan_nama",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'",
                    data: "carabayar_id",
                    searchable: false,
                    name: "carabayar_id",
                    render: (data, rowElement, rowData) => {
                        const caraBayarPenjamin = rowData.carabayar_penjamin;
                        return `<p>${caraBayarPenjamin}</p>`
                    }
                },
                {
                    title: "'.(\Yii::t("fe", "Dokter Pengirim/Perujuk")).'",
                    data: "nama_pegawai"
                },
                {
                    title: "'.(\Yii::t("fe", "Ruangan")).'",
                    data: "ruangan_nama",
                    searchable: false,
                    visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Instalasi/Ruangan")).'", 
                    data: "instalasi_ruangan",
                    visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", 
                    data: "kelaspelayanan_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Total (Rp.)")).'", 
                    data: "total",
                    searchable: false,
                    className:"text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar")).'",
                    data: "carabayar_id",
                    visible: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Penjamin")).'",
                    data: "penjamin_id",
                    visible: false,
                },
            ],
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();
                let res = this.api().ajax.json();
                if(res) {
                    totalPendapatan = res.total_pendapatan
                }
                $(".total_pendapatan").html("Rp. " + totalPendapatan)
            },
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
                [
                    1, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
                ],
                [
                    10, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelaspelayanan', '', $response['kelas_pelayanan'], ['class' => 'form-control select2', 'id' => 'kelaspelayanan', 'prompt' => Yii::t('fe', '--Pilih Kelas Pelayanan--') ]))).'\' 
                ],
                [7, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('dokter', '', $response['pegawai'], ['class' => 'form-control select2', 'id' => 'dokter', 'prompt' => Yii::t('fe', '--Pilih Dokter--') ]))).'\' 
                ],
                [
                    12, 
                    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('carabayar_id', '', 
                            $response['cara_bayar'], 
                            [
                                'id' => 'filter_carabayar', 
                                'class' => 'form-control select2 dep-to-child', 
                                'prompt' => \Yii::t('fe', 'Cara Bayar'),
                                'data-url' =>  '/informasi/pendapatan-ruangan/get-penjamin',
                                'data-depend_id' => 'filter_penjamin',
                                'data-depend_prompt' => \Yii::t('fe', '--Pilih Penjamin--'),
                                'data-storage' => 'penjamin',
                                'data-key' => 'penjamin_id',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    13, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('penjamin_id', '',
                            $response['penjamin'],
                            [
                                'id' => 'filter_penjamin',
                                'class' => 'form-control select2 dep-to-parent',
                                'prompt' => \Yii::t('fe', 'Penjamin'),
                                'data-url' =>  '/informasi/pendapatan-ruangan/get-carabayar',
                                'data-depend_id' => 'filter_carabayar',
                            ]
                        )
                    )).'</div>\'
                ],
                [
                    9, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('instalasi_ruangan', '',
                            [],
                            [
                                'id' => 'filter_instalasi_ruangan',
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', 'Instalasi/Ruangan'),
                                'disabled' => ($instalasi_id == DocoConstants::INSTALASI_KASIR) ? false : true,
                            ]
                        )
                    )).'</div>\'
                ],
             ], {
                1:0,
                2:1,
                3:2,
                4:3,
                5:4,
                6:5,
                7:6,
                8:7,
                10:8,
            }, true);

        dateRangeHelper(".startDate",".endDate",".targetDate");
        var primaryKey;
        //add for handle checkbox click
        $("#table_lap_pendapatan_ruangan tbody").on("click", "tr", function(){

            primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        });

        $("#filter_instalasi_ruangan").docoPaginationSelec2(
            config = {
                placeholder : "-- Cari Instalasi / Ruangan --",  
                _api : "/informasi/pendapatan-ruangan/list-ruangan",
            }
        );
    });
', View::POS_END, 'b-index');
?>
