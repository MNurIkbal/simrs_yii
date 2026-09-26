<?php
// Author : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->title = \Yii::t('fe', 'Laporan Kunjungan Rawat Inap');
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                                'id'=>'reset-lap-ranap',
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
                                'data-url' => Url::home() . 'pendaftaran/kunjungan-rawat-inap/show-popup?tipe=excel&',
                                'data-width' => '75%',
                            ]
                        ],
                    ],'#laporan');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="laporan" class="table table-striped table-condensed table-hover">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Info Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Status Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Umur");?></th>
                            <th><?=\Yii::t("fe", "Golongan Umur");?></th>
                            <th><?=\Yii::t("fe", "Agama");?></th>
                            <th><?=\Yii::t("fe", "Status Perkawinan");?></th>
                            <th><?=\Yii::t("fe", "Pekerjaan");?></th>
                            <th><?=\Yii::t("fe", "Alamat");?></th>
                            <th><?=\Yii::t("fe", "Kota/Kab");?></th>
                            <th><?=\Yii::t("fe", "Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar/Penjamin");?></th>
                            <th></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "Rujukan");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Kamar Bed");?></th>
                            <th></th>
                            <th></th>
                            <th><?=\Yii::t("fe", "Dokter");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan/Kelas Tagihan");?></th>
                            <th><?=\Yii::t("fe", "No SEP");?></th>
                            <th><?=\Yii::t("fe", "Status Diperiksa");?></th>
                            <th><?=\Yii::t("fe", "Status Pulang/Kondisi");?></th>
                            <th><?=\Yii::t("fe", "Diagnosa");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Keluar");?></th>  
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="20"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
            sorting: [[1, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: {
                url: baseUrl+"pendaftaran/kunjungan-rawat-inap/get-data",
                data : {}
            },
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                }, // 0
                {title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", data: "tgl_pendaftaran"}, // 1
                {title: "'.(\Yii::t("fe", "Info Kunjungan")).'", data: "info_kunjungan", searchable:false}, // 2
                {title: "'.(\Yii::t("fe", "Status Pasien")).'", data: "status_pasien", searchable:false}, // 3
                {title: "'.(\Yii::t("fe", "Jenis kelamin")).'", data: "jenis_kelamin",searchable: false}, // 4
                {title: "'.(\Yii::t("fe", "Umur")).'", data: "umur",searchable: false}, // 5
                {title: "'.(\Yii::t("fe", "Golongan Umur")).'", data: "golonganumur_nama",searchable: false}, // 6
                {title: "'.(\Yii::t("fe", "Agama")).'", data: "agama",searchable: false}, // 7
                {title: "'.(\Yii::t("fe", "Status perkawinan")).'", data: "statusperkawinan",searchable: false}, // 8
                {title: "'.(\Yii::t("fe", "Pekerjaan")).'", data: "pekerjaan_nama",searchable: false}, // 9
                {title: "'.(\Yii::t("fe", "Alamat")).'", data: "alamat_pasien",searchable: false}, // 10
                {title: "'.(\Yii::t("fe", "Kota/kab")).'", data: "kabupaten_nama",searchable: false}, // 11
                {title: "'.(\Yii::t("fe", "Kunjungan")).'", data: "kunjungan",searchable: false}, // 12
                {title: "'.(\Yii::t("fe", "Jenis Kasus Penyakit")).'", data: "jeniskasuspenyakit_nama",searchable: false}, // 13
                {title: "'.(\Yii::t("fe", "Cara Bayar / Penjamin")).'", data: "carabayar_penjamin", searchable: false}, // 14
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_id", visible: false}, // 15
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_id", visible: false}, // 16
                {title: "'.(\Yii::t("fe", "Rujukan")).'", data: "nama_perujuk",searchable: false}, // 17
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", name: "ruangan_id"}, // 18
                {title: "'.(\Yii::t("fe", "Kamar - Bed")).'", data: "kamar_bed", searchable: false}, // 19
                {title: "'.(\Yii::t("fe", "Kamar")).'", data: "kamarruangan_id", visible: false}, // 20
                {title: "'.(\Yii::t("fe", "No. Tempat Tidur")).'", data: "kamartempattidur_id", visible: false}, // 21
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_pegawai", name: "pegawai_id"}, // 22
                {title: "'.(\Yii::t("fe", "Kelas pelayanan / Kelas Tagihan")).'", data: "kelaspelayanan_nama",searchable: false}, // 23
                {title: "'.(\Yii::t("fe", "No SEP")).'", data: "nosep"}, // 34
                {title: "'.(\Yii::t("fe", "Status Diperiksa")).'", data: "status_ranap_nama",searchable: false}, // 25
                {title: "'.(\Yii::t("fe", "Status Pulang / Kondisi")).'", data: "carakeluar_nama",searchable: false}, // 26
                {title: "'.(\Yii::t("fe", "Diagnosa")).'", data: "diagnosa",searchable: false}, // 27
                {title: "'.(\Yii::t("fe", "Tanggal Keluar")).'", data: "tgl_keluar",searchable: false}, // 28
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
                                    'prompt' => \Yii::t('fe', '--Pilih Carabayar--')
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
                   16,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name'=>'penjamin_id',
                                    'options'=>['id'=>'penjamin_id', 'class' => 'select2'],
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'placeholder'=>\Yii::t('fe', '--Pilih Penjamin--'),
                                        'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                    ]
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
                                    'prompt' => \Yii::t('fe', '--Pilih Ruangan--')
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                   20,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name'=>'kamar_id',
                                    'options'=>['id'=>'kamar_id', 'class' => 'select2'],
                                    'pluginOptions'=>[
                                        'depends'=>['ruangan_id'],
                                        'placeholder'=>\Yii::t('fe', '--Pilih Kamar--'),
                                        'url'=>Url::to(['/pendaftaran/kunjungan-rawat-inap/get-kamar'])
                                    ]
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                   21,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name'=>'tempattidur_id',
                                    'options'=>['id'=>'tempattidur_id', 'class' => 'select2'],
                                    'pluginOptions'=>[
                                        'depends'=>['kamar_id'],
                                        'placeholder'=>\Yii::t('fe', '--Pilih Tempat Tidur--'),
                                        'url'=>Url::to(['/pendaftaran/kunjungan-rawat-inap/get-tempat-tidur'])
                                    ]
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
        })

        table.on("preDraw", function () {
            let paramsFilterName = {};

            $(".advancedFilter select").each(function() {
                if ($(this).val() != "" && $(this).val() != undefined && $(this).val != null){
                    paramsFilterName[$(this).attr("name")] = $("option:selected", $(this)).html();
                }
            });
            table.context[0].ajax.data.filters_name = paramsFilterName;
        })
    });
    $("#reset-lap-rajal").on("click", function(){
        $("#toggle_tablex").val("").trigger("change");
        $("#toggle_tablex").multiselect("refresh");
        $(\'ul.multiselect-container > li\').removeClass(\'active\')
        $(\'.multiselect-selected-text\').text(\'Pilih Kolom Yang Ingin Ditampilkan\');
    })
', View::POS_END, 'b-index');

?>
