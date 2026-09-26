<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: ui untuk display antrian
**/

use app\components\DocoHelpers;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = $judulLayarAntrian;
$this->context->layout = 'display-antrian';
$arrLoket = [];
$minLoket = count($loketList) >= 4 ? 0 : 4 - count($loketList);
$tanggalIndonesia = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
$tanggal = $tanggalIndonesia['hari'].', '.date('d-m-Y');
$jenisantriandetail_id = DocoHelpers::decrypt($detail);
?>

<style>
    .header-logo-img-responsive {
        max-width: 250px;
        padding-top: 10px;
        padding-bottom: 10px;
    }
</style>

<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<img src="/media/img/layar-antrian/background.png" class="img-background">
<?php if ($konfig_layar['is_banyakloket'] == 1): ?>
<div class="row">
    <div class="column-5-small">
        <div class="title-column-5-small"><?= Yii::t('fe', 'NOMOR ANTRIAN') ?></div>
        <img src="/media/img/layar-antrian/panggil.png" class="img-responsive-hd">
        <p class="content-column-5-small velocity-panggil" data-animation="flash" id="no_antrian_panggil">
            <span class="prefix-color">-</span>
        </p>
        <div class="subcontent-column-5-small">
            <span class="light-navy"><?= Yii::t('fe', 'KE COUNTER ') ?></span><span class="dark-navy" id="no_loket_panggil">-</span>
        </div>
    </div>
    <div class="column-7-small">
        <?php if ($konfig_layar['is_slider'] == 0): ?>
            <?php if (!empty($slides)): ?>
                <ul class="rslides">
                    <?php foreach ($img as $value): ?>
                        <div>
                            <?= Html::img($value, ['class' => 'img-responsive-hd img-slider']) ?>
                        </div>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        <?php elseif ($konfig_layar['is_slider'] == 1): ?>
            <video id="idle_video" onended="onVideoEnded();" muted="muted"></video>
        <?php else: ?>
            <video id="idle_video" onended="onVideoEnded();" muted="muted">
                <source src="<?= $konfig_layar['url_slider'] ?>" type="video/mp4">
            </video>
        <?php endif ?>
    </div>
</div>
<div class="row">
<?php for ($i=0; $i < 8; $i++) { 
    if (array_key_exists($i, $loketList)) {
        $arrLoket[$i] = $loketList[$i]['loket_id'];
?>
        <div class="column-3-small">
            <div class="title-column-3-small"><?= Yii::t('fe', 'COUNTER ').$loketList[$i]['loket_nourut'] ?></div>
            <img src="/media/img/layar-antrian/loket_kecil.png" class="img-responsive">
            <div class="content-column-3-small velocity-panggil" data-animation="flash" id="set-antrian-<?= $loketList[$i]['loket_id']; ?>">
                <span class="prefix-color">-</span>
            </div>
        </div>
<?php
    } else {
?>
        <div class="column-3-small">
            <div class="title-column-3-small"></div>
            <img src="/media/img/layar-antrian/loket_kecil.png" class="img-responsive">
            <div class="content-column-3-small velocity-panggil" data-animation="flash">
                <span class="prefix-color">-</span>
            </div>
        </div>
<?php
    }
}

$arrLoket = json_encode($arrLoket); 
?>
</div>
<?php else: ?>
<div class="row">
    <div class="column-5">
        <div class="title-column-5"><?= Yii::t('fe', 'NOMOR ANTRIAN') ?></div>
        <img src="/media/img/layar-antrian/panggil.png" class="img-responsive-hd">
        <p class="content-column-5 velocity-panggil" data-animation="flash" id="no_antrian_panggil">
            <span class="prefix-color">-</span>
        </p>
        <div class="subcontent-column-5">
            <span class="light-navy"><?= Yii::t('fe', 'KE COUNTER ') ?></span><span class="dark-navy" id="no_loket_panggil">-</span>
        </div>
    </div>
    <div class="column-7">
        <?php if ($konfig_layar['is_slider'] == 0): ?>
            <?php if (!empty($slides)): ?>
                <ul class="rslides">
                    <?php foreach ($img as $value): ?>
                        <div>
                            <?= Html::img($value, ['class' => 'img-responsive-hd img-slider']) ?>
                        </div>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        <?php elseif ($konfig_layar['is_slider'] == 1): ?>
            <video id="idle_video" onended="onVideoEnded();" muted="muted"></video>
        <?php else: ?>
            <video id="idle_video" onended="onVideoEnded();" muted="muted">
                <source src="<?= $konfig_layar['url_slider'] ?>" type="video/mp4">
            </video>
        <?php endif ?>
    </div>
</div>
<div class="row">
<?php for ($i=0; $i < 4; $i++) { 
    if (array_key_exists($i, $loketList)) {
        $arrLoket[$i] = $loketList[$i]['loket_id'];
?>
        <div class="column-3">
            <div class="title-column-3"><?= Yii::t('fe', 'COUNTER ').$loketList[$i]['loket_nourut'] ?></div>
            <img src="/media/img/layar-antrian/loket.png" class="img-responsive">
            <div class="content-column-3 velocity-panggil" data-animation="flash" id="set-antrian-<?= $loketList[$i]['loket_id']; ?>">
                <span class="prefix-color">-</span>
            </div>
        </div>
<?php
    } else {
?>
        <div class="column-3">
            <div class="title-column-3"></div>
            <img src="/media/img/layar-antrian/loket.png" class="img-responsive">
            <div class="content-column-3 velocity-panggil" data-animation="flash">
                <span class="prefix-color">-</span>
            </div>
        </div>
<?php
    }
}

$arrLoket = json_encode($arrLoket);
?>
<?php endif ?>
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
    var _arrloket = <?= $arrLoket ?>;
    var jenisantriandetail_id = <?= 0 ?>;
    var konfig_jenisantriandetail = <?= (isset($konfig_jenisantriandetail) === false) ? 1 : $konfig_jenisantriandetail ?>;
    var newDesign = true;
    var videos = <?=  $videos ?>;
    var is_slider = <?= $konfig_layar['is_slider'] ?>;
    var index = 0;
    var player = null;
    var isPoli = false;


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
    $this->registerJs($this->render('../assets/js/antrian-listener.js'), View::POS_END);
?>
