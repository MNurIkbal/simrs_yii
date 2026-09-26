<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use kartik\widgets\DepDrop;
?>
<style type="text/css">
    .outline-pasien {
        padding-left: 0px !important;
        padding-right: 0px !important;
        margin-top: 10px !important;
    }
    .tbl-prmrj-title {
        margin-bottom: 10px !important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row tbl-filter">
        <div class="form-group new-filter col-md-3">
            <label>Tanggal :</label>
            <br/>
            <div class='input-group'>
                <input id="filter-prmrj-startDate" type='text' class='form-control input-xs startDate' />
                <span class='input-group-addon'>-</span>
                <input id="filter-prmrj-endDate" type='text' class='form-control input-xs endDate' />
                <input type='text' style='display:none' id="filter-prmrj-tgl_kunjungan" name="filter-prmrj-tgl_kunjungan" class='targetDate' readonly='true'>
            </div>
        </div>
        <div class="form-group new-filter col-md-2">
            <label>Ruangan :</label>
            <br/>
            <?php foreach ($instalasi as $value) { ?>
                <label class="checkbox-inline">
                    <input 
                        type="checkbox" 
                        class="filter-prmrj-instalasi" 
                        name="filter-prmrj-instalasi_id" 
                        id="filter-prmrj-instalasi_id" 
                        value="<?= $value['instalasi_id']; ?>" 
                        data-name="<?= $value['instalasi_nama']; ?>"
                    ><?= $value['instalasi_nama']; ?>
                </label>
            <?php } ?>
        </div>
        <div class="form-group new-filter col-md-3">
            <label></label>
            <br/>
            <select id="filter-prmrj-ruangan_id" name="filter-prmrj-ruangan_id" class="form-control input-xs">
            </select>
        </div>
        <div class="col-md-4">
            <br/>
            <button type="button" class="btn btn-info btn-labeled btn-xs btn-cari-prmrj"><b><i class="fa fa-search"></i></b><?=Yii::t('fe','Cari')?></button> 
            <button type="button" class="btn btn-info btn-labeled btn-xs btn-cetak-pdf-prmrj"><b><i class="fa fa-file-pdf-o"></i></b><?=Yii::t('fe','PDF')?></button>
        </div>
    </div>
    <div class="row table-responsive">
        <div class="col-md-7 outline-pasien">
            <div class="col-xs-3">Nama Lengkap</div>
            <div class="col-xs-9">: <?= ArrayHelper::getValue($pasien,'nama_pasien'); ?></div>
            <div class="col-xs-3">Alamat</div>
            <div class="col-xs-9">: <?= ArrayHelper::getValue($pasien,'alamat_pasien'); ?></div>
            <div class="col-xs-3">Umur</div>
            <div class="col-xs-9">: <?= ArrayHelper::getValue($pasien,'umur'); ?></div>
            <div class="col-xs-3">Jenis Kelamin</div>
            <div class="col-xs-9">: <?= ArrayHelper::getValue($pasien,'jenis_kelamin'); ?></div>
            <div class="col-xs-3">Agama</div>
            <div class="col-xs-9">: <?= ArrayHelper::getValue($pasien,'agama_pasien'); ?></div>
        </div>
        <div class="col-md-5 outline-pasien">
            <div class="col-xs-4">No Rekam Medis</div>
            <div class="col-xs-8">: <?= ArrayHelper::getValue($pasien,'no_rekam_medik'); ?></div>
            <div class="col-xs-4">Data Kunjungan</div>
            <div class="col-xs-8 data-kunjungan-pasien">: <?= ArrayHelper::getValue($pasien,'data_kunjungan'); ?></div>
            <div class="col-xs-4">Tgl Pembuatan Resume</div>
            <div class="col-xs-8">: <?= ArrayHelper::getValue($pasien,'tgl_pembuatan_resume'); ?></div>
            <div class="col-xs-4">Alergi</div>
            <div class="col-xs-8">: <?= ArrayHelper::getValue($pasien,'alergi'); ?></div>
        </div>
        <div class="col-md-12 tbl-prmrj-title text-center">
            <h5><?=\Yii::t("fe", "PROFIL RINGKAS MEDIS RAWAT JALAN");?></h5>
            <em><?=\Yii::t("fe", "(Resume Medis ini Sah Tanpa Tanda Tangan)");?></em>
        </div>
        <div class="col-md-12">
            <table id="tbl-prmrj" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="80">No</th>
                        <th><?=\Yii::t("fe", "Tanggal Berkunjung");?></th>
                        <th><?=\Yii::t("fe", "Poli/IGD");?></th>
                        <th><?=\Yii::t("fe", "Nama Dokter");?></th>
                        <th><?=\Yii::t("fe", "Diagnosa");?></th>
                        <th><?=\Yii::t("fe", "Obat-obatan / Jenis Pemeriksaan");?></th>
                        <th><?=\Yii::t("fe", "Tindak Lanjut");?></th>
                        <th><?=\Yii::t("fe", "Anamnesa (S)");?></th>
                        <th><?=\Yii::t("fe", "Temuan Klinis (O)");?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" id="script-modal-prmrj">
    var pasien_id = '<?= $pasien['primary']; ?>'
    var tablePrmrj;

    $(document).ready(() => {
        dateRangeHelper(
            "#filter-prmrj-startDate",
            "#filter-prmrj-endDate",
            "#filter-prmrj-tgl_kunjungan",
            true,
            false,
            true,
        );

        $('#filter-prmrj-ruangan_id').select2({
            data: [
                {id: 0, text: '- Semua -'}
            ]
        });

        $('input[type="checkbox"]').on('click', function() {
            $('input[type="checkbox"]').not(this).prop('checked', false);   
            var instalasi_id = $('input[name="filter-prmrj-instalasi_id"]:checked').val();
            updateSelectRuangan(instalasi_id);
        });

        $('.btn-cari-prmrj').on('click', function() {
            var tgl_pendaftaran = $('#filter-prmrj-tgl_kunjungan').val();
            var instalasi_id = $('input[name="filter-prmrj-instalasi_id"]:checked').val();
            if (instalasi_id == '' || instalasi_id == null || instalasi_id == undefined) {
                instalasi_id = 0;
            }
            var ruangan_id = $('#filter-prmrj-ruangan_id').val();
            var tableUrl = `/pendaftaran/informasi-pencarian-pasien/get-data-profil-ringkas-medis-rj?`+
                `pasien_id=${pasien_id}`+
                `&tgl_pendaftaran=${tgl_pendaftaran}`+
                `&instalasi_id=${instalasi_id}`+
                `&ruangan_id=${ruangan_id}`;

            updateDataKunjungan(instalasi_id);
            generateTablePrmrj(tableUrl);            
        });

        $('.btn-cetak-pdf-prmrj').on('click', function() {
            var tgl_pendaftaran = $('#filter-prmrj-tgl_kunjungan').val();
            var instalasi_id = $('input[name="filter-prmrj-instalasi_id"]:checked').val();
            if (instalasi_id == '' || instalasi_id == null || instalasi_id == undefined) {
                instalasi_id = 0;
            }
            var ruangan_id = $('#filter-prmrj-ruangan_id').val();
            var urlCetak = `/pendaftaran/informasi-pencarian-pasien/cetak-pdf-profil-ringkas-medis-rj?`+
                `pasien_id=${pasien_id}`+
                `&tgl_pendaftaran=${tgl_pendaftaran}`+
                `&instalasi_id=${instalasi_id}`+
                `&ruangan_id=${ruangan_id}`;
            window.open(urlCetak, '_blank');
        });
    });

    function updateSelectRuangan(instalasi_id) {
        showLoader();
        if (instalasi_id == '' || instalasi_id == null || instalasi_id == undefined || instalasi_id == 0) {
            $('#filter-prmrj-ruangan_id').html('')
            $('#filter-prmrj-ruangan_id').select2({
                data: [
                    {id: 0, text: '- Semua -'}
                ]
            });
            hideLoader();
        } else {
            $.ajax({
                url: '/pendaftaran/end-point/filter-ruangan',
                data: {
                    instalasi_id: instalasi_id,
                },
                success: (res) => {
                    var data = [
                        {id: 0, text: '- Semua -'}
                    ]
                    data = data.concat(res.data)
                    $('#filter-prmrj-ruangan_id').html('')
                    $('#filter-prmrj-ruangan_id').select2({
                        data,
                    })
                }
            })
        }
    }

    function generateTablePrmrj(tableUrl) {
        if (!$.fn.DataTable.isDataTable('#tbl-prmrj')) {
            tablePrmrj = $('#tbl-prmrj').docoTabel({
                filter: true,
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollY: false,
                order: [1,'asc'],
                sorting: [[1, 'asc']],
                ajax: tableUrl,
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: '<?=\Yii::t("fe", "Tanggal Berkunjung");?>', 
                        data: 'tgl_pendaftaran', 
                        searchable: false
                    },
                    {
                        title: '<?=\Yii::t('fe', "Poli/IGD");?>', 
                        data: 'ruangan_nama', 
                        searchable: false
                    },
                    {
                        title: '<?=\Yii::t('fe', "Nama Dokter");?>', 
                        data: 'nama_dokter', 
                        searchable: false
                    },
                    {
                        title: '<?=\Yii::t('fe', "Diagnosa");?>', 
                        data: 'diagnosa', 
                        searchable: false
                    },
                    {
                        title: '<?=\Yii::t('fe', "Obat-obatan / Jenis Pemeriksaan");?>', 
                        data: 'obat_tindakan', 
                        searchable: false
                    },
                    {
                        title: '<?=\Yii::t('fe', "Tindak Lanjut");?>', 
                        data: 'tindak_lanjut', 
                        searchable: false
                    },
                    {
                        title: '<?=\Yii::t('fe', "Anamnesa (S)");?>', 
                        data: 'anamnesa', 
                        searchable: false
                    },
                    {
                        title: '<?=\Yii::t('fe', "Temuan Klinis (O)");?>', 
                        data: 'object', 
                        searchable: false
                    },
                ],
            });
            $('.dataTables_filter').hide();
        } else {
            tablePrmrj.ajax.url(tableUrl).load();
        }
    }

    function updateDataKunjungan(instalasi_id) {
        var data_kunjungan;
        if (instalasi_id == '' || instalasi_id == null || instalasi_id == undefined) {
            var instalasi_nama = $('input[name="filter-prmrj-instalasi_id"]').map(function(){
                return $(this).data('name');
            }).get();
            data_kunjungan = instalasi_nama.join(" & ");
        } else {
            data_kunjungan = $('input[name="filter-prmrj-instalasi_id"]:checked').data('name');
        }
        $('.data-kunjungan-pasien').html(`: ${data_kunjungan}`);
    }
</script>