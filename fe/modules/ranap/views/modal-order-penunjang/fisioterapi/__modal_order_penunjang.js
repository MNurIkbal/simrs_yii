/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */

var { instalasiId, listPemeriksaanPure } = phpVars;

var selectedOption = [];
var selectedDays = [];

$(document).ready(function () {
    initFunction();
    renderContent();
});

function initFunction() {
    $('#btn-search_fisio').unbind("click");
    $('#btn-search_fisio').on('click', function () {
        renderContentSearch();
    })

    $('#txt-search').unbind("keyup");
    $('#txt-search').keyup(function (e) {
        if (e.keyCode === 13) {
            if (!$('#btn-search_fisio').is(":disabled")) {
                $('#btn-search_fisio').click();
            }
        }
    });

    $(".check_kode").unbind("change");
    $(".check_kode").on('change', function () {
        if (!$(this).is(':checked')) {
            $('.kode-tindakan').addClass('hidden');
        } else {
            $('.kode-tindakan').removeClass('hidden');
        }
    })

    $("#instruksipenunjangform-frekuensi_terapi").unbind("keyup");
    $("#instruksipenunjangform-frekuensi_terapi").on('keyup', function () {
        handleResetSelectedDays();
    })
}

function renderContentSearch() {
    // Define Data
    let keyword = $('.search-fisio').val();
    const encodeKeyword = encodeURIComponent(keyword);
    const ruanganId = $('.search-fisio').attr('data-ruangan_id');
    const penjaminId = $('.search-fisio').attr('data-penjamin_id');
    const kelasPelayananId = $('.search-fisio').attr('data-kelaspelayanan_id');
    const instalasiId = $('.search-fisio').attr('data-instalasi_id');
    const spesialisId = $('.search-fisio').attr('data-spesialis_id');
    let params = `ruangan_id=${ruanganId}`
    params += `&penjamin_id=${penjaminId}`
    params += `&kelaspelayanan_id=${kelasPelayananId}`
    params += `&instalasi_id=${instalasiId}`;
    params += `&spesialis_id=${spesialisId}`;
    params += `&searching=${encodeKeyword}`;
    const url = `/ranap/pemeriksaan-rawat-inap/data-pemeriksaan-penunjang-fisio?${params}`;
    // Loading
    $('.content-fisio').html('');
    const loading = $('#loading-content');
    loading.append('<h1 align="center"><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Memuat ... </b></h1>');
    $('#btn-search_fisio').prop('disabled', true);
    // Parser To Html
    const parseIntoHtml = (datas) => {
        const convertToSlug = (text) => {
            if (!text) return '';
            return text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        }
        let html = '';
        if (datas && datas.length != 0) {
            let counterDaftarTindakan = 0;
            html += `<div class="col-sm-12">`;
            $.each(datas, function (header, detail_header) {
                let titleHead = convertToSlug(header);
                let countDetail = detail_header.length;
                let paketFisioBgClass = '';
                let isPaketFisioterapi = false;
                if (countDetail >= 0) isPaketFisioterapi = detail_header[0]?.is_paketfisio;
                if (isPaketFisioterapi) paketFisioBgClass = 'paket-fisio-header';
                html += `<div class="col-sm-4">`; // Div-Parent-1
                html += `<div class="panel panel-default">`; // Div-Parent-2
                // Head Content
                html += `
                     <a id="heading-${titleHead}" data-toggle="collapse" href="#tab-${titleHead}" role="button" aria-expanded="true" aria-controls="tab-${titleHead}" class="">
                         <div class="panel-heading flex-container ${paketFisioBgClass}" style="background-color:#37474f;color:white;">
                             <h6 class="panel-title text-bold" style="font-size:12px;">${header.toUpperCase()}</h6>
                             <ul class="icons-list">
                                 <li><i id="chevron" class="fa fa-chevron-up"></i></li>
                             </ul>
                         </div>
                     </a>
                 `;
                html += `<div class="panel-body multi-collpase label-information collapse out" id="tab-${titleHead}" aria-expanded="true">`; // Div-Parent-3
                html += `<div class="row" id="parentOption">`; // Div-Parent-4
                if (countDetail == 0) {
                    html += '<p style="text-align:center;font-weight:bold;">Tidak Ada Data.</p>';
                } else {
                    $.each(detail_header, function (key, detail2) {
                        let daftarTindakanId = detail2?.is_paketfisio ? `${detail2?.parentdaftartindakan_id}-${detail2?.daftartindakan_id}` : detail2?.daftartindakan_id;
                        let checkboxHtml = `
                             <p style="margin-left:10px;margin-top:10px;">
                                 <input type="checkbox" id="${daftarTindakanId}" 
                                     class="cb_penunjang"
                                     name="checkPemeriksaan" 
                                     value="${detail2.daftartindakan_id}" 
                                     data-catatan="",
                                     data-jenis="${detail2?.jenis}"
                                     data-tariftindakan_id="${detail2?.tariftindakan_id}"
                                     data-ruangan_id="${detail2?.ruangan_id}"
                                     data-ruangan_nama="${detail2?.ruangan_nama}"
                                     data-instalasi_id="${detail2?.instalasi_id}"
                                     data-instalasi_nama="${detail2?.instalasi_nama}"
                                     data-ruanganpaket_id="${detail2?.ruanganpaket_id}"
                                     data-ruanganpaket_nama="${detail2?.ruanganpaket_nama}"
                                     data-perdatarif_id="${detail2?.perdatarif_id}"
                                     data-perdanama_sk="${detail2?.perdanama_sk}"
                                     data-kelaspelayanan_id="${detail2?.kelaspelayanan_id}"
                                     data-kelaspelayanan_nama="${detail2?.kelaspelayanan_nama}"
                                     data-penjamin_id="${detail2?.penjamin_id}"
                                     data-penjamin_nama="${detail2?.penjamin_nama}"
                                     data-kelompoktindakan_id="${detail2?.kelompoktindakan_id}"
                                     data-kelompoktindakan_nama="${detail2?.kelompoktindakan_nama}"
                                     data-kategoritindakan_id="${detail2?.kategoritindakan_id}"
                                     data-kategoritindakan_nama="${detail2?.kategoritindakan_nama}"
                                     data-daftartindakan_id="${detail2?.daftartindakan_id}"
                                     data-daftartindakan_nama="${detail2?.daftartindakan_nama}"
                                     data-tipepaket_id="${detail2?.tipepaket_id}"
                                     data-tipepaket_nama="${detail2?.tipepaket_nama}"
                                     data-komponentarif_id="${detail2?.komponentarif_id}"
                                     data-komponentarif_nama="${detail2?.komponentarif_nama}"
                                     data-harga_tariftindakan="${detail2?.harga_tariftindakan}"
                                     data-persencyto_tindakan="${detail2?.persencyto_tindakan}"
                                     data-persendiskon_tindakan="${detail2?.persendiskon_tindakan}"
                                     data-is_default="${detail2?.is_default}"
                                     data-is_akomodasi="${detail2?.is_akomodasi}"
                                     data-carabayar_id="${detail2?.carabayar_id}"
                                     data-is_konsultasi="${detail2?.is_konsultasi}"
                                     data-kamarruangan_nokamar="${detail2?.kamarruangan_nokamar}"
                                     data-kamarruangan_id="${detail2?.kamarruangan_id}"
                                     data-ambulan_id="${detail2?.ambulan_id}"
                                     data-no_polisi="${detail2?.no_polisi}"
                                     data-kelompokpemeriksaanlab_id="${detail2?.kelompokpemeriksaanlab_id}"
                                     data-nama_kelompok="${detail2?.nama_kelompok}"
                                     data-jenispemeriksaanlab_id="${detail2?.jenispemeriksaanlab_id}"
                                     data-jenispemeriksaanlab_nama="${detail2?.jenispemeriksaanlab_nama}"
                                     data-pemeriksaanlab_id="${detail2?.pemeriksaanlab_id}"
                                     data-pemeriksaanlab_nama="${detail2?.pemeriksaanlab_nama}"
                                     data-persen_penyulit="${detail2?.persen_penyulit}"
                                     data-index="${counterDaftarTindakan}" 
                                     data-is_cyto="0"
                                     data-is_paketfisio="${detail2?.is_paketfisio}"
                                     data-parentdaftartindakan_id="${detail2?.parentdaftartindakan_id}"
                                     data-paketfisio_jumlah="${detail2?.paketfisio_jumlah}"
                                     data-paketfisio_frekuensi="${detail2?.paketfisio_frekuensi}"
                                     >
                                 </span>
                                 <label>
                                     &nbsp;&nbsp; <span class="kode-tindakan hidden">${detail2?.kode} - </span> ${detail2?.daftartindakan_nama}
                                 </label>
                             </p>
                         `;
                        html += checkboxHtml;
                        counterDaftarTindakan++;
                    })
                }
                html += `</div>`; // Div-Parent-1
                html += `</div>`; // Div-Parent-2
                html += `</div>`; // Div-Parent-3
                html += `</div>`; // Div-Parent-4

            });
            html += `</div>`;
            $('.content-fisio').append(html);
            // Auto Checked if re-render search
            if (selectedOption.length > 0) {
                let isPaketFisio = selectedOption[0]?.is_paketfisio;
                let elementIds = [];
                $.each(selectedOption, (k, v) => {
                    let elementId = v?.daftartindakan_id;
                    if (isPaketFisio) {
                        elementId = `${v?.parentdaftartindakan_id}-${v?.daftartindakan_id}`;
                    }
                    elementIds.push(elementId);
                })
                $.each(selectedOption, (k, v) => {
                    let elementId = v?.daftartindakan_id;
                    if (isPaketFisio) {
                        elementId = `${v?.parentdaftartindakan_id}-${v?.daftartindakan_id}`;
                    }
                    // Checkbox re-render (paket and non-paket)
                    // Logic disabled and not disabled paket/non-paket
                    $(`.cb_penunjang`).each(function (i, obj) {
                        const currentId = $(this).attr(`id`);
                        const currentCheckboxIsPaket = $(this).attr(`data-is_paketfisio`);
                        if (isPaketFisio) {
                            if (currentCheckboxIsPaket != "true") {
                                $(this).prop("disabled", true);
                            } else {
                                const maksPilihan = $(this).attr(`data-paketfisio_jumlah`);
                                if (selectedOption.length >= maksPilihan) {
                                    if (elementIds.includes(currentId)) {
                                        $(this).prop("disabled", false);
                                    } else {
                                        $(this).prop("disabled", true);
                                    }
                                } else {
                                    $(this).prop("disabled", false);
                                }
                            }
                        } else {
                            if (currentCheckboxIsPaket == "true") {
                                $(this).prop("disabled", true);
                            } else {
                                $(this).prop("disabled", false);
                            }
                        }
                        $.uniform.update();
                    });
                    // End Logic
                    // Auto checked
                    $(`#${elementId}`).prop('checked', true);
                })
            }
        } else {
            $('.content-fisio').html('<p style="text-align:center">Tidak Ada Data.</p>');
        }
        renderContent();
    }
    // Fetch Data
    $.get(url, function (responseData) {
        const datas = JSON.parse(responseData);
        // Stop Loading
        loading.empty();
        $('#btn-search_fisio').prop('disabled', false);
        parseIntoHtml(datas)
    });
}

