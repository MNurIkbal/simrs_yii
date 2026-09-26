<?php
/**
 * @Author: Sirs Developer
 * @Date:   2025-11-24
 * Extension View for SIRS Only - Periksa Rujukan Bantaran
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rujukan Bantaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    #modal_backdrop {
        z-index: 1041 !important;
        max-height: calc(100vh);
        overflow-y: auto;
    }
    
    .patient-info-box {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    
    .patient-info-box .panel-heading {
        background-color: #f5f5f5;
        padding: 10px 15px;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        border-radius: 5px 5px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .patient-info-box .panel-heading:hover {
        background-color: #e9ecef;
    }
    
    .patient-info-box .panel-heading h4 {
        margin: 0;
        color: #2563eb;
        font-weight: 600;
    }
    
    .patient-info-box .panel-body {
        padding: 15px;
    }
    
    .patient-info-box .info-row {
        margin-bottom: 10px;
    }
    
    .patient-info-box .info-label {
        font-weight: bold;
        width: 180px;
        display: inline-block;
    }
    
    .keterangan-box {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px;
        background-color: #f9f9f9;
        min-height: 80px;
        max-height: 150px;
        overflow-y: auto;
    }
    
    .dokumen-list {
        margin-top: 10px;
    }
    
    .dokumen-list a {
        color: #16a34a;
        text-decoration: none;
    }
    
    .dokumen-list a:hover {
        text-decoration: underline;
    }
    
    .chevron-icon {
        transition: transform 0.3s ease;
    }
    
    .chevron-icon.collapsed {
        transform: rotate(-90deg);
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
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
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
                            'href' => Url::to(['index'])
                        ]
                    ],
                ]) ?>
            </div>
            
            <div class="panel-body">
                <!-- INFORMASI PASIEN -->
                <div class="patient-info-box">
                    <a data-toggle="collapse" href="#info-pasien-collapse" role="button" aria-expanded="true" aria-controls="info-pasien-collapse">
                        <div class="panel-heading">
                            <h4>
                                <strong>Informasi Pasien</strong> | 
                                <span><?= Html::encode(isset($dataPasien['no_rujukanbantaran']) ? $dataPasien['no_rujukanbantaran'] : '-') ?></span> - 
                                <span><?= Html::encode(isset($dataPasien['nama_pasien']) ? $dataPasien['nama_pasien'] : '-') ?></span>
                            </h4>
                            <i class="fa fa-chevron-down chevron-icon"></i>
                        </div>
                    </a>
                    <div class="panel-body collapse in" id="info-pasien-collapse">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">No. Rujukan Bantaran:</span>
                                    <span><?= Html::encode(isset($dataPasien['no_rujukanbantaran']) ? $dataPasien['no_rujukanbantaran'] : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">No. Tahanan:</span>
                                    <span><?= Html::encode(isset($dataPasien['no_tahanan']) ? $dataPasien['no_tahanan'] : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Nama Tahanan:</span>
                                    <span><?= Html::encode(isset($dataPasien['nama_pasien']) ? $dataPasien['nama_pasien'] : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">NIK:</span>
                                    <span><?= Html::encode(isset($dataPasien['no_identitas_pasien']) ? $dataPasien['no_identitas_pasien'] : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tujuan Pemeriksaan:</span>
                                    <span><?= Html::encode(isset($dataPasien['tujuan_pemeriksaan']) && !empty($dataPasien['tujuan_pemeriksaan']) ? $dataPasien['tujuan_pemeriksaan'] : '-') ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Jenis Kelamin:</span>
                                    <span><?= Html::encode(isset($dataPasien['jenis_kelamin']) ? ($dataPasien['jenis_kelamin'] == 1 ? 'Laki-laki' : 'Perempuan') : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tempat, Tanggal Lahir:</span>
                                    <?php 
                                        $tempat_lahir = isset($dataPasien['tempat_lahir']) ? $dataPasien['tempat_lahir'] : '-';
                                        $tgl_lahir = isset($dataPasien['tgl_lahir']) ? date('d M Y', strtotime($dataPasien['tgl_lahir'])) : '-';
                                    ?>
                                    <span><?= Html::encode($tempat_lahir . ', ' . $tgl_lahir) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">UPT Asal:</span>
                                    <span><?= Html::encode(isset($dataPasien['uptasal_nama']) ? $dataPasien['uptasal_nama'] : '-') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Keterangan Rujukan:</span>
                                </div>
                                <div class="keterangan-box">
                                    <?= Html::encode(isset($dataPasien['keterangan_rujukan']) ? $dataPasien['keterangan_rujukan'] : '-') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Lampiran Dokumen:</span>
                                </div>
                                <div class="dokumen-list">
                                    <?php if(!empty($responseDokumenBantaran)): ?>
                                        <?php foreach($responseDokumenBantaran as $key => $value): ?>
                                            <p>
                                                <i class="fa fa-file-pdf-o text-danger"></i>
                                                <a href="<?= $value['url_dokumen']; ?>" target="_blank" rel="noopener noreferrer">
                                                    <?= Html::encode($value['nama_dokumen']); ?>
                                                </a>
                                            </p>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted"><em>Tidak ada lampiran dokumen</em></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFORMASI KUNJUNGAN -->
                <div class="patient-info-box">
                    <a data-toggle="collapse" href="#info-kunjungan-collapse" role="button" aria-expanded="true" aria-controls="info-kunjungan-collapse">
                        <div class="panel-heading">
                            <h4>
                                <strong>Informasi Kunjungan</strong> | 
                                <span><?= Html::encode(isset($dataPasien['instalasi_nama']) ? $dataPasien['instalasi_nama'] : '-') ?></span> - 
                                <span><?= Html::encode(isset($dataPasien['dokter_nama']) ? $dataPasien['dokter_nama'] : '-') ?></span> | 
                                <span><?= Html::encode(isset($dataPasien['status_pelayanan_bantaran']) ? $dataPasien['status_pelayanan_bantaran'] : '-') ?></span>
                            </h4>
                            <i class="fa fa-chevron-down chevron-icon"></i>
                        </div>
                    </a>
                    <div class="panel-body collapse in" id="info-kunjungan-collapse">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Tanggal Kunjungan:</span>
                                    <span><?= Html::encode(isset($dataPasien['tgl_kunjungan']) ? date('d M Y', strtotime($dataPasien['tgl_kunjungan'])) : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Instalasi:</span>
                                    <span><?= Html::encode(isset($dataPasien['instalasi_nama']) ? $dataPasien['instalasi_nama'] : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Ruangan:</span>
                                    <span><?= Html::encode(isset($dataPasien['ruangan_nama']) ? $dataPasien['ruangan_nama'] : '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Dokter:</span>
                                    <span><?= Html::encode(isset($dataPasien['dokter_nama']) ? $dataPasien['dokter_nama'] : '-') ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">Rencana Tindakan:</span>
                                </div>
                                <div class="keterangan-box">
                                    <?= Html::encode(isset($dataPasien['rencana_tindakan']) && !empty($dataPasien['rencana_tindakan']) ? $dataPasien['rencana_tindakan'] : '-') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#view-cppt" aria-controls="view-cppt" role="tab" data-toggle="tab">
                                    <i class="fa fa-stethoscope"></i> CPPT
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#view-monitoring-ttv" aria-controls="view-monitoring-ttv" role="tab" data-toggle="tab">
                                    <i class="fa fa-heartbeat"></i> Monitoring TTV
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#view-upload-dokumen" aria-controls="view-upload-dokumen" role="tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> Upload Dokumen
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#view-pemulangan-tahanan" aria-controls="view-pemulangan-tahanan" role="tab" data-toggle="tab">
                                    <i class="fa fa-sign-out"></i> Pemulangan Tahanan
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="view-cppt">
                                <?= $this->render('_soap-content', [
                                    'responseBantaran' => $responseBantaran,
                                    'statusPelayananBantaranId' => isset($statusPelayananBantaranId) ? $statusPelayananBantaranId : null,
                                ]) ?>
                            </div>
                            
                            <div role="tabpanel" class="tab-pane" id="view-monitoring-ttv">
                                <?= $this->render('_monitoring-ttv-content', [
                                    'pendaftaranId' => isset($pendaftaranId) ? $pendaftaranId : null,
                                    'pendaftaranIdDecrypt' => isset($pendaftaranIdDecrypt) ? $pendaftaranIdDecrypt : null,
                                    'jenisTtv' => isset($jenisTtv) ? $jenisTtv : [],
                                    'tingkatKesadaran' => isset($tingkatKesadaran) ? $tingkatKesadaran : [],
                                    'statusPelayananBantaranId' => isset($statusPelayananBantaranId) ? $statusPelayananBantaranId : null,
                                ]) ?>
                            </div>
                            
                            <div role="tabpanel" class="tab-pane" id="view-upload-dokumen">
                                <?= $this->render('_upload-dokumen-content', [
                                    'responseBantaran' => $responseBantaran,
                                    'responseDokumenMedis' => $responseDokumenMedis,
                                    'statusPelayananBantaranId' => isset($statusPelayananBantaranId) ? $statusPelayananBantaranId : null,
                                ]) ?>
                            </div>
                            
                            <div role="tabpanel" class="tab-pane" id="view-pemulangan-tahanan">
                                <?= $this->render('_pemulangan-tahanan-content', [
                                    'responseBantaran' => $responseBantaran,
                                    'statusPelayananBantaranId' => isset($statusPelayananBantaranId) ? $statusPelayananBantaranId : null,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_backdrop" class="modal fade" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
$this->registerJs("
    // Handle hash navigation for tabs
    $(document).ready(function() {
        var hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href=\"' + hash + '\"]').tab('show');
        }
        
        // Update hash on tab change
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
        
        // Handle collapsible chevron rotation
        $('[data-toggle=\"collapse\"]').on('click', function() {
            $(this).find('.chevron-icon').toggleClass('collapsed');
        });
    });
", View::POS_READY);
?>
