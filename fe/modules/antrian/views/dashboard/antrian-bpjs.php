<?php
/**
 * @author: Indra Tiola
 * @description: ui untuk display antrian
**/

use app\components\DocoConstants;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Url;

$this->title = $judulLayarAntrian;
?>
<style>
    .container-custom {
        max-width: 150rem !important; 
    }
</style>
<div class="container container-custom">
    <div class="content-header">
        <div class="row">
            <div class="col-sm-3 wrapper-back-button">
                <a class="back-antrian" id="back-antrian" href="/antrian" >
                    <img src="<?=Url::base(true).'/media/img/icon-antrian/arrow-left.png';?>" alt="">
                </a>
                <a class="back-form">
                    <img src="<?=Url::base(true).'/media/img/icon-antrian/arrow-left.png';?>" alt="">
                </a>
            </div>
            <div class="col-sm-6">
                <h5 class="content-header-title">Tipe Antrian</h5>
            </div>
            <div class="col-sm-3 text-right" style="font-size: 20px;">
                <a class="fa fa-chevron-up mr-2 hd-up"><span></span></a>
                <a class="fa fa-chevron-down mr-2 hd-down" style="display:none"><span></span></a>
            </div>
        </div>
    </div>

    <div class="content-body ">
        <?php
        $form = ActiveForm::begin([
            'id' => 'ambil-antrian-form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
        ]);
        ?>
        <div class="item">
            <div class="row get-parent">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card card-poli mx-auto">
                            <div class="card-container">
                                <div class="card-row">
                                    <img src="<?=Url::base(true).'/media/img/icon-antrian/bpjs.svg';?>">
                                </div>
                                <div class="card-row" data-toggle="modal" data-target="#modal_status_pasien" action = "/antrian/dashboard/pilih-status-pasien?cara_bayar=<?= DocoConstants::CARA_BAYAR_BPJS ?>&jenis_antrian_id=<?= DocoConstants::ANTRIAN_BPJS ?>">
                                    <h1 class="title-card"> Antrian Onsite BPJS </h1>
                                    <span class="desc-card">
                                        Pendaftaran pasien dengan cara bayar BPJS
                                    </span>
                                </div>
                                <div class="card-row my-auto">
                                    <div class="icon-chevron">
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-poli mx-auto">
                            <div class="card-container">
                                <div class="card-row">
                                    <img src="<?=Url::base(true).'/media/img/icon-antrian/pribadi.svg';?>">
                                </div>
                                <div class="card-row" data-toggle="modal" data-target="#modal_status_pasien" data-width="60%" action = "/antrian/dashboard/pilih-status-pasien?cara_bayar=<?= DocoConstants::CARA_BAYAR_UMUM ?>&jenis_antrian_id=<?= DocoConstants::JA_PDN ?>">
                                    <h1 class="title-card">Pribadi</h1>
                                    <span class="desc-card">
                                        Pendaftaran pasien dengan cara bayar Pribadi
                                    </span>
                                </div>
                                <div class="card-row my-auto">
                                    <div class="icon-chevron">
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card card-poli mx-auto">
                            <div class="card-container">
                                <div class="card-row">
                                    <img src="<?=Url::base(true).'/media/img/icon-antrian/mjkn.svg';?>">
                                </div>
                                <div class="card-row" data-toggle="modal" data-target = "#modal_backdrop" action = "/antrian/dashboard/checkin-mjkn/">
                                    <h1 class="title-card">Check-In MJKN</h1>
                                    <span class="desc-card">
                                        Konfirmasi kehadiran Anda
                                    </span>
                                </div>
                                <div class="card-row my-auto">
                                    <div class="icon-chevron">
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-poli mx-auto">
                            <div class="card-container">
                                <div class="card-row">
                                    <img src="<?=Url::base(true).'/media/img/icon-antrian/perusahaan.svg';?>">
                                </div>
                                <div class="card-row" id="pasien-asuransi-content" data-carabayar="<?= DocoConstants::CARA_BAYAR_ASU ?>" data-ruangan="<?= DocoConstants::RUANGAN_PENDAFTARAN_RAJAL ?>" data-jenis-antrian="<?= DocoConstants::JA_PDN ?>">
                                    <h1 class="title-card">Perusahaan/Asuransi</h1>
                                    <span class="desc-card">
                                        Pendaftaran pasien cara bayar perusahaan/asuransi
                                    </span>
                                </div>
                                <div class="card-row my-auto">
                                    <div class="icon-chevron">
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card card-poli mx-auto">
                            <div class="card-container">
                                <div class="card-row">
                                    <img src="<?=Url::base(true).'/media/img/icon-antrian/routing.svg';?>">
                                </div>
                                <div class="card-row" data-toggle="modal" data-target = "#modal_backdrop" action = "/antrian/dashboard/pasien-routing/">
                                    <h1 class="title-card">Pasien Routing</h1>
                                    <span class="desc-card">
                                        Pendaftaran pasien rujukan internal Rumah Sakit
                                    </span>
                                </div>
                                <div class="card-row my-auto">
                                    <div class="icon-chevron">
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-poli mx-auto">
                            <div class="card-container">
                                <div class="card-row" data-toggle="modal" data-target = "#modal_backdrop" action = "/antrian/dashboard/reservasi-non-mjkn">
                                    <h1 class="title-card">Reservasi Non - MJKN</h1>
                                    <span class="desc-card">
                                        Pendaftaran mandiri pasien reservasi dari external dan Non-MJKN
                                    </span>
                                </div>
                                <div class="card-row my-auto">
                                    <div class="icon-chevron">
                                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo Html::hiddenInput('jenis_id' , $jenis_id, ['id' => 'jenis-id']); ?>
        <?php echo Html::hiddenInput('konfig_url_cetak' , $konfig_url_cetak, ['id' => 'konfig-url-cetak']); ?>
        <button id="btn-cetak-antrian" type="submit" class="button-print stepy-finish"><i class="fa fa-print"></i> Cetak </button>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<!-- modal status pasien  -->
<div id="modal_status_pasien" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- modal rujukan rencana  -->
<div id="modal-rujukan-rencana" class="modal fade" data-backdrop="static">
  <div class="modal-dialog">
      <div class="modal-content">
      </div>
  </div>
</div>

<!-- modal pasien routing  -->
<div id="modal-konsul-pasien-routing" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- modal konfirmasi pasien  -->
<div id="modal-konfirmasi-pasien" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- modal pasien non mjkn  -->
<div id="modal-pasien-non-mjkn" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- modal pasien bpjs onsite  -->
<div id="modal-pasien-bpjs-onsite" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
    $this->registerCss($this->render('../assets/css/antrian-bpjs.css'));
    $this->registerJs("
        var baseAsset = '". Url::base(true) . '/media/img/icon-antrian/' ."';
        var fullscreen = 0;
        var channelListenerName = `bulk-register`;
        var konfigAutoDaftar = ".json_encode($konfig_auto_daftar).";
    " .$this->render('../assets/js/antrian-bpjs.js'), View::POS_END, 'js' );
    $this->registerJs($this->render('../assets/js/antrian-asuransi.js'));
?>