function renderContent() {
    handleResetSelectedDays();
    runApplyFrekuensi();
    const valueIsDaily = $('input[name="rb_is_daily"]:checked').val();
    if (valueIsDaily != 1) {
        $("#contentDays").show();
    } else {
        $("#contentDays").hide();
    }
    $('.rb_is_daily').change(function () {
        if ($('input[name="rb_is_daily"]:checked').val() != 1) {
            $("#contentDays").show();
        } else {
            $("#contentDays").hide();
        }
    });

    handleCheckBox();
    handleCheckBoxDays();

    $(".cb_days").css("margin-bottom", "8px !important");
    $(".cb_days").uniform();
    $(".cb_penunjang").closest("td").css("margin-bottom", "8px !important");
    $(".cb_penunjang").uniform();

    $(`#btn-add-order`).unbind("click");
    $(`#btn-add-order`).on("click", function () {
        $("#fretif").remove();
        $("#instruksipenunjangform-frekuensi_terapi")
            .parent(".input-group")
            .removeClass("has-error");
        $("#jalor").remove();
        $(".form-group").removeClass("has-error");
        if (selectedOption.length < 1) {
            docoNotification(
                "error",
                "Silahkan Cek Inputan",
                `Pemeriksaan harus dipilih terlebih dahulu !`
            );
            return false;
        }
        if ($('input[name="rb_is_daily"]:checked').val() != 1) {
            if (selectedDays.length < 1) {
                docoNotification(
                    "error",
                    "Silahkan Cek Inputan",
                    `Custom Days, Pilih minimal 1!`
                );
                return false;
            }
        }

        const valueFrekuensi = $("#instruksipenunjangform-frekuensi_terapi").val();
        const isPaketFisio = selectedOption[0].is_paketfisio;
        const greaterThan = parseInt(valueFrekuensi) > parseInt(maksFrekuensi);

        if (!isPaketFisio & greaterThan) {
            $("#instruksipenunjangform-frekuensi_terapi")
                .parent(".input-group")
                .addClass("has-error");
            $("#instruksipenunjangform-frekuensi_terapi")
                .parent(".input-group")
                .after(
                    `<span id="fretif" class="help-block error"><i class="fa fa-exclamation-circle"></i>Frekuensi Terapi harus tidak boleh lebih besar dari ${maksFrekuensi}.</span>`
                );
            return false;
        }
        if (
            isPaketFisio &&
            selectedOption.length < selectedOption[0].paketfisio_jumlah
        ) {
            docoNotification(
                "error",
                "Silahkan Cek Inputan",
                `Jumlah pilihan terapi paket harus sesuai`
            );
            return false;
        }

        if (
            !valueFrekuensi
        ) {
            docoNotification(
                "error",
                "Silahkan Cek Inputan",
                `Frekuensi harus diisi`
            );
            return false;
        }

        for (let i = 0; i < parseInt(valueFrekuensi); i++) {
            const currentHtml = $(`#schedule-${i}`);
            const currentAwal = $(`#jamMulai-${i}`);
            const currentAkhir = $(`#jamSelesai-${i}`);
            const totalPickadate = $(`.pickadate-input`);
            const currentValue = currentHtml.val();
            const currentAwalValue = currentAwal.val();
            const currentAkhirValue = currentAkhir.val();
            if (totalPickadate.length < parseInt(valueFrekuensi)) {
                return docoNotification(
                    "error",
                    "Silahkan Cek Inputan",
                    `Jumlah Jadwal Harus Sama Dengan Frekuensi`
                );
            }
            if (!currentValue) {
                currentHtml.parent(".form-control").addClass("has-error");
                currentHtml.after(
                    '<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>'
                );
                return docoNotification(
                    "error",
                    "Proses Gagal",
                    "Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong"
                );
            }
            if (!currentAwalValue) {
                currentAwal.parent(".form-control").addClass("has-error");
                currentAwal.after(
                    '<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>'
                );
                return docoNotification(
                    "error",
                    "Proses Gagal",
                    "Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong"
                );
            }
            if (!currentAkhirValue) {
                currentAkhir.parent(".form-control").addClass("has-error");
                currentAkhir.after(
                    '<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Boleh Kosong.</span>'
                );
                return docoNotification(
                    "error",
                    "Proses Gagal",
                    "Silahkan Cek Inputan. Jadwal Tidak Boleh Kosong"
                );
            }

        }

        showLoader('Memeriksa Ketersediaan Jadwal..');
        cekKetersediaanJadwal().then((value) => {
            if (value.data == false) {
                $(`#schedule-${value.dataKe}`).parent(".form-control").addClass("has-error");
                $(`#schedule-${value.dataKe}`).after(
                    '<span id="jalor" class="help-block error"><i class="fa fa-exclamation-circle"></i>Jadwal Terapi Tidak Tersedia.</span>'
                );
                hideLoader();
                return docoNotification(
                    "error",
                    "Proses Gagal",
                    "Silahkan Cek Inputan. Jadwal Terapi Pada Tanggal Tersebut Tidak Tersedia"
                );
            } else {
                var jadwalPerOrder = [];
                for (let z = 0; z < parseInt(valueFrekuensi); z++) {
                    const tgl = $(`#schedule-${z}`).val();
                    const jamAwal = $(`#jamMulai-${z}`).val();
                    const jamAkhir = $(`#jamSelesai-${z}`).val();
                    jadwalPerOrder.push({
                        tgl_penjadwalan_awal: tgl + " " + jamAwal,
                        tgl_penjadwalan_akhir: tgl + " " + jamAkhir,
                        tgl_only: tgl,
                        jam_awal: jamAwal,
                        jam_akhir: jamAkhir,
                    });
                }

                _listpemeriksaanpenunjang.push({
                    orders: selectedOption,
                    schedule_details: jadwalPerOrder,
                    frekuensi: valueFrekuensi,
                });
                $("#modal-lab").find("#tbl-order-penunjang tbody").find(".order").remove();
                for (let a = 0; a < _listpemeriksaanpenunjang.length; a++) {
                    let _html = ``;
                    let daftartindakan_nama = `<td>`;
                    let catatan = `<td>`;
                    let qty_pemeriksaan = `<td>`;
                    let jadwal_html = `<td>`;
                    let jenispemeriksaanlab_nama = ``;
                    let daftartindakan_id = ``;
                    const listOrders = _listpemeriksaanpenunjang[a]["orders"];
                    const firstOrder = _listpemeriksaanpenunjang[a]["orders"][0];
                    let isPaket = false;
                    if (firstOrder) {
                        isPaket = typeof firstOrder.is_paketfisio != 'undefined';
                    }
                    for (let counterChild = 0; counterChild < listOrders.length; counterChild++) {
                        daftartindakan_nama += `<span style="display:block">${listOrders[counterChild].daftartindakan_nama}</span>`;
                        const defaultValueCatatan = listOrders[counterChild].catatan
                            ? listOrders[counterChild].catatan
                            : "";
                        catatan += `
                        <textarea style="margin-bottom:5px;" rows="3" maxlength="100" class="form-control pemeriksaan-catatan-text catatan-order" 
                            placeholder="Catatan Terapi ${listOrders[counterChild].daftartindakan_nama}" 
                            value="${defaultValueCatatan}" 
                            data-index1="${a}" 
                            data-index2="${counterChild}" 
                            data-daftartindakan_id='${listOrders[counterChild].daftartindakan_id}'>${defaultValueCatatan}</textarea>`;
                        const defaultValueQtyPemeriksaan = listOrders[counterChild].qty_pemeriksaan
                            ? listOrders[counterChild].qty_pemeriksaan
                            : "1";

                        let isDisabledQtyPemeriksaan = isPaket ? 'disabled' : '';
                        _listpemeriksaanpenunjang[a]["orders"][counterChild].qty_pemeriksaan = '1';
                        qty_pemeriksaan += `
                        <div class="form-group highlight-addon has-size-sm required">
                            <input class="form-control input-sm qty_pemeriksaan-order"
                                value="${defaultValueQtyPemeriksaan}" 
                                data-index1="${a}" 
                                data-index2="${counterChild}" 
                                data-daftartindakan_id='${listOrders[counterChild].daftartindakan_id}'
                                placeholder="Qty." 
                                ${isDisabledQtyPemeriksaan} />
                        </div>
                        `;

                        jenispemeriksaanlab_nama = `<span style="display:block">${listOrders[counterChild].jenispemeriksaanlab_nama}</span>`;

                        daftartindakan_id =
                            listOrders[counterChild].daftartindakan_id;
                    }
                    daftartindakan_nama += `</td>`;
                    qty_pemeriksaan += ` </td>`;
                    catatan += ` </td>`;

                    for (
                        let c = 0;
                        c < _listpemeriksaanpenunjang[a]["schedule_details"].length;
                        c++
                    ) {
                        jadwal_html += `<span style="display:block">${_listpemeriksaanpenunjang[a]["schedule_details"][c]["tgl_only"]} ${_listpemeriksaanpenunjang[a]["schedule_details"][c]["jam_awal"]} - ${_listpemeriksaanpenunjang[a]["schedule_details"][c]["jam_akhir"]}</span>`;
                    }

                    jadwal_html += `</td>`;
                    _html += `
                       <tr class='order order-row-${a}'>
                       <td></td>
                       <td>${jenispemeriksaanlab_nama}</td> 
                       ${daftartindakan_nama}
                       ${qty_pemeriksaan}
                       ${catatan}
                       <td>${_listpemeriksaanpenunjang[a]["frekuensi"]}</td>
                       ${jadwal_html}
                       <td>
                           <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item delete-order-${a}' data-daftartindakan_id='${a}' type='button'><i class='fa fa-trash'></i></button>
                       </td>
                       </tr>`;
                    $("#modal-lab")
                        .find("#tbl-order-penunjang tbody")
                        .find(".no-data-row")
                        .parent()
                        .remove();
                    $("#modal-lab").find("#tbl-order-penunjang tbody").append(_html);
                    updateNumbering();

                    $(`.delete-order-${a}`).unbind("click");
                    $(`.delete-order-${a}`).bind("click", ({ delegateTarget }) => {
                        _listpemeriksaanpenunjang.splice(a, 1);
                        $("#modal-lab")
                            .find("#tbl-order-penunjang tbody")
                            .find(`.order-row-${a}`)
                            .remove();
                        if (_listpemeriksaanpenunjang.length <= 0) {
                            $("#modal-lab").find("#tbl-order-penunjang tbody").append(`
                               <tr>
                                   <td colspan="8" class="title-empty text-center no-data-row">Belum Ada Data yang terpilih</td>
                               </tr>
                           `);
                        }
                    });

                    $(`.catatan-order`).unbind("change");
                    $(`.catatan-order`).bind("change", function () {
                        var delegateTarget = $(this).val();
                        var index1 = $(this).attr("data-index1");
                        var index2 = $(this).attr("data-index2");
                        _listpemeriksaanpenunjang[index1]["orders"][index2].catatan =
                            delegateTarget != "" ? delegateTarget : null;
                    });

                    $(`.qty_pemeriksaan-order`).unbind("change");
                    $(`.qty_pemeriksaan-order`).bind("change", function () {
                        var delegateTarget = $(this).val();
                        var index1 = $(this).attr("data-index1");
                        var index2 = $(this).attr("data-index2");
                        _listpemeriksaanpenunjang[index1]["orders"][index2].qty_pemeriksaan =
                            delegateTarget != "" ? delegateTarget : null;
                    });
                }
                hideLoader();
                $("#modal-order-pemeriksaan").find(".close").click();
            }
        });
    });
}

