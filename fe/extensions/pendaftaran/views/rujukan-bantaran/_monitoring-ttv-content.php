<?php
/**
 * @Author: Sirs Developer
 * @Date:   2025-11-25
 * Extension View for SIRS Only - Monitoring TTV Content for Rujukan Bantaran
 */

use yii\helpers\Html;
use yii\web\View;
use app\components\DocoConstants;

$jenisTtvLov = isset($jenisTtv) && is_array($jenisTtv) ? $jenisTtv : [];
$tingkatKesadaranLov = isset($tingkatKesadaran) && is_array($tingkatKesadaran) ? $tingkatKesadaran : [];
$isSelesaiPelayanan = isset($statusPelayananBantaranId) && $statusPelayananBantaranId == DocoConstants::SELESAI_PELAYANAN_BANTARAN;
?>

<div class="panel-body">
    <div id="content-monitoring-ttv">
        <!-- Button Tambah TTV -->
        <div style="margin-bottom: 15px;">
            <button type="button" class="btn btn-primary btn-labeled" id="btn-tambah-ttv">
                <b><i class="fa fa-plus"></i></b> Tambah Monitoring TTV
            </button>
        </div>

        <!-- Tabel Monitoring TTV -->
        <div class="panel panel-default" style="margin-top: 20px;">
            <div class="panel-heading">
                <h5 class="panel-title">Riwayat Monitoring TTV</h5>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-ttv">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="5%">No</th>
                                <th width="12%">Tanggal</th>
                                <th>Data TTV</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" style="text-align:center;">Belum ada data Monitoring TTV</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah TTV -->
