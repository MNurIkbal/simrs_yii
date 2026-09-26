<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use app\components\DocoConstants;

$bantaran = $responseBantaran;
$dokumen = $responseDokumenBantaran;
$uploadUrl = Url::to(['/pendaftaran/rujukan-bantaran/upload-dokumen']);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Kirim Dokumen Pendukung</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="well well-sm">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>No. Rujukan:</strong> <?= Html::encode(ArrayHelper::getValue($bantaran, 'no_rujukanbantaran', '-')); ?></p>
                        <p><strong>Nama Pasien:</strong> <?= Html::encode(ArrayHelper::getValue($bantaran, 'nama_pasien', '-')); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>UPT Asal:</strong> <?= Html::encode(ArrayHelper::getValue($bantaran, 'uptasal_nama', '-')); ?></p>
                        <p><strong>Tgl Kunjungan:</strong> <?= Html::encode(!empty($bantaran['tgl_kunjungan']) ? date('d M Y', strtotime($bantaran['tgl_kunjungan'])) : '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="form-upload-dokumen" enctype="multipart/form-data" data-url="<?= $uploadUrl; ?>">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()); ?>
        <?= Html::hiddenInput('rujukanbantaran_id', ArrayHelper::getValue($bantaran, 'rujukanbantaran_id')); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Dokumen</label>
                    <input type="text" class="form-control" name="nama_dokumen" id="nama_dokumen" placeholder="Contoh: Surat Pengantar" maxlength="200">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Berkas (PDF / JPG / PNG)</label>
                    <input type="file" class="form-control" name="dokumen_file" id="dokumen_file" accept=".pdf,image/*" required>
                    <small class="text-muted">Maksimal ukuran mengikuti konfigurasi server.</small>
                </div>
            </div>
        </div>
    </form>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Dokumen Terkirim</h6>
        </div>
        <div class="panel-body">
            <?php if (empty($dokumen)) : ?>
                <p class="text-muted">Belum ada dokumen yang dikirim.</p>
            <?php else : ?>
                <ul class="list-unstyled list-dokumen-terkirim">
                    <?php foreach ($dokumen as $doc) : 
                        $docId = ArrayHelper::getValue($doc, 'dokumenrujukanbantaran_id', ArrayHelper::getValue($doc, 'dokumenmedisbantaran_id'));
                    ?>
                        <li>
                            <i class="fa fa-file-o text-primary"></i>
                            <a href="<?= Html::encode($doc['url_dokumen']); ?>" target="_blank" rel="noopener noreferrer" data-id="<?= Html::encode($docId); ?>">
                                <?= Html::encode($doc['nama_dokumen']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-labeled btn-xs" data-dismiss="modal"><b><i class="fa fa-ban"></i></b>Tutup</button>
    <button type="button" class="btn btn-primary btn-labeled btn-xs" id="btn-submit-dokumen">
        <b><i class="fa fa-upload"></i></b> Kirim Dokumen
    </button>
</div>

<script>
(function() {
    var form = $('#form-upload-dokumen');
    var submitBtn = $('#btn-submit-dokumen');
    var namaInput = $('#nama_dokumen');
    var fileInput = $('#dokumen_file');

    console.log('Modal JS loaded. Form:', form, 'Button:', submitBtn);

    // Auto-fill nama dokumen from file name
    fileInput.on('change', function() {
        var file = this.files[0];
        if (file && !namaInput.val()) {
            var baseName = file.name.replace(/\.[^/.]+$/, '');
            namaInput.val(baseName);
        }
    });

    // Handle form submission
    submitBtn.on('click', function() {
        console.log('Button clicked!');
        console.log('Form element:', form[0]);
        console.log('Form validity:', form[0].checkValidity());
        
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        var formData = new FormData(form[0]);
        
        // Log FormData contents
        console.log('FormData contents:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ':', pair[1]);
        }
        console.log('Upload URL:', form.data('url'));
        
        submitBtn.prop('disabled', true).html('<b><i class="fa fa-spinner fa-spin"></i></b> Mengirim...');

        $.ajax({
            url: form.data('url'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Success response:', response);
                var message = response.message || (response.metadata && response.metadata.message) || 'Dokumen berhasil dikirim.';
                docoNotification('success', 'Berhasil', message);
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                console.error('Error response:', xhr);
                console.error('Response JSON:', xhr.responseJSON);
                var resp = xhr.responseJSON || {};
                var message = resp.message || (resp.response && resp.response.text) || (resp.metadata && resp.metadata.message) || 'Dokumen gagal dikirim.';
                docoNotification('error', 'Gagal', message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<b><i class="fa fa-upload"></i></b> Kirim Dokumen');
            }
        });
    });
})();
</script>