var updateNumbering = () => {
    $("#tbl-order-penunjang > tbody > tr").each(function (i, val) {
        $("td:first", this).text(i + 1);
    });
};

function handleCheckBox() {
    $(`.cb_penunjang`).unbind("click");
    $(`.cb_penunjang`).on("click", function () {
        let _checkboxData = $(this).data();
        var isPaket = $(this).attr(`data-is_paketfisio`);
        if ($(this).prop("checked") == true) {
            $.each(_checkboxData, (k, v) => {
                if (v == "") {
                    _checkboxData[k] = null;
                }
                if (k == "is_cyto") {
                    _checkboxData[k] = false;
                }
            });
            selectedOption.push(_checkboxData);
            if (!isPaket) {
                $(`.cb_penunjang`).each(function (i, obj) {
                    var isPaketFisio = $(this).attr(`data-is_paketfisio`);
                    if (isPaketFisio == "true") {
                        $(this).prop("disabled", true);
                        $.uniform.update();
                    }
                });
            } else {
                var currentParent = $(this).attr(`data-parentdaftartindakan_id`);
                var maksPilihan = $(this).attr(`data-paketfisio_jumlah`);
                $(`.cb_penunjang`).each(function (i, obj) {
                    var isPaketFisio = $(this).attr(`data-is_paketfisio`);
                    var loopParent = $(this).attr(`data-parentdaftartindakan_id`);
                    if (!isPaketFisio || currentParent != loopParent) {
                        $(this).prop("disabled", true);
                        $.uniform.update();
                    }
                    if (selectedOption.length >= maksPilihan) {
                        if ($(this).prop("checked") == false) {
                            $(this).prop("disabled", true);
                            $.uniform.update();
                        }
                    }
                });
            }
        } else {
            let _index = selectedOption.findIndex(
                (obj) => obj.daftartindakan_id == _checkboxData.daftartindakan_id
            );
            selectedOption.splice(_index, 1);
            var maksPilihan = $(this).attr(`data-paketfisio_jumlah`);
            var currentParent = $(this).attr(`data-parentdaftartindakan_id`);
            if (selectedOption.length < 1) {
                $(`.cb_penunjang`).each(function (i, obj) {
                    $(this).prop("disabled", false);
                    $.uniform.update();
                });
            }
            if (
                selectedOption.length > 0 &&
                selectedOption.length < maksPilihan &&
                isPaket == "true"
            ) {
                $(`.cb_penunjang`).each(function (i, obj) {
                    var loopParent2 = $(this).attr(`data-parentdaftartindakan_id`);
                    if (currentParent == loopParent2) {
                        $(this).prop("disabled", false);
                        $.uniform.update();
                    }
                });
            }
        }
        if (selectedOption.length > 0 && selectedOption[0].is_paketfisio == true) {
            $(`#instruksipenunjangform-frekuensi_terapi`)
                .val("")
                .attr("readonly", true);
            if (selectedOption.length >= selectedOption[0].paketfisio_jumlah) {
                $(`#instruksipenunjangform-frekuensi_terapi`)
                    .val(selectedOption[0].paketfisio_frekuensi)
                    .attr("readonly", true);
            }
        } else {
            $(`#instruksipenunjangform-frekuensi_terapi`)
                .val("")
                .attr("readonly", false);
        }
    });
}

