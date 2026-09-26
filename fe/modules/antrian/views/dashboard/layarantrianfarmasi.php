<?php

/**
 * @author: ali.padilah@docotel.com
 * @description: ui untuk display antrian
**/

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Url;
use app\components\DocoHelpers;

$this->title = $judulLayarAntrian;
$this->context->layout = 'display-antrian';
$arrLoket = '';

foreach ($loketList as $key => $value) {
  $arrLoket[] = $value['loket_id'];
}

$arrLoket = json_encode($arrLoket);
$tanggalIndonesia = DocoHelpers::getTanggalIndonesia(date('Y-m-d'));
$tanggal = $tanggalIndonesia['hari'].', '.date('d-m-Y');

?>


<style>
    .header-logo-img-responsive {
        max-width: 250px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .border-antrian {
      border-width: 5px;
      border-color: #0063a1;
      border-radius: 9px;
    }

    .dis-right {
      top: 100px;
    }

    .dis-left {
      top: 100px;
    }

    .panel[class*=border-top-] {
         border-top-right-radius: 9px; 
         border-top-left-radius: 9px; 
    }

    .panel-body {
      padding: 0px 10px 0px 10px !important;
    }

    .wrapper-title {
      background-color: #0063a1;
    }

    .title {
      font-family: HelveticaNeueHv;
      text-align: center;
      color: white;
      z-index: 999;
      font-size: 1vw;
      letter-spacing: 0.6vw;
      left: 0;
      right: 0;
      top: 1.8vw;
    }

    .nomer_panggil {
      font-family: HelveticaNeueHv;
      text-align: center;
      color: white;
      z-index: 999;
      font-size: 5vw;
      color: #e69500;
    }

    .card {
        border: 2px solid #0063A1;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.8);
    }

    .card-dalam-proses .card-header {
        font-size: 3rem;
        text-align: center;
    }

    .card-header {
      background-image: url(../../images/antrian/card-header-bg.png);
      background-repeat: no-repeat;
      background-position: center center;
      background-size: cover;
      border: none;
      font-size: 1.5rem;
      font-weight: 700;
      color: #FFFFFF;
      margin-bottom: 0;
    }

    .table-dalam-proses {
        border-color: #233A72;
        font-size: 3rem;
        font-weight: bold;
    }

    .nomor-antrian {
        font-size: 2.5rem;
        color: #233A72;
    }

    .header-dis-farmasi {
      font-size: 2.5rem;
    }

</style>

<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<img src="/media/img/layar-antrian/background.png" class="img-background">
<!-- <br><br><br>
<br><br><br> -->
<div class="row">
  <div class="col-md-3">
     <div class="panel panel-body border-top-success col-md-12 dis-right border-antrian">
        <div class="row">
            <div class="col-sm-12 wrapper-title">
              <p class="title">
                <?= strtoupper(Yii::t('fe','Nomor antrian Racikan'))?>
              </p>
            </div>
            <div class="col-sm-12 dis-panel-right-isi nomer_panggil">
                <p class="velocity-panggil" data-animation="flash" id="no_antrian_panggil_r">-</p>   
            </div>
            <div class="col-sm-12" style="font-size: 2vw;font-weight: bold;color: #00264d">
                <?= strtoupper(Yii::t('fe','Ke Counter'))?> <label id="counter_panggil_r" style="font-size: 2vw;font-weight: bold;color: #00264d">-</label>
            </div>
        </div>
     </div>
     <br><br><br>
     <div class="panel panel-body border-top-success col-md-12 dis-right border-antrian">
        <div class="row">
            <div class="col-sm-12 wrapper-title">
              <p class="title">
                <?= strtoupper(Yii::t('fe','Nomor antrian Non Racikan'))?>
              </p>
            </div>
            <div class="col-sm-12 dis-panel-right-isi nomer_panggil">
                <p class="velocity-panggil" data-animation="flash" id="no_antrian_panggil_nr">-</p>   
            </div>
            <div class="col-sm-12" style="font-size: 2vw;font-weight: bold;color: #00264d">
                <?= strtoupper(Yii::t('fe','Ke Counter'))?> <label id="counter_panggil_nr" style="font-size: 2vw;font-weight: bold;color: #00264d">-</label>
            </div>
        </div>
     </div>
  </div>
  <div class="col-md-9">
     <div class="panel panel-body border-top-success col-md-12 dis-left border-antrian">
        <div class='row'>
          <div class="col-sm-12">
            <div class="header-dis-farmasi">
                   <?= $namaLoket ?>
              </div>
          </div>
          <div class="col-sm-12">
            <div class="header-dis-farmasi">
                   <?=Yii::t('fe', 'Antrian yang sedang di proses')?>
              </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="card card-dalam-proses">
              <div class="card-header">
                  Resep Racikan
              </div>
              <div class="card-body">
                  <div class="table-responsive">
                      <table class="table table-bordered table-dalam-proses">
                          <thead>
                              <tr>
                                  <th scope="col" width="200">No. Urut</th>
                                  <th scope="col">No. Antrian</th>
                                  <th scope="col">Status</th>
                              </tr>
                          </thead>
                          <tbody class="data-racikan">
                            
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="card card-dalam-proses">
              <div class="card-header">
                  Resep Non Racikan
              </div>
              <div class="card-body">
                  <div class="table-responsive">
                      <table class="table table-bordered table-dalam-proses">
                          <thead>
                              <tr>
                                  <th scope="col" width="200">No. Urut</th>
                                  <th scope="col">No. Antrian</th>
                                  <th scope="col">Status</th>
                              </tr>
                          </thead>
                          <tbody class="data-non-racikan">
                            
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
        </div>
      </div>
     </div>
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

<script type="text/javascript">
  var _arrloket = <?= $arrLoket ?>;
  var _idLayar = <?= $id ?>;
  var _dataRacikan = <?= json_encode($dt_antrian["racikan"]) ?>;
  var _dataNonRacikan = <?= json_encode($dt_antrian["non_racikan"]) ?>;
  var _ruangan_response = <?= $ruangan_response ?>;
  var konfig_jenisantriandetail = <?= (isset($konfig_jenisantriandetail) === false) ? 1 : $konfig_jenisantriandetail ?>;

  console.log(_dataRacikan);
</script>
<?php
    $this->registerJs($this->render('../assets/js/farmasi.js'));
    $this->registerJs($this->render('../assets/js/antrian-listener.js'));
?>