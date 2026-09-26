// ====================== //
// DATATABLE PINDAH KAMAR //
// ====================== //
let table;
let isStillExistDataKamar = {
    general: true,
    titipan: true
}
let pageDataKamar = {
    general: 1,
    titipan: 1
}
let paramDatatable = {
    'titipan': {},
    'general': {},
}
let columnGenerated = [];
let kelas_titipan = false;

function createHeaderDatatableKamar(isKamarTitipan) {
    const key = isKamarTitipan ? 'Titipan' : 'NonTitipan'
    if ($(`#filterHeaderKamar${key}`).length === 0) {
        $("#filterHeader").append(`
            <div id="filterHeaderKamar${key}">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="jenis_penyakit">Jenis Penyakit</label>
                        <select name="jenisPenyakit" id="jenisPenyakitFilter${key}" class="form-control"></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="jenis_penyakit">Kelas</label>
                        <select name="jenisPenyakit" id="kelasFilter${key}" class="form-control"></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="jenis_penyakit">Ruangan</label>
                        <select name="jenisPenyakit" id="ruanganFilter${key}" class="form-control"></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="jenis_penyakit">Kamar</label>
                        <select name="jenisPenyakit" id="kamarFilter${key}" class="form-control"></select>
                    </div>
                </div>
                <div class="col-sm-12 button-search-section">
                    <button type="button" id="searchBtn${key}" class="btn btn-info btn-sm btn-labeled pull-right"><b class="fa fa-lg fa-search"></b> Cari</button>
                </div>
            </div>
        `)
        initFilter(key)
        const kelasPelayananId = $("#kelaspelayanan_id").val();
        $(`#kamarFilter${key}`).prop('disabled', true)
        $(`#ruanganFilter${key}`).prop('disabled', true)
        $(`#ruanganFilter${key}`).bind('change', ({ delegateTarget }) => {
            if ($(delegateTarget).val() === '' || $(delegateTarget).val() === '-' || $(delegateTarget).val() === 'Semua' || $(delegateTarget).val() == null) {
                $(`#kamarFilter${key}`).prop('disabled', true)
                $(`#kamarFilter${key}`).val('Semua').trigger('change')
                return false
            }
            if ($(`#kamarFilter${key}`).hasClass("select2-hidden-accessible")) {
                $(`#kamarFilter${key}`).select2('destroy')
                $(`#kamarFilter${key}`).html('')
            }
            $.ajax({
                url: `daftar/kamar-ruangan`,
                data: {
                    ruanganId: $(delegateTarget).val(),
                    kelasPelayananId: !isKamarTitipan ? $("#kelaspelayanan_id").val() : $(`#kelasFilter${key}`).val(),
                },
                success: (res) => {
                    let dataKamar = [{
                        id: '',
                        text: 'Semua'
                    }];
                    res.data.map((item) => {
                        dataKamar.push({
                            id: item.kamarruangan_id,
                            text: item.kamarruangan_nokamar,
                        })
                    });
                    $(`#kamarFilter${key}`).prop('disabled', false)
                    $(`#kamarFilter${key}`).select2({
                        data: dataKamar
                    })
                }
            });
        })
        $(`#jenisPenyakitFilter${key}`).bind('change', ({ delegateTarget }) => {
            initRuanganSource(key, isKamarTitipan)
        })
        $(`#kelasFilter${key}`).bind('change', ({ delegateTarget }) => {
            initRuanganSource(key, isKamarTitipan)
        })
        $(`#jenisPenyakitFilter${key}`).val($("#jeniskasuspenyakit_id").val()).trigger('change');
        $(`#kelasFilter${key}`).val($("#kelaspelayanan_id").val()).trigger('change');
        $(`#searchBtn${key}`).bind('click', ({ delegateTarget }) => {
            initDatatable(isKamarTitipan, true)
        })
    }
}

