<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-13 10:16:03
 */

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
?>

<style type="text/css">
    .nav-sidebar {
        border-right: 1px solid #ddd;
        height: 100%;
        position: sticky;
        top: 0;
        padding-right: 10px;
        max-height: calc(100vh - 20px);
        overflow-y: auto;
    }

    .nav-sidebar li a {
        padding: 10px 15px;
        display: block;
    }

    .nav-sidebar li.active>a {
        background: #428bca;
        color: white;
    }
</style>

<div class='tabbable'>

    <div class="row">
        <div class="col-sm-2">
            <ul id="tab-igd" class="nav nav-pills nav-stacked nav-sidebar nav-tabs nav-tab-periksa">
                <li id="tab-formulir-triase" <?=isset($tabs['triase']) && !$tabs['triase'] ? 'class="hidden"' : ''?>>
                    <a href="#view-formulir-triase" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Formulir Triase')?>
                    </a>
                </li>
                <li id="tab-asesmen-keperawatan" >
                    <a href="#view-asesmen-keperawatan" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Asesmen Perawat')?>
                    </a>
                </li>
                <li id="tab-asesmen-medis">
                    <a href="#view-asesmen-medis" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Asesmen Dokter')?>
                    </a>
                </li>
                <li id="tab-nursing-note">
                    <a href="#view-nursing-note" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Catatan Perawat')?>
                    </a>
                </li>
                <!-- <li id="tab-asesmen-dokter">
                    <a href="#view-asesmen-dokter" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Asesmen Dokter')?>
                    </a>
                </li> -->
                <li class="active" id="tab-asesmen-dpjp">
                    <a href="#view-asesmen-dpjp" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'CPPT IGD')?>
                    </a>
                </li>
                <li id="tab-implementasi">
                    <a href="#view-implementasi" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Instruksi & Implementasi')?>
                    </a>
                </li>
                <li id="tab-retur" class="hidden">
                    <a href="#view-retur" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Retur Obat')?>
                    </a>
                </li>
                <li id="tab-partograf">
                    <a href="#view-partograf" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Partograf') ?>
                    </a>
                </li>
                <li id="tab-kesimpulan">
                    <a href="#view-kesimpulan" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Tindak Lanjut Pasien')?>
                    </a>
                </li>
                <li id="tab-resume">
                    <a href="#view-resume" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Resume')?>
                    </a>
                </li>
                <li id="tab-upload-dokumen">
                    <a href="#view-upload-dokumen" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe','Upload Dokumen')?>
                    </a>
                </li>
                <!-- <li id="tab-permintaan-makan" <?php /* echo isset($tabs['permintaanmakan']) && !$tabs['permintaanmakan'] ? 'class="hidden"' : '' */?>>
                    <a href="#view-permintaan-makan" data-toggle="tab" aria-expanded="true">
                        <?php /* echo Yii::t('fe', 'Permintaan Makan') */?>
                    </a>
                </li> Hide Tab Permintaan Makanan -->
                <li id="tab-cathlab-koroangiografi" class="cathlab-tabs <?=$cathlabTabs?> ">
                    <a href="#view-cathlab" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Cathlab Koroangiografi')?>
                    </a>
                </li>
                <li id="tab-cathlab-pci" class="cathlab-tabs <?=$cathlabTabs?> ">
                    <a href="#view-cathlab" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Cathlab PCI')?>
                    </a>
                </li>
                <li id="tab-cathlab-dsa" class="cathlab-tabs <?=$cathlabTabs?> ">
                    <a href="#view-cathlab" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Cathlab DSA')?>
                    </a>
                </li>
                <?php if(empty($showTtvTab)) : ?>
                <li id="tab-monitoring-ttv" <?= $showTtvTab ? 'class="hidden"' : '' ?>>
                    <a href="#view-monitoring-ttv" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe','Monitoring TTV')?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(empty($showEwsTab)) : ?>
                <li id="tab-monitoring-ews" <?= $showEwsTab ? 'class="hidden"' : '' ?>>
                    <a href="#view-monitoring-ews" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe','Observasi EWS')?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if(empty($showSbarTab)) : ?>
                <li id="tab-sbar" <?= $showSbarTab ? 'class="hidden"' : '' ?>>
                    <a href="#view-sbar" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe','SBAR')?>
                    </a>
                </li>
                <?php endif; ?>
                <li id="tab-surat-keterangan">
                    <a href="#view-surat-keterangan" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe', 'Surat Keterangan')?>
                    </a>
                </li>
            </ul>
        </div>
    

    <div class="col-sm-10">
        <div class="tab-content">
            <div class="tab-pane" id="view-formulir-triase">
                <div id="content-formulir-triase">  </div>
            </div>
            <div class="tab-pane" id="view-asesmen-keperawatan">
                <div id="content-asesmen-keperawatan">  </div>
            </div>
            <div class="tab-pane" id="view-asesmen-medis">
                <div id="content-asesmen-medis">  </div>
            </div>
            <div class="tab-pane" id="view-nursing-note">
                <div id="content-nursing-note">  </div>
            </div>
            <div class="tab-pane" id="view-asesmen-dokter">
                <div id="content-asesmen-dokter">  </div>
            </div>
            <div class="tab-pane active" id="view-asesmen-dpjp">
                <div id="content-asesmen-dpjp">  </div>
            </div>
            <div class="tab-pane" id="view-implementasi">
                <div id="content-implementasi">  </div>
            </div>
            <div class="tab-pane" id="view-retur">
                <div id="content-retur"></div>
            </div>
            <div class="tab-pane" id="view-kesimpulan">
                <div id="content-kesimpulan"></div>
            </div>
            <div class="tab-pane" id="view-resume">
                <div id="content-resume"></div>
            </div>
            <div class="tab-pane has-padding" id="view-upload-dokumen">
                <div id="content-upload-dokumen"></div>
            </div>
            <div class="tab-pane has-padding" id="view-partograf">
                <div id="content-partograf"></div>
            </div>
            <!-- <div class="tab-pane has-padding" id="view-permintaan-makan">
                <div id="content-permintaan-makan"></div>
            </div> Hide Tab Permintaan Makanan -->
            <div class="tab-pane" id="view-cathlab">
                <div id="content-cathlab"></div>
            </div>
            <div class="tab-pane" id="view-monitoring-ttv">
                <div id="content-monitoring-ttv"></div>
            </div>
            <div class="tab-pane" id="view-surat-keterangan">
                <div id="content-surat-keterangan"></div>
            </div>
            <div class="tab-pane" id="view-monitoring-ews">
                <div id="content-monitoring-ews"></div>
            </div>
            <div class="tab-pane" id="view-sbar">
                <div id="content-sbar"></div>
            </div>
        </div>
    </div>
    </div>
</div>

<?php
$this->registerJs('
    var pendaftaran_id = "'.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'";
    var pasien_id = "'. DocoHelpers::encrypt($data_pasien['pasien_id']).'";
    var pasienadmisi_id = "'. $data_pasien['pasien_id'] .'";
    var namaPasien = "'. $data_pasien['nama_pasien'] .'";

', View::POS_END);
$this->registerJs($this->render('js/pasien_pemeriksaan.js'), View::POS_END);
?>