function handleCheckBoxDays() {
    $(`.cb_days`).unbind("click");
    $(`.cb_days`).on("click", function () {
        let _checkboxDaysData = $(this).data();
        const valueFrekuensi = $("#instruksipenunjangform-frekuensi_terapi").val();
        const isAddAndOverToFrekuensi = ($(this).prop("checked") == true) && (selectedDays.length >= valueFrekuensi);
        console.log('Has Called ');
        if (isAddAndOverToFrekuensi) {
            $(this).prop("checked", false);
            docoNotification(
                "error",
                "Proses Gagal",
                "Hari tidak boleh lebih dari frekuensi"
            );
            $.uniform.update();
            return false;
        }
        if ($(this).prop("checked") == true) {
            selectedDays.push(_checkboxDaysData['days']);
        } else {
            let removeItem = _checkboxDaysData['days'];
            selectedDays = selectedDays.filter(e => e !== removeItem)
        }
        $.uniform.update();
    });
}

function runApplyFrekuensi() {
    $(`#btn-apply-schedule`).unbind("click");
    $(`#btn-apply-schedule`).on(`click`, function () {
        handlerBtnApplySchedule();
    });
}

function fixBackdropMultipleModal() {
    $(`.datetimepicker-custom`).unbind("click");
    $(`.datetimepicker-custom`).on("click", function () {
        setTimeout(() => {
            $("#modal-lab").css("z-index", "1041");
        }, 10);
    });
    setTimeout(() => {
        $("#modal-lab").css("z-index", "1041");
    }, 10);
}