function initRuanganSource(key, isKamarTitipan) {
    const kelasPelayananId = $("#kelaspelayanan_id").val();
    if ($(`#jenisPenyakitFilter${key}`).val() === '' || $(`#jenisPenyakitFilter${key}`).val().toLowerCase() === 'semua' || kelasPelayananId.toLowerCase() === 'semua' || kelasPelayananId === '') {
        $(`#ruanganFilter${key}`).prop('disabled', true)
        $(`#ruanganFilter${key}`).val('Semua').trigger('change')
        return false
    }
    if ($(`#ruanganFilter${key}`).hasClass("select2-hidden-accessible")) {
        $(`#ruanganFilter${key}`).select2('destroy')
        $(`#ruanganFilter${key}`).html('')
    }
    $.ajax({
        url: `/ranap/end-point/get-list-ruangan`,
        method: 'GET',
        data: {
            jenisKasusPenyakit: $(`#jenisPenyakitFilter${key}`).val(),
            kelasPelayanan: kelasPelayananId,
        },
        success: (res) => {
            $(`#ruanganFilter${key}`).prop('disabled', false)
            const dataOutput = res.response
            const dataRuangan = [{
              id: '-',
              text: 'Semua'
            }];
            Object.keys(dataOutput).map((keyItem) => {
                dataRuangan.push({
                    id: keyItem,
                    text: dataOutput[keyItem]
                });
            });
            refreshOptionSelect2($(`#ruanganFilter${key}`), dataRuangan)
        },
        error: () => {
          refreshOptionSelect2($(`#ruanganFilter${key}`), [])
        },
        complete: () => {
            hideLoader()
            $("#kamarFilter").prop('disabled', true)
        }
    });
}

function initFilter(key) {
    let dataKasusArray = [{
        id: '',
        text: 'Semua'
    }];
    dataKasus.map((item) => {
        dataKasusArray.push({
            id: item.kelaspelayanan_id,
            text: item.kelaspelayanan_nama
        });
    });
    let dataJenisPenyakit = [{
        id: '',
        text: 'Semua'
    }];
    dataPenyakit.map((item) => {
        dataJenisPenyakit.push({
            id: item.jeniskasuspenyakit_id,
            text: item.jeniskasuspenyakit_nama
        });
    });
    $(`#jenisPenyakitFilter${key}`).select2({
        data: dataJenisPenyakit,
    });
    $(`#kelasFilter${key}`).select2({
        data: dataKasusArray,
    });
}

function setParamDatatable(typeName) {
    if (Object.keys(paramDatatable[typeName]).length == 0) {
        const key = (typeName === 'titipan' ? 'Titipan' : 'NonTitipan');
        paramDatatable[typeName] = {
            penjamin_id: $(".penjamin_id").val(),
            kamar_id: typeof $(`#kamarFilter${key}`) !== 'undefined' && $(`#kamarFilter${key}`).val() !== null ? $(`#kamarFilter${key}`).val() : '',
            status_kamar: typeof $(`#statusKamarFilter${key}`) !== 'undefined' && $(`#statusKamarFilter${key}`).val() !== null ? $(`#statusKamarFilter${key}`).val() : '',
            ruangan_id: typeof $(`#ruanganFilter${key}`) !== 'undefined' && $(`#ruanganFilter${key}`).val() !== null ? $(`#ruanganFilter${key}`).val() : '',
            jenis_id: typeof $(`#jenisPenyakitFilter${key}`) !== 'undefined' && $(`#jenisPenyakitFilter${key}`).val() !== null ? $(`#jenisPenyakitFilter${key}`).val() : '',
            kelas_id: typeof $(`#kelasFilter${key}`) !== 'undefined' && $(`#kelasFilter${key}`).val() !== null ? $(`#kelasFilter${key}`).val() : ''
        }
    }
}

