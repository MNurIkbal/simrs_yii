<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 16:31:06
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-26 11:10:19
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoConstants;
$cpptClass = "";
$anamnesa = "active";
if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS){
    $cpptClass = "active";
    $anamnesa = "";
}

?>
<style>
#tab-permintaankonsul{
    display:<?= $cekDataKonsul ?>;
}

.nav-sidebar > li {
    float: none !important;
    width: 100%;
}
.nav-sidebar > li > a {
    display: block;
    width: 100%;
}
</style>
<div class="tabbable" style="position: relative">
    <div class="row">
        <div class="col-sm-2">
            <ul class="nav nav-pills nav-tabs nav-stacked nav-tab-cppt nav-sidebar nav-tab-periksa">
                <li class="active"><a id="tab-anamnesa" data-toggle="tab" href="#view-anamnesa">Asesmen Perawat</a></li>
                <li><a id="tab-periksafisik" data-toggle="tab" href="#view-periksafisik">Asesmen Dokter</a></li>
                <li><a id="tab-nursingnote" data-toggle="tab" href="#view-nursingnote">Catatan Perawat</a></li>
                <li><a id="tab-cppt" data-toggle="tab" href="#view-cppt">CPPT</a></li>
                <li class="<?= isset($tabs['diagnosa']) && !$tabs['diagnosa'] ? 'hidden' : '' ?>"><a id="tab-diagnosa" data-toggle="tab" href="#view-diagnosa">Diagnosa</a></li>
                <li><a id="tab-resume" data-toggle="tab" href="#view-resume">Resume</a></li>
                <li><a id="tab-upload-dokumen" data-toggle="tab" href="#view-upload-dokumen">Upload Dokumen</a></li>
                <li><a id="tab-rujukanpasien" data-toggle="tab" href="#view-rujukanpasien">Rujukan Pasien</a></li>
                <li><a id="tab-permintaankonsul" data-toggle="tab" href="#view-permintaankonsul">Permintaan Konsul</a></li>
                <li class="<?= isset($patientData['jenis']) && $patientData['jenis'] == 'MCU' ? '' : 'hidden' ?>"><a id="tab-pemeriksaan-mcu" data-toggle="tab" href="#view-pemeriksaan-mcu">Pemeriksaan MCU</a></li>
                <li class="cathlab-tabs <?=$cathlabTabs?>"><a id="tab-cathlab-koroangiografi" data-toggle="tab" href="#view-cathlab">Cathlab Koroangiografi</a></li>
                <li class="cathlab-tabs <?=$cathlabTabs?>"><a id="tab-cathlab-pci" data-toggle="tab" href="#view-cathlab">Cathlab PCI</a></li>
                <li class="cathlab-tabs <?=$cathlabTabs?>"><a id="tab-cathlab-dsa" data-toggle="tab" href="#view-cathlab">Cathlab DSA</a></li>
                <li class="rujuk-balik <?= isset($patientData['rujukan_id']) && isset($patientData['bpjs_id']) && !empty($patientData['rujukan_id']) && !empty($patientData['bpjs_id']) ? '' : 'hidden' ?>">
                    <a id="tab-rujuk-balik" data-toggle="tab" href="#view-rujuk-balik">Rujuk Balik</a>
                </li>
                <li><a id="tab-surat-keterangan" data-toggle="tab" href="#view-surat-keterangan">Surat Keterangan</a></li>
                <?php if(empty($showTtvTab)) : ?>
                <li class="<?=$showTtvTab?>">
                    <a id="tab-monitoring-ttv" data-toggle="tab" href="#view-monitoring-ttv">Monitoring TTV</a>
                </li>
                <?php endif; ?>

                <?php if(empty($showEwsTab)) : ?>
                <li class="<?=$showEwsTab?>">
                    <a id="tab-monitoring-ews" data-toggle="tab" href="#view-monitoring-ews">Observasi EWS</a>
                </li>
                <?php endif; ?>

                <?php if(empty($showSbarTab)) : ?>
                <li class="<?=$showSbarTab?>">
                    <a id="tab-sbar" data-toggle="tab" href="#view-sbar">SBAR</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="col-sm-10">
            <div class="tab-content">
                <div class="tab-pane" id="view-anamnesa">
                    <div id="content-anamnesa">  </div>
                </div>
                <div class="tab-pane" id="view-periksafisik">
                    <div id="content-periksafisik">  </div>
                </div>
                <div class="tab-pane" id="view-nursingnote">
                    <div id="content-nursingnote">  </div>
                </div>
                <div class="tab-pane" id="view-cppt">
                    <div id="content-cppt">  </div>
                </div>
                <div class="tab-pane" id="view-diagnosa">
                    <div id="content-diagnosa" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-tindakan">
                    <div id="content-tindakan" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-reseptur">
                    <div id="content-reseptur" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-penunjang">
                    <div id="content-penunjang" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-rehabmedis">
                    <div id="content-rehabmedis" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-rujukanpasien">
                    <div id="content-rujukanpasien" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-pembebasantarif">
                    <div id="content-pembebasantarif" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-konsulpoli">
                    <div id="content-konsulpoli" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-permintaankonsul">
                    <div id="content-permintaankonsul" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-resume">
                    <div id="content-resume" class="col-lg-12">  </div>
                </div>
                <div class="tab-pane" id="view-upload-dokumen">
                    <div id="content-upload-dokumen" class="col-lg-12"></div>
                </div>
                <div class="tab-pane" id="view-pemeriksaan-mcu">
                    <div id="content-pemeriksaan-mcu" class="col-lg-12">  </div>
                </div>
                <!-- <div class="tab-pane" id="view-permintaanmakan">
                    <div id="content-permintaanmakan" class="col-lg-12">  </div>
                </div> -->
                <div class="tab-pane" id="view-cathlab">
                    <div id="content-cathlab"></div>
                </div>
                <div class="tab-pane" id="view-rujuk-balik">
                    <div id="content-rujuk-balik"></div>
                </div>
                <div class="tab-pane" id="view-surat-keterangan">
                    <div id="content-surat-keterangan"></div>
                </div>
                <div class="tab-pane" id="view-monitoring-ttv">
                    <div id="content-monitoring-ttv"></div>
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
