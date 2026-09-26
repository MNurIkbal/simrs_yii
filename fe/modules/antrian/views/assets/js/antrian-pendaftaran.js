$(document).ready(function() {
    
    Array.prototype.chunk = function (chunk_size) {
        let results = [];
        const temp = this.slice(0);
        while (temp.length) {
            results.push(temp.splice(0, chunk_size));
        }
      
        return results;
    };

    $.fn.stepy.defaults.legend = false;
    $.fn.stepy.defaults.transition = 'fade';
    $.fn.stepy.defaults.duration = 150;
    $.fn.stepy.defaults.backLabel = '';
    $.fn.stepy.defaults.nextLabel = '';

    $('#ambil-antrian-form').stepy({
        block: true,
        legend:false,
        back: function(index) {
        },
        next: function(index) {
        },
        finish: function(index) {
            if($('input:radio[name=\"status_pilih\"]:checked').length == 0){
                $('#error-opt-jaminan').empty();
                var error = '<label id=\"status_pilih-error\" class=\"label label-danger label-roundless\" for=\"status_pilih\">Status Pasien Belum Dipilih</label>';
                $('#error-opt-jaminan').append(error);
                return false;
            }else if($('input:radio[name=\"carabayar_pilih\"]:checked').length == 0){
                $('#error-opt-jaminan').empty();
                var error = '<label id=\"carabayar_pilih-error\" class=\"label label-danger label-roundless\" for=\"carabayar_pilih\">Cara Bayar Belum Dipilih</label>';
                // error.appendTo($('#error-opt-jaminan'));
                $('#error-opt-jaminan').append(error);
                return false;
            }else{
                return true;
            }
        }
    });

    $('.card-poli').on('click', function(){
        $('.back-antrian').hide();
        $('.back-form').show();
        page = 2;
        $('#slide-dokter .carousel-control').hide();
        postData.ruangan_id = $(this).data('poli-id'); // ruangan_id
        postData.poly_pilih = $(this).data('poli-id'); // ruangan_id
        var jadwalbukapoli_id = $(this).data('jadwalbukapoli-id'); //jadwalbukapoli_id 
        
        setActiveCard($(this), '.card-poli');
        $('#ambil-antrian-form').stepy('step',2);
        var loading = $('#loading-content');
        loading.empty();
        $('#slide-dokter ol').empty();
        $('#slide-dokter .carousel-inner').empty();

        $.ajax({
            type: 'GET',
            url: '/antrian/dashboard/get-list-dokter-by-poly-id',
            data: {
                poly_id: postData.poly_pilih,
                jadwalbukapoli_id: jadwalbukapoli_id
            },
            beforeSend: function(){
                loading.append('<h1 align="center"><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Memuat ... </b></h1>');
            },
            success: function(res) {
                loading.empty();
                if(res.message == 'success'){
                    var data = res.data;
                    var total_card = 0;
                    var total_slide = 1;
                    var items = [];
                    var hide = 'hide';
                    
                    if (data.length > 0) {
                        total_card = data.length;
                        if (total_card > 0) {
                            if (total_card > 8) {
                                hide = 'show';
                            }

                            pembagi = total_card / 8;
                            total_slide = Math.ceil(pembagi);
                            items = data.chunk(8);
                        }
                    }
                    if (hide) {
                        $('#slide-dokter ol').hide();
                        $('#slide-dokter .carousel-control').hide();
                    } else {
                        $('#slide-dokter ol').show();
                        $('#slide-dokter .carousel-control').show();
                    }

                    // Add element List
                    var elOl = '';
                    var elCarousel = '';
                    for (var i=0; i < total_slide; i++) { 
                        var className = (i == 0) ? 'active' : '';
                        elOl += `<li data-target="#slide-dokter" data-slide-to="${i}" class="${className}"></li>`;

                        //Element Carousel
                        elCarousel += `<div class="item ${className}">`;

                        if (items.length > 0) {
                            elCarousel += `<div class="row">`;

                            $.each(items[i], function(key, val) {
                                disable_card = val.sisa_kuota == 0 ? 'inactive' : '';
                                icon_poli = val.sisa_kuota == 0 ? 'inactive-doctor.png' : 'doctor.png';

                                var j_mulai = this.jadwaldokter_mulai.split(':');
                                    j_mulai = j_mulai[0]+':'+j_mulai[1];
    
                                var j_tutup = this.jadwaldokter_tutup.split(':');
                                    j_tutup = j_tutup[0]+':'+j_tutup[1];

                                elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;
                                                 elCarousel += `<div class="col-sm-3">
                                                    <div class="card card-dokter ${disable_card}" data-pegawai-id="${val.pegawai_id}" data-jadwaldokter-id="${val.jadwaldokter_id}">
                                                        
                                                        <h4 class="nama">${val.nama_dokter}</h4>
                                                        <div class="jam-operasional">
                                                            <span>Jam Buka</span>
                                                            <h6>${j_mulai +' - '+ j_tutup}</h6>
                                                        </div>
                                                        <div class="quota">
                                                            <span>Sisa Kuota</span>
                                                            <span class="sisa-kuota">${val.kuota_tersedia}</span>
                                                        </div>
                                                        <button type="button" class="btn btn-primary">Pilih</button>
                                                    </div>
                                                </div>`;

                            });
                            elCarousel += `</div>`;
                        } else {

                        }
                        elCarousel += `</div>`;
                    }
                    $('#slide-dokter ol').html(elOl);
                    $('#slide-dokter .carousel-inner').html(elCarousel);
                }
            }
        });
    });

    $(document).on('click', '.card-dokter', function(){
        var loading = $('#loading-content');
        loading.empty();
        setActiveCard($(this), '.card-dokter');
        page = 3;
        
        postData.dokter_pilih = $(this).data('pegawai-id');
        postData.jadwaldokter_pilih = $(this).data('jadwaldokter-id');
        if (isKeteranganPasien == 1) {
            loading.append('<h1 align="center"><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Memuat ... </b></h1>');
            $('#ambil-antrian-form').stepy('step', 3);
            loading.empty();
        } else {
            var form = $('#ambil-antrian-form');
            var url = form.attr('action');

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: postData,
                success: function (res) {

                    var succMessage = 'Proses Berhasil!';
                    var succText = 'Antrian Dicetak';
                    var msg = res.response;
                    var noantrian = '';
                    var jenis_id = 'Mq';
                    var antrian_id = 0;
                    var no_antrian_poli = '';
                    if(msg.text != undefined){
                        succText = msg.text;
                    }
                    if(msg.message != undefined){
                        succMessage = msg.message;
                    }
                    new PNotify({
                        title: succMessage,
                        text: succText,
                        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                        type: 'success'
                    });
                    if(msg.data.no_antrian != undefined){
                        noantrian = msg.data.no_antrian;
                    }
                    if(msg.data.jenisantrian_id != undefined){
                        jenis_id = msg.data.jenisantrian_id;
                    }
                    if(msg.data.antrian_id != undefined){
                        antrian_id = msg.data.antrian_id;
                    }
                    if(msg.data.no_antrian_poli != undefined){
                        no_antrian_poli = msg.data.no_antrian_poli;
                    }
                    $('#modal_backdrop').modal('hide');
                    
                    $.ajax({
                        type: 'GET',
                        url: konfig_url_cetak,
                        timeout: (5 * 1000),
                        data: res['response']['data']['cetak'],
                        success: function (res){
                            location.reload();
                        },
                        error: function () {
                            $.redirect("/antrian/dashboard/cetak-antrian-dashboard",{no_antrian:noantrian,jenisantrian_id:jenis_id,antrian_id:antrian_id,no_antrian_poli:no_antrian_poli});
                        }
                    });
                },
                error: function (res) {
                    $('body').find('.confirm-dialog-overlay').remove();
                    $('body').find('#confirm-dialog').remove();
                    var errMessage = 'Gagal Diproses';
                    var errText = 'Terjadi Kesalahan';
                    new PNotify({
                        title: errMessage,
                        text: errText,
                        addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
                        type: 'danger'
                    });
                }
            });
        }
    });


    $('.card-pasien').on('click', function() {
        postData.klasifikasi_pilih = $(this).data('klasifikasipasienid');
        klasifikasiPasien.klasifikasipasien_id = $(this).data('klasifikasipasienid');
        var carabayar = $(this).data('carabayar');

        resetCard('.card-pasien');
        setActiveCard($(this), '.card-pasien');
        if (carabayar) {
            carabayar = carabayar.toString();
            klasifikasiPasien.cara_bayar = carabayar.split("-");
        }
    });

    $('.back-form').on('click', function() {
        if (page == 2) {
            page = 1;
            $(this).hide();
            $('#ambil-antrian-form').stepy('step', 1);
        } else if (page == 3) {
            page = 2;
            $('#ambil-antrian-form').stepy('step', 2);
        } else {
             $(this).hide();
        }
    });

    $("#slide-poli").carousel({
        interval: false,
        wrap: false
    });
    $("#slide-dokter").carousel({
        interval: false,
        wrap: false
    });

    $("#slide-poli").on("slid.bs.carousel", "", function() {
        var $this;
        $this = $("#slide-poli");
        if ($("#slide-poli .carousel-inner .item:first").hasClass("active")) {
            $this.children(".left").addClass('disabled');
            $this.children(".right").removeClass('disabled');
        } else if ($("#slide-poli .carousel-inner .item:last").hasClass("active")) {
            $this.children(".right").addClass('disabled');
            $this.children(".left").removeClass('disabled');
        } else {
            $this.children(".carousel-control").show();
        }
    });
    $("#slide-dokter").on("slid.bs.carousel", "", function() {
        var $this;
        $this = $("#slide-dokter");
        if ($("#slide-dokter .carousel-inner .item:first").hasClass("active")) {
            $this.children(".left").addClass('disabled');
            $this.children(".right").removeClass('disabled');
        } else if ($("#slide-dokter .carousel-inner .item:last").hasClass("active")) {
            $this.children(".right").addClass('disabled');
            $this.children(".left").removeClass('disabled');
        } else {
            $this.children(".carousel-control").show();
        }
    });

    function setActiveCard(el, card) {
        $(card+' .card-checked').remove();
        var elm = `<div class="card-checked"><i class="fa fa-check fa-lg"></i></div>`;
        $(el).prepend(elm);
    }

    function resetCard(card) {
        $(card+' .card-checked').remove();
    }

    
});