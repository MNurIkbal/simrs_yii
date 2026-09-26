<?php
/**
 * @Author: Sirs Developer
 * @Date:   2025-11-25
 * Extension View for SIRS Only - SOAP Content for Rujukan Bantaran
 */

use yii\helpers\Html;
use yii\web\View;
use app\components\DocoConstants;

$isSelesaiPelayanan = isset($statusPelayananBantaranId) && $statusPelayananBantaranId == DocoConstants::SELESAI_PELAYANAN_BANTARAN;
?>

<div class="panel-body">
    <div id="content-cppt">
        <!-- Button Tambah SOAP -->
        <div style="margin-bottom: 15px;">
            <button type="button" class="btn btn-primary btn-labeled" id="btn-tambah-soap">
                <b><i class="fa fa-plus"></i></b> Tambah SOAP
            </button>
        </div>

        <!-- Tabel SOAP -->
        <div class="panel panel-default" style="margin-top: 20px;">
            <div class="panel-heading">
                <h5 class="panel-title">Riwayat SOAP</h5>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-soap">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="5%">No</th>
                                <th width="12%">Tanggal</th>
                                <th>SOAP</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" style="text-align:center;">Belum ada data SOAP</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah SOAP -->
<div id="modal-soap" class="modal fade" data-backdrop="static">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Tambah SOAP</h5>
            </div>
            <div class="modal-body">
                <form id="form-soap">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()); ?>
                    <?= Html::hiddenInput('rujukanbantaran_id', isset($responseBantaran['rujukanbantaran_id']) ? $responseBantaran['rujukanbantaran_id'] : ''); ?>
                    <?= Html::hiddenInput('a_diag_utama', '', ['id' => 'a_diag_utama']); ?>
                    <?= Html::hiddenInput('a_diag_penyerta', '', ['id' => 'a_diag_penyerta']); ?>
                    
                    <div class="form-group">
                        <label><strong>Tanggal CPPT</strong> <span class="text-danger">*</span></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <input type="text" class="form-control" id="tgl_cppt" name="tgl_cppt" value="<?= date('d/m/Y H:i') ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Subjektif</strong> <span class="text-danger">*</span></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <textarea class="form-control" id="subjektif" name="subjektif" rows="3" placeholder="Masukkan keluhan/subjektif pasien"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Objektif</strong> <span class="text-danger">*</span></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <textarea class="form-control" id="objektif" name="objektif" rows="3" placeholder="Masukkan hasil pemeriksaan objektif"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Diagnosa Utama</strong></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <textarea class="form-control" id="a_diag_utama_text" name="a_diag_utama_text" placeholder="Masukkan diagnosa utama"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Diagnosa Penyerta</strong></label>
                        <div style="margin-top: 8px; margin-bottom: 6px;">
                            <textarea class="form-control" id="a_diag_penyerta_text" name="a_diag_penyerta_text" rows="2" placeholder="Masukkan diagnosa penyerta"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Planning</strong> <span class="text-danger">*</span></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <textarea class="form-control" id="planning" name="planning" rows="3" placeholder="Masukkan rencana tindakan/planning"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Instruksi</strong></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <textarea class="form-control" id="instruksi" name="instruksi" rows="2" placeholder="Masukkan instruksi (opsional)"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Catatan Dokter</strong> <span class="text-danger">*</span></label>
                        <div style="margin-top: 8px; margin-bottom: 10px;">
                            <textarea class="form-control" id="catatan_dokter" name="catatan_dokter" rows="3" placeholder="Masukkan assessment/penilaian"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-labeled" id="btn-simpan-soap">
                    <b><i class="fa fa-save"></i></b> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $(document).ready(function() {
        var isSelesaiPelayanan = " . ($isSelesaiPelayanan ? 'true' : 'false') . ";
        
        // SOAP Form functionality
        var soapForm = $('#form-soap');
        var btnSimpanSoap = $('#btn-simpan-soap');
        var modalSoap = $('#modal-soap');
        
        // Open SOAP modal
        $('#btn-tambah-soap').on('click', function() {
            // Reset form
            $('#subjektif').val('');
            $('#objektif').val('');
            $('#catatan_dokter').val('');
            $('#instruksi').val('');
            $('#planning').val('');
            $('#a_diag_utama_text').val('');
            $('#a_diag_penyerta_text').val('');
            $('#a_diag_utama').val('');
            $('#a_diag_penyerta').val('');
            $('#tgl_cppt').val(moment().format('DD/MM/YYYY HH:mm'));
            
            modalSoap.modal('show');
        });
        
        btnSimpanSoap.on('click', function() {
            var subjektif = $('#subjektif').val().trim();
            var objektif = $('#objektif').val().trim();
            var catatan_dokter = $('#catatan_dokter').val().trim();
            var planning = $('#planning').val().trim();
            
            // Validation
            if (!subjektif) {
                docoNotification('warning', 'Perhatian', 'Subjektif harus diisi.');
                $('#subjektif').focus();
                return;
            }
            if (!objektif) {
                docoNotification('warning', 'Perhatian', 'Objektif harus diisi.');
                $('#objektif').focus();
                return;
            }
            if (!catatan_dokter) {
                docoNotification('warning', 'Perhatian', 'Catatan Dokter (Assessment) harus diisi.');
                $('#catatan_dokter').focus();
                return;
            }
            if (!planning) {
                docoNotification('warning', 'Perhatian', 'Planning harus diisi.');
                $('#planning').focus();
                return;
            }

            // Prepare diagnosis payload (store as JSON string)
            var diagUtamaText = $('#a_diag_utama_text').val().trim();
            var diagPenyertaText = $('#a_diag_penyerta_text').val().trim();
            var diagPenyertaList = [];
            if (diagPenyertaText) {
                diagPenyertaList = diagPenyertaText.split('\\n').map(function(item) {
                    return item.trim();
                }).filter(Boolean);
            }

            var diagUtamaPayload = diagUtamaText ? JSON.stringify({ text: diagUtamaText }) : '';
            var diagPenyertaPayload = diagPenyertaList.length ? JSON.stringify(diagPenyertaList.map(function(text) { return { text: text }; })) : '';

            $('#a_diag_utama').val(diagUtamaPayload);
            $('#a_diag_penyerta').val(diagPenyertaPayload);
            
            var formData = soapForm.serialize();
            
            btnSimpanSoap.prop('disabled', true).html('<b><i class=\"fa fa-spinner fa-spin\"></i></b> Menyimpan...');
            
            $.ajax({
                url: '/pendaftaran/rujukan-bantaran/simpan-soap',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    docoNotification('success', 'Berhasil', 'Data SOAP berhasil disimpan.');
                    
                    // Close modal
                    modalSoap.modal('hide');
                    
                    // Reload SOAP table
                    loadSoapData();
                },
                error: function(xhr) {
                    var resp = xhr.responseJSON || {};
                    var message = resp.message || 'Gagal menyimpan data SOAP.';
                    docoNotification('error', 'Gagal', message);
                },
                complete: function() {
                    btnSimpanSoap.prop('disabled', false).html('<b><i class=\"fa fa-save\"></i></b> Simpan');
                }
            });
        });
        
        // Load SOAP data
        function loadSoapData() {
            var rujukanbantaran_id = $('input[name=\"rujukanbantaran_id\"]').val();
            
            if (!rujukanbantaran_id) {
                $('#table-soap tbody').html('<tr><td colspan=\\\"4\\\" style=\\\"text-align:center;\\\">ID rujukan bantaran tidak tersedia</td></tr>');
                return;
            }
            
            $.ajax({
                url: '/pendaftaran/rujukan-bantaran/get-soap-data',
                type: 'GET',
                data: { rujukanbantaran_id: rujukanbantaran_id },
                dataType: 'json',
                success: function(response) {
                    var tbody = $('#table-soap tbody');
                    tbody.empty();
                    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            var parseJsonSafe = function(value) {
                                if (!value) return null;
                                var parsed = value;
                                for (var i = 0; i < 2; i++) { // try to unwrap double-encoded JSON
                                    if (typeof parsed === 'object') {
                                        return parsed;
                                    }
                                    if (typeof parsed === 'string') {
                                        try {
                                            parsed = JSON.parse(parsed);
                                            continue;
                                        } catch (e) {
                                            return parsed;
                                        }
                                    }
                                }
                                return parsed;
                            };

                            var extractDiagText = function(diagItem) {
                                if (!diagItem) return null;
                                if (typeof diagItem === 'string') return diagItem;
                                if (diagItem.text) return diagItem.text;
                                if (diagItem.nama) return diagItem.nama;
                                if (diagItem.diagnosa) return diagItem.diagnosa;
                                return null;
                            };

                            var diagUtama = parseJsonSafe(item.a_diag_utama);
                            var diagUtamaText = extractDiagText(diagUtama);

                            var diagPenyertaRaw = parseJsonSafe(item.a_diag_penyerta);
                            var diagPenyertaTexts = [];
                            if (Array.isArray(diagPenyertaRaw)) {
                                diagPenyertaTexts = diagPenyertaRaw.map(extractDiagText).filter(Boolean);
                            } else {
                                var singleDiag = extractDiagText(diagPenyertaRaw);
                                if (singleDiag) {
                                    diagPenyertaTexts = [singleDiag];
                                }
                            }

                            var soapContent = '<div style=\\\"margin-bottom: 10px;\\\">' +
                                '<strong>S (Subjektif):</strong><br>' + (item.subject || '-') +
                            '</div>' +
                            '<div style=\\\"margin-bottom: 10px;\\\">' +
                                '<strong>O (Objektif):</strong><br>' + (item.object || '-') +
                            '</div>' +
                            (diagUtamaText ? '<div style=\\\"margin-bottom: 10px;\\\"><strong>A (Assesment Diagnosa Utama):</strong><br>' + diagUtamaText + '</div>' : '') +
                            (diagPenyertaTexts.length ? '<div style=\\\"margin-bottom: 10px;\\\"><strong>Diagnosa Penyerta:</strong><br>' + diagPenyertaTexts.join(', ') + '</div>' : '') +
                            '<div style=\\\"margin-bottom: 10px;\\\">' +
                                '<strong>P (Planning):</strong><br>' + (item.planning || '-') +
                            '</div>' +
                            '<div style=\\\"margin-bottom: 10px;\\\">' +
                                '<strong>Instruksi:</strong><br>' + (item.instruksi || '-') +
                            '</div>' +
                            '<div style=\\\"margin-bottom: 10px;\\\">' +
                                '<strong>Catatan Dokter:</strong><br>' + (item.catatan_dokter || '-') +
                            '</div>';
                            
                            var row = '<tr>' +
                                '<td class=\"text-center\">' + (index + 1) + '</td>' +
                                '<td>' + (item.tgl_cppt || '-') + '</td>' +
                                '<td>' + soapContent + '</td>' +
                                '<td class=\"text-center\">' +
                                    '<button class=\"btn btn-xs btn-danger btn-hapus-soap\" data-id=\"' + item.cppt_id + '\" title=\"Hapus\">' +
                                        '<i class=\"fa fa-trash\"></i>' +
                                    '</button>' +
                                '</td>' +
                            '</tr>';
                            tbody.append(row);
                        });
                    } else {
                        tbody.html('<tr><td colspan=\\\"4\\\" style=\\\"text-align:center;\\\">Belum ada data SOAP</td></tr>');
                    }
                },
                error: function(xhr) {
                    var resp = xhr.responseJSON || {};
                    var message = resp.message || 'Gagal memuat data SOAP.';
                    $('#table-soap tbody').html('<tr><td colspan=\\\"4\\\" style=\\\"text-align:center;color:red;\\\">' + message + '</td></tr>');
                }
            });
        }
        
        // Delete SOAP
        $(document).on('click', '.btn-hapus-soap', function() {
            var cppt_id = $(this).data('id');
            
            if (!cppt_id) {
                docoNotification('error', 'Gagal', 'ID CPPT tidak valid.');
                return;
            }
            
            var confirmHeader = 'Konfirmasi Hapus';
            var confirmMessage = 'Apakah Anda yakin ingin menghapus data SOAP ini?';
            var confirmLabel = { buttons: { Yes: 'button-yes', No: 'button-no' } };

            $.showQuestionDialog(confirmHeader, confirmMessage, confirmLabel, function (reaction) {
                if (reaction !== 'Yes') {
                    hideQuestionDialog();
                    return;
                }

                hideQuestionDialog();
                $.ajax({
                    url: '/pendaftaran/rujukan-bantaran/hapus-soap',
                    type: 'POST',
                    data: {
                        cppt_id: cppt_id,
                        " . Yii::$app->request->csrfParam . ": '" . Yii::$app->request->getCsrfToken() . "'
                    },
                    dataType: 'json',
                    success: function(response) {
                        docoNotification('success', 'Berhasil', 'Data SOAP berhasil dihapus.');
                        loadSoapData();
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON || {};
                        var message = resp.message || 'Gagal menghapus data SOAP.';
                        docoNotification('error', 'Gagal', message);
                    }
                });
            });
        });
        
        // Load initial SOAP data
        loadSoapData();
    });
", View::POS_READY);
?>
