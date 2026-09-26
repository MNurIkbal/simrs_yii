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
        height: 220px;
        overflow-y: auto;
    }

    .dokumen-list a {
        color: #16a34a;
        text-decoration: none;
    }

    .dokumen-list a:hover {
        text-decoration: underline;
    }

    h3 {
        margin-top: 0;
        font-weight: 600;
    }
    .form-group {
        margin-top: 10px;
    }


    #tolak_bantaran, 
    #tolak_bantaran:hover, 
    #tolak_bantaran:focus {
        background-color: #CF212A !important;
        border-color: #CF212A !important;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row" id="form_rujukan_bantaran">
        <!-- Kolom Kiri -->
        <div class="col-md-4">
            <div class="form-group">
                <label>Nama Pasien/ No. RM</label>
                <?php 
                    $no_rm = !empty($responseBantaran['no_rekam_medik']) ? $responseBantaran['no_rekam_medik'] : '-';
                ?>
                <input type="text" class="form-control" value="<?= $responseBantaran['nama_pasien'] ?> / <?= $no_rm; ?>" readonly>
            </div>

            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control" value="<?= $responseBantaran['no_identitas_pasien'] ?>" readonly>
            </div>

            <div class="form-group">
                <label>Cara Bayar <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <?= Html::dropDownList('carabayar_id', '', $listCarabayar, [
                                'class' => 'form-control select2',
                                'id' => 'filter_carabayar',
                                'prompt' => \Yii::t('fe', '--pilih cara bayar--')
                            ]
                        );
                    ?>
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= $responseBantaran['carabayar_nama'] ?>" readonly>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Penjamin <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <?=                        
                        DepDrop::widget([
                            'name' => 'penjamin_id',
                            'options' => [
                                'id' => 'filter_penjamin',
                                'class' => 'select2',
                            ],
                            'pluginOptions' => [
                                'depends' => ['filter_carabayar'],
                                'placeholder' => \Yii::t('fe', '-- pilih penjamin --'),
                                'url' => '/pendaftaran/informasi-pasien/list-penjamin'
                            ]
                            ]);
                    ?>
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= $responseBantaran['penjamin_nama'] ?>" readonly>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Instalasi <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <?= Html::dropDownList('instalasi_id', $responseBantaran['instalasi_id'], $listInstalasi, [
                                'class' => 'form-control select2',
                                'id' => 'filter_instalasi',
                                'prompt' => \Yii::t('fe', '--pilih instalasi--')
                            ]
                        );
                    ?>
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= $responseBantaran['instalasi_nama'] ?>" readonly>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Tanggal Kunjungan <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <input type="text" class="form-control pickadate" id="tgl_rujukan" value="<?= date('Y-m-d', strtotime($responseBantaran['tgl_kunjungan'])); ?>">
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= date('d M Y', strtotime($responseBantaran['tgl_kunjungan'])); ?>" readonly>
                <?php endif;?>
            </div>

            <div class="form-group">
                <label>Ruangan <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <?=
                        DepDrop::widget([
                            'name' => 'ruangan_id',
                            'options' => [
                                'id' => 'ruangan_id',
                                'class' => 'select2',
                            ],
                            'pluginOptions' => [
                                'depends' => ['filter_instalasi'],
                                'placeholder' => \Yii::t('fe', '-- pilih ruangan --'),
                                'url' => '/api/master/get-ruangan-by-instalasi-dep'
                            ]
                        ]);
                    ?>
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= $responseBantaran['ruangan_nama'] ?>" readonly>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Dokter <span class="text-required">*</span></label>
                <?php if(isset($isVerifikasiRujukan)): ?>
                    <select id="filter_dokter" class="select2 select2-hidden-accessible" name="dokter_id" tabindex="-1" aria-hidden="true">
                    </select>
                <?php else: ?>
                    <input type="text" class="form-control" value="<?= $responseBantaran['nama_dokter'] ?>" readonly>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Tengah -->
        <div class="col-md-4">
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <input type="text" class="form-control" value="<?= $responseBantaran['jenis_kelamin'] == DocoConstants::LOOKUP_LAKI ? "Laki - laki" : "Perempuan"; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Tgl Lahir / Umur</label>
                <?php 
                    $tgl_lahir = date('d M Y', strtotime($responseBantaran['tgl_lahir']));
                    $umur = DocoHelpers::getUmur($responseBantaran['tgl_lahir'], true);
                ?>
                <input type="text" class="form-control" value="<?= $tgl_lahir ?> / <?= $umur ?>th" readonly>
            </div>

            <div class="form-group">
                <label>Keterangan Rujukan</label>
                <div class="keterangan-box">
                    <?= $responseBantaran['keterangan_rujukan']; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-4">
            <div class="form-group">
                <label>UPT Asal</label>
                <input type="text" class="form-control" value="<?= $responseBantaran['uptasal_nama']; ?>" readonly>
            </div>

            <div class="form-group">
                <label>Dokumen Penyerta</label>
                <div class="dokumen-list">
                    <?php if(!empty($responseDokumenBantaran)): ?>
                        <?php foreach($responseDokumenBantaran as $key => $value) { 
                            $dokumenId = ArrayHelper::getValue($value, 'dokumenrujukanbantaran_id', ArrayHelper::getValue($value, 'dokumenmedisbantaran_id'));
                        ?>
                            <p>
                                <a href="<?= $value['url_dokumen']; ?>" target="_blank" rel="noopener noreferrer" dokumen-id="<?= $dokumenId; ?>">
                                    <?= $value['nama_dokumen']; ?>
                                </a>
                            </p>
                        <?php } ?>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>

    <?php if($responseBantaran['status_verifikasi_bantaran_id'] == DocoConstants::TOLAK_VERFIKASI_BANTARAN && !isset($isVerifikasiRujukan)) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Keterangan Penolakan Rujukan</label>
                    <div class="keterangan-box">
                        <?= $responseBantaran['keterangan_penolakan_rujukan']; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php if(isset($isVerifikasiRujukan)): ?>
    <div class="modal-footer">
        <button type="button" class="btn btn-info btn-labeled btn-xs" id="terima_bantaran">
            <b>
                <i class="fa fa-check"></i>
            </b>
            Terima
        </button>
        <button type="button" class="btn btn-info btn-labeled btn-xs" id="tolak_bantaran">
            <b>
                <i class="fa fa-ban"></i>
            </b>
            Tolak
        </button>
    </div>
