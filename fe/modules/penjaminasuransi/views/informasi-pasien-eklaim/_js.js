$(document).ready(function () {
    let cari = $('#btn-cari')
    let tipeKlaim = $('#tipe-klaim')
    let jenisTanggal = $('#tipe-tanggal')
    let tgl = $('#tanggal')
    let tblTotal = $('#table-total')
    let tblDetail = $('#table-detail')
    let btnDetailKlaim = $('#btn-detail-klaim')
    let listEklaim = []
    let postKlaim = []
    let totalSend = 0;

    getData();

    tipeKlaim.on('change', function () {
        getData()
    })

    jenisTanggal.on('change', function () {
        let val = $(this).val()
        let today = formatDate()
        tgl.val(today)
        tgl.attr('disabled', false)

        if (val) {
            getData()
        } else {
            tgl.val('')
            tgl.attr('disabled', true)
        }

    })

    tgl.on('change', function () {
        getData()
    })

    cari.on('click', function() {
        getData()
    })

    function formatDate() {
        var list = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        var d = new Date(),
            index = (d.getMonth()),
            day = d.getDate(),
            year = d.getFullYear();
        var month = list[index];

        return [day, month, year].join('-');
    }

    $(document).on('click', '#btn-detail-klaim', function () {
        tblTotal.fadeOut('fast', 'swing');
        tblDetail.fadeIn('slow', 'swing');

        let tipeKlaim = $(this).data('tipe-klaim')
        let jenisTanggal = $(this).data('jenis-tanggal')
        let tanggal = $(this).data('tanggal')

        $.ajax({
            type: 'GET',
            url: '/penjamin-asuransi/informasi-pasien-eklaim/get-detail',
            data: {
                tipe_klaim: tipeKlaim,
                jenis_tanggal: jenisTanggal,
                tanggal: tanggal
            },
            success: function (res) {
                if (res.total > 0) {
                    var data = res.data;
                    listEklaim = res.data;
                    var el = ``;
                    var no = 1;

                    $.each(data, function (index, value) {
                        var trClass = 'warning';
                        if (value.is_terkirim) {
                            trClass = 'success';
                        }
                        el += `<tr class="parent ${trClass}" data-active-child="false" data-no-sep="${value.no_sep}">
		           	 			<td class="text-center">${no}</td>
		           	 			<td class="text-center"><span class="action-click">${value.tgl_masuk_1}</span></td>
		           	 			<td class="text-center">${value.tgl_keluar_1}</td>
		           	 			<td class="text-center">${value.no_sep}</td>
		           	 			<td>${value.pasien}</td>
		           	 			<td class="text-left">${value.cbg_icd}</td>
		           	 			<td class="text-center">${value.special_all}</td>
		           	 			<td class="text-right">${value.tarif_klaim}</td>
		           	 			<td class="text-right">${value.total_tarifrs}</td>
		           	 			<td class="text-center">${value.terkirim}</td>
	           	 		      </tr>`;

                        no++;
                    });
                    el += `
	           			<tr>
	           				<td colspan="6" class="text-right"><h4 class="grand-total"><b>Total :</b></h4></td>
	           				<td class="text-right" colspan="2"><h4 class="grand-total">${res.total_klaim}</h4></td>
	           				<td class="text-left" colspan="2"><h4 class="grand-total">${res.total_rs}</h4></td>
	           				<td></td>
	           			</tr>
	           			`;

                    tblDetail.children('tbody').html(el);
                } else {
                    var el = `<tr>
                                <td colspan="10" class="text-center"> Tidak ada data </td>
                              </tr>`;
                    tblDetail.children('tbody').html(el);
                }
            }
        });
    });

    function getData() {
        let valTipeKlaim = tipeKlaim.val()
        let valJenisTanggal = jenisTanggal.val()
        let valTgl = tgl.val()
        totalSend = 0;
        var elDetail = `<tr>
                <td colspan="10" class="text-center"> Sedang Memuat </td>
              </tr>`;
        tblDetail.children('tbody').html(elDetail);

        if (valTgl) {
            $.ajax({
                type: 'GET',
                url: '/penjamin-asuransi/informasi-pasien-eklaim/get-data',
                data: {
                    tipe_klaim: valTipeKlaim,
                    jenis_tanggal: valJenisTanggal,
                    tanggal: valTgl
                },
                success: function (res) {
                    var data = res.data;
                    tblDetail.fadeOut('fast', 'swing');
                    tblTotal.fadeIn('slow', 'swing');
                    $('#total-klaim').html(data.total);

                    if (res.total > 0) {

                        postKlaim = res.klaim;

                        var el = `<tr>
    		           	 			<td class="text-center">${data.rajal}</td>
    		           	 			<td class="text-center">${data.ranap}</td>
    		           	 			<td class="text-center">${data.total}</td>
    		           	 			<td class="text-center">${data.unsend}</td>
    		           	 			<td class="text-center">${data.send}</td>
    		           	 			<td class="text-center">${data.aksi}</td>
    	           	 		      </tr>`;
                        tblTotal.children('tbody').html(el);
                        totalSend = data.unsend;
                        if (data.unsend > 0) {
                            $('#btn-kirim-online').attr('disabled', false);
                        } else {
                            $('#btn-kirim-online').attr('disabled', true);
                        }
                    } else {
                        $('#btn-kirim-online').attr('disabled', true);
                        var el = `<tr>
                                    <td colspan="6" class="text-center"> Tidak ada data</td>
                                  </tr>`;
                        tblTotal.children('tbody').html(el);
                    }
                }
            });
        } else {
            // let el = `
            //     <tr>
            //         <td colspan="6" class="text-center"> Tidak ada data/td>
            //     </tr>
            // `;
            // $('#total-klaim').html(0);
            // tblTotal.children('tbody').html(el)

            $.ajax({
                type: 'GET',
                url: '/penjamin-asuransi/informasi-pasien-eklaim/get-data',
                data: {
                    tipe_klaim: valTipeKlaim,
                    jenis_tanggal: valJenisTanggal,
                    tanggal: valTgl
                },
                success: function (res) {
                    var data = res.data;
                    tblDetail.fadeOut('fast', 'swing');
                    tblTotal.fadeIn('slow', 'swing');
                    $('#total-klaim').html(data.total);

                    if (res.total > 0) {

                        postKlaim = res.klaim;

                        var el = `<tr>
    		           	 			<td class="text-center">${data.rajal}</td>
    		           	 			<td class="text-center">${data.ranap}</td>
    		           	 			<td class="text-center">${data.total}</td>
    		           	 			<td class="text-center">${data.unsend}</td>
    		           	 			<td class="text-center">${data.send}</td>
    		           	 			<td class="text-center">${data.aksi}</td>
    	           	 		      </tr>`;
                        tblTotal.children('tbody').html(el);

                        totalSend = data.unsend;
                        if (data.unsend > 0) {
                            $('#btn-kirim-online-batch').attr('disabled', false);
                        } else {
                            $('#btn-kirim-online-batch').attr('disabled', true);
                        }
                    } else {
                        $('#btn-kirim-online-batch').attr('disabled', true);
                        var el = `<tr>
                                    <td colspan="6" class="text-center"> Tidak ada data</td>
                                  </tr>`;
                        tblTotal.children('tbody').html(el);
                    }
                }
            });
        }
    }
    $('#btn-kirim-online').on('click', function () {
        if (postKlaim) {
            $().docoForm('click', {
                confirmMessage: 'Apakah Anda yakin akan melakukan pengiriman klaim (online) Sebanyak ' + totalSend + ' Klaim?',
                data: postKlaim,
                url: '/penjamin-asuransi/informasi-pasien-eklaim/kirim-klaim-online',
                success: function (data) {
                    $('#btn-detail-klaim').click();

                }
            });
        }
    });

    $('#btn-kirim-online-batch').on('click',function (event) {
       if(postKlaim) {
            event.preventDefault();
            var header = "Perhatian!";
            var message = "Apakah anda yakin untuk melakukan kirim klaim batch?";
            var label = {
                buttons: {
                    "Yes": "button-yes",
                    "No": "button-no"
                },
            };

            $.showQuestionDialog(header, message, label, function (reaction) {
                if (reaction == "Yes") {
                    hideQuestionDialog();
                    $.ajax({
                        url : '/penjamin-asuransi/informasi-pasien-rajal-bpjs/get-random-string',
                        success : function (data) {
                            var randString = data
                            if(randString !== null || randString != '') {
                                $('#modal_progress').modal('toggle');
                                updateProgressBarSinkron(randString)
                            }
                        }
                    });
                } else {
                    hideQuestionDialog();
                }
            });
        }
    });

    const showInfo = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve($(".populate-data").html(`mempersiapkan data ...`))
            }, 1000);
            setTimeout(() => {
                resolve($(".populate-data").css("display", "none"))
                resolve($(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
            }, 2000);
        })
    }
    
    const setPresentase = function(progress) {
        $(".progress .label-persentase").html(progress)
        $(".progress .progress-bar").css("width", progress +"%")
        .attr("aria-valuenow", progress)
        .attr("aria-volume", progress);
    }    

    async function updateProgressBarSinkron(randString) {
        let config = await $.getJSON("./../../json/setup.json")
        if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
        } else {
            var socket = io.connect(config.ip+':'+config.port);
        }
    
        const channel = `export-excel:`
        await showInfo()
        $.ajax({
            url : '/penjamin-asuransi/informasi-pasien-eklaim/kirim-klaim-online-batch?randString=' + randString,
            data: postKlaim,
            success : function (data) {
                let startNum = 5
                setPresentase(startNum)
                let totalProgres = parseInt(startNum) + parseInt(data.response.totalPerPage) + 20;
                $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Pengiriman ke BPJS sedang berjalan </p>`);

                socket.on(channel + data.response.randString, (message) => {
                    console.log(message)
                    const _data = $.parseJSON(message);
                    const { status , messageProcess , progress, process} = _data
                    if(status == 'finish') {
                        $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                        setPresentase(progress)
                        if (progress == 100) {
                            docoNotification("success", "Proses Berhasil", "Data Berhasil Tersinkronisasi");
                            $('#modal_progress').modal('hide');
                            getData();
                            return false;
                        }
                        
                        if (process == 'gagal') {
                            docoNotification("error", "Proses Gagal", messageProcess);
                            return false;
                        }

                        if (progress == 40 && process != 'gagal') {
                            docoNotification("success", "Proses Berhasil", messageProcess);
                        }

                    } else if (status == 'finish') {
                        docoNotification('error','Proses Gagal!', messageProcess)
                    } else {
                        startNum++
                        setPresentase(Math.ceil((startNum/totalProgres) * 100))
                        $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan  </p>`)
                    }
                });
            }
        });
    }

    $(document).on('click', 'tr.parent', function () {
        let _this = $(this);
        var show = true;
        var no_sep = $(this).data('no-sep');

        if ($(".child")[0]) {
            var activeChild = $(this).attr('data-active-child');
            if (activeChild == 'true') {
                _this.attr('data-active-child', false);
                show = false;
            } else {
                $('.child').closest('tr').prev().attr('data-active-child', false);
                _this.attr('data-active-child', true);
                show = true;
            }
            $('tr.child').remove();
        } else {
            _this.attr('data-active-child', true);
        }

        if (show) {
            var info = listEklaim.find(x => x.no_sep == no_sep);
            $.ajax({
                type: 'GET',
                url: '/penjamin-asuransi/informasi-pasien-eklaim/get-prosedur',
                data: {
                    klaiminacbg_id: info.klaiminacbg_id,
                },
                success: function (res) {
                    if (res) {
                        var elDiagnosaIcd10 = ``;
                        var elDiagnosaIcd9 = ``;

                        if (res.icd_10) {
                            elDiagnosaIcd10 = `<ul class='prosedur-list'>`;
                            $.each(res.icd_10, function (index, value) {
                                var icdPrimer = '';
                                if (value.is_icdprimer) {
                                    icdPrimer = `<div class='diagnosa-label diagnosa-primer'> Primer </div>`;
                                }
                                elDiagnosaIcd10 += `<li>
                                                        <div class='diagnosa-title'> ${value.nama_diagnosa} </div>
                                                        <div class="wrapper-label">
                                                            <div class='diagnosa-label'> ${value.kode_diagnosa} </div>
                                                            ${icdPrimer}
                                                        </div>
                                                    </li>`;
                            });
                            elDiagnosaIcd10 += `</ul>`;
                        }

                        if (res.icd_9) {
                            elDiagnosaIcd9 = `<ul class='prosedur-list'>`;
                            $.each(res.icd_9, function (index, value) {
                                elDiagnosaIcd9 += `<li>
                                                        <div class='diagnosa-title'> ${value.nama_diagnosa} </div>
                                                        <div class='diagnosa-label'> ${value.kode_diagnosa} </div>
                                                    </li>`;
                            });
                            elDiagnosaIcd9 += `</ul>`;
                        }

                        var ext = ``;

                        if (info.is_naikkelas) {
                            ext += `
                                <tr>
                                    <td class='title'>Kelas Pelayanan</td>
                                    <td>${info.kelas} </td>
                                    <td class='title'>Lama (hari)</td>
                                    <td>${info.lama_naikkelas}</td>
                                </tr>
                            `;
                        }
                        if (info.is_kelasintensif) {
                            ext += `
                                <tr>
                                    <td class='title'>Rawat Intensif (hari)</td>
                                    <td>${info.lama_kelasintensif} </td>
                                    <td class='title'>Ventilator (jam)</td>
                                    <td>${info.ventilator}</td>
                                </tr>
                            `;
                        }

                        var extEnd = ``;
                        if (info.tarif_polieksekutif || info.tarif_polieksekutif == 0) {
                            extEnd += `
                                <tr>
                                    <td class='title'></td>
                                    <td></td>
                                    <td class='title'>Tarif Poli Eks.</td>
                                    <td>${info.tarif_polieksekutif}</td>
                                </tr>
                            `;
                        }
                        var el = `
                                <tr class='child'>
                                    <td style='border-top:0;background: #fff;'></td>
                                    <td colspan='9' class="colspan">
                            `;
                        var biayaTambahan = ``;
                        if (info.is_naikkelas) {
                            biayaTambahan = `
                                            <tr>
                                                <td colspan='4' class='text-center' style='border-top:none;'>Tambahan Biaya yang Dibayar Pasien Untuk Naik ${info.kelas}</td>
                                            </tr>
                                            <tr>
                                                <td class='title'>Tambahan Biaya</td>
                                                <td class='text-center' colspan='2'>${info.info_tambahan_biaya}</td>
                                                <td class='text-right'>Rp. ${info.tambahan_biaya}</td>
                                            </tr>`;
                        }

                        el += `
                            <table style='width: 100%;' class='table table-expand' id='table-user-info'>
                                
                                <tbody>
                                    <tr class='header-tbl'> 
                                        <td class='text-center' colspan='4'>
                                            <div class="wrapper-top">
                                                <div class="info">
                                                    <label>Jaminan / Cara Bayar</label>
                                                    <h3>${info.cara_bayar}</h3>
                                                </div>
                                                <div class="info">
                                                    <label>No. Peserta</label>
                                                    <input type='text' value='${info.no_asuransi}' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                                </div>
                                                <div class="info">
                                                    <label>Nomor Surat Eligibilitas Peserta (SEP)</label>
                                                    <input type='text' value='${info.no_sep}' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Jenis Rawat</td>
                                        <td>${info.jenis_rawat_group}</td>
                                        <td class='title'>Hak Kelas</td>
                                        <td>${info.kelas_hak}</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Tanggal Rawat</td>
                                        <td><div class="inner-column"><span>Masuk : ${info.tgl_masuk}</span><span>Pulang : ${info.tgl_keluar}</span></div></td>
                                        <td class='title'>Umur</td>
                                        <td>${info.umur}</td>
                                    </tr>
                                     ${ext}
                                    <tr>
                                        <td class='title'>LOS (hari)</td>
                                        <td>${info.los} </td>
                                        <td class='title'>Berat Lahir (gram)</td>
                                        <td>-</td>
                                    </tr>
                                    
                                    <tr>
                                        <td class='title'>ADL Score</td>
                                        <td><div class="inner-column"><span>Sub Acute :  ${info.adl_subacute} </span> <span>Chronic : ${info.adl_cronic} </span></div></td>
                                        <td class='title'>Cara Pulang</td>
                                        <td>${info.cara_keluar} </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>DPJP</td>
                                        <td> ${info.nama_dokter} </td>
                                        <td class='title'>Jenis Tarif </td>
                                        <td>${info.jenis_tarif}</td>
                                    </tr>
                                    ${extEnd}
                                    <tr>
                                        <td class='text-center' colspan="4">Tarif Rumah Sakit : Rp.  ${info.total_tarifrs}</td> 
                                    </tr>
                                    <tr>
                                        <td colspan='4' class="contain">
                                            <table style='width: 100%;' class='table' id='table-prosedur'>
                                                <tr>
                                                    <td class='title no-border-left'>Prosedur Bedah</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.prosedur_bedah}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title '>Prosedur Non Bedah</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.prosedur_nonbedah}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Konsultasi</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.konsultasi}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='title no-border-left'>Tenaga Ahli</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.tenaga_ahli}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Keperawatan</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.keperawatan}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Penunjang</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.penunjang}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='title no-border-left'>Radiologi</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.radiologi}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Laboratorium</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.laboratorium}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Pelayanan Darah</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.pelayanan_darah}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='title no-border-left'>Rehabilitasi</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.rehabilitasi}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Kamar / Akomodasi</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.kamar_akomodasi}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Rawat Intensif</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.rawat_intensif}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='title no-border-left'>Obat</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.obat}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Alkes</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.alkes}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>BMHP</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.bmhp}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='title no-border-left'>Sewa Alat</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.sewa_alat}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Obat Kemoterapi</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.obat_kemoterapi}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                    <td class='title'>Obat Kronis</td>
                                                    <td class='no-border-left'>
                                                        <input type='text' value='${info.obat_kronis}' class='form-control input-sm text-right validate-minus delete-on-edit' disabled >
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan='4' class="contain">
                                            <table style='width: 100%;' class='table table-expand' id='table-diagnosa'>
                                                <tr class='header-tbl'>
                                                    <td class='title'>Diagnosa (ICD 10)</td>
                                                    <td class='border-left'>`+ elDiagnosaIcd10 + `</td>
                                                </tr>
                                                <tr>
                                                    <td class='title'>Diagnosa (ICD 9)</td>
                                                    <td class='border-left'>`+ elDiagnosaIcd9 + `</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='4' class="contain">
                                            <div style="border:1px solid #bbb;border-radius:0.5em;padding:0.5em;background-color:#eeffee;">
                                                <table style='width: 100%;' class='table table-expand' id='table-grouper'>
                                                    <tr> 
                                                        <td class='text-center no-border-top' colspan='4'>
                                                            <h3>Hasil Grouper - Final</h3>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Info</td>
                                                        <td colspan='3' class='border-left'>${info.infoTxt}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Jenis Rawat</td>
                                                        <td colspan='3' class='border-left'>${info.jenis_rawat_group}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Group</td>
                                                        <td class='border-left'>${info.group_nama}</td>
                                                        <td class='text-right'>${info.cbg}</td>
                                                        <td class='text-right'>Rp. ${info.group_tarif}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Sub Acute</td>
                                                        <td class='border-left'> - </td>
                                                        <td class='text-right'> - </td>
                                                        <td class='text-right'>Rp. 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Chronic</td>
                                                        <td class='border-left'>-</td>
                                                        <td class='text-right'>- </td>
                                                        <td class='text-right'>Rp. 0</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Special Procedure</td>
                                                        <td class='border-left'>${info.sp_procedure_nama}</td>
                                                        <td class='text-right'>${info.sp_procedure_kode}</td>
                                                        <td class='text-right'>Rp. ${info.spesial_procedure}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Special Prosthesis</td>
                                                        <td class='border-left'>${info.sp_prosthesis_nama}</td>
                                                        <td class='text-right'>${info.sp_prosthesis_kode}</td>
                                                        <td class='text-right'>Rp. ${info.spesial_prosthesis}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Special Investigation</td>
                                                        <td class='border-left'>${info.sp_investigation_kode}</td>
                                                        <td class='text-right'>${info.sp_investigation_nama}</td>
                                                        <td class='text-right'>Rp. ${info.spesial_investigation}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Special Drug</td>
                                                        <td class='border-left'>${info.sp_drug_nama}</td>
                                                        <td class='text-right'>${info.sp_drug_kode}</td>
                                                        <td class='text-right'>Rp. ${info.spesial_drug}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Status Data Klaim</td>
                                                        <td class='border-left'>${info.status_kirim}</td>
                                                        <td class='text-right'></td>
                                                        <td class='text-right'></td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title'>Status Klaim</td>
                                                        <td class='border-left'></td>
                                                        <td class='text-right'></td>
                                                        <td class='text-right'></td>
                                                    </tr>
                                                    <tr>
                                                        <td class='title text-right' colspan='3'><b>Total</b></td>
                                                        <td class='text-right'><b>Rp. ${info.tarif_klaim}</b></td>
                                                    </tr>
                                                    ${biayaTambahan}
                                                </table>
                                            </div>
                                        </td>
                                    <tr>
                                </tbody>
                            </table>
                        `;
                        el += `</td></tr>`;
                        $(el).insertAfter(_this.closest('tr'));
                    }
                }
            });
        }
    });
    $('#btn-refresh').on('click', function () {
        var el = `<tr>
                    <td colspan="6" class="text-center"> Tidak ada data/td>
                </tr>`;
        $('#table-total tbody').html(el);
        $('#table-total').show();
        $('#table-detail').hide();

        let today = formatDate();
        tgl.val(today);
        tgl.attr('disabled', false);

        $('#tipe-klaim').val('').trigger('change');
        jenisTanggal.val('tgl_keluar').trigger('change');
        postKlaim = [];
        totalSend = 0;
        $('#total-klaim').html(totalSend);
    });
});
