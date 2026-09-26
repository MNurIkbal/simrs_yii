<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-10 17:49:00
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat Darurat'), 'url' => ['/igd/dashboard']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$instalasi_id = DocoHelpers::encrypt(Yii::$app->docoVars->workspace('instalasi_id'));
?>
<style type="text/css">
    .row {
        padding-right: 5px;
    }

    #modal_backdrop {
        z-index: 1041 !important;
        max-height: calc(100vh);
        overflow-y: auto;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => $urlBack
                        ]
                    ],
                    // 'print' => [
                    //     'title' => Yii::t('fe', 'Rincian Tagihan'),
                    //     'icon' => 'fa fa-file-pdf-o',
                    //     'attributes' => [
                    //         'id'=>'cetak-rincian-tagihan',
                    //         'data-options' => 'link',
                    //         'data-target' => '/igd/pemeriksaan-igd/cetak-rincian-tagihan?id='.$encrytedPendaftaranId,
                    //         'target'=>'_blank'
                    //     ]
                    // ],
                    'riwayat-pasien' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Riwayat Pasien'),
                        'icon'  => 'fa fa-history',
                        'attributes' => [
                            'id'          => 'btn-riwayat-pasien',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action'      => '/igd/riwayat-pasien/history-patient?norm='.$data_pasien['no_rekam_medik'].'&instalasi='.$instalasi_id.'&modal=is_modal',true,
                        ]
                    ],
                    'terra-medik-soap' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Arsip Riwayat Pasien'),
                        'icon'  => 'fa fa-history',
                        'attributes' => [
                            'id' => 'terra-medik-soap',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/igd/riwayat-pasien/modal-history-terra-medik-soap',true,
                            'class' => isset($thirdApp['terramedik']) && !$thirdApp['terramedik'] ? 'hidden' : ''
                        ]
                    ],
                    'i-Care' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'i-Care'),
                        'icon'  => 'fa fa-info',
                        'attributes' => [
                            'id' => 'btn-icare',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/rajal/pemeriksaan/modal-icare?' . http_build_query($query_params_icare),
                            'class' => !$hasAccessIcare ? 'hidden' : (isset($iCare['url']) && !empty($iCare['url']) ? '' : 'hidden')
                        ]
                    ],
                ]) ?>

            </div>
            <div class="panel-body">
                <input type="hidden" name="cppt-id" id="edited-cppt-id" value="<?= $cpptId ?>">

                <!-- identitas pasien start -->
                <?=Yii::$app->controller->renderPartial('//cppt/__patient', [
                    'data_pasien' => $data_pasien
                ]);?>
                <!-- identitas pasien end -->

                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= Yii::$app->controller->renderPartial('pasien_pemeriksaan', [
                            'data_pasien' => $data_pasien,
                            'tabs' => $tabs,
                            'cathlabTabs' => $cathlabTabs ? '' : 'hidden',
                            'showTtvTab' => $showTtvTab ? '' : 'hidden',
                            'showEwsTab' => $showEwsTab ? '' : 'hidden',
                            'showSbarTab' => $showSbarTab ? '' : 'hidden',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-lab" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="modal-form" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        </div>
    </div>
</div>
<div id="modal-order-pemeriksaan" style="z-index: 2041 !important" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>

<div id="modal-jadwal-dokter" style="z-index: 2041 !important" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>


<div id="modal-preview" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Preview</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                <!-- <div class="overlay-preview"></div> -->
                <iframe frameborder="0" id="preview-content" style="width:100%;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>

<div id="modal-reseptur" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="modal-batal-instruksi" tabindex="-1" role="dialog" style="z-index: 1050 !important">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-preview-img" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>
<div id="modal-template-resep" class="modal fade" style="z-index: 1050 !important;">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
<div id="modal-gambar-radiologi" class="modal">
    <div class="modal-dialog modal-xl" style="height: 100%;width: 99%;margin: 2px;">
        <div class="modal-content" style=" height: 100%;">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">�</button>
                <h5 class="modal-title">Gambar Radiologi</h5>
            </div>
            <div class="modal-body" style="height: 100%;">
                <iframe  style="width: 100%; height: 95%;" src=""></iframe>
            </div>
        </div>
    </div>
</div>
<div id="modal-informasi-pasien" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<div id="modal-show-konsul" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn-konsul" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Konsul Poli</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper-konsul">
                <!-- <div class="overlay-preview"></div> -->
                <iframe frameborder="0" id="preview-content-konsul" style="width:100%;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>

<div id="modal-surat-keterangan" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="modal_berkas_pasien" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var pasien_id = "'. $data_pasien['pasien_id'] .'";
    var pasienadmisi_id = "'. $data_pasien['pasien_id'] .'";
    var namaPasien = "'. $data_pasien['nama_pasien'] .'";
    var ruanganperiksa_id = "'.$ruanganperiksa_id.'";
', View::POS_END);
$this->registerJs($this->render('js/igd.js'), View::POS_END);

?>
