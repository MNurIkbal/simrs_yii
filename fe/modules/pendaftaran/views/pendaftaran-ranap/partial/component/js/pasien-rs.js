$(document).ready(function() {
    $(".jenis_pendaftaran").change(function() {
        if ($("input[name='TipePasienForm[is_aps]']")[1]['checked'] === true) { // pasien APS
            $(".pasien-aps").css({
                display: 'block'
            });
            $(".pasien-rs").css({
                display: 'none'
            });
            resetForm();
        } else { // pasien RS
            $(".pasien-aps").css({
                display: 'none'
            });
            $(".pasien-rs").css({
                display: 'block'
            });
        }
    });

    $(document).on('select2:select', '#tipepasienform-pendaftaran_id', function(e) {
        _data = e.params.data.data;
        _formPendaftaran.dataPendaftaran = _data;
        $('input[name=\"pendaftaran_id\"]').val(_data.pendaftaran_id);
        $('input[name=\"TipePasienForm[pasien]\"]').val(_data.nama_pasien);
        $('input[name=\"pasienrs_pasien_id_hidden\"]').val(_data.no_rekam_medik);
        $('input[name=\"pasienrs_carabayar_id_hidden\"]').val(_data.carabayar_id);
        $('input[name=\"pasienrs_penjamin_id_hidden\"]').val(_data.penjamin_id);
        $('input[name=\"pasienrs_group_carabayar_hidden\"]').val(_data.group_carabayar);
        $('input[name=\"pasienrs_group_carabayar_hidden\"]').val(_data.group_carabayar);
        $('input[name=\"pasienrs_no_pendaftaran_hidden\"]').val(_data.no_pendaftaran);

        populateDokterDropdown(_data, '#tipepasienform-dokter_perujuk');
    });

    function populateDokterDropdown(_data, id) {
        var dokter_id;
        var dokter_nama;
        switch (_data.instalasi_id) {
            default:
                dokter_id = _data.dokterrj_id;
                dokter_nama = _data.nama_dok_rj_rd;
                break;
            case 1: //RJ
                dokter_id = _data.dokterrj_id;
                dokter_nama = _data.nama_dok_rj_rd;
                break;
            case 2: //RD
                dokter_id = _data.dokterrj_id;
                dokter_nama = _data.nama_dok_rj_rd;
                break;
            case 3: // RI
                dokter_id = _data.dokterri_id;
                dokter_nama = _data.nama_dok_ri;
                break;
        }

        var data = {
            id: dokter_id,
            text: dokter_nama
        }

        var newOption = new Option(data.text, data.id, true, false);
        $(id).append(newOption).trigger('change');
        $(id).val(dokter_id).trigger('change');
    }

    function resetForm() {
        $('#tipepasienform-pendaftaran_id').val(null).trigger('change');
        $('#pasienrs_no_pendaftaran_hidden').val(null);
        $('input[name=\"TipePasienForm[pasien]\"]').val(null);
        $('#tipepasienform-dokter_perujuk').val(null).trigger('change');
        $('#pasienrs_pasien_id_hidden').val(null);
        $('#pasienrs_carabayar_id_hidden').val(null);
    }
});