function initDatatable(isKamarTitipan = false, reinit = false) {
    const typeName = isKamarTitipan ? 'titipan' : 'general';
    const key = isKamarTitipan ? 'Titipan' : 'NonTitipan';
    $(`#searchBtn${key}`).prop('disabled', true);

    if (reinit) {
        pageDataKamar[typeName] = 1;
        paramDatatable[typeName] = {}
    }

    createHeaderDatatableKamar(isKamarTitipan);
    setParamDatatable(typeName);

    if (kelas_titipan) {
        columnGenerated = columnKamarTitipan;
        table = $('#tableKamarTitipan');
        $('#tableKamarWrapper').hide();
    } else {
        columnGenerated = columns;
        table = $('#tableKamar');
        $('#tableKamarTitipanWrapper').hide();
    }

    table.parent().show();

    if (pageDataKamar[typeName] === 1 || reinit) {
        table.find('tbody').html('');
    }

    table.block({
        message: null
    });

    if ($('#tempKamar').val() == 1) {
        var params = { ...paramDatatable[typeName], page: pageDataKamar[typeName], gender: $(".jeniskelamin_id").val(), pasien_titipan: true}
    } else {
        var params = { ...paramDatatable[typeName], page: pageDataKamar[typeName], gender: $(".jeniskelamin_id").val()}
    }

    $.ajax({
        url: `/ranap/end-point/get-data-kamar-default`,
        data: params,
        method: 'GET',
        beforeSend: () => {
            hideLoader();
        },
        success: (res) => {
            // Append if
            if (res.data.length === 0 && pageDataKamar[typeName] == 1) {
                table.find('tbody').html(`
                    <tr>
                    <td class="text-center" colspan="${columnGenerated.length}">Data tidak tersedia</td>
                    </tr>
                `);
            } else {
                const records = res.data;
                const tbodySection = table.find('tbody');
                const length = records.length;
                for (let indexRecord = 0; indexRecord < length; indexRecord++) {
                    let trHtml = '<tr>';
                    let valueOfColumn = '';
                    for (let indexColumn = 0; indexColumn < columnGenerated.length; indexColumn++) {
                        if (columnGenerated[indexColumn].data === 'rowNum') {
                            valueOfColumn = (pageDataKamar[typeName] - 1) * 10 + (indexRecord + 1);
                        } else {
                            valueOfColumn = records[indexRecord][columnGenerated[indexColumn].data];
                        }
                        trHtml += `<td ${typeof columnGenerated[indexColumn].className !== 'undefined' ? `class="${columnGenerated[indexColumn].className}"` : ''}>${valueOfColumn}</td>`;
                    }
                    tbodySection.append(`${trHtml}</tr>`)
                    tbodySection.find('td').last().parent().attr('data-akomodasi', records[indexRecord]['is_akomodasi'] ? '1' : '0')
                }
                isStillExistDataKamar[typeName] = records.length > 10;
                pageDataKamar[typeName] += 1;
            }
        },
        error: () => {
            table.find('tbody').html(`
                <tr>
                    <td colspan="${columnGenerated.length}">Terjadi kesalahan</td>
                </tr>
            `);
        },
        complete: () => {
            table.unblock();
            $(`#searchBtn${key}`).prop('disabled', false);
        }
    });
}

const scrollWrapper = document.querySelector('#tableKamarWrapper')
scrollWrapper.addEventListener('scroll', function () {
    if (scrollWrapper.scrollTop + scrollWrapper.clientHeight >= scrollWrapper.scrollHeight && isStillExistDataKamar.general) {
        // initDatatable()
    }
});
const scrollWrapperTitipan = document.querySelector('#tableKamarTitipanWrapper')
scrollWrapperTitipan.addEventListener('scroll', function () {
    if (Math.round(scrollWrapperTitipan.scrollTop + scrollWrapperTitipan.clientHeight) >= scrollWrapperTitipan.scrollHeight && isStillExistDataKamar.titipan) {
        // initDatatable(true)
    }
});
let activeTable = 'kamar';


