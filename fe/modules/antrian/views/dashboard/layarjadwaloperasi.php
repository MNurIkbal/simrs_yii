<?php   

/**
 * @author: ali.padilah@docotel.com
 * @description: ui untuk display antrian
 **/

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

$this->title = $judulLayarAntrian;
$this->context->layout = 'display-antrian';
$tanggalIndonesia = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
$tanggal = $tanggalIndonesia['hari'] . ', ' . date('d-m-Y');
?>
<style>
    ::-webkit-scrollbar {
        display: none;
    }
    th {
        font-weight: bold !important;
        font-size: 17px !important;
        text-align: center;
        position: sticky;
    }

    td {
        font-weight: bold !important;
        font-size: 17px !important;
        color: #233a72 !important;
        height: 50px !important;
        text-align: center;
        padding: 10px !important;
    }

    table, th, td {
        border: 2px solid !important;
        color: #233a72 !important;
    }

    .header-custom {
        background-image: url(/media/img/layar-antrian/panggil.png); 
        background-size: 700px;
        max-height: 37px !important;
    }

    table{
        position: relative;
        border-collapse: collapse;
    }

    tbody{
        background-color: white;
    }
    body{
        background-image: url(/media/img/layar-antrian/background.png); 
        

    }
</style>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<body>
<div class="row">
    <div class="column-12-small">
        <div>
            <div style="display: flex; justify-content: center;">
                <span style="font-size: 32px; font-weight: bold; color: #233a72;">Jadwal Operasi, <?= $tanggalIndonesia['tanggal'] . ' ' .  $tanggalIndonesia['bulan'] . ' ' . $tanggalIndonesia['tahun'] ?></span>
            </div>
        </div>
        <div class="container-fluid" style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr class="header-custom">
                        <th style="width: 10px !important;"><span class="text-white">No</span></th>
                        <th><span class="text-white text-uppercase">No.Operasi</span></th>
                        <th><span class="text-white text-uppercase">No. Rekam Medik</span></th>
                        <!-- <th><span class="text-white text-uppercase">Nama Pasien</span></th> -->
                        <th><span class="text-white text-uppercase">No. Peserta</span></th>
                        <th><span class="text-white text-uppercase">Tanggal</span></th>
                        <th><span class="text-white text-uppercase">Unit Asal</span></th>
                        <th><span class="text-white text-uppercase">Dokter Pengirim</span></th>
                        <th><span class="text-white text-uppercase">Tindakan</span></th>
                        <th><span class="text-white text-uppercase">Status</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $no = 1;
                        foreach ($listData as $key => $value) : 
                    ?>
                    <tr class="part-<?php 
                        if($no >= 1 && $no <= 5) {
                            echo "1";
                        }
                        if($no >= 6 && $no <= 10) {
                            echo "2 hide";
                        }
                        if($no >= 11 && $no <= 15) {
                            echo "3 hide";
                        }
                        if($no >= 16 && $no <= 20) {
                            echo "4 hide";
                        }
                        if($no >= 21 && $no <= 25) {
                            echo "5 hide";
                        }
                        ?>"
                    >
                        <td><?= $no++ ?></td>
                        <td><?= ArrayHelper::getValue($value, 'no_masukpenunjang') ?></td>
                        <td><?= ArrayHelper::getValue($value, 'no_rekam_medik') ?></td>
                        <td><?= ArrayHelper::getValue($value, 'no_peserta') ? ArrayHelper::getValue($value, 'no_peserta') : "-"?></td>
                        <td><?= ArrayHelper::getValue($value, 'tgl_operasi') ?></td>
                        <td><?= ArrayHelper::getValue($value, 'ruangan_nama') ?></td>
                        <td><?= ArrayHelper::getValue($value, 'dok_perujuk') ?></td>
                        <td><?= ArrayHelper::getValue($value['pemeriksaan'], 'tindakan') ?></td>
                        <td><?= ArrayHelper::getValue($value, 'status') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="navbar-fixed-bottom">
        <img src="/media/img/layar-antrian/footer.png" alt="Footer" class="img-responsive">
        <a class="navbar-brand">
            <div id="time" class="footer-time"></div>
            <div class="footer-date"><?= $tanggal ?></div>
        </a>
        <marquee class="marquee" direction="" onmouseover="this.stop();" onmouseout="this.start();">
            <?= $konfig_layar['footer'] ?>
        </marquee>
    </div>
</div>
                    </body>
<script type="text/javascript">
    var newDesign = true;
    var is_slider = -1;
    var isPoli = true;
    var videos = '';
    var index = 0;
    var player = null;

    function onVideoEnded() {
        if (is_slider == 1) {
            $('#idle_video')[0].load();
            $('#idle_video')[0].play();
        } else {
            if (index < videos.length - 1) {
                index++;
            } else {
                index = 0;
            }

            player.setAttribute('src', videos[index]);
            player.play();
        }
    }
</script>
<?php
$this->registerJs($this->render('../assets/js/antrian-operasi.js'));
?>