async function cekKetersediaanJadwal() {
    const valueFrekuensi = $("#instruksipenunjangform-frekuensi_terapi").val();
    var initialValue = null;
    for (let i = 0; i < parseInt(valueFrekuensi); i++) {
        const currentHtml = $(`#schedule-${i}`);
        const currentAwal = $(`#jamMulai-${i}`);
        const currentAkhir = $(`#jamSelesai-${i}`);
        const currentValue = currentHtml.val();
        const currentAwalValue = currentAwal.val();
        const currentAkhirValue = currentAkhir.val();
        const tglPenjadwalanAwal = currentValue + ' ' + currentAwalValue;
        const tglPenjadwalanAkhir = currentValue + ' ' + currentAkhirValue;
        if (tglPenjadwalanAwal && tglPenjadwalanAkhir) {
            var terapis = pegawaiId;
            var data =
                tglPenjadwalanAwal && tglPenjadwalanAkhir && terapis
                    ? `?tgl_penjadwalan_awal=${tglPenjadwalanAwal}&tgl_penjadwalan_akhir=${tglPenjadwalanAkhir}&pegawai_id=${terapis}`
                    : "";
            var url = `/ranap/pemeriksaan-rawat-inap/cek-ketersediaan-jadwal${data}`;
            await $.ajax({
                type: "GET",
                url: url,
                contentType: "application/json",
                success: function (res) {
                    initialValue = res;
                },
            });
            if (initialValue && initialValue.data == false) {
                initialValue.dataKe = i;
                break;
            }
        }
    }
    return initialValue;
}

