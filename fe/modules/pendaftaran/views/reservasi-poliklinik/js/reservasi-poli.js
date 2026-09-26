/*
* @Author: Naufal Ziyad L
* @Date:   2018-02-08 14:39:00
*/

var listdata = {listpendaftaran: {},listpasien: {}, listnorm: {}};
var selectRekamMedik = $('.selectRekamMedik');
var selectPasien = $('.selectPasien');
var selectPanggilan = $('.selectPanggilan');
var selectTempatLahir = $('.selectTempatLahir');
var selectTanggalLahir = $('.selectTanggalLahir');
var selectUmur = $('.selectUmur');
var selectAlamatPasien = $('.selectAlamatPasien');
var selectNoTelp = $('.selectNoTelp');
var selectNoRm = $('.selectRekamMedik');
var selectJenisKelamin = $('.selectJenisKelamin');
var selectPasienId = $('.selectPasienId');
var selectStatusJanji = $('.selectStatusJanji');
var selectTanggalSekarang = $('.selectTanggalSekarang');
var selectHari = $('.selectHari');
var selectAntrian = $('.selectAntrian');
var selectByPhone = $('.selectByPhone');
var infoPasien = null;
var groupBPJS = 418;

var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();

if(dd<10) {
    dd = '0'+dd
} 

if(mm<10) {
    mm = '0'+mm
} 

today = yyyy + '/' + mm + '/' + dd;

/*var byphone = $('.byphone');*/
$('.inputpemesan').hide();
$('.ByPhoneText').val(0);
$('#by_phonecheck').click(function(){
    if($(this).is(":checked")){ 
        $('.ByPhoneText').val(1);
      }
    else{
        $('.ByPhoneText').val(0);
      }
});

