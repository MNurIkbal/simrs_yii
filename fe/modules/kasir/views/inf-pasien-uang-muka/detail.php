<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-11-05 13:19:27
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-19 10:01:35
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe',$_title), 'url' => ['index']];
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
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'print-kwitansi'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Kwitansi'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-print-kwitansi',
                            'class'=>'btn-print-uang-muka',
                            'data-options'=>'click',
                            'data-target'=> '/kasir/inf-pasien-uang-muka/print-kwitansi',
                            'data-target2nd'=> '/kasir/inf-pasien-uang-muka/print-kwitansi-keluar',
                        ]
                    ],
                    'batal'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'method' => '#',
                        'attributes' => [
                            'class'=>'btn-batal-uang-muka',
                            'data-options'=>'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-additional' => 'data-rm',
                            'data-url'=>'/kasir/inf-pasien-uang-muka/cancel-uang-muka?id=',
                            'data-conditions'=>'pendaftaran_id'
                        ]
                    ],
                    // 'print-bkm'=>[
                    //     'type'=>'button',
                    //     'title' => \Yii::t('fe', 'BKM'),
                    //     'icon' => 'fa fa-print',
                    //     'method' => 'not-exist',
                    //     'attributes' => [
                    //         'id'=>'btn-print-bkm',
                    //         'class'=>'btn-print-uang-muka',
                    //         'data-options'=>'click',
                    //         'data-target'=>'/kasir/inf-pasien-uang-muka/print-bkm',
                    //     ]
                    // ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12" id="informasi" style="margin-top:10px;">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                            </div>
                            <div class="panel-body">
                                <div class="row row-eq-height " style="margin-top:10px;">
                                    <div class="col-md-8" id="informasi">
                                        <div class="panel panel-default">
                                            <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                                                <div class="panel-heading flex-container">
                                                    <h6 class="panel-title"><?= Yii::t('fe', 'Data Pasien') ?></h6>
                                                    <p class="p-data" id="data-pasien">
                                                        <?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?> - 
                                                        <b class="font" ><?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?></b>
                                                        (<?= isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-' ?>) 
                                                    </p>
                                                    <ul class="icons-list">
                                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                                    </ul>          
                                                
                                                </div>
                                            </a>
                        
                                            <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                                                <div class="col-xs-2">
                                                    <div class="border-img">
                                                        <?php 
                                                        $filename = isset($header['photopasien']) ? !empty($header['photopasien']) ? '/media/img/pasien/'.$header['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                                        ?>
                                                        <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-xs-9">
                                                    <div class="row">
                                                        <br>
                                                        <div class="col-xs-6">
                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                                            <br>
                                                            <p>
                                                                <?= isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-' ?> -
                                                                <?= isset($header['nama_pasien']) ? $header['nama_pasien'] : '-' ?> -
                                                                <?= isset($header['jeniskelamin']) ? $header['jeniskelamin'] : '-' ?>
                                                            </p>
                                                            
                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>
                                                            <br>
                                                            <p>
                                                                <?= isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-' ?> - 
                                                                (<?= isset($header['umur']) ? $header['umur'] : '-' ?>)
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Pendaftaran") ?></b>
                                                            <p>
                                                                <?= isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-' ?> - 
                                                                <?= isset($header['tgl_pendaftaran']) ? date('d-M-Y', strtotime($header['tgl_pendaftaran'])) : '-' ?>
                                                            </p>

                                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Cara Bayar") ?></b>
                                                            <p>
                                                                <?= isset($header['carabayar_nama']) ? $header['carabayar_nama'] : '-' ?> - <?= isset($header['penjamin_nama']) ? $header['penjamin_nama'] : '-' ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="panel panel-default">
                                            <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                                                <div class="panel-heading flex-container">
                                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                                                    <ul class="icons-list">
                                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                                    </ul>         
                                                </div>
                                            </a>
                                            <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                                                <div class="row row-eq-height">
                                                    <br>
                                                    <div class="col-xs-6">
                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", " Penyakit") ?></b>
                                                        <p>
                                                            <?= isset($header['jeniskasuspenyakit_nama']) ? $header['jeniskasuspenyakit_nama'] : '-' ?> 
                                                        </p>
                                                        
                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan") ?></b>
                                                        <p>
                                                            <?php $ruanganBayar = isset($header['ruangan_nama']) ? $header['ruangan_nama'] : ''?>
                                                            <?= $ruanganBayar ?>
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="col-xs-6">
                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Dokter") ?></b>
                                                        <p> 
                                                            <?= !empty($header['pegawai_rd_rj']) ? $header['pegawai_rd_rj'] : null ?> 
                                                        </p>

                                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Status Bayar") ?></b>
                                                        <p>
                                                            <?= isset($header['status_bayar']) ? $header['status_bayar'] : '-' ?>  
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end info pasien -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?= Yii::t('fe', 'Data Pembayaran Uang Muka Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                            </div>
                            <div class="panel-body">
                                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1"></th>
                                            <th width="70">No</th>
                                            <th><?=\Yii::t("fe", "Tanggal Pembayaran");?></th>
                                            <th><?=\Yii::t("fe", "No Pembayaran");?></th>
                                            <th><?=\Yii::t("fe", "Jumlah Uang Muka");?></th>
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
                <?php if ($pengembalian_uangmuka) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?= Yii::t('fe', 'Data Pengembalian Uang Muka Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                            </div>
                            <div class="panel-body">
                                <table id="example2nd" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1"></th>
                                            <th width="70">No</th>
                                            <th><?=\Yii::t("fe", "Tanggal Pengembalian");?></th>
                                            <th><?=\Yii::t("fe", "No Pengembalian");?></th>
                                            <th><?=\Yii::t("fe", "Jumlah Pengembalian");?></th>
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
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php 

$this->registerjs('
    var table;
    var table2nd;
    var uangmuka_dipakai = ' .$dipakai_uangmuka. '
    $(document).ready(function(){
        table = $("#example").docoTabel({
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
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: false,
            lengthChange: false,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pasien-uang-muka/get-detail?id='.$id.'",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'", data: "tgl_uangmuka", searchable: false},
                {title: "'.(\Yii::t("fe", "No Pembayaran")).'", data: "no_uangmuka", searchable: false},
                {title: "'.(\Yii::t("fe", "Jumlah Uang Muka (Rp.)")).'", data: "jumlah_uangmuka", searchable: false, class: "text-right"},
            ]
        });

        $(".dataTables_filter").hide();
        if(uangmuka_dipakai == 1){
            $(".btn-batal-uang-muka").prop("disabled",true)
        }
    });

    $(document).ready(function(){
        table2nd = $("#example2nd").docoTabel({
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
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: false,
            lengthChange: false,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pasien-uang-muka/get-detail-pengembalian?id='.$id.'",
            columns: [
                {data: null, searchable: false, sortable: false, defaultContent:""},
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Pengembalian")).'", data: "tgl_pengembalian", searchable: false},
                {title: "'.(\Yii::t("fe", "No Pengembalian")).'", data: "no_kwitansi", searchable: false},
                {title: "'.(\Yii::t("fe", "Jumlah Pengembalian (Rp.)")).'", data: "jml_pengembalian", searchable: false, class: "text-right"},
            ]
        });

        $(".dataTables_filter").hide();
    });

    $("#example2nd").on("click", "td", function(e) {
        table.row(".selected").deselect()
    })

    $("#example").on("click", "td", function(e) {
        if (typeof table2nd !== "undefined") {
            table2nd.row(".selected").deselect()
        }
    })

    $(document).on("click",".btn-print-uang-muka",function(e) {
        e.preventDefault();
        var tableData = table.row(".selected").data();
        var tableData2nd;
        if (typeof table2nd !== "undefined") {
            tableData2nd = table2nd.row(".selected").data();
        }

        if (typeof tableData !== "undefined") {
            if ("primary" in tableData) {
                var target = $(this).attr("data-target");
                var primary = tableData.primary;
                window.open(target+"?id="+primary);
            } else {
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        } else if (typeof tableData2nd !== "undefined") {
            if ("primary" in tableData2nd) {
                var target = $(this).attr("data-target2nd");
                var primary = tableData2nd.primary;
                window.open(target+"?id="+primary);
            } else {
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        } else {
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

    $(".btn-batal-uang-muka").click(function(e){
        e.preventDefault()
        var tableData = table.row(".selected").data();
        PNotify.removeAll();
        if (typeof tableData !== "undefined") {
            var primary = tableData.primary;
            var url = $(this).attr("data-url");
            var conditions = $(this).attr("data-conditions") ? $(this).attr("data-conditions").split(",") : "";
            var ext = "";
            if(conditions.length > 0){
                $.each(conditions, function(index, value){
                    ext += "&"+value+"="+tableData[value];
                });
            }
        } else {
            new PNotify({
            title: "Terjadi Kesalahan",
            text: "Belum ada data yang dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    
        }
    });
    
    ', View::POS_END, 'js')

?>