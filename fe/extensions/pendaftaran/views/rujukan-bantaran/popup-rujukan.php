<?php
// Author : Naufal Ziyad L
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
?>

<style>
    body {
        background-color: #f9fafb;
        padding: 30px;
    }

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

    .dokumen-list a {
        color: #16a34a;
        text-decoration: none;
    }

    .dokumen-list a:hover {
        text-decoration: underline;
    }

    h3, h4 {
        margin-top: 0;
        font-weight: 600;
    }
    
    h4 {
        color: #2563eb;
        border-bottom: 2px solid #2563eb;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }
    
    .form-group {
        margin-top: 10px;
    }

    .section-divider {
        margin-top: 25px;
        margin-bottom: 15px;
    }

    #tolak_bantaran, 
    #tolak_bantaran:hover, 
    #tolak_bantaran:focus {
        background-color: #CF212A !important;
        border-color: #CF212A !important;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <!-- INFORMASI TAHANAN -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h4 class="section-divider" style="margin-bottom: 0;">Informasi Pembantaran</h4>
        <?php if(isset($isVerifikasiRujukan)): ?>
            <button type="button" class="btn btn-info btn-labeled btn-xs" id="tolak_bantaran">
                <b>
                    <i class="fa fa-ban"></i>
                </b>
                Tolak Pembantaran
            </button>
        <?php endif; ?>
    </div>
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

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Keterangan Rujukan</label>
                <div class="keterangan-box">
                    <?= isset($responseBantaran['keterangan_rujukan']) ? $responseBantaran['keterangan_rujukan'] : '-' ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Tujuan Pemeriksaan</label>
                <div class="keterangan-box">
                    <?= isset($responseBantaran['tujuan_pemeriksaan']) && !empty($responseBantaran['tujuan_pemeriksaan']) ? $responseBantaran['tujuan_pemeriksaan'] : '-' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- DOKUMEN PENYERTA -->
    <h4 class="section-divider">Dokumen Penyerta</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="dokumen-list">
                <?php if(!empty($responseDokumenBantaran)): ?>
                    <?php foreach($responseDokumenBantaran as $key => $value) { 
                        $dokumenId = ArrayHelper::getValue($value, 'dokumenrujukanbantaran_id', ArrayHelper::getValue($value, 'dokumenmedisbantaran_id'));
                    ?>
                        <p>
                            <i class="fa fa-file-pdf-o text-danger"></i>
                            <a href="<?= $value['url_dokumen']; ?>" target="_blank" rel="noopener noreferrer" dokumen-id="<?= $dokumenId; ?>">
                                <?= $value['nama_dokumen']; ?>
                            </a>
                        </p>
                    <?php } ?>
                <?php else: ?>
                    <p class="text-muted"><em>Tidak ada dokumen penyerta</em></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(isset($responseBantaran['status_verifikasi_bantaran_id']) && $responseBantaran['status_verifikasi_bantaran_id'] == DocoConstants::TOLAK_VERFIKASI_BANTARAN): ?>
    <!-- PERMINTAAN DITOLAK -->
    <h4 class="section-divider" style="color: #CF212A; border-bottom-color: #CF212A;">Permintaan Ditolak</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label style="color: #CF212A;">Alasan Ditolak</label>
                <div class="keterangan-box" style="border-color: #CF212A; background-color: #fff5f5;">
                    <?= isset($responseBantaran['keterangan_penolakan_rujukan']) && !empty($responseBantaran['keterangan_penolakan_rujukan']) ? $responseBantaran['keterangan_penolakan_rujukan'] : '-' ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- INFORMASI PERAWATAN -->
    <?php if(!isset($responseBantaran['status_verifikasi_bantaran_id']) || $responseBantaran['status_verifikasi_bantaran_id'] != DocoConstants::TOLAK_VERFIKASI_BANTARAN): ?>
    <h4 class="section-divider">Informasi Perawatan</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Instalasi <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <input type="text" class="form-control" id="instalasi_nama" name="instalasi_nama">
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= isset($responseBantaran['instalasi_nama']) ? $responseBantaran['instalasi_nama'] : '-' ?>" readonly>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Ruangan <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <input type="text" class="form-control" id="ruangan_nama" name="ruangan_nama">
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= isset($responseBantaran['ruangan_nama']) ? $responseBantaran['ruangan_nama'] : '-' ?>" readonly>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Tanggal Kunjungan <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <input type="text" class="form-control pickadate" id="tgl_rujukan" value="<?= date('Y-m-d'); ?>">
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= isset($responseBantaran['tgl_kunjungan']) ? date('d M Y', strtotime($responseBantaran['tgl_kunjungan'])) : '-' ?>">
                <?php endif;?>
            </div>

            <div class="form-group">
                <label>Dokter <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <input type="text" class="form-control" id="dokter_nama" name="dokter_nama">
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= isset($responseBantaran['dokter_nama']) ? $responseBantaran['dokter_nama'] : '-' ?>" readonly>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Rencana Tindakan</label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <textarea class="form-control" id="rencana_tindakan" name="rencana_tindakan" rows="4" maxlength="1000" placeholder="Masukkan rencana tindakan (maksimal 1000 karakter)"></textarea>
                <?php else: ?>
                    <div class="keterangan-box">
                        <?= isset($responseBantaran['rencana_tindakan']) && !empty($responseBantaran['rencana_tindakan']) ? $responseBantaran['rencana_tindakan'] : '-' ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php if(isset($isVerifikasiRujukan)): ?>
    <div class="modal-footer">
        <button type="button" class="btn btn-info btn-labeled btn-xs" id="terima_bantaran">
            <b>
                <i class="fa fa-check"></i>
            </b>
            Terima Pembantaran
        </button>
    </div>
<?php endif; ?>

<script type="text/javascript">
    var bantaran_id = '<?= $responseBantaran['rujukanbantaran_id'] ?>';

    $(document).ready(function() {
        var today = new Date();
        // Set to start of day
        today.setHours(0,0,0,0);

        $('#tgl_rujukan').pickadate({
            format: 'dd-mm-yyyy',
            min: today,
            max: false,
            onClose: function () {
                $(this).focus();
            },
            onSet: function(context) {
                // Date changed
            }
        });

        let tgl_rujukan_picker = $('#tgl_rujukan').pickadate('picker');

        $("#tgl_rujukan").val(function() {
            var d = new Date();
            return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear();
        });
        
        $("#tgl_rujukan").AnyTime_picker({
            format: "%d-%m-%Y",
        });
    })

    $('#terima_bantaran').click(function() {
        var instalasi_nama = $('#instalasi_nama').val();
        var ruangan_nama = $('#ruangan_nama').val();
        var tgl_kunjungan = $('#tgl_rujukan').val();
        var dokter_nama = $('#dokter_nama').val();
        var rencana_tindakan = $('#rencana_tindakan').val();

        $.ajax({
            type: 'POST',
            url: '/pendaftaran/rujukan-bantaran/approve-bantaran',
            data: {
                bantaran_id: bantaran_id,
                instalasi_nama: instalasi_nama,
                ruangan_nama: ruangan_nama,
                tgl_kunjungan: tgl_kunjungan,
                dokter_nama: dokter_nama,
                rencana_tindakan: rencana_tindakan
            },
            dataType: 'JSON',
            success: function (res) {
                docoNotification('success', 'Proses Berhasil', 'Data berhasil disimpan.')

                setTimeout(function() { 
                    window.location.reload() 
                }, 3000);
            },
            error: function (res) {
                docoNotification('error', 'Proses Gagal', 'Data gagal disimpan.')
            },
        });
    });

    $('#tolak_bantaran').click(function() {
        // Close current modal
        $('#modal_backdrop').modal('hide');
        
        // Open tolak pembantaran modal
        setTimeout(function() {
            $.ajax({
                type: 'GET',
                url: '<?= Url::to(['/pendaftaran/rujukan-bantaran/tolak-rujukan']) ?>',
                data: {
                    id: '<?= DocoHelpers::encrypt($responseBantaran['rujukanbantaran_id']) ?>'
                },
                success: function(res) {
                    $('#modal_backdrop .modal-content').html(res);
                    $('#modal_backdrop').modal('show');
                },
                error: function() {
                    docoNotification('error', 'Error', 'Gagal memuat form penolakan.');
                }
            });
        }, 300);
    });

</script>