<?php endif; ?>

<script type="text/javascript">
    var bantaran_id = '<?= $responseBantaran['rujukanbantaran_id'] ?>';
    var setTglRujukan = '<?= date('Y-m-d', strtotime($responseBantaran['tgl_kunjungan'])); ?>';
    var selectedInstalasi = '<?= $responseBantaran['instalasi_id'] ?>';

    $('#filter_penjamin').depdrop({
        allowClear: true,
        depends: ["filter_carabayar"],
        placeholder: "-- Pilih --",
        url: "/pendaftaran/informasi-pasien/list-penjamin"
    });

    $('#ruangan_id').depdrop({
        allowClear: true,
        depends: ["filter_instalasi"],
        placeholder: "-- Pilih --",
        url: "/api/master/get-ruangan-by-instalasi-dep"
    });

    $(document).on('change', '#ruangan_id', function() {
        filterDokter();
    });
    
    $(document).ready(function() {
        var today = new Date();
        today.setHours(0,0,0,0);

        var picker = $('#tgl_rujukan').pickadate({
            format: 'dd-mm-yyyy',
            min: today,
            max: false,
            onClose: function () {
                $(this).focus();
            },
            onSet: function(context) {
                // Ambil tanggal yang dipilih
                if (context.select) {
                    var selectedDate = new Date(context.select);
                    var tgl = ("0" + selectedDate.getDate()).slice(-2) + "-"
                            + ("0" + (selectedDate.getMonth() + 1)).slice(-2) + "-"
                            + selectedDate.getFullYear();

                    // Set otomatis ke input
                    $('#tgl_rujukan').val(tgl);
                }

                // Jalankan filter jika ruangan di-set
                var ruangan_id = $('#ruangan_id').val();
                if (ruangan_id) {
                    filterDokter();
                }
            }
        });

        // Jika ingin set default tanggal dari variabel setTglRujukan
        let d = new Date(setTglRujukan);
        let tgl = ("0" + d.getDate()).slice(-2) + "-"
                + ("0" + (d.getMonth() + 1)).slice(-2) + "-"
                + d.getFullYear();

        $('#tgl_rujukan').val(tgl);

        // Set ke pickadate juga
        let pickerInstance = $('#tgl_rujukan').pickadate('picker');
        pickerInstance.set('select', d);

        $('.modal-body').find(".select2").select2({
            dropdownParent: $("#form_rujukan_bantaran")
        });

        if(parseInt(selectedInstalasi) > 0) {
            $('#filter_instalasi').val(selectedInstalasi).trigger('depdrop:change');
        }
    })

    $('#terima_bantaran').click(function() {
        var carabayar_id = $('#filter_carabayar').val();
        var penjamin_id = $('#filter_penjamin').val();
        var instalasi_id = $('#filter_instalasi').val();
        var ruangan_id = $('#ruangan_id').val();
        var tgl_kunjungan = $('#tgl_rujukan').val();
        var dokter_id = $('#filter_dokter').val();
        var jadwaldokter_id = $('option[value="'+dokter_id+'"]').attr('jadwaldokter-id');

        $.ajax({
            type: 'POST',
            url: '/pendaftaran/rujukan-bantaran/approve-bantaran',
            data: {
                bantaran_id: bantaran_id,
                carabayar_id: carabayar_id,
                penjamin_id: penjamin_id,
                instalasi_id: instalasi_id,
                ruangan_id: ruangan_id,
                tgl_kunjungan: tgl_kunjungan,
                dokter_id: dokter_id,
                jadwaldokter_id: jadwaldokter_id
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

    $(document).on('click', '#tolak_bantaran', function() {
        $('#modal_penolakan').modal('toggle');
        $('#post_bantaran_id').val(bantaran_id);
    });

    function filterDokter() {
        var instalasi_id = $('#filter_instalasi').val();
        var ruangan_id = $('#ruangan_id').val();
        var tgl_rujukan = $('#tgl_rujukan').val();

        if(parseInt(instalasi_id) == 1) {
            // rajal
            var path_url = '/pendaftaran/daftar/get-dokter?param=rajal&with_kuota=true&is_bpjs=false&tgl_rujukan='+tgl_rujukan;
        } else {
            // igd
            var path_url = '/pendaftaran/daftar/get-dokter?param=penunjang&is_bpjs=false';
        }

        $.ajax({
            type: 'POST',
            url: path_url,
            data: {
                depdrop_parents: [ruangan_id],
                depdrop_all_params: {'ruangan_id': ruangan_id}
            },
            dataType: 'JSON',
            beforeSend: function () {

            },
            success: function (res) {
                $('#filter_dokter').find('option').remove();

                var result = res.output;

                if(result.length > 0) {
                    var html = '';

                    for(var i = 0; i < result.length; i++) {
                        var pegawai_id = result[i]['id'];
                        var jadwaldokter_id = result[i]['jadwaldokter_id'];
                        var nama_dokter = result[i]['name'];
                        var kuota = result[i]['kuota_tersedia'];

                        var disabled = (parseInt(kuota) > 0 || parseInt(instalasi_id) == 2) ? '' : 'disabled';

                        html = html + '<option value="'+pegawai_id+'" jadwaldokter-id="'+jadwaldokter_id+'" '+disabled+'>'+nama_dokter+'</option>';
                    }
                    
                    $('#filter_dokter').append(html);
                }
            },
            error: function (res) {
                //code
            },
        });
    }

</script>
