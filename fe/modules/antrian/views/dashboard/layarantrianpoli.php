<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: ui untuk display antrian
 **/

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use app\components\DocoHelpers;

$this->title = $judulLayarAntrian;
$this->context->layout = 'display-antrian';
$tanggalIndonesia = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
$tanggal = $tanggalIndonesia['hari'] . ', ' . date('d-m-Y');
?>
<style>
    ::-webkit-scrollbar {
        display: none;
    }
</style>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<img src="/media/img/layar-antrian/background.png" class="img-background">

<div class="row">
    <?php if ($layarTampilFull): ?>
        <div id="displayCarousel" class="carousel slide column-12-small-main" data-ride="carousel" data-interval="5000" style="display: block;margin-left: -40px;height: 100vh;">
    <?php else: ?>
        <div id="displayCarousel" class="carousel slide column-7-small-main" data-ride="carousel" data-interval="5000" style="display: block;">
    <?php endif ?>
        <div class="carousel-inner carousel-parent" style="height: inherit;">
            <?php foreach ($listData as $k => $v) : ?>
                <div class="item <?= $k == 0 ? 'active' : '' ?>">
                    <center>
                        <div class="carousel-item ">
                            <?php foreach ($v as $key => $value) : ?>
                                <div class="column-3-small-main">
                                    <div class="title-column-3-small-main"><?= Yii::t('fe', strtoupper($value['ruangan_nama'])) ?></div>
                                    <p class="content-column-3-small-title">
                                        <?= $value['pegawai_nama']?>
                                    </p>
                                    <p class="content-column-3-small-subtitle">
                                        <?= $value['jadwal']?>
                                    </p>
                                    <img src="/media/img/layar-antrian/card_antrian.png" class="img-responsive-hd">
                                    <p class="content-column-3-small-main velocity-panggil" data-animation="flash" id="no_antrian_panggil_<?= $value['jadwaldokter_id']?>" style="font-size: 5vw;top: 10.8vw;">
                                        <span class="prefix-color">-</span>
                                    </p>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </center>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <?php if (!$layarTampilFull): ?>
        <div class="column-5-small-main">
            <?php if ($konfig_layar['is_slider'] == 0) : ?>
                <?php if (!empty($slides)) : ?>
                    <ul class="rslides">
                        <?php foreach ($img as $value) : ?>
                            <div>
                                <?= Html::img($value, ['class' => 'img-responsive-hd-slider img-slider']) ?>
                            </div>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            <?php elseif ($konfig_layar['is_slider'] == 1) : ?>
                <video id="idle_video" onended="onVideoEnded();" muted="muted"></video>
            <?php else : ?>
                <video id="idle_video" onended="onVideoEnded();" muted="muted">
                    <source src="<?= $konfig_layar['url_slider'] ?>" type="video/mp4">
                </video>
            <?php endif ?>
        </div>
    <?php endif ?>
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

<script type="text/javascript">
    var _arrJadwal = <?= $jadwalList ?>;
    var newDesign = true;
    var isPoli = true;
    var videos = <?= $videos ?>;
    var is_slider = <?= $konfig_layar['is_slider'] ?>;
    var index = 0;
    var player = null;
    var konfig_jenisantriandetail = <?= (isset($konfig_jenisantriandetail) === false) ? 1 : $konfig_jenisantriandetail ?>;

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
$this->registerJs($this->render('../assets/js/antrian-listener.js'));
?>