<div id="modal-ttv" class="modal fade" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Tambah Monitoring TTV</h5>
            </div>
            <div class="modal-body">
                <form id="form-ttv">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()); ?>
                    <?= Html::hiddenInput('pendaftaran_id', isset($pendaftaranIdDecrypt) ? $pendaftaranIdDecrypt : ''); ?>
                    
                    <!-- Input TTV Pasien Section -->
                    <h5 style="margin-bottom: 15px; font-weight: bold;">Input TTV Pasien</h5>
                    
                    <div class="form-group">
                        <label>Jenis TTV <span class="text-danger">*</span></label>
                        <select class="form-control" id="jenis_ttv" name="jenis_ttv">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($jenisTtvLov as $jenisTtvItem): ?>
                                <option value="<?= Html::encode($jenisTtvItem); ?>"><?= Html::encode($jenisTtvItem); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal & Waktu <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="tgl_ttv_date" name="tgl_ttv_date" placeholder="Pilih Tanggal" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                    <input type="text" class="form-control" id="tgl_ttv_time" name="tgl_ttv_time" placeholder="Pilih Waktu" readonly>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="tgl_ttv" name="tgl_ttv">
                    </div>

                    <div class="form-group">
                        <label>Tingkat Kesadaran <span class="text-danger">*</span></label>
                        <select class="form-control" id="tingkat_kesadaran" name="tingkat_kesadaran">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($tingkatKesadaranLov as $kesadaranItem): ?>
                                <option value="<?= Html::encode($kesadaranItem); ?>"><?= Html::encode($kesadaranItem); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- TTV Section -->
                    <h5 style="margin-top: 25px; margin-bottom: 15px; font-weight: bold;">TTV</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sistol</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="sistol" name="sistol" placeholder="">
                                    <span class="input-group-addon">mmHg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tinggi Badan</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" class="form-control" id="tinggi_badan" name="tinggi_badan" placeholder="">
                                    <span class="input-group-addon">Cm</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Diastol</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="diastol" name="diastol" placeholder="">
                                    <span class="input-group-addon">mmHg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berat Badan</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" class="form-control" id="berat_badan" name="berat_badan" placeholder="">
                                    <span class="input-group-addon">Kg</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Denyut Nadi</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="nadi" name="nadi" placeholder="">
                                    <span class="input-group-addon">x/Menit</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SpO2</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="spo2" name="spo2" placeholder="">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Frekuensi Nafas</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="pernapasan" name="pernapasan" placeholder="">
                                    <span class="input-group-addon">x/Menit</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Suhu</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" class="form-control" id="suhu" name="suhu" placeholder="">
                                    <span class="input-group-addon">°C</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GCS Section -->
                    <h5 style="margin-top: 25px; margin-bottom: 15px; font-weight: bold;">GCS</h5>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>E</label>
                                <input type="number" class="form-control" id="gcs_e" name="gcs_e" placeholder="" min="1" max="4">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>M</label>
                                <input type="number" class="form-control" id="gcs_m" name="gcs_m" placeholder="" min="1" max="6">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>V</label>
                                <input type="number" class="form-control" id="gcs_v" name="gcs_v" placeholder="" min="1" max="5">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-labeled" id="btn-simpan-ttv">
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
        // TTV Form functionality
        var ttvForm = $('#form-ttv');
        var btnSimpanTtv = $('#btn-simpan-ttv');
        var modalTtv = $('#modal-ttv');
        
        // Initialize Select2 for dropdowns
        $('#jenis_ttv').select2({
            dropdownParent: modalTtv,
            placeholder: '-- Pilih --',
            allowClear: true,
            width: '100%'
        });
        
        $('#tingkat_kesadaran').select2({
            dropdownParent: modalTtv,
            placeholder: '-- Pilih --',
            allowClear: true,
            width: '100%'
        });
        
        // Initialize pickadate for date
        var datePicker = $('#tgl_ttv_date').pickadate({
            format: 'dd/mm/yyyy',
            formatSubmit: 'dd/mm/yyyy',
            selectYears: true,
            selectMonths: true,
            today: 'Hari Ini',
            clear: 'Bersihkan',
            close: 'Tutup',
            onSet: function(context) {
                updateDateTime();
            }
        });
        
        // Initialize pickatime for time
        var timePicker = $('#tgl_ttv_time').pickatime({
            format: 'HH:i',
            formatSubmit: 'HH:i',
            interval: 5,
            clear: 'Bersihkan',
            onSet: function(context) {
                updateDateTime();
            }
        });
        
        // Function to update combined datetime
        function updateDateTime() {
            var dateVal = $('#tgl_ttv_date').val();
            var timeVal = $('#tgl_ttv_time').val();
            
            if (dateVal && timeVal) {
                $('#tgl_ttv').val(dateVal + ' ' + timeVal);
            } else if (dateVal) {
                $('#tgl_ttv').val(dateVal + ' 00:00');
            }
        }
        
        // Set default date and time
        var picker_date = datePicker.pickadate('picker');
        var picker_time = timePicker.pickatime('picker');
        
        picker_date.set('select', new Date());
        picker_time.set('select', [new Date().getHours(), new Date().getMinutes()]);
        updateDateTime();
        
        // Check if pendaftaran_id exists
        var pendaftaran_id = $('input[name=\"pendaftaran_id\"]').val();
        
        if (!pendaftaran_id) {
            $('#btn-tambah-ttv').prop('disabled', true);
            $('#table-ttv tbody').html('<tr><td colspan=\"4\" style=\"text-align:center;\"><div class=\"alert alert-info\" style=\"margin: 10px;\"><i class=\"fa fa-info-circle\"></i> <strong>Informasi:</strong> Fitur Monitoring TTV akan tersedia setelah rujukan bantaran diverifikasi dan pendaftaran pasien dibuat.</div></td></tr>');
            return;
        }
        
        // Open TTV modal
        $('#btn-tambah-ttv').on('click', function() {
            // Reset form
            $('#jenis_ttv').val('').trigger('change');
            $('#tingkat_kesadaran').val('').trigger('change');
            $('#sistol').val('');
            $('#diastol').val('');
            $('#nadi').val('');
            $('#suhu').val('');
            $('#pernapasan').val('');
            $('#spo2').val('');
            $('#tinggi_badan').val('');
            $('#berat_badan').val('');
            $('#gcs_e').val('');
            $('#gcs_m').val('');
            $('#gcs_v').val('');
            
            // Reset date and time pickers to current date/time
            picker_date.set('select', new Date());
            picker_time.set('select', [new Date().getHours(), new Date().getMinutes()]);
            updateDateTime();
            
            modalTtv.modal('show');
        });
        
        btnSimpanTtv.on('click', function() {
            var formData = ttvForm.serialize();
            
            btnSimpanTtv.prop('disabled', true).html('<b><i class=\"fa fa-spinner fa-spin\"></i></b> Menyimpan...');
            
            $.ajax({
                url: '/pendaftaran/rujukan-bantaran/simpan-ttv',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    docoNotification('success', 'Berhasil', 'Data Monitoring TTV berhasil disimpan.');
                    
                    // Close modal
                    modalTtv.modal('hide');
                    
                    // Reload TTV table
                    loadTtvData();
                },
                error: function(xhr) {
                    var resp = xhr.responseJSON || {};
                    var message = resp.message || 'Gagal menyimpan data Monitoring TTV.';
                    docoNotification('error', 'Gagal', message);
                },
                complete: function() {
                    btnSimpanTtv.prop('disabled', false).html('<b><i class=\"fa fa-save\"></i></b> Simpan');
                }
            });
        });
        
        // Load TTV data
        function loadTtvData() {
            if (!pendaftaran_id) {
                return;
            }
            
            $.ajax({
                url: '/pendaftaran/rujukan-bantaran/get-ttv-data',
                type: 'GET',
                data: { pendaftaran_id: pendaftaran_id },
                dataType: 'json',
                success: function(response) {
                    var tbody = $('#table-ttv tbody');
                    tbody.empty();
                    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            var ttvItems = [];
                            
                            if (item.jenis_ttv) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Jenis TTV:</strong> ' + item.jenis_ttv + '</div>');
                            }
                            if (item.tingkat_kesadaran) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Tingkat Kesadaran:</strong> ' + item.tingkat_kesadaran + '</div>');
                            }
                            if (item.sistol || item.diastol) {
                                var tekananDarah = (item.sistol || '-') + '/' + (item.diastol || '-');
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Tekanan Darah:</strong> ' + tekananDarah + ' mmHg</div>');
                            }
                            if (item.nadi) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Denyut Nadi:</strong> ' + item.nadi + ' x/Menit</div>');
                            }
                            if (item.suhu) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Suhu:</strong> ' + item.suhu + ' °C</div>');
                            }
                            if (item.pernapasan) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Frekuensi Nafas:</strong> ' + item.pernapasan + ' x/Menit</div>');
                            }
                            if (item.spo2) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>SpO2:</strong> ' + item.spo2 + ' %</div>');
                            }
                            if (item.tinggi_badan) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Tinggi Badan:</strong> ' + item.tinggi_badan + ' cm</div>');
                            }
                            if (item.berat_badan) {
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>Berat Badan:</strong> ' + item.berat_badan + ' kg</div>');
                            }
                            if (item.gcs_e || item.gcs_m || item.gcs_v) {
                                var gcsTotal = (parseInt(item.gcs_e) || 0) + (parseInt(item.gcs_m) || 0) + (parseInt(item.gcs_v) || 0);
                                var gcsText = 'E: ' + (item.gcs_e || '-') + ', M: ' + (item.gcs_m || '-') + ', V: ' + (item.gcs_v || '-') + ' (Total: ' + gcsTotal + ')';
                                ttvItems.push('<div style=\"margin-bottom: 8px;\"><strong>GCS:</strong> ' + gcsText + '</div>');
                            }
                            
                            var ttvContent = '-';
                            if (ttvItems.length) {
                                var mid = Math.ceil(ttvItems.length / 2);
                                var leftCol = ttvItems.slice(0, mid).join('');
                                var rightCol = ttvItems.slice(mid).join('');
                                ttvContent = '<div class=\"row\" style=\"margin: 0 -5px;\">' +
                                    '<div class=\"col-sm-6\" style=\"padding: 0 5px;\">' + (leftCol || '-') + '</div>' +
                                    '<div class=\"col-sm-6\" style=\"padding: 0 5px;\">' + (rightCol || '') + '</div>' +
                                '</div>';
                            }
                            
                            var row = '<tr>' +
                                '<td class=\"text-center\">' + (index + 1) + '</td>' +
                                '<td>' + (item.tanggal_ttv || '-') + '</td>' +
                                '<td>' + ttvContent + '</td>' +
                                '<td class=\"text-center\">' +
                                    '<button class=\"btn btn-xs btn-danger btn-hapus-ttv\" data-id=\"' + item.ttv_id + '\" title=\"Hapus\">' +
                                        '<i class=\"fa fa-trash\"></i>' +
                                    '</button>' +
                                '</td>' +
                            '</tr>';
                            tbody.append(row);
                        });
                    } else {
                        tbody.html('<tr><td colspan=\"4\" style=\"text-align:center;\">Belum ada data Monitoring TTV</td></tr>');
                    }
                },
                error: function(xhr) {
                    var resp = xhr.responseJSON || {};
                    var message = resp.message || 'Gagal memuat data Monitoring TTV.';
                    $('#table-ttv tbody').html('<tr><td colspan=\"4\" style=\"text-align:center;color:red;\">' + message + '</td></tr>');
                }
            });
        }
        
        // Delete TTV
        $(document).on('click', '.btn-hapus-ttv', function() {
            var ttv_id = $(this).data('id');
            
            if (!ttv_id) {
                docoNotification('error', 'Gagal', 'ID Monitoring TTV tidak valid.');
                return;
            }
            
            if (confirm('Apakah Anda yakin ingin menghapus data Monitoring TTV ini?')) {
                $.ajax({
                    url: '/pendaftaran/rujukan-bantaran/hapus-ttv',
                    type: 'POST',
                    data: {
                        ttv_id: ttv_id,
                        " . Yii::$app->request->csrfParam . ": '" . Yii::$app->request->getCsrfToken() . "'
                    },
                    dataType: 'json',
                    success: function(response) {
                        docoNotification('success', 'Berhasil', 'Data Monitoring TTV berhasil dihapus.');
                        loadTtvData();
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON || {};
                        var message = resp.message || 'Gagal menghapus data Monitoring TTV.';
                        docoNotification('error', 'Gagal', message);
                    }
                });
            }
        });
        
        // Load initial TTV data
        loadTtvData();
    });
", View::POS_READY);
?>
