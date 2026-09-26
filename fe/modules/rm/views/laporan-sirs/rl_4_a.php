<?php

use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<style type="text/css">
    th {
        font-weight: 0px !important; 
        font-size: 11px;
    }

    .border-tab {
        border-right: 1px solid white;
    }

    .dataTables_scroll {
    max-height: 99999em !important
    }

</style>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 4.a'); ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'DATA KEADAAN MORBIDITAS PASIEN RAWAT INAP RUMAH SAKIT'); ?></h3>
    <div class="form-group">
        <div class="col-md-12">
            <h5>Kode RS : <?=@$profil['nokode_rumahsakit']?></h5>
			<h5>Nama RS : <?=@$profil['nama_rumahsakit']?></h5>
			<h5>Bulan   : <?=@$textBulan?></h5>
			<h5>Tahun   : <?=@$tahun?></h5>
            <table id="rl-morbiditas" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th rowspan="3" class="border-tab" width="1"><?=\Yii::t("fe", "No. Urut");?></th>
                        <th rowspan="3" class="border-tab"><?=\Yii::t("fe", "No. DTD");?></th>
                        <th rowspan="3" class="border-tab"><?=\Yii::t("fe", "No. Daftar terperinci");?></th>
                        <th rowspan="3" class="border-tab" class="border-tab"><?=\Yii::t("fe", "Golongan sebab penyakit");?></th>
                        <th colspan="<?= $countJk ?>" class="text-center border-tab"><?=\Yii::t("fe", "Jumlah Pasien Hidup dan Mati menurut Golongan Umur & Jenis Kelamin");?></th>
                        <th colspan="2" class="border-tab"><?=\Yii::t("fe", "Pasien Keluar (Hidup & Mati) Menurut Jenis Kelamin");?></th>
                        <th rowspan="3" class="border-tab"><?=\Yii::t("fe", "Jumlah Pasien Keluar Hidup (23 + 24)");?></th>
                        <th rowspan="3" class="border-tab"><?=\Yii::t("fe", "Jumlah Pasien Keluar mati");?></th>
                    </tr>
                    <tr class="bg-inverse">
                        <?php 
                        for ($i=0; $i < $countHeader; $i++) { ?>
                            <th colspan="2" class="text-center border-tab"><?= $header[$i] ?></th>
                        <?php } ?>  
                        <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "LK");?></th>  
                        <th rowspan="2" class="text-center border-tab"><?=\Yii::t("fe", "PR");?></th>  
                    </tr>
                    <tr class="bg-inverse">
                        <?php 
                        for ($i=0; $i < $countJk; $i++) { ?>
                            <th class="text-center border-tab"><?= $listJk[$i] ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <center><span class="populate-data-rl" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                    <div class="progress-rl" style="margin-left: 12px;">
                        <div class="progress-rl-bar progress-rl-bar-striped progress-rl-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <span class="label-persentase-rl">0</span>%</div>
                    </div>
                    <span class="help-block label-progress-rl" style="margin-left: 12px;"></span>
                </tfoot>
            </table>
            
        </div>
    </div>
<script type="text/javascript">
var table;
var bulan           = "<?= !empty($bulan) ? $bulan : "" ?>";
var tahun           = "<?= !empty($tahun) ? $tahun : "" ?>";
var jenis           = "<?= !empty($jenis_laporan) ? $jenis_laporan : "" ?>";
var columns         = <?= json_encode($columns) ?>;
var pageSize        = 10;
var query_params    = {
                        bulan           : bulan,
                        tahun           : tahun,
                        jenis_laporan   : jenis,
                        pageSize: pageSize,
                    }
var progress = $('.progress-rl');
var progressBar = $('.progress-rl .progress-bar-rl');
var draw = 0;

$(document).ready(function() {
    progress.css("display", "none")

    const showInfo = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve($(".populate-data-rl").html(`mempersiapkan data ...`))
            }, 1000);
            setTimeout(() => {
                resolve($(".populate-data-rl").css("display", "none"))
                resolve(progress.css("display", "block"))
                resolve($(".label-progress-rl").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
            }, 2000);
        })
    }

    const setPresentase = function(progress) {
        setTimeout(() => {
            $(".progress-rl .label-persentase-rl").html(progress)
            $(".progress-rl .progress-bar-rl").css("width", progress +"%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
        }, 2500);
    }

    async function getDataMorbiditas() {
        let config = await $.getJSON("./../../json/setup.json")
        if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
        } else {
            var socket = io.connect(config.ip+':'+config.port);
        }
        const channel = `laporan-morbiditas-ranap`;
        showInfo();
        $.ajax({
            type: "GET",
            url: baseUrl+'rm/laporan-sirs/get-data-morbiditas-ranap',
            dataType: "json",
            data: query_params,
            beforeSend: function () {
                socket.on(channel, (data) => {
                    const _data = $.parseJSON(data);
                    const { status } = _data;
                    if(status == 'getData') {
                        let valProgress = 30
                        setPresentase(valProgress)
                    } else {
                        let valProgress = 100
                        setPresentase(valProgress)
                        $('#cari').prop('disabled', false);
                        setTimeout(() => {
                            $(".label-progress-rl").html(`<p style="font-size:16px;font-weight:bold;"> Berhasil menyiapkan data</p>`)
                            if(draw > 0) {
                                table.clear().draw()
                                table.rows.add(_data.data); 
                                table.columns.adjust().draw(); 
                            } else {
                                table = $("#rl-morbiditas").DataTable({
                                    language: {
                                    search: "Pencarian&nbsp;:&nbsp;"
                                    },
                                    ordering : false,
                                    filter: false,
                                    sorting: false,
                                    displayLength: 10,
                                    paging: true,
                                    scrollX: true,
                                    processing: true,
                                    serverSide: false,
                                    data:_data.data,
                                    columns: columns,
                                });
                                draw++;
                            }
                        }, 2500);
                        setTimeout(() => {
                            setPresentase(0)
                            progress.css("display", "none");
                            $(".label-progress-rl").html("")
                            table.columns.adjust().draw(); 
                        }, 4000);
                    }
                });
            },
            success: function(response) {

            }
        });
    };
    getDataMorbiditas();    
    

});
</script>