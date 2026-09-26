<?php
/**
 * @Author: Sirs Developer
 * @Date:   2025-11-26
 * Extension View for SIRS Only - Pemulangan Tahanan Content for Rujukan Bantaran
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\DocoConstants;

$isSelesaiPelayanan = isset($statusPelayananBantaranId) && $statusPelayananBantaranId == DocoConstants::SELESAI_PELAYANAN_BANTARAN;

?>

<style>
.pemulangan-tahanan-wrapper {
    padding: 20px 0;
}

.pemulangan-form-group {
    margin-bottom: 20px;
}

.pemulangan-textarea {
    width: 100%;
    min-height: 150px;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.3s;
}

.pemulangan-textarea:focus {
    outline: none;
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.btn-pemulangan {
    padding: 10px 30px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 4px;
    transition: all 0.3s;
}

.btn-pemulangan:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.pemulangan-info-box {
    background-color: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 20px;
}

.pemulangan-info-box i {
    color: #856404;
    margin-right: 10px;
}

.pemulangan-info-box p {
    margin: 0;
    color: #856404;
}
</style>

<div class="panel-body">
    <div id="content-pemulangan-tahanan" class="pemulangan-tahanan-wrapper">
        <h4><i class="fa fa-sign-out"></i> Pemulangan Tahanan</h4>
        
        <div class="pemulangan-info-box">
            <i class="fa fa-info-circle fa-lg"></i>
            <p><strong>Informasi:</strong> Form ini digunakan untuk proses pemulangan tahanan. Pastikan semua data telah diisi dengan benar sebelum melakukan pemulangan.</p>
        </div>

        <form id="form-pemulangan-tahanan" data-url="<?= Url::to(['/pendaftaran/rujukan-bantaran/pemulangan-tahanan']); ?>">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()); ?>
            <?= Html::hiddenInput('rujukanbantaran_id', isset($responseBantaran['rujukanbantaran_id']) ? $responseBantaran['rujukanbantaran_id'] : ''); ?>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="pemulangan-form-group">
                        <label><strong>Catatan Pemulangan <span class="text-danger">*</span></strong></label>
                        <div style="margin-top: 8px;">
                            <textarea 
                                class="pemulangan-textarea" 
                                name="catatan_pemulangan" 
                                id="catatan_pemulangan" 
                                placeholder="Masukkan catatan pemulangan tahanan, seperti kondisi kesehatan, tindakan yang telah dilakukan, obat yang diberikan, dan saran perawatan selanjutnya..."
                                required
                                maxlength="1000"
                                <?= $isSelesaiPelayanan ? 'readonly' : '' ?>><?= $isSelesaiPelayanan && isset($responseBantaran['catatan_pemulangan']) ? htmlspecialchars($responseBantaran['catatan_pemulangan']) : '' ?></textarea>
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> Maksimal 1000 karakter
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <button type="button" id="btn-submit-pemulangan" class="btn btn-danger btn-pemulangan" <?= $isSelesaiPelayanan ? 'disabled' : '' ?>>
                            <i class="fa fa-sign-out"></i> Proses Pemulangan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$this->registerJs("
    $(document).ready(function() {
        // Handle submit pemulangan
        $('#btn-submit-pemulangan').on('click', function() {
            var catatan = $('#catatan_pemulangan').val().trim();
            
            if (catatan === '') {
                docoNotification('warning', 'Peringatan', 'Catatan pemulangan harus diisi!');
                return;
            }
            
            confirmationDialog('Apakah Anda yakin akan memproses pemulangan tahanan ini?', function(confirmed) {
                if (!confirmed) {
                    return;
                }
                
                var form = $('#form-pemulangan-tahanan');
                var url = form.data('url');
                var formData = form.serialize();
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#btn-submit-pemulangan').prop('disabled', true).html('<i class=\"fa fa-spinner fa-spin\"></i> Memproses...');
                    },
                    success: function(response) {
                        if (response.success) {
                            var message = response.message || response.metadata && response.metadata.message || 'Pemulangan tahanan berhasil diproses';
                            docoNotification('success', 'Berhasil', message);
                            setTimeout(function() {
                                // Reset form
                                $('#catatan_pemulangan').val('');
                                // Reload riwayat or redirect
                                location.reload();
                            }, 1500);
                        } else {
                            var message = response.message || response.metadata && response.metadata.message || 'Terjadi kesalahan saat memproses pemulangan';
                            docoNotification('error', 'Gagal', message);
                        }
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON || {};
                        var errorMsg = resp.message || (resp.metadata && resp.metadata.message) || 'Terjadi kesalahan pada server';
                        docoNotification('error', 'Error', errorMsg);
                    },
                    complete: function() {
                        $('#btn-submit-pemulangan').prop('disabled', false).html('<i class=\"fa fa-sign-out\"></i> Proses Pemulangan');
                    }
                });
            });
        });
    });
", View::POS_READY);
?>
