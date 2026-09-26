<?php
/**
 * @Author: Sirs Developer
 * @Date:   2025-11-25
 * Extension View for SIRS Only - Upload Dokumen Content for Rujukan Bantaran
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\DocoConstants;

$isSelesaiPelayanan = isset($statusPelayananBantaranId) && $statusPelayananBantaranId == DocoConstants::SELESAI_PELAYANAN_BANTARAN;
?>

<style>
.file-upload-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 30%;
    float: left;
}

.file-upload-wrapper input[type=file] {
    position: absolute;
    left: -9999px;
}

.file-upload-label {
    display: block;
    padding: 10px 15px;
    background-color: #2196F3;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    transition: background-color 0.3s;
    font-weight: 500;
}

.file-upload-label:hover {
    background-color: #1976D2;
}

.file-upload-label i {
    margin-right: 8px;
}

.file-name-display {
    width: 70%;
    float: left;
    margin-left: 0;
    padding: 10px 12px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 13px;
    color: #555;
    min-height: 42px;
    display: flex;
    align-items: center;
}

.file-name-display.has-file {
    background-color: #e3f2fd;
    border-color: #2196F3;
    color: #1976D2;
}

.file-name-display i {
    margin-right: 8px;
}

.file-upload-container {
    display: flex;
    gap: 10px;
    width: 100%;
}
</style>

<div class="panel-body">
    <div id="content-upload-dokumen">
        <h4>Upload Dokumen</h4>

        <form id="form-upload-dokumen" enctype="multipart/form-data" data-url="<?= Url::to(['/pendaftaran/rujukan-bantaran/upload-dokumen']); ?>">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()); ?>
            <?= Html::hiddenInput('rujukanbantaran_id', isset($responseBantaran['rujukanbantaran_id']) ? $responseBantaran['rujukanbantaran_id'] : ''); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Nama Dokumen</strong></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <input type="text" class="form-control" name="nama_dokumen" id="nama_dokumen" placeholder="Contoh: Surat Pengantar" maxlength="200">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Berkas (PDF / JPG / PNG)</strong></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <div class="file-upload-container">
                                <div class="file-upload-wrapper">
                                    <input type="file" name="dokumen_file" id="dokumen_file" accept=".pdf,image/*" required>
                                    <label for="dokumen_file" class="file-upload-label">
                                        <i class="fa fa-cloud-upload"></i> Pilih File
                                    </label>
                                </div>
                                <div class="file-name-display" id="file-name-display">
                                    <i class="fa fa-file-o"></i>
                                    <span>Belum ada file dipilih</span>
                                </div>
                            </div>
                            <div style="clear: both;"></div>
                            <small class="text-muted" style="display: block; margin-top: 5px;">Maksimal ukuran mengikuti konfigurasi server.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary btn-labeled" id="btn-submit-dokumen">
                        <b><i class="fa fa-upload"></i></b> Kirim Dokumen
                    </button>
                </div>
            </div>
        </form>

        <hr>

        <div class="panel panel-default" style="margin-top: 20px;">
            <div class="panel-heading">
                <h5 class="panel-title">Dokumen Terkirim</h5>
            </div>
            <div class="panel-body">
                <?php if (empty($responseDokumenMedis)) : ?>
                    <p class="text-muted">Belum ada dokumen yang dikirim.</p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="5%">No</th>
                                    <th>Nama Dokumen</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($responseDokumenMedis as $index => $doc) : 
                                    $docId = isset($doc['dokumenrujukanbantaran_id']) ? $doc['dokumenrujukanbantaran_id'] : (isset($doc['dokumenmedisbantaran_id']) ? $doc['dokumenmedisbantaran_id'] : '');
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <i class="fa fa-file-pdf-o text-danger"></i>
                                            <?= Html::encode($doc['nama_dokumen']) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= Html::encode($doc['url_dokumen']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-info" title="Lihat">
                                                <i class="fa fa-eye"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $(document).ready(function() {
        // Upload dokumen functionality
        var form = $('#form-upload-dokumen');
        var submitBtn = $('#btn-submit-dokumen');
        var namaInput = $('#nama_dokumen');
        var fileInput = $('#dokumen_file');

        // Auto-fill nama dokumen from file name
        fileInput.on('change', function() {
            var file = this.files[0];
            var fileDisplay = $('#file-name-display');
            
            if (file) {
                // Update file display
                var fileName = file.name;
                var fileSize = (file.size / 1024).toFixed(2); // KB
                var fileSizeText = fileSize > 1024 ? (fileSize / 1024).toFixed(2) + ' MB' : fileSize + ' KB';
                
                fileDisplay.addClass('has-file');
                fileDisplay.html('<i class=\"fa fa-file-text-o\"></i> <span>' + fileName + ' (' + fileSizeText + ')</span>');
                
                // Auto-fill nama dokumen if empty
                if (!namaInput.val()) {
                    var baseName = fileName.replace(/\.[^/.]+$/, '');
                    namaInput.val(baseName);
                }
            } else {
                fileDisplay.removeClass('has-file');
                fileDisplay.html('<i class=\"fa fa-file-o\"></i> <span>Belum ada file dipilih</span>');
            }
        });

        // Handle form submission
        submitBtn.on('click', function() {
            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }

            var formData = new FormData(form[0]);
            
            submitBtn.prop('disabled', true).html('<b><i class=\"fa fa-spinner fa-spin\"></i></b> Mengirim...');

            $.ajax({
                url: form.data('url'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var message = response.message || (response.metadata && response.metadata.message) || 'Dokumen berhasil dikirim.';
                    docoNotification('success', 'Berhasil', message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    var resp = xhr.responseJSON || {};
                    var message = resp.message || (resp.response && resp.response.text) || (resp.metadata && resp.metadata.message) || 'Dokumen gagal dikirim.';
                    docoNotification('error', 'Gagal', message);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<b><i class=\"fa fa-upload\"></i></b> Kirim Dokumen');
                }
            });
        });
    });
", View::POS_READY);
?>
