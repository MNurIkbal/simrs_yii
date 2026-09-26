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
<div class="notif-progress">
    <span class="help-block label-progress"></span>
    <hr>
</div>

<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 4.b'); ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'DATA KEADAAN MORBIDITAS PASIEN RAWAT JALAN RUMAH SAKIT'); ?></h3>
    <div class="form-group">
        <div class="col-md-12">
            <h5>Kode RS : <?=@$profil['nokode_rumahsakit']?></h5>
			<h5>Nama RS : <?=@$profil['nama_rumahsakit']?></h5>
			<h5>Bulan   : <?=@$textBulan?></h5>
			<h5>Tahun   : <?=@$tahun?></h5>
            <table id="rl-morbiditas-rajal" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th rowspan="3" class="border-tab" width="1"><?=\Yii::t("fe", "No. Urut");?></th>
                        <th rowspan="3" class="border-tab"><?=\Yii::t("fe", "No. DTD");?></th>
                        <th rowspan="3" class="border-tab"><?=\Yii::t("fe", "No. Daftar terperinci");?></th>
                        <th rowspan="3" class="border-tab" class="border-tab"><?=\Yii::t("fe", "Golongan sebab penyakit");?></th>
                        <th colspan="<?= $countJk ?>" class="text-center border-tab"><?=\Yii::t("fe", "Jumlah Pasien Kasus Menurut Golongan Umur & Jenis Kelamin");?></th>
                        <th colspan="2" class="border-tab"><?=\Yii::t("fe", "Kasus Baru Menurut Jenis Kelamin");?></th>
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
            </table>
        </div>
    </div>
<script type="text/javascript">
var table;
var bulan = "<?= !empty($bulan) ? $bulan : "" ?>";
var tahun = "<?= !empty($tahun) ? $tahun : "" ?>";
var jenis = "<?= !empty($jenis_laporan) ? $jenis_laporan : "" ?>";
var instalasi_id = "<?= !empty($instalasi_id) ? $instalasi_id : "" ?>";
var columns = <?= json_encode($columns) ?>;

$(document).ready(function() { 
    const showInfo = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve($(".notif-progress").css("display", "block"))
                resolve($(".label-progress").html(`<p class="text-center" style="font-size:16px;font-weight:bold;"> Sedang memproses Data </p>`))
            }, 1000);
        })
    }

    const showFinished = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve($(".label-progress").html(`<p class="text-center" style="font-size:16px;color:green;font-weight:bold;">Data berhasil di proses</p>`))
            }, 1000);
            setTimeout(() => {
                resolve($(".notif-progress").css("display", "none"))
            }, 2300);
        })
    }

    async function getDataLaporan() {
        let config = await $.getJSON("./../../json/setup.json")
        if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
        } else {
            var socket = io.connect(config.ip+':'+config.port);
        }

        let randString = "<?=$randString?>";

        const channel = 'get-laporan:';
        await showInfo()

        $.ajax({
            url : '/rm/laporan-sirs/process-get-data-morbiditas-rajal?randString=<?= $randString ?>',
            beforeSend : function () {
                socket.on(channel + randString, (message) => {
                    const _data = $.parseJSON(message);
                    const { status , messageProcess , data, progress} = _data
                    if(status == 'finish') {
                        showFinished()
                            
                        table = $("#rl-morbiditas-rajal").DataTable({
                            language: {
                                search: "Pencarian&nbsp;:&nbsp;"
                            },
                            ordering : false,
                            searching : false,
                            filter: true,
                            sorting: false,
                            displayLength: 10,
                            paging: true,
                            scrollX: true,
                            processing: true,
                            serverSide: false,
                            data: data,
                            columns: columns,
                        });
                    }
                });
            },
            success : function (data) {

            }
        });
    }

    getDataLaporan()
});
</script>