// ================= //
// FORM PINDAH KAMAR //
// ================= //
function pilihKamar(identifier) {
    let link = document.URL;
    let patternAction = link.match(/pemesanan-kamar/g);
    const jenisTempatTidur = $(identifier).data('kettempattidur_id');
    const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
    const kamarruangan_id = $(identifier).data('kamarruangan_id');
    const kamartempattidur_id = $(identifier).data('kamartempattidur_id');

    var jk_kamar;
    var allow_jk;
    var attr = $(identifier).data('allow_jk');

    if (typeof attr !== typeof undefined && attr !== false) {
        allow_jk = $(identifier).data('allow_jk');
    }

    if (parseInt(jenisTempatTidur) == 1) {
        jk_kamar = 16;
    } else if (parseInt(jenisTempatTidur) == 2) {
        jk_kamar = 15;
    } else {
        jk_kamar = $(".jeniskelamin_id").val();
    }

    if (allow_jk && allow_jk != $(".jeniskelamin_id").val()) {
        docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
        return false;
    }

    if ($(".jeniskelamin_id").val() != jk_kamar) {
        docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
        return false;
    }

    $.ajax({
        url: '/ranap/end-point/cek-ruangan-default',
        data: {
            ruangan_id: $(identifier).data('ruangan_id'),
            kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
            penjamin_id: $(".penjamin_id").val(),
            kamarruanganId: kamarruangan_id,
            kamartempattidurId: kamartempattidur_id
        },
        method: 'POST',
        success: () => {
            $('#modalTempatTidur').modal('hide');
            $(document).find('#kamartempattidur_id').val($(identifier).data('kamartempattidur_id'));
            $(document).find('#kamarruangan_id').val($(identifier).data('kamarruangan_id'));
            $(document).find('#nokamar').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
            $(document).find('#ruanganLabelValue').html(': '+$(identifier).data('ruangan_nama'));
            $(document).find('.ruangan_nama').html(': '+$(identifier).data('ruangan_nama'));
            $(document).find('.kamarruangan_nokamar').text(': '+$(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
            $(document).find('#ruanganIdHidden').val($(identifier).data('ruangan_id'));
            $(document).find('#kelaspelayanan_id').val($(identifier).data('kelaspelayanan_id'));
            $(document).find('.nokamar').text(': '+$(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
            $(document).find('.kelasPelayananLabelValue').text(': '+$(identifier).data('kelaspelayanan_nama'));
            $('.kelasPelayananCariKamar').text($(identifier).data('kelaspelayanan_nama'));
            $(document).find('.kasusPenyakitLabelValue').text(': '+$(identifier).data('jeniskasuspenyakit_nama'));
            $(document).find('.is_pasientitipan').removeClass('hidden');
        },
        error: ({ responseJSON }) => {
            docoNotification('error', responseJSON.meta.message, '');
        },
        complete: () => {
            hideLoader();
            $.unblockUI();
        }
    });
}

function pilihKamarTitipan(identifier) {
    let link = document.URL;
    let patternAction = link.match(/pemesanan-kamar/g);
    const jenisTempatTidur = $(identifier).data('kettempattidur_id');
    const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
    const kamarruangan_id = $(identifier).data('kamarruangan_id');
    const kamartempattidur_id = $(identifier).data('kamartempattidur_id');

    $.ajax({
        url: '/ranap/end-point/cek-ruangan-default',
        data: {
            ruangan_id: $(identifier).data('ruangan_id'),
            kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
            penjamin_id: $(".penjamin_id").val(),
            kamarruanganId: kamarruangan_id,
            kamartempattidurId: kamartempattidur_id
        },
        method: 'POST',
        success: () => {
            var kelas_pelayanan =  $(".kelasPelayananLabelValue").text().slice(2)
            if($(identifier).data('kelaspelayanan_nama') == kelas_pelayanan)
            {
                 return docoNotification('error', 'Untuk kamar titipan tidak boleh menggunakan kelas pelayanan yang sama', '');

            }else {
                $('#modalTempatTidur').modal('hide');
                $(document).find('#ruangan_titipan_id').val($(identifier).data('ruangan_id'));
                $(document).find('#kelas_ditagihkan_id').val($(identifier).data('kelaspelayanan_id'));
                $(document).find('#kamar_titipan_id').val($(identifier).data('kamarruangan_id'));
                $(document).find('#ruanganTitipanLabelValue').html(': '+$(identifier).data('ruangan_nama'));
                $(document).find('#kelasPelayananTitipanLabelValue').text(': '+$(identifier).data('kelaspelayanan_nama'));
            }
;
        },
        error: ({ responseJSON }) => {
            docoNotification('error', responseJSON.meta.message, '');
        },
        complete: () => {
            hideLoader();
            $.unblockUI();
        }
    });
}

function getCurrentDate() {
    var d = new Date();
    var date = d.getDate();
    var month = d.getMonth();
    var montharr = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    month = montharr[month];
    var year = d.getFullYear();
    var day = d.getDay();
    return date + " " + month + " " + year;
}

function clock() {
    var d = new Date();
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour + ":" + min + ":" + sec;

    document.getElementById("tgl_pindahkamar").innerHTML = getCurrentDate() + '  ' + currentTime;
}

function checkTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

setInterval(clock, 1000);

$(document).ready(function() {

    if (nomor_pendaftaran != '' || nomor_rm != ''){
        $('#btn-cari').click();  
    }
    $("#kamarTitipanCheck").bind('change', ({ delegateTarget }) => {
        if ($(delegateTarget).is(':checked')) {
            initDatatable(true)
            activeTable = 'kamarTitipan';
            $("#tableKamarTitipanWrapper").show();
            $(".filterKamarSection").show();
            $("#tableKamarWrapper").hide();
            $("#filterHeaderKamarNonTitipan").hide();
            $("#filterHeaderKamarTitipan").show();
        } else {
            initDatatable(true)
            activeTable = 'kamar';
            $("#filterHeaderKamarNonTitipan").show();
            $("#filterHeaderKamarTitipan").hide();
            $("#tableKamarTitipanWrapper").hide();
            $("#tableKamarWrapper").show();
        }
    });

    $("#jeniskasuspenyakit_id,#kelaspelayanan_id").bind('change', () => {
        $("#kamarTitipanCheck").prop('checked', false);
        $("#filterHeader").html('');
        activeTable = 'kamar';
    });

    $("#pindahkamarform-is_pasientitipan").on("change", function () {
        if (this.checked) {
            $('#tempKamar').val(1);
            var jenis_id = $('#jeniskasuspenyakit_id').val();
            var kelas_id = $('#kelaspelayanan_id').val();
            var ruangan_id = $('#ruangan_id').val();
            kelas_titipan = true;
            $("#modalTempatTidur").find(".modal-title").html("Kelas Tagihan");

            if (!jenis_id) {
                return new PNotify({
                    title: "Terjadi Kesalahan",
                    text: "Jenis Kasus belum dipilih!",
                    addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                    type: "warning"
                });
            }

            if (!kelas_id) {
                return new PNotify({
                    title: "Terjadi Kesalahan",
                    text: "List Kelas Pelayanan belum dipilih!",
                    addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                    type: "warning"
                });
            }
           

            $(document).find('#kelasPelayananTitipanLabelValue').text(': '+$(".kelas_ditagihkan_nama").text());
            $(document).find('#ruanganTitipanLabelValue').text(': '+$(".ruangan_titipan_nama").text());
            initDatatable(activeTable !== 'kamar', true);

            $("#modalTempatTidur").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('.kamar_titipan').removeClass('hidden');
        } else {
            $(document).find('#ruangan_titipan_id').val(null);
            $(document).find('#kelas_ditagihkan_id').val(null);
            $(document).find('#kamar_titipan_id').val(null);
            $(document).find('#tempattidur_titipan_id').val(null);
            $(document).find('#ruanganTitipanLabelValue').html(': -');
            $(document).find('#kelasPelayananTitipanLabelValue').text(': -');
            $(document).find('#kasurTitipanLabelValue').text(': -');
            $(document).find('.kamar_titipan').addClass('hidden');
        }
    });
});

$("#btnCariKamar").bind('click', () => {
    $('#tempKamar').val(0);
    var jenis_id = $('#jeniskasuspenyakit_id').val();
    var kelas_id = $('#kelaspelayanan_id').val();
    var ruangan_id = $('#ruangan_id').val();
    $("#modalTempatTidur").find(".modal-title").html("Tempat Tidur");

    if (!jenis_id) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: "Jenis Kasus belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    }

    if (!kelas_id) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: "List Kelas Pelayanan belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    }

    if ($(".carabayar_id").val() === '5') {
        $("#kamarTitipanCheck").parent().hide();
        activeTable = 'kamar';
    } else {
        $("#kamarTitipanCheck").parent().show();
    }

    initDatatable(activeTable !== 'kamar', true);

    $("#modalTempatTidur").modal({
        backdrop: 'static',
        keyboard: false
    });
});

$("#btnCariKamarTitipan").bind('click', () => {
    $('#tempKamar').val(1);
    var jenis_id = $('#jeniskasuspenyakit_id').val();
    var kelas_id = $('#kelaspelayanan_id').val();
    var ruangan_id = $('#ruangan_id').val();
    $("#modalTempatTidur").find(".modal-title").html("Kelas Tagihan");

    if (!jenis_id) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: "Jenis Kasus belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    }

    if (!kelas_id) {
        return new PNotify({
            title: "Terjadi Kesalahan",
            text: "List Kelas Pelayanan belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
        });
    }

    if ($(".carabayar_id").val() === '5') {
        $("#kamarTitipanCheck").parent().hide();
        activeTable = 'kamar';
    } else {
        $("#kamarTitipanCheck").parent().show();
    }

    initDatatable(activeTable !== 'kamar', true);

    $("#modalTempatTidur").modal({
        backdrop: 'static',
        keyboard: false
    });
});

$('#btn-simpan').click(function (e) {
    e.preventDefault();
    var data = $('#pindah-kamar-form').serializeArray();

    $().docoForm('click', {
        data: data,
        url: $('#pindah-kamar-form').attr('action'),
        success: function (data) {
            if (data.statusCode == 200) {
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
        },
        error: function (data) {
        }
    });
});

$("#no_pendaftaran").keypress(function(event) {
    var key = event.which;

    if (key == 13) {
        $('#btn-cari').click();  
    }
});

$("#no_rekam_medik").keypress(function(event) {
    var key = event.which;

    if (key == 13) {
        $('#btn-cari').click();  
    }
});

$('#btn-cari').click(function(event) {
    event.preventDefault();
    var no_pendaftaran = (nomor_pendaftaran != '') ? nomor_pendaftaran : $('#no_pendaftaran').val();
    var no_rekam_medik = (nomor_rm != '') ? nomor_rm : $('#no_rekam_medik').val();
    if (no_pendaftaran != '' || no_rekam_medik != '') {
        $.ajax({
            url: 'pindah-kamar/get-data-pasien',
            data: {
                no_pendaftaran: no_pendaftaran,
                no_rekam_medik: no_rekam_medik
            },
            success: (result) => {
                var data = JSON.parse(result);
                if (!Array.isArray(data)) {
                    $(".no_pendaftaran").text(data.no_pendaftaran);
                    $(".no_rekam_medik").text(data.no_rekam_medik);
                    $(".nama_pasien").text(data.nama_pasien);
                    $(".jenis_kelamin").text(data.jenis_kelamin);
                    $(".tgl_pendaftaran").text(data.tgl_pendaftaran);
                    $(".tanggal_lahir").text(data.tanggal_lahir);
                    $(".umur").text(data.umur);
                    $(".kelaspelayanan_nama").text(data.kelaspelayanan_nama);
                    $(".kelasPelayananLabelValue").text(': '+data.kelaspelayanan_nama);
                    $(".carabayar_nama").text(data.carabayar_nama);
                    $(".penjamin_nama").text(data.penjamin_nama);
                    $(".dokter_dpjp").text(data.dokter_admisi);
                    $(".jeniskasuspenyakit_nama").text(data.jeniskasuspenyakit_nama);
                    $(".kasusPenyakitLabelValue").text(': '+data.jeniskasuspenyakit_nama);
                    $(".ruangan_nama").text(': '+data.ruangan_nama);
                    $(".info_pasien_ruangan_nama").text(''+data.ruangan_nama);
                    $(".kamarruangan_nokamar").text(': '+data.kamarruangan_nokamar);
                    $(".info_pasien_kamarruangan_nokamar").text(''+data.kamarruangan_nokamar);
                    $(".no_tempattidur").text(data.no_tempattidur);
                    $(".stat_ranap").text(data.stat_ranap);
                    $(".penjamin_id").val(data.penjamin_id);
                    $(".carabayar_id").val(data.carabayar_id);
                    $(".jeniskelamin_id").val(data.jeniskelamin_id);
                    $(".kelas_ditagihkan_nama").text(data.kelas_ditagihkan_nama);
                    $(".ruangan_titipan_nama").text(data.ruangan_titipan_nama);
                    $(".kamar_titipan_nama").text(data.kamar_titipan_nama);
                    $("#kelasPelayananTitipanLabelValue").text(": "+data.kelas_ditagihkan_nama);

                    $("#jeniskasuspenyakit_id").val(data.jeniskasuspenyakit_id);
                    $("#kelaspelayanan_id").val(data.kelaspelayanan_id);
                    $("#ruangan_id").val(data.ruangan_id);
                    $("#kamartempattidur_no_tempattidur").val(data.kamartempattidur_no_tempattidur);
                    $("#pendaftaran_id").val(data.pendaftaran_id);
                    $("#pasienadmisi_id").val(data.pasienadmisi_id);
                    $("#kamarruangan_id").val(data.kamarruangan_id);
                    $("#old_kamarruangan_id").val(data.kamarruangan_id);
                    $("#kamartempattidur_id").val(data.kamartempattidur_id);

                    $(".identitas_pasien").prop("hidden", false);
                    $(".pindah_kamar_form").prop("hidden", false);
                } else {
                    $(".no_pendaftaran").text("");
                    $(".no_rekam_medik").text("");
                    $(".nama_pasien").text("");
                    $(".jenis_kelamin").text("");
                    $(".tgl_pendaftaran").text("");
                    $(".tgl_lahir").text("");
                    $(".umur").text("");
                    $(".kelaspelayanan_nama").text("");
                    $(".kelasPelayananLabelValue").text("");
                    $(".carabayar_nama").text("");
                    $(".penjamin_nama").text("");
                    $(".dokter_dpjp").text("");
                    $(".jeniskasuspenyakit_nama").text("");
                    $(".kasusPenyakitLabelValue").text("");
                    $(".ruangan_nama").text("");
                    $(".kamarruangan_nokamar").text("");
                    $(".no_tempattidur").text("");
                    $(".stat_ranap").text("");
                    $(".penjamin_id").val("");
                    $(".carabayar_id").val("");
                    $(".jeniskelamin_id").val("");
                    $("#pindahkamarform-pendaftaran_id").val("");
                    $("#pindahkamarform-pasienadmisi_id").val("");

                    $("#jeniskasuspenyakit_id").val(null);
                    $("#kelaspelayanan_id").val(null);
                    $("#ruangan_id").val(null);
                    $("#kamartempattidur_no_tempattidur").val(null);
                    $("#pendaftaran_id").val(null);
                    $("#pasienadmisi_id").val(null);
                    $("#kamarruangan_id").val(null);
                    $("#kamartempattidur_id").val(null);

                    $(".identitas_pasien").prop("hidden", true);
                    $(".pindah_kamar_form").prop("hidden", true);

                    docoNotification("warning", 'Peringatan!', "Data pasien tidak ditemukan.");
                }
            }
        });
    }
});

$('#chevron').click(function(event) {
    event.preventDefault();
    var no_pendaftaran = $(".no_pendaftaran").text();
    var no_rekam_medik = $(".no_rekam_medik").text();
    var nama_pasien = $(".nama_pasien").text();

    if ($("#infopasien").hasClass("in")) {
        $(".informasi_pasien").html("<span><b>INFORMASI PASIEN</b></span>&nbsp;(<span><b>"+no_pendaftaran+"</b></span>&nbsp;-&nbsp;<span><b>"+no_rekam_medik+"</b></span>&nbsp;-&nbsp;<span><b>"+nama_pasien+"</b></span>)");
    } else {
        $(".informasi_pasien").html("<span><b>INFORMASI PASIEN</b></span>");
    }
});

$('#btn-reset').click(function(event) {
    location.reload();
});