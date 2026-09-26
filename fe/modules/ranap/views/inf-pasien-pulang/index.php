<?php

/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-08-15 17:29:55
 * @Description:
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['/ranap/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$this->registerCss('
    .DTFC_LeftBodyWrapper{
        width: 0px;
    }
    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
        }
    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }
    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        width: 150px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 20px;
        width: 150px;
        border: solid 0.2px;
    }
    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }
    .my-legend a {
        color: #777;
    }
');
?>


<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= $title; ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'pdf',
                    'excel',
                    'batal-pulang' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Batal pulang'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'id' => 'btn-batal-pulang',
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => '/ranap/inf-pasien-pulang/batal-pulang?pendaftaran_id=',
                        ],
                    ],
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Lihat Pemeriksaan'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'id' => 'btn-lihat-periksa',
                            'data-target'=> '/ranap/inf-pasien-pulang/periksa?id=',
                            // 'disabled'=>'true',
                        ]
                    ],
                    'rincian' => [
                        'title' => 'Rincian Tagihan',
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id'=>'cetak-rincian-tagihan',
                            'data-options' => 'link',
                            'class'=>'spa '.$hide_button_cetak,
                            'target'=>'_blank'
                        ]
                    ],
                    'detail-rincian' => [
                        'type' => 'button',
                        'title' => 'Cetak Detail Rincian',
                        'icon' => 'fa fa-file-pdf-o',
                        'method' => '#',
                        'attributes' => [
                           'data-options' => 'link',
                           'id' => 'cetak-detail-rincian-tagihan-designer',
                           'class' => $hide_button_cetak
                        ]
                    ],
                    'pulang'=>[
                      'title' => \Yii::t('fe', 'Pulang'),
                      'icon' => 'fa fa-home',
                      'attributes' => [
                          'data-target'=> '/ranap/inf-pasien-ranap/pasien-pulang?id=',
                          'disabled' => true,
                          'id' => 'btn-pulang'
                      ]
                    ],
                    'surat-kematian' => [
                        'title' => Yii::t('fe', 'Surat Kematian'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id'=>'cetak-surat-kematian',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'data-target' => '/ranap/inf-pasien-pulang/export-pdf-surat-kematian?pendaftaran_id=',
                            'disabled'=>'true',
                            'target'=>'_blank',
                            'data-requiredchecked' => true,
                        ]
                    ],
                    'konfirmasi'=>[
                        'title' => \Yii::t('fe', 'Konfirmasi'),
                        'icon' => 'fa fa-check',
                        'attributes' => [
                            'data-target'=> '/ranap/inf-pasien-ranap/konfirmasi?id=',
                            'disabled' => true,
                            'id' => 'btn-konfirmasi',
                            'data-options' => 'click',
                            'data-requiredchecked' => true,
                        ]
                    ],
                ], '#informasi-pasien-pulang');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form advanced-filter"></div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li style="font-weight:bold;"><span style='background:#AED6F1;'></span>Tagihan Belum di Konfirmasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable"
                    id="informasi-pasien-pulang"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Tanggal masuk')?></th>
                            <th><?=Yii::t('fe', 'Tanggal pasien pulang')?></th>
                            <th><?=Yii::t('fe', 'No rekam medik')?></th>
                            <th><?=Yii::t('fe', 'No pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No Telepon')?></th>
                            <th><?=Yii::t('fe', 'Nama pasien')?></th>
                            <th><?=Yii::t('fe', 'Kelas pelayanan')?></th>
                            <th><?=Yii::t('fe', 'Ruangan')?></th>
                            <th><?=Yii::t('fe', 'Jenis kasus penyakit')?></th>
                            <th><?=Yii::t('fe', 'Dokter')?></th>
                            <th><?=Yii::t('fe', 'Cara Bayar - Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Cara Bayar')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Cara  - kondisi pulang')?></th>
                            <th><?=Yii::t('fe', 'Cara Keluar')?></th>
                            <th><?=Yii::t('fe', 'Kondisi Pulang')?></th>
                            <th><?=Yii::t('fe', 'Lama Dirawat')?></th>
                            <th><?=Yii::t('fe', 'Dipulangkan Oleh')?></th>
                            <th><?=Yii::t('fe', 'Pasien')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="clearfix">
    <!-- Modal -->
    <?php
       /* Modal::begin([
            'header' => '<h4>'.\Yii::t("fe", "Konfirmasi Dokter").'</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
        ]);
        echo "
            <div id='modalContent'>
                <div class='row'>
                    <div class='col-md-12'>
                        ".Html::dropDownList(
                            'nama_pegawai',
                            '',
                            ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'),
                            [
                                'class' => 'form-control select2',
                                'id' => 'dd-pegawai',
                                'prompt' => \Yii::t('fe', '--pilih dokter--'),
                            ]
                        )."
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        ".Html::hiddenInput('temp-pendaftaran', null, ['id' => 'temp-pendaftaran', 'read-only' => 'read-only'])."
                        ".Html::hiddenInput('temp-pegawai', null, ['id' => 'temp-pegawai', 'read-only' => 'read-only'])."
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        ".Html::button(Yii::t('fe', 'Pilih Dokter'), ['class' => 'btn btn-primary btn-update-pegawai'])."
                    </div>
                </div>
            </div>
        ";
        Modal::end();*/
    ?>
