<?php
/**
 * @Author: Sirs Developer
 * @Date:   2025-11-27
 * Extension View for SIRS Only - Popup Tolak Pembantaran
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\DocoConstants;
?>

<style>
    .panel-custom {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
        padding: 25px;
    }

    .form-group label {
        font-weight: 500;
        color: #333;
    }

    .form-control[readonly], .form-control[disabled] {
        background-color: #f9f9f9;
        color: #555;
    }

    .text-required {
        color: red;
    }

    .keterangan-box {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px;
        background-color: #f9f9f9;
        min-height: 100px;
        overflow-y: auto;
    }

    h4 {
        color: #2563eb;
        border-bottom: 2px solid #2563eb;
        padding-bottom: 8px;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .form-group {
        margin-top: 10px;
    }

    .section-divider {
        margin-top: 25px;
        margin-bottom: 15px;
    }

    #submit_tolak_bantaran, 
    #submit_tolak_bantaran:hover, 
    #submit_tolak_bantaran:focus {
        background-color: #CF212A !important;
        border-color: #CF212A !important;
    }
    
    .penolakan-textarea {
        width: 100%;
        min-height: 120px;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
        transition: border-color 0.3s;
    }

    .penolakan-textarea:focus {
        outline: none;
        border-color: #CF212A;
        box-shadow: 0 0 0 0.2rem rgba(207, 33, 42, 0.25);
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Tolak Pembantaran</h5>
</div>

<div class="modal-body">
    <!-- INFORMASI PEMBANTARAN -->
    <h4 class="section-divider">Informasi Pembantaran</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" value="<?= isset($responseBantaran['nama_pasien']) ? $responseBantaran['nama_pasien'] : '-' ?>" readonly>
            </div>

            <div class="form-group">
                <label>No. Tahanan</label>
                <input type="text" class="form-control" value="<?= isset($responseBantaran['no_tahanan']) ? $responseBantaran['no_tahanan'] : '-' ?>" readonly>
            </div>

            <div class="form-group">
                <label>No. Rujukan Bantaran</label>
                <input type="text" class="form-control" value="<?= isset($responseBantaran['no_rujukanbantaran']) ? $responseBantaran['no_rujukanbantaran'] : '-' ?>" readonly>
            </div>

            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control" value="<?= isset($responseBantaran['no_identitas_pasien']) ? $responseBantaran['no_identitas_pasien'] : '-' ?>" readonly>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <input type="text" class="form-control" value="<?= isset($responseBantaran['jenis_kelamin']) && $responseBantaran['jenis_kelamin'] == DocoConstants::LOOKUP_LAKI ? 'Laki-laki' : 'Perempuan' ?>" readonly>
            </div>

            <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" class="form-control" value="<?= isset($responseBantaran['tempat_lahir']) ? $responseBantaran['tempat_lahir'] : '-' ?>" readonly>
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <?php 
                    $tgl_lahir = isset($responseBantaran['tgl_lahir']) ? date('d M Y', strtotime($responseBantaran['tgl_lahir'])) : '-';
                    $umur = isset($responseBantaran['tgl_lahir']) ? DocoHelpers::getUmur($responseBantaran['tgl_lahir'], true) : 0;
                ?>
                <input type="text" class="form-control" value="<?= $tgl_lahir ?> (<?= $umur ?> tahun)" readonly>
            </div>

            <div class="form-group">
                <label>UPT Asal</label>
                <input type="text" class="form-control" value="<?= isset($responseBantaran['uptasal_nama']) ? $responseBantaran['uptasal_nama'] : '-' ?>" readonly>
            </div>
        </div>
    </div>

    <!-- KETERANGAN RUJUKAN -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Keterangan Rujukan</label>
                <div class="keterangan-box">
                    <?= isset($responseBantaran['keterangan_rujukan']) ? nl2br(Html::encode($responseBantaran['keterangan_rujukan'])) : '-' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- DOKUMEN LAMPIRAN -->
    <?php if(!empty($responseDokumenBantaran)): ?>
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-12">
                <label>Dokumen Lampiran</label>
                <div class="dokumen-list">
                    <?php foreach($responseDokumenBantaran as $key => $value): ?>
                        <p>
                            <i class="fa fa-file-pdf-o text-danger"></i>
                            <a href="<?= $value['url_dokumen']; ?>" target="_blank" rel="noopener noreferrer">
                                <?= Html::encode($value['nama_dokumen']); ?>
                            </a>
                        </p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- INFORMASI PENOLAKAN -->
    <h4 class="section-divider">Informasi Penolakan</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Keterangan Penolakan <span class="text-required">*</span></label>
                <div style="margin-top: 8px;">
                    <textarea 
                        class="penolakan-textarea" 
                        name="keterangan_tolak_rujukan" 
                        id="keterangan_tolak_rujukan" 
                        placeholder="Masukkan alasan penolakan pembantaran..."
                        required
                        maxlength="500"></textarea>
                    <small class="text-muted">
                        <i class="fa fa-info-circle"></i> Maksimal 500 karakter
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default btn-labeled btn-xs" data-dismiss="modal">
        <b style="background-color: #6c757d;"><i class="fa fa-times"></i></b> Batal
    </button>
    <button type="button" class="btn btn-info btn-labeled btn-xs" id="submit_tolak_bantaran">
        <b>
            <i class="fa fa-ban"></i>
        </b>
        Tolak Pembantaran
    </button>
</div>

<script type="text/javascript">
    var bantaran_id = '<?= $responseBantaran['rujukanbantaran_id'] ?>';

    $(document).ready(function() {
        $('#submit_tolak_bantaran').click(function() {
            var keterangan_tolak = $('#keterangan_tolak_rujukan').val().trim();

            if (keterangan_tolak === '') {
                docoNotification('warning', 'Peringatan', 'Keterangan penolakan harus diisi!');
                return;
            }

            confirmationDialog('Apakah Anda yakin akan menolak pembantaran ini?', function(confirmed) {
                if (!confirmed) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '<?= Url::to(['/pendaftaran/rujukan-bantaran/reject-bantaran']) ?>',
                    data: {
                        bantaran_id: bantaran_id,
                        keterangan_tolak_rujukan: keterangan_tolak
                    },
                    dataType: 'JSON',
                    beforeSend: function() {
                        $('#submit_tolak_bantaran').prop('disabled', true).html('<b><i class="fa fa-spinner fa-spin"></i></b> Memproses...');
                    },
                    success: function (res) {
                        var message = res.message || 'Pembantaran berhasil ditolak.';
                        docoNotification('success', 'Berhasil', message);

                        setTimeout(function() { 
                            window.location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        var resp = xhr.responseJSON || {};
                        var message = resp.message || 'Terjadi kesalahan saat memproses penolakan.';
                        docoNotification('error', 'Gagal', message);
                    },
                    complete: function() {
                        $('#submit_tolak_bantaran').prop('disabled', false).html('<b><i class="fa fa-ban"></i></b> Tolak Pembantaran');
                    }
                });
            });
        });
    });
</script>
