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
        font-size: 26px !important;
        text-align: left;
        position: sticky;
    }

    td {
        font-weight: bold !important;
        font-size: 26px !important;
        color: #233a72 !important;
        height: 50px !important;
        text-align: left;
    }

    table, th, td {
        border: 2px solid !important;
        color: #233a72 !important;
    }

    .header-custom {
        background-image: url(/media/img/layar-antrian/panggil.png); 
        background-size: 400px;
        max-height: 37px !important;
    }

    table{
        position: relative;
        border-collapse: collapse;
    }

    tbody{
        background-color: white;
    }
</style>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<img src="/media/img/layar-antrian/background.png" class="img-background">

<div class="row">
    <div class="column-12-small">
        <div>
            <div style="display: flex; justify-content: center;">
                <span style="font-size: 32px !important; font-weight: bold; color: #233a72;">Ketersediaan Kamar <?= $tanggalIndonesia['hari'] . ', '. $tanggalIndonesia['tanggal'] . ' ' .  $tanggalIndonesia['bulan'] . ' ' . $tanggalIndonesia['tahun'] ?></span>
            </div>
        </div>
        <div class="container-fluid" style="margin-top: 20px;">
            <table class="table table-responsive">
                <thead>
                    <tr class="header-custom">
                        <th style="width: 40%;"><span class="text-white text-uppercase">Kelas Pelayanan</span></th>
                        <th style="width: 20%;"><span class="text-white text-uppercase">Jumlah Bed</span></th>
                        <th style="width: 20%;"><span class="text-white text-uppercase">Bed Terpakai</span></th>
                        <th style="width: 20%;"><span class="text-white text-uppercase">Bed Tersedia</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listData as $key => $value) : ?>
                        <tr>
                            <td style="width: 40%;"><?= ArrayHelper::getValue($value, 'kelas_pelayanan') ?></td>
                            <td style="width: 20%;"><?= ArrayHelper::getValue($value, 'total_bed') ?></td>
                            <td style="width: 20%;"><span class="text-danger"><?= ArrayHelper::getValue($value, 'total_terisi') ?></span></td>
                            <td style="width: 20%;"><?= ArrayHelper::getValue($value, 'total_kosong') ?></td>
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
$this->registerJs($this->render('./js/ketersediaan-kamar.js'));
?>