</div>

<?php
$this->registerJs('
    const cetakRincian = "/ranap/inf-pasien-ranap/export-rincian-tagihan-pdf?pendaftaran_id=";
    const cetakDetailRincian = "/ranap/inf-pasien-ranap/export-detail-rincian?pendaftaran_id=";
    const cetakSuratKematian = "/ranap/inf-pasien-pulang/export-pdf-surat-kematian?pendaftaran_id=";
    const instalasiId = "'.$instalasiId.'";
    const instalasiRanap = "'.$instalasiRanap.'";
   
    // Event Ready
    // Generate Table
    var status_belum_lunas = "'.$status_belum_lunas.'";
    var status_pulang = "'.$status_pulang.'";
    var table = $("#informasi-pasien-pulang").docoTabel({
         filter: true,
        //add for handle checkbox
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets: 0,
            checkboxes: {
                selectRow: true
            }
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        sorting: [[3, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"ranap/inf-pasien-pulang/get-data",
        columns: [
            {title: "", data: null, defaultContent: "", searchable: false, orderable: false}, //0
            {title: "No", data: "rowNum", searchable: false, orderable: false}, //1
            {title: "'.(\Yii::t("fe", "Tanggal")).'", searchable: false, data: "tgl_admisi"}, //2
            {title: "'.(\Yii::t("fe", "Tanggal Pasien Pulang")).'", data: "tglpasienpulang", visible: false}, //3
            {title: "'.(\Yii::t("fe", "Pasien")).'", data: "no_rekam_medik_old", searchable: false}, //4
            {title: "'.(\Yii::t("fe", "No Telepon")).'", data: "no_telepon_pasien",searchable: false}, //5
            {title: "'.(\Yii::t("fe", "Nomor Pendaftaran")).'", data: "no_pendaftaran", visible: false}, //6
            {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien", visible: false}, //7
            {title: "'.(\Yii::t("fe", "Kelas")).'", data: "kelaspelayanan_nama"}, //8
            {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama"}, //9
            {title: "'.(\Yii::t("fe", "Jenis Kasus Penyakit")).'", data: "jeniskasuspenyakit_nama", visible : false}, //10
            {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_penjamin", searchable: false}, //11
            {title: "'.(\Yii::t("fe", "Dokter Penanggung Jawab")).'", data: "dokter"}, //12
            {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "carabayar_nama",searchable:true}, //13
            {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama",visible:true}, //14
            {title: "'.(\Yii::t("fe", "Status")).'", data: "cara_kondisipulang",searchable: false},//15
            {title: "'.(\Yii::t("fe", "Cara Keluar")).'", data: "carakeluar_nama",visible:false}, //16
            {title: "'.(\Yii::t("fe", "Kondisi Pulang")).'", data: "kondisikeluar_nama",visible:false}, //17
            {title: "'.(\Yii::t("fe", "Lama Dirawat")).'", data: "lama_rawat", searchable: false, visible : false}, //18
            {title: "'.(\Yii::t("fe", "Dipulangkan Oleh")).'", data: "petugas_pemulang_nama", searchable: false, visible : true}, //19
            {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", visible:false}, //20
        ],
        fixedColumns:   {
            rightColumns: 0,
        },
        createdRow: (rowElement, data) => {
            var is_stopakomodasi = data.is_stopakomodasi;
            var status_bayar = data.status_bayar;
            var konfirmasi = data.konfirmasi;
            var pasienpulang_id = data.pasienpulang_id;

            if(is_stopakomodasi && !konfirmasi && status_bayar == status_belum_lunas && pasienpulang_id == null || pasienpulang_id == "") {
                $(rowElement).css("background-color", "#AED6F1")
                $(rowElement).css("font-weight", "bold")
            }
        },
        language: {
            lengthMenu: "'.(\Yii::t("fe", "Menampilkan")).' _MENU_ '.(\Yii::t("fe", "data perhalaman")).'",
        }
    });
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            3,
            \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
        ],

        [20, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_rekam_medik', '', ['class' => 'form-control','placeholder'=>'No Rekam Medik']))).'\'],
        [6, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_pendaftaran', '', ['class' => 'form-control','placeholder'=>'No Pendaftaran']))).'\'],

        [7, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_pasien', '', ['class' => 'form-control','placeholder'=>'Nama Pasien']))).'\'],
        [8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelaspelayanan_nama', '', $dataKelasPelayanan, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Kelas Pelayanan--')]))).'\'],
        [9, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', $dataRuangan, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Ruangan--')]))).'\'],
        [10, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jeniskasuspenyakit_nama', '', $dataJenisKasusPenyakit, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Jenis Kasus Penyakit--')]))).'\'],

        [12, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_pegawai', '', $dataDokter, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Dokter Penanggung Jawab--')]))).'\'],
        [13, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_nama', '', $dataCaraBayar, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Cara Bayar--')]))).'\'],
        [14, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('penjamin_nama', '', $dataPenjamin, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Penjamin--')]))).'\'],


        [16, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carakeluar_nama', '', $dataCaraKeluar, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Cara Keluar--')]))).'\'],
        [17, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kondisikeluar_nama', '', $dataKondisiKeluar, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Kondisi Keluar--')]))).'\'],
    ],{
        3:0,
        20:1,
        6:2,
        7:3,
        8:4,
        9:5,
        10:6,
        12:7,
        16:8,
        17:9,
        14:10,
        11:11,
        13:11,
    });
    $(".dataTables_filter").hide();
    dateRangeHelper(".startDate",".endDate",".targetDate");
    dateRangeHelper(".startDate2",".endDate2",".targetDate2");
    $(".select2", $("form.form-filter")).change(function (event) {
        event.preventDefault();
        table.reload();
    });

    $("form.form-filter").on("submit", function (e) {
        e.preventDefault();
        table.reload();
    });

    $(document).on("click","#btnCariKamar", () => {
        
    loadModal()
    })

    $(document).on("click",".close", function(event) {
        
        if(isModalTempatTidur){
            isModalTempatTidur = false;
            
            $("#modalTempatTidur").addClass("hide");
            $("#modalPasienPulang").removeClass("hide");
            $(".modal-title").text("Pembatalan Pulang Pasien Rawat Inap");
        }else{

           delete window.tableKamar;
            $("#modal_backdrop").modal("hide"); // Menutup modal
            // $("#modal_backdrop" ).first().css( "display", "none" ); 
            $(".modal-backdrop").remove()
            // location.reload()
            table.draw();
        }
    });


    $(document).on("click","#btn-simpan", function(event) {
        event.preventDefault();
        var tableData = table.row(".selected").data();
        var kamarruangan_id = $("#pasienbatalpulangform-kamarruangan_id").val()
        var ruangan_id = $("#pasienbatalpulangform-ruangan_id").val()
        
        
        var kamartempattidur_id = $("#pasienbatalpulangform-kamartempattidur_id").val()
        if (typeof tableData !== "undefined") {
            
            if("primary" in tableData ){
                if(tableData.status_isi) {
                    confirmationDialog(tableData.ruangan_nama.split("<br>")[1]==undefined?tableData.ruangan_nama:tableData.ruangan_nama.split("<br>")[1] + " Dengan Tempat Tidur "+ tableData.kamarruangan_nokamar + " Ini Sudah Terisi <br/>Ingin Menempati Kamar Tempat Tidur Lain? ", (isConfirm) => {
                        if (isConfirm) {
                            loadModal();
                        }else{
                            $("#btn-simpan").attr("disabled", false);
                        }
                    });
                }else{
                    batalPulang();
                }
            }else{
                $("#btn-batal-pulang").attr("disabled", true);
            }
        }else{
            $("#btn-batal-pulang").attr("disabled", false);
        }
    });

    $("#btn-konfirmasi").on("click", function(e){
        e.preventDefault();
        var tableData = table.row(".selected").data();
        var pendaftaran_id = tableData.pendaftaran_id;
        $().docoForm("click", {
            url: baseUrl+"ranap/inf-pasien-pulang/konfirmasi",
            data: {
                pendaftaran_id: pendaftaran_id
            },
            skipConfirm: true,
            success: function(res){
                table.draw();
                $("#btn-konfirmasi").attr("disabled", true);
            }
        });
    });

    // Tooltip
    $("table").tooltip({selector: "[data-tooltip=tooltip]"});

    $(document).ready(function() {
        $("input[name=tgl_admisi]").val(" - ");

        $("#informasi-pasien-pulang").on("draw.dt", function () {
            resetDefaultToolbar();
        });

        $("#informasi-pasien-pulang tbody").on("click", "tr", function(){
            try {
                pendaftaran_id = table.row(".selected").data().pendaftaran_id;
                instalasi_id = table.row(".selected").data().instalasi_id;
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
                caraKeluar = table.row(".selected").data().carakeluar_nama ? table.row(".selected").data().carakeluar_id : null;
            } catch (e) {
                primaryKey = false;
                pendaftaran_id = null;
                instalasi_id = null;
            }


            if (primaryKey) {
                $("#btn-batal-pulang").attr("action",$("#btn-batal-pulang").data("url")+primaryKey);
                $("#cetak-rincian-tagihan").attr("data-target","/kasir/inf-pasien-belum-bayar/cetak-rincian?id="+pendaftaran_id+"&instalasi_id="+instalasi_id);
                $("#cetak-rincian-tagihan").attr("disabled", false);

                $("#cetak-detail-rincian").attr("data-target",cetakDetailRincian+primaryKey);
                $("#cetak-detail-rincian").attr("disabled", false);

                if (caraKeluar === '.DocoConstants::CARA_KELUAR_MENINGGAL.'){
                    $("#cetak-surat-kematian").attr("data-target",cetakSuratKematian+primaryKey);
                    $("#cetak-surat-kematian").attr("disabled", false);
                } else {
                    $("#cetak-surat-kematian").attr("disabled", true);
                }
            } else {
                $("#btn-batal-pulang").removeAttr("action");
                $(".data-delete").removeAttr("action");

            }

        });

    });

    $("#informasi-pasien-pulang tbody").on("click", "tr", function(e){
        e.preventDefault();
        var tableData = table.row(".selected").data();

        if (typeof tableData !== "undefined") {
            var status_bayar = tableData.status_bayar;
            var is_stopakomodasi = tableData.is_stopakomodasi;
            var pasienpulang_id = tableData.pasienpulang_id;
            var konfirmasi = tableData.konfirmasi;

            if("primary" in tableData ){
                $("#cetak-rincian-tagihan").attr("disabled", false);
                $("#cetak-detail-rincian").attr("disabled", false);
                if(status_bayar == status_belum_lunas) {
                    $("#btn-pulang").attr("disabled", true);
                    if(is_stopakomodasi) {
                        if(pasienpulang_id == null) {
                            $("#btn-batal-pulang").attr("disabled", true);
                            $("#btn-pulang").attr("disabled", false);
                            $("#btn-konfirmasi").attr("disabled", false);
                        }
                        else {
                            $("#btn-batal-pulang").attr("disabled", false);
                            $("#btn-pulang").attr("disabled", true);
                            $("#btn-konfirmasi").attr("disabled", true);
                        }
                    }
                }
                else {
                    $("#btn-pulang").attr("disabled", true);
                    $("#btn-batal-pulang").attr("disabled", true);
                    $("#btn-konfirmasi").attr("disabled", true);
                }
                if(tableData.status_bayar == status_belum_lunas){
                    $("#cetak-detail-rincian-tagihan-designer").attr("disabled", false);
                }else{
                    $("#cetak-detail-rincian-tagihan-designer").attr("disabled", true);
                }
                // if(tableData.is_stopakomodasi && tableData.status_bayar == status_belum_lunas || tableData.pasienpulang_id != null) {
                //     $("#btn-pulang").attr("disabled", true);
                // }
                // else {
                //     $("#btn-pulang").attr("disabled", false);
                // }
            }else{
                 $("#btn-batal-pulang").attr("disabled", true);
                 $("#btn-pulang").attr("disabled", true);
                 resetDefaultToolbar();
            }
        }else{
            $("#cetak-rincian-tagihan").attr("disabled", true);
            $("#cetak-detail-rincian").attr("disabled", true);
            $("#btn-batal-pulang").attr("disabled", false);
            $("#btn-pulang").attr("disabled", true);
            $("#cetak-detail-rincian").attr("disabled", true);
            $("#btn-konfirmasi").attr("disabled", true);
            resetDefaultToolbar();
        }
        if(instalasiId != instalasiRanap) {
            $("#btn-pulang").attr("disabled", true);
            $("#btn-lihat-periksa").attr("disabled", true);
        }
        if(konfirmasi === true) {
            $("#btn-konfirmasi").attr("disabled", true);
        }
    });

    function resetDefaultToolbar(){
        // semua button yang memiliki data required checked akan didisabled
        $(".panel-toolbar button[data-requiredchecked]").attr("disabled", true);
    }
    
    $("#cetak-detail-rincian-tagihan-designer").click(function(e){
        e.preventDefault();
        var tableData = table.row(".selected").data();
        var primary = null;
        if(typeof tableData !== "undefined") {
            var pendaftaran_id = tableData.pendaftaran_id;
            var instalasi_id = tableData.instalasi_id;
            var url = "/kasir/inf-pasien-belum-bayar/show-popup?id=" + pendaftaran_id+"&instalasi_id="+instalasi_id;
            window.open(url);
        }
    });


    //batal pulang
    var typeName;
    var key;
    var isModalTempatTidur=false;

    let tableKamar=null;
    let isStillExistDataKamar = {
        semua: true,
    }
    let pageDataKamar = {
        semua: 1,
    }
    let columnGenerated = [];
    var tablesData = table.row(".selected").data();
    const columns = [
            {title: "No", data: "rowNum", searchable: false, orderable: false},
            {title: "Jenis Kasus Penyakit", data: "jeniskasuspenyakit_nama", searchable: false, orderable: false },
            {title: "Ruangan", data: "ruangan_nama", searchable: false, orderable: false},
            {title: "Kamar", data: "kamarruangan_nokamar", searchable: false, orderable: false },
            {title: "Kelas", data: "kelaspelayanan_nama", searchable: false, orderable: false },
            {title: "No Tempat Tidur", data: "datakamar", searchable: false, orderable: false },
    ];

     
    function pilihKamar(identifier) {
        const kamarruangan_id = $(identifier).data("kamarruangan_id");
        const kamartempattidur_id = $(identifier).data("kamartempattidur_id");
        const kamarruangan_nokamar = $(identifier).data("kamarruangan_nokamar");
        const ruangan_nama = $(identifier).data("ruangan_nama");
        const no_tempattidur = $(identifier).data("no_tempattidur");
        const ruangan_id = $(identifier).data("ruangan_id");
        confirmationDialog("Tempat Tidur Akan Dipindahkan Ke Kamar Ruangan : <b>"+ ruangan_nama +" "+ kamarruangan_nokamar+ "</b> Dan Nomor Tempat Tidur : <b>"+no_tempattidur +"</b>", (isConfirm) => {
            if (isConfirm) {
                $("#pasienbatalpulangform-kamarruangan_id").val(kamarruangan_id)
                $("#pasienbatalpulangform-kamartempattidur_id").val(kamartempattidur_id)
                $("#pasienbatalpulangform-ruangan_id").val(ruangan_id)
                table.row(".selected").data().kamarruangan_id=kamarruangan_id;
                table.row(".selected").data().kamartempattidur_id=kamartempattidur_id;
                table.row(".selected").data().kamarruangan_nokamar=kamarruangan_nokamar;
                table.row(".selected").data().ruangan_nama=ruangan_nama;
                table.row(".selected").data().no_tempattidur=no_tempattidur;
                table.row(".selected").data().status_isi=$(identifier).data("status_isi");
                table.row(".selected").data().ruangan_id=ruangan_id;
                isModalTempatTidur=false;
                $("#modalTempatTidur").addClass("hide");
                $("#modalPasienPulang").removeClass("hide");
                $(".modal-title").text("Pembatalan Pulang Pasien Rawat Inap");
                $(".kamar-ditempati").text(ruangan_nama + " - " + kamarruangan_nokamar + " - " + no_tempattidur)
            }else{
                $("#btn-simpan").attr("disabled", false);
            }
        });
    }

    function createHeaderDatatableKamar(typeName, key) {
        var jenisKamar = typeName; 
        if ($(`#filterHeaderKamar${key}`).length === 0) {
            $("#filterHeader").append(`
                <div id="filterHeaderKamar${key}">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenis_penyakit">Jenis Penyakit</label>
                            <select name="jenisPenyakit" id="jenisPenyakitFilter${key}" class="form-control select2" placeholder="Semuaxxcc"></select>
                            <option></option>
                        </div>
                    </div>
                    <div class="col-md-3 filterKamarSection">
                        <div class="form-group">
                            <label for="kelas_pelayanan">Kelas</label>
                            <select name="kelasPelayanan" id="kelasFilter${key}" class="form-control select2"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ruangan">Ruangan</label>
                            <select name="ruangan" id="ruanganFilter${key}" class="form-control select2"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kamar">Kamar</label>
                            <select name="kamar" id="kamarFilter${key}" class="form-control select2"></select>
                            
                        </div>  
                    </div>
                    <div class="col-sm-12 button-search-section">
                        <button type="button" id="searchBtn${key}" class="btn btn-info btn-sm btn-labeled pull-right"><b class="fa fa-lg fa-search"></b> Cari</button>
                    </div>
                </div>`
            );
            $("select[name=hidden_data_jeniskasuspenyakit] option").each(function(i, item){
                $(`#jenisPenyakitFilter${key}`).append($("<option>", { 
                    value: item.value,
                    text : item.text 
                }));
            });
            $("select[name=hidden_data_kelaspelayanan] option").each(function(i, item){
                $(`#kelasFilter${key}`).append($("<option>", { 
                    value: item.value,
                    text : item.text 
                }));
            });
            $("select[name=hidden_data_ruangan] option").each(function(i, item){
                $(`#ruanganFilter${key}`).append($("<option>", { 
                    value: item.value,
                    text : item.text 
                }));
            });
            $("select[name=hidden_data_kamar] option").each(function(i, item){
                $(`#kamarFilter${key}`).append($("<option>", { 
                    value: item.value,
                    text : item.text 
                }));
            });

            // $(`#kelasFilter${key}`).val(null).trigger("change");
            // $(`#kamarFilter${key}`).prop("disabled", true)
            // $(`#ruanganFilter${key}`).prop("disabled", true)
            // $(`#ruanganFilter${key}`).bind("change", ({ delegateTarget }) => {
            //     if ($(delegateTarget).val() === "" || $(delegateTarget).val() === "-" || $(delegateTarget).val() === "Semua" || $(delegateTarget).val() == null) {
            //         $(`#kamarFilter${key}`).prop("disabled", true)
            //         $(`#kamarFilter${key}`).val("Semua").trigger("change")
            //         return false
            //     }
            //     if ($(`#kamarFilter${key}`).hasClass("select2-hidden-accessible")) {
            //         $(`#kamarFilter${key}`).select2("destroy")
            //         $(`#kamarFilter${key}`).html("")
            //     }
            //     $.ajax({
            //         url: `end-point/get-kamar-ruangan-by-kp`,
            //         data: {
            //         ruanganId: $(delegateTarget).val(),
            //         kelasPelayananId: $(`#kelasFilter${key}`).val(),
            //         },
            //         success: (res) => {
            //         let dataKamar = []
            //         res.output.map((item) => {
            //             dataKamar.push({
            //             id: item.id,
            //             text: item.name,
            //             })
            //         })
            //         $(`#kamarFilter${key}`).prop("disabled", false)
            //         $(`#kamarFilter${key}`).select2({
            //             data: dataKamar
            //         });
            //         var optionKamar = new Option("Semua", "", true, true);
            //         $(`#kamarFilter${key}`).prepend(optionKamar).trigger("change");
            //         }
            //     })
            // })
            // $(`#jenisPenyakitFilter${key}`).bind("change", ({ delegateTarget }) => {
            // initRuanganSource(key)
            // })
            // $(`#kelasFilter${key}`).bind("change", ({ delegateTarget }) => {
            // initRuanganSource(key)
            // })
            // if (key == "Semua") {
            // }
            $(`#searchBtn${key}`).bind("click", ({ delegateTarget }) => {
            initDatatable(jenisKamar, true)
            });
        }
    }


    function initRuanganSource(key) {
        if ($(`#jenisPenyakitFilter${key}`).val() === "" || $(`#jenisPenyakitFilter${key}`).val().toLowerCase() === "semua") {
            $(`#ruanganFilter${key}`).prop("disabled", true)
            $(`#ruanganFilter${key}`).val("Semua").trigger("change")
            return false
        }

        if ($(`#ruanganFilter${key}`).hasClass("select2-hidden-accessible")) {
            $(`#ruanganFilter${key}`).select2("destroy")
            $(`#ruanganFilter${key}`).html("")
        }
        $.ajax({
            url: `end-point/get-list-ruangan-by-jenis`,
            method: "POST",
            data: {
            jeniskasuspenyakit_nama: $(`#jenisPenyakitFilter${key}`).val(),
            kelaspelayanan_nama: $(`#kelasFilter${key}`).val(),
            },
            success: (res) => {
            $(`#ruanganFilter${key}`).prop("disabled", false)
            
            const dataRuangan = []
            res.output.map(({ id, name }) => {
                dataRuangan.push({
                id,
                text: name
                })
            })
            $(`#ruanganFilter${key}`).select2({
                data: dataRuangan,
            });
            var optionRuangan = new Option("Semua", "", true, true);
            $(`#ruanganFilter${key}`).prepend(optionRuangan).trigger("change");
            },
            error: () => {
            $(`#ruanganFilter${key}`).select2({
                data: []
            })
            },
            complete: () => {
            $("#kamarFilter").prop("disabled", true)
            hideLoader()
            }
        })
    }

    let paramDatatable = {
        "titipan": {},
    }
    function setParamDatatable(typeName, key) {
        if (Object.keys(paramDatatable[typeName]).length == 0) {
            paramDatatable[typeName] = {
            penjamin_id: table.row(".selected").data().penjamin_id ,
            kamar_id: typeof $(`#kamarFilter${key}`) !== "undefined" && $(`#kamarFilter${key}`).val() !== null ? $(`#kamarFilter${key}`).val() : "",
            status_kamar: typeof $(`#statusKamarFilter${key}`) !== "undefined" && $(`#statusKamarFilter${key}`).val() !== null ? $(`#statusKamarFilter${key}`).val() : "",
            ruangan_id: typeof $(`#ruanganFilter${key}`) !== "undefined" && $(`#ruanganFilter${key}`).val() !== null ? $(`#ruanganFilter${key}`).val() : 0,
            jenis_id: typeof $(`#jenisPenyakitFilter${key}`) !== "undefined" && $(`#jenisPenyakitFilter${key}`).val() !== null ? $(`#jenisPenyakitFilter${key}`).val() : 0,
            kelas_id: typeof $(`#kelasFilter${key}`) !== "undefined" && $(`#kelasFilter${key}`).val() !== null ? $(`#kelasFilter${key}`).val() : 0
            }
        }
    }


    function initDatatable(jenisKamar, reinit = false) {
        key = "Semua";
        $(`#searchBtn${key}`).prop("disabled", true);
        if (reinit) {
            pageDataKamar[typeName] = 1
            paramDatatable[typeName] = {}
        }
        createHeaderDatatableKamar(typeName, key);
        setParamDatatable(typeName, key);
        const { kamar_id, status_kamar, ruangan_id, jenis_id, penjamin_id, kelas_id } = paramDatatable[typeName]
        
        columnGenerated = columns
        tableKamar = $("#tableKamar")
        $("#filterHeaderKamarSemua").show();
        
        tableKamar.parent().show()
        if (pageDataKamar[typeName] === 1 || reinit) {
            tableKamar.find("tbody").html("")
        }
        tableKamar.block({
            message: null
        })
        // Append table
        var _default = {
            page: pageDataKamar[typeName], 
            gender: table.row(".selected").data().jeniskelamin 
        }
        var content = paramDatatable[typeName];
        var _mergeObject = $.extend({}, _default, content);
        $.ajax({
            url: `${baseUrl}ranap/end-point/get-data-kamar-default`,
            data: _mergeObject,
            method: "GET",
            beforeSend: () => {
            hideLoader()
            },
            success: (res) => {
            // Append if
            if(typeof res.data.length != "undefined") {
                if (res.data.length === 0 && pageDataKamar[typeName] == 1) {
                tableKamar.find("tbody").html(`
                    <tr>
                    <td class="text-center" colspan="${columnGenerated.length}">Data tidak tersedia</td>
                    </tr>
                `)
            } else {
                const records = res.data
                const tbodySection = tableKamar.find("tbody")
                const length = records.length
                for (let indexRecord = 0; indexRecord < length; indexRecord++) {
                let trHtml = "<tr>"
                let valueOfColumn = ""
                for (let indexColumn = 0; indexColumn < columnGenerated.length; indexColumn++) {
                    if (columnGenerated[indexColumn].data === "rowNum") {
                    valueOfColumn = (pageDataKamar[typeName] - 1) * 10 + (indexRecord + 1)
                    } else {
                    valueOfColumn = records[indexRecord][columnGenerated[indexColumn].data]
                    }
                    trHtml += `<td ${typeof columnGenerated[indexColumn].className !== "undefined" ? `class="${columnGenerated[indexColumn].className}"` : ""}>${valueOfColumn}</td>`
                }
                tbodySection.append(`${trHtml}</tr>`)
                tbodySection.find("td").last().parent().attr("data-akomodasi", records[indexRecord]["is_akomodasi"] ? "1" : "0")
                }
                isStillExistDataKamar[typeName] = records.length > 10
                pageDataKamar[typeName] += 1
            }
            }
            
            },
            error: () => {
            tableKamar.find("tbody").html(`
                <tr>
                <td colspan="${columnGenerated.length}">Terjadi kesalahan</td>
                </tr>
            `)
            },
            complete: () => {
            tableKamar.unblock()
            $(`#searchBtn${key}`).prop("disabled", false)
            }
        })
    }
    // date & time
    function getCurrentDate(){
        var d = new Date();
        var date = d.getDate();
        var month = d.getMonth();
        var montharr = ["Jan","Feb","Mar","April","May","June","July","Aug","Sep","Oct","Nov","Dec"];

        month = montharr[month];

        var year = d.getFullYear();
        var day = d.getDay();

        var dayarr =["Sun","Mon","Tues","Wed","Thurs","Fri","Sat"];

        day = dayarr[day];
        return date +" "+ month +" "+ year;
    }





    let activeTable = "kamar"



    function loadModal() {
        isModalTempatTidur = true;
        var jenisKamar = "semua";
        
        initDatatable(jenisKamar, true);
        // $("#modalTempatTidur").modal({
        //     backdrop: "static",
        //     keyboard: false
        // })
        $("#modalTempatTidur").removeClass("hide");
        $("#modalPasienPulang").addClass("hide");
        $(".modal-title").text("Pilih Tempat Tidur");
        $(".select2").select2();
    }
    
    $(document).ready(function () {
        $("label[for=pasienbatalpulangform-alasan_pembatalan]").hide();
    });
    function batalPulang() {
        var data = $("#batal-pulang-form").serializeArray();
        $("#btn-simpan").docoForm("click",{
            url: "/ranap/inf-pasien-pulang/save-batal-pulang",
            data: data,
            success : function(res) {
                var form = $("#batal-pulang-form");
                form[0].reset();
                table.draw();
                $("#modal_backdrop").modal("toggle");
                window.location.href =`${baseUrl}ranap/inf-pasien-pulang`;
            },
            error : function(res) {
                if(typeof res.responseJSON.response != undefined) {
                    docoNotification("error", "Proses Gagal !", res.responseJSON.response.data);
                }
            }
        });
    }

    

   
    

', View::POS_END, 'b-index');

$this->registerJs($this->render('js/_index.js'), View::POS_END);
?>
