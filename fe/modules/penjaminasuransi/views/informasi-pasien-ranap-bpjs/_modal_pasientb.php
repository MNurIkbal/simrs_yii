<?php
use app\components\DocoConstants;
?>

<div class="row p-5">
    <div class="col-md-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pasien</th>
                    <th>NIK</th>
                    <th>Jenis Kelamin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($validation as $key => $value): ?>
                    <tr>
                        <th><?= $value['id']?></th>
                        <th><?= $value['nama']?></th>
                        <th><?= $value['nik']?></th>
                        <th><?= $value['jenis_kelamin_id'] == 1 ? 'Laki - laki' : 'Perempuan' ?></th>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <div class="mt-3" style="display: flex; justify-content: center;">
            <button class="btn btn-success mr-3" id="check-sitb"><i class="fa fa-check"></i> Ya (Benar)</button>
            <button class="btn btn-danger" id="batal-sitb"><i class="fa fa-times"></i> Tidak (Batal)</button>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    const numberSep = $('#klaiminacbgranapform-no_sep').val()
    const numberSitb = $('#sitb').val()
    const kunjungan_number = $('#klaiminacbgranapform-kunjungan_id').val()
    $('#check-sitb').click(function(e) {
        e.preventDefault()
        $.ajax({
            type: "POST",
            url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/konfirmasi-sitb",
            data: {
                nosep: numberSep,
                nomer_sitb: numberSitb,
                kunjungan_id: kunjungan_number
            },
            success: function (response) {
                $("#klaiminacbgranapform-is_pasiensitb").val("validate")
                $('#modal-konfirmasi').modal('hide')
                $('.search-sitb').addClass('hidden')
                $('.ubah-sitb').removeClass('hidden')
                $('.pasien_tb').prop('disabled', true)
            }
        });
    })

    $('#batal-sitb').click(function(e) {
        e.preventDefault()
        $.ajax({
            type: "POST",
            url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/batal-validasi-sitb",
            data: {
                nosep: numberSep
            },
            success: function (response) {
                $('#modal-konfirmasi').modal('hide')
                $('.pasien_tb').val(null)
            }
        });
    })
});
</script>