$(document).ready(function(){
    $(".pickadate").pickadate({
        format: "dd mmm yyyy",
    });

    // $('#form-reservasi-poli').docoForm('submit',{    
    //     before: function(){
    //         console.log('tester');
    //         return false;
    //     },  
    //     success : function(response) {            
    //         this.formInput[0].reset();
    //         console.log('suksas');
    //         selectRekamMedik.val("").trigger("change");
    //     }
    // });  

    datapendaftaran = [];
    datanorm = [];
    datapasien = [];
    selectRekamMedik.select2({          
            placeholder: 'Pilih No Rekam Medik',
            minimumInputLength: 2,              
            ajax: {
                url: "/pendaftaran/reservasi-poliklinik/get-rekam-medik",
                dataType: 'json',
                quietMillis: 250,               
                data: function (term, page) {
                    return {
                        q: term,
                        page: page
                    };
                },
                processResults: function (data) {                
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
    selectRekamMedik.change(function(e){
        e.preventDefault();
        var id = $(this).val();         
        getData(id);        
    });
    $('#tombol-reset').click(function(){        
        selectRekamMedik.val("").trigger('change.select2');
    });

    var startDate = new Date();
    startDate.setDate(startDate.getDate());
    var endDate = new Date(startDate);
    endDate.setDate(endDate.getDate() + reservasiAkhir);
    $(".tgl_pendaftaranol").pickadate({
        monthsFull: [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ],
        formatSubmit: 'yyyy-mm-dd',
        format: 'dd mmmm yyyy',
        min: startDate,
        max: endDate
    });

    $(".field-no_rujukan").hide();
    $(".field-no_bpjs").hide();
    $(".field-jeniskunjungan").hide();
    
});

$(".btn-batal-info-pasien").click(function() {
    $('#info-pasien').hide();
    $('#cari-pasien').show();
    $("#cari_pasien").val("").trigger("change");
});

$("#reservasipoliklinikform-jam_kunjungan").change(function() {
    var jam = $(this).val();
    var jadwalId = $('option:selected', this).data('jadwal');

    if (jam) {
        jam = jam.split("-");

        $("#reservasipoliklinikform-jam_mulai").val(jam[0]);
        $("#reservasipoliklinikform-jam_tutup").val(jam[1]);
    }

    if (jadwalId) {
        $("#reservasipoliklinikform-jadwaldokter_id").val(jadwalId);
        $("#reservasipoliklinikform-jadwalbukapoli_id").val(jadwalId);
    }
});

$("#form-reservasi-poliklinik").docoForm("submit", {
    success : function(data) {
        var no_pendaftaranol = data.response.data[0].no_pendaftaranol;
        var pendaftaranol_id = data.response.data[0].pendaftaranol_id;
        if(no_pendaftaranol) {
            (new PNotify({
                title: "Proses Berhasil",
                text: "Reservasi Poliklinik berhasil, nomor reservasi poli Anda adalah "+no_pendaftaranol+". Apakah anda akan mencetak karcis reservasi?",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true,
                    buttons: [
                        {
                            text: 'Ya',
                            addClass: 'btn btn-xs btn-success',
                        },
                        {
                            text: 'Tidak',
                            addClass: 'btn btn-xs btn-danger',
                        }
                    ]
                },
                history: {
                    history: false
                }
            })).get().on('pnotify.confirm', function() {
                window.open("/pendaftaran/reservasi-poliklinik/cetak-karcis?id="+pendaftaranol_id);
                location.reload();
            }).on('pnotify.cancel', function() {
                location.reload();
            });
        }
    },
    error : function(data) {
        if('is_pasien' in data.responseJSON.response) {
            (new PNotify({
                title: "Proses Gagal",
                text: "Tidak dapat melanjutkan reservasi pasien baru. Harap membuat no rekam medik pasien terlebih dahulu. Ingin membuat nomor rekam medik?",
                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                type: "warning",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true,
                    buttons: [
                        {
                            text: 'Ya',
                            addClass: 'btn btn-xs btn-success',
                        },
                        {
                            text: 'Tidak',
                            addClass: 'btn btn-xs btn-danger',
                        }
                    ]
                },
                history: {
                    history: false
                }
            })).get().on('pnotify.confirm', function() {
                window.open("/pendaftaran/pembuatan-nomor-rekam-medik/create");
            }).on('pnotify.cancel', function() {
                
            });
        }
    }
});

$(".carabayar_id").on('change', function() {
    var _groupCaraBayar = $(this).find(':selected').attr('data-id');
    if (_groupCaraBayar == groupBPJS) {
        $(".field-no_rujukan").show();
        $(".field-no_bpjs").show();
        $(".field-jeniskunjungan").show();
    } else {
        $(".field-no_rujukan").hide();
        $(".field-no_bpjs").hide();
        $(".field-jeniskunjungan").hide();
    }
});

function getData(id){
    $.ajax({
        url: '/pendaftaran/reservasi-poliklinik/get-data?id='+id,
        type: 'GET',
        beforeSend: function(){
            let loadtext = 'Loading...';
            selectNoRm.val(loadtext)
            selectPasien.val(loadtext)
            selectPanggilan.val(loadtext)
            selectTempatLahir.val(loadtext)
            selectTanggalLahir.val(loadtext)
            selectUmur.val(loadtext)
            selectAlamatPasien.val(loadtext)
            selectNoTelp.val(loadtext)
            selectJenisKelamin.val(loadtext)
            selectStatusJanji.val(loadtext)

        },
        success: function(response){
            if(response.data_pasien != 'kosong'){
                let data = response.data_pasien;
                selectNoRm.val(data.no_rekam_medik);
                selectPasien.val(data.nama_pasien);
                selectPanggilan.val(data.nama_bin);
                selectTempatLahir.val(data.tempat_lahir);
                selectTanggalLahir.val(data.tanggal_lahir);
                selectAlamatPasien.val(data.alamat_pasien);
                selectNoTelp.val(data.no_telepon_pasien);
                selectJenisKelamin.val(data.jeniskelamin);
                selectUmur.val(data.umur);
                selectPasienId.val(data.pasien_id);
                selectStatusJanji.val(357);
                selectTanggalSekarang.val(today);
                selectHari.val(0);
                selectAntrian.val(1);
                $('.jk'+data.jeniskelamin).prop('checked', true);
            }
        }
    })
}

function getInfoPasien(id) {
    $.ajax({
        url: '/pendaftaran/reservasi-poliklinik/get-pasien?id='+id,
        type: 'GET',
        success: function(response) {
            infoPasien = response.results;
        }
    }).done(function() {
        console.log(infoPasien.tanggal_lahir,"hitto");
        $('#info-pasien').show();
        $('#cari-pasien').hide();
        $('.inf-pasien-title-nama').html("<strong> "+infoPasien.nama_depan+infoPasien.nama_pasien+"</strong>");
        $('.inf-pasien-title-norm').html(i18next.t('No. rekam medis') + " : " + infoPasien.no_rekam_medik);
        $('#inf-pasien-namapasien').html(": "+infoPasien.nama_pasien);
        $('#inf-pasien-tempatlahir').html(": "+infoPasien.tempat_lahir);

        if (infoPasien.tanggal_lahir == "-") {
            $('#inf-pasien-tanggallahir').html(": -");
            $('#inf-pasien-umur').html(": -");
        } else {
            $('#inf-pasien-tanggallahir').html(": "+convertTanggalView(infoPasien.tanggal_lahir));
            $('#inf-pasien-umur').html(": "+getUmur(infoPasien.tanggal_lahir, new Date()));
        }
        $('#inf-pasien-jeniskelamin').html(": "+infoPasien.jenis_kelamin);
        $('#inf-pasien-alamatpasien').html(": "+infoPasien.alamat_pasien);
        $('#inf-pasien-notelepon').html(": "+infoPasien.no_telepon_pasien);
        $('#inf-pasien-nomobile').html(": "+infoPasien.no_mobile_pasien);

        if (infoPasien.photopasien) {
          img = "/media/img/pasien/"+infoPasien.photopasien;
        }else {
          img = "/media/img/icon-app/default.jpg";
        }

        $('#pasang_image').empty().append('<img id="profilePict" src="'+img+'" alt="">');
    });
}

function clearInfoPasien() {
    $('.inf-pasien-title-nama').html("");
    $('.inf-pasien-title-norm').html("");
    $('#inf-pasien-namapasien').html("");
    $('#inf-pasien-tempatlahir').html("");
    $('#inf-pasien-tanggallahir').html("");
    $('#inf-pasien-umur').html("");
    $('#inf-pasien-jeniskelamin').html("");
    $('#inf-pasien-alamatpasien').html("");
    $('#inf-pasien-notelepon').html("");
    $('#inf-pasien-nomobile').html("");
}