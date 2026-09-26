<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
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
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'id'=>'reset-lap-rajal',
                                // 'data-parent'=>'.filter-form'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'cetak-pdf'
                            ]
                        ],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Unduh Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'pendaftaran/kunjungan-rawat-jalan/show-popup-excel?',
                                'data-width' => '75%',
                            ]
                        ],
                    ],'#laporan');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="laporan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tgl pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Info Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Status Pasien");?></th>
                            <th><?=\Yii::t("fe", "NIK");?></th>
                            <th><?=\Yii::t("fe", "Jenis kelamin");?></th>
                            <th><?=\Yii::t("fe", "Umur");?></th>
                            <th><?=\Yii::t("fe", "Golongan umur");?></th>
                            <th><?=\Yii::t("fe", "Agama");?></th>
                            <th><?=\Yii::t("fe", "Status perkawinan");?></th>
                            <th><?=\Yii::t("fe", "Pekerjaan");?></th>
                            <th><?=\Yii::t("fe", "Kota/kab.");?></th>
                            <th><?=\Yii::t("fe", "Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Jenis kasus penyakit");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar / Penjamin");?></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "Rujukan");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Dokter");?></th>
                            <th><?=\Yii::t("fe", "Kelas pelayanan");?></th>
                            <th><?=\Yii::t("fe", "No SEP");?></th>
                            <th><?=\Yii::t("fe", "Status Pulang");?></th>
                            <th><?=\Yii::t("fe", "Status Periksa");?></th>
                            <th><?=\Yii::t("fe", "Status Periksa");?></th>
                            <th></th>
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
    // Global Var
    var table;

    // Event Ready
    $(document).ready(function() {
        $("#cetak-pdf").on("click", function(){
            if(table.data().count() > 0){
                var params = table.ajax.params();
                params.toggle = $("#toggle_tablex").val();
                window.open(window.location.origin + $(this).attr("data-target") + $.param(params));
            }else{
                docoNotification(\'warning\', \'Terjadi Kesalahan\', \'Data Tidak Tersedia!\');
            }
        })
        $("#cetak-excel").on("click", function(){
            if(table.data().count() > 0){
                var params = table.ajax.params();
                params.toggle = $("#toggle_tablex").val();
                window.open(window.location.origin + $(this).attr("data-target") + $.param(params));
            }else{
                docoNotification(\'warning\', \'Terjadi Kesalahan\', \'Data Tidak Tersedia!\');
            }
        })

        // Generate Table
        table = $("#laporan").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/kunjungan-rawat-jalan/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                }, // 0
                {title: "'.(\Yii::t("fe", "Tgl pendaftaran")).'", data: "tgl_pendaftaran"}, // 1
                {title: "'.(\Yii::t("fe", "Info Kunjungan")).'", data: "info_kunjungan", searchable:false}, // 2
                {title: "'.(\Yii::t("fe", "Status Pasien")).'", data: "status_pasien", searchable:false}, // 3
                {
                    title: "'.(\Yii::t("fe", "NIK")).'",
                    data: "no_induk_kependudukan", 
                    searchable: true,
                }, // 4
                {title: "'.(\Yii::t("fe", "Jenis kelamin")).'", data: "jenis_kelamin",searchable: false}, // 5
                {title: "'.(\Yii::t("fe", "Umur")).'", data: "umur",searchable: false}, // 6
                {title: "'.(\Yii::t("fe", "Golongan umur")).'", data: "golonganumur_nama",searchable: false}, // 7
                {title: "'.(\Yii::t("fe", "Agama")).'", data: "agama",searchable: false}, // 8
                {title: "'.(\Yii::t("fe", "Status perkawinan")).'", data: "statusperkawinan",searchable: false}, // 9
                {title: "'.(\Yii::t("fe", "Pekerjaan")).'", data: "pekerjaan_nama",searchable: false}, // 10
                {title: "'.(\Yii::t("fe", "Kota/kab")).'", data: "alamat_pasien",searchable: false}, // 11
                {title: "'.(\Yii::t("fe", "Kunjungan")).'", data: "kunjungan",searchable: false}, // 12
                {title: "'.(\Yii::t("fe", "Jenis kasus penyakit")).'", data: "jeniskasuspenyakit_nama",searchable: false}, // 13
                {title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'", data: "carabayar_penjamin", searchable: false}, // 14
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_id", visible: false}, // 15
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_id", visible: false}, // 16
                {title: "'.(\Yii::t("fe", "Rujukan")).'", data: "nama_perujuk",searchable: false}, // 17
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", name: "ruangan_id"}, // 18
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_pegawai", name: "pegawai_id"}, // 19
                {title: "'.(\Yii::t("fe", "Kelas pelayanan")).'", data: "kelaspelayanan_nama",searchable: false}, // 20
                {title: "'.(\Yii::t("fe", "No SEP")).'", data: "nosep",searchable: true}, // 21
                {title: "'.(\Yii::t("fe", "Status Pulang")).'", data: "carakeluar_nama"}, // 22
                {title: "'.(\Yii::t("fe", "Status Periksa")).'", data: "status_periksa"}, // 23
                {title: "'.(\Yii::t("fe", "Status Skrining")).'", data: "status_skrining"}, // 24
                {title: "'.(\Yii::t("fe", "Jenis pencarian")).'", data: "toggle", visible:false}, // 25
            ],
            scrollCollapse: true,
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    1,
                    "<div class=\'input-group\' style=\"margin-bottom: 0px !important;\"><input type=\'text\' value=\''.date('d-M-Y').'\' readonly=\'true\' id=\'rangeDemoStart\' class=\'form-control startDate\'/><span class=\'input-group-addon\' style=\'border-left: 0; border-right: 0;\'>-</span><input type=\'text\'  id=\'rangeDemoFinish\' readonly=\'true\' value=\''.date('d-M-Y').'\' class=\'form-control endDate\'/><input type=\'text\' style=\'display:none\' class=\'targetDate\' col-index=2></div>"
                ],
                [
                    15,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'carabayar_id',
                                '',
                                $carabayar,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'carabayar_id',
                                    'prompt' => \Yii::t('fe', '--pilih cara bayar--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    16,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name'=>'penjamin_id',
                                    'options'=>['id'=>'penjamin_id', 'class'=>'form-control select2'],
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'placeholder'=>\Yii::t('fe', '--pilih penjamin--'),
                                        'url'=>Url::to(['/pendaftaran/end-point/list-penjamin'])
                                    ]
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    19,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'pegawai_id',
                                '',
                                $pegawai,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'pegawai_id',
                                    'prompt' => \Yii::t('fe', '--Pilih Dokter--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    18,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'ruangan_id',
                                '',
                                $ruangan,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'ruangan_id',
                                    'prompt' => \Yii::t('fe', '--pilih ruangan--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    22,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'carakeluar_nama',
                                '',
                                $status_pulang,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'carakeluar_nama',
                                    'prompt' => \Yii::t('fe', '--Pilih Status--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    23,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'status_periksa',
                                '',
                                $status_periksa,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'status_periksa',
                                    'prompt' => \Yii::t('fe', '--Pilih Status--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    24,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'status_skrining',
                                '',
                                $status_skrining,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'status_skrining',
                                    'prompt' => \Yii::t('fe', 'Semua')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    25,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'toggle',
                                '',
                                [
                                    '4' => 'NIK',
                                    '5' => 'Jenis Kelamin',
                                    '6' => 'Umur',
                                    '7' => 'Golongan Umur',
                                    '8' => 'Agama',
                                    '9' => 'Status Perkawinan',
                                    '10' => 'Pekerjaan',
                                    '11' => 'Kota/Kab',
                                    '12' => 'Status Kunjungan',
                                    '13' => 'Jenis Kasus Penyakit',
                                    '14' => 'Cara Bayar / Penjamin',
                                    '17' => 'Rujukan',
                                    '18' => 'Ruangan',
                                    '19' => 'Dokter',
                                    '20' => 'Kelas Pelayanan',
                                    '21' => 'No SEP',
                                    '22' => 'Status Pulang / Kondisi',
                                    '23' => 'Status Pemeriksaan',
                                    '24' => 'Status Skring',
                                ],
                                [
                                    'class' => 'form-control',
                                    'id' => 'toggle_tablex',
                                    'multiple' => 'multiple',
                                ]
                            )
                        )
                    ).'\'
                ],
            ]
        );
        dateRangeHelper(\'.startDate\',\'.endDate\',\'.targetDate\');
        $("#toggle_tablex").multiselect({
            buttonWidth: "100%",
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return \'Pilih Kolom Yang Ingin Ditampilkan\';
                }
                else if (options.length > 3) {
                    return options.length + \' Kolom Dipilih\';
                }else {
                     var labels = [];
                     options.each(function() {
                         if ($(this).attr(\'label\') !== undefined) {
                             labels.push($(this).attr(\'label\'));
                         }
                         else {
                             labels.push($(this).html());
                         }
                     });
                     return labels.join(\', \') + \'\';
                 }
            },
            includeResetOption: true,
            includeResetDivider: true
        });
        $(".multiselect-container li input:checkbox").css("margin-left", "5px");
        $("#toggle_tablex").on("change", function(){ 
            var col = $(this).val();
            if(col == ""){
                showcolumn();
            }else{
                hidecolumn();
                $.each(col, function(k,v){
                    table.column(v).visible(true);
                })
            }
            $(".data-filter").trigger("click");
        })
    });
    $("#reset-lap-rajal").on("click", function(){
        $("#toggle_tablex").val("").trigger("change");
        $("#toggle_tablex").multiselect("refresh");
        $(\'ul.multiselect-container > li\').removeClass(\'active\')
        $(\'.multiselect-selected-text\').text(\'Pilih Kolom Yang Ingin Ditampilkan\');
    })
     var hidecolumn = function(){
        table.column(4).visible(false);
        table.column(5).visible(false);
        table.column(6).visible(false);
        table.column(7).visible(false);
        table.column(8).visible(false);
        table.column(9).visible(false);
        table.column(10).visible(false);
        table.column(11).visible(false);
        table.column(12).visible(false);
        table.column(13).visible(false);
        table.column(14).visible(false);
        table.column(17).visible(false);
        table.column(18).visible(false);
        table.column(19).visible(false);
        table.column(20).visible(false);
        table.column(21).visible(false);
        table.column(22).visible(false);
        table.column(23).visible(false);
        table.column(24).visible(false);
    }
    var showcolumn = function(){
        table.column(4).visible(true);
        table.column(5).visible(true);
        table.column(6).visible(true);
        table.column(7).visible(true);
        table.column(8).visible(true);
        table.column(9).visible(true);
        table.column(10).visible(true);
        table.column(11).visible(true);
        table.column(12).visible(true);
        table.column(13).visible(true);
        table.column(14).visible(true);
        table.column(17).visible(true);
        table.column(18).visible(true);
        table.column(19).visible(true);
        table.column(20).visible(true);
        table.column(21).visible(true);
        table.column(22).visible(true);
        table.column(23).visible(true);
        table.column(24).visible(true);
    }
', View::POS_END, 'b-index');
?>