function handlerBtnApplySchedule() {
    $("#fretif").remove();
    $("#instruksipenunjangform-frekuensi_terapi")
        .parent(".input-group")
        .removeClass("has-error");
    $("#jalor").remove();
    $(".form-group").removeClass("has-error");
    resetContentSchedule();
    if (selectedOption.length <= 0) {
        docoNotification(
            "error",
            "Silahkan Cek Inputan",
            `Pemeriksaan harus dipilih terlebih dahulu !`
        );
        return false;
    }
    if ($('input[name="rb_is_daily"]:checked').val() != 1) {
        if (selectedDays.length < 1) {
            docoNotification(
                "error",
                "Silahkan Cek Inputan",
                `Custom Days, Pilih minimal 1!`
            );
            return false;
        }
    }
    const valueFrekuensi = $("#instruksipenunjangform-frekuensi_terapi").val();
    const isPaketFisio = selectedOption[0].is_paketfisio;
    const isDaily = $('input[name="rb_is_daily"]:checked').val();
    const days = isDaily == 1 ? "" : selectedDays;
    const greaterThan = parseInt(valueFrekuensi) > parseInt(maksFrekuensi);
    if (!valueFrekuensi) {
        $("#instruksipenunjangform-frekuensi_terapi")
            .parent(".input-group")
            .addClass("has-error");
        $("#instruksipenunjangform-frekuensi_terapi")
            .parent(".input-group")
            .after(
                `<span id="fretif" class="help-block error"><i class="fa fa-exclamation-circle"></i>Frekuensi harus lebih dari 0.</span>`
            );
        return false;
    }
    if (!isPaketFisio & greaterThan) {
        $("#instruksipenunjangform-frekuensi_terapi")
            .parent(".input-group")
            .addClass("has-error");
        $("#instruksipenunjangform-frekuensi_terapi")
            .parent(".input-group")
            .after(
                `<span id="fretif" class="help-block error"><i class="fa fa-exclamation-circle"></i>Frekuensi Terapi harus tidak boleh lebih besar dari ${maksFrekuensi}.</span>`
            );
        return false;
    }
    $("#content-schedule").docoLoad({
        url: `/ranap/pemeriksaan-rawat-inap/form-modal-fisio-schedule?frekuensi=${valueFrekuensi}&isDaily=${isDaily}&days=${days}`,
        dataType: "html",
        success: function (data) {
            hideLoader();
            fixBackdropMultipleModal();
        },
        error: function () {
            hideLoader();
            fixBackdropMultipleModal();
        },
    });
}

function handleResetSelectedDays() {
    selectedDays = [];
    // Reset Days Checkbox
    $(`.cb_days`).each(function (i, obj) {
        $(this).prop("disabled", false);
        $(this).prop("checked", false);
        $.uniform.update();
    });
    // Reset Days Checkbox
}