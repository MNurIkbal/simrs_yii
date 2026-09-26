var loading_spinner = '<i class="icon-spinner4 spinner position-center form-control-feedback spinner-text" style="display: block;"></i>';
var _editAble = false;
var _titleConfirmation  = '<p>Bed tersebut sudah berisi data triase,</p><p>Apakah anda yakin untuk meneruskan penyimpanan data ini ?</p>';
$(() => {
    $("#bed-validation").hide();
    $("#nip-scan").focus();
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            $("#nip-scan").trigger("change");
            $("#nip-scan").blur();
        }
    });

    window.onscroll = function(ev) {
        //$('.float').css({ 'margin-top': '-20px'});
        var B= document.body;
        var D= document.documentElement;
        D= (D.clientHeight)? D: B;
        var height = window.screen.availHeight
        if (D.scrollTop >= height*(1/100) && D.scrollTop <= height*(2.9/100) )
        {
            $('.float').css({ 'margin-top': '-20px'});
        }else if(D.scrollTop >= height*(3/100)){
            $('.float').css({ 'margin-top': '-20px'});
        } 
        if (D.scrollTop == 0)
            {
                $('.float').css({ 'margin-top': '0px'});
            }      
    };

    $("#gcseyeForm").select2({
        data: dataGcs.E
    })
    $("#gcsverbalForm").select2({
        data: dataGcs.V
    })
    $("#gcsmotorikForm").select2({
        data: dataGcs.M
    })

    $("#gcseyeForm,#gcsverbalForm,#gcsmotorikForm").bind('change', () => {
        const totalScoreEye = typeof $("#gcseyeForm").select2('data')[0].metodegcs_nilai != 'undefined' ? $("#gcseyeForm").select2('data')[0].metodegcs_nilai : 0
        const totalScoreVerbal = typeof $("#gcsverbalForm").select2('data')[0].metodegcs_nilai != 'undefined' ? $("#gcsverbalForm").select2('data')[0].metodegcs_nilai : 0
        const totalScoreMotorik = typeof $("#gcsmotorikForm").select2('data')[0].metodegcs_nilai != 'undefined' ? $("#gcsmotorikForm").select2('data')[0].metodegcs_nilai : 0
        const totalScoreGcs = totalScoreEye + totalScoreVerbal + totalScoreMotorik
        $("#triaseform-hasil_gcs").val(totalScoreGcs);
        const kategoriTriase = getKategoriTriase();
        updateRenderResultLabelTriase(kategoriTriase);
    })

    var gcsEye = 0, gcsEyeId = null;
    var gcsVerbal = 0, gcsVerbalId = null;
    var gcsMotorik = 0, gcsMotorikId = null;
    const hitungGcs = () => {
        let nilaiGcs = gcsEye + gcsVerbal + gcsMotorik;
        $("#hasil-gcs").text(nilaiGcs);
        $("#triaseform-gcseye_id").val(gcsEyeId);
        $("#triaseform-gcsverbal_id").val(gcsVerbalId);
        $("#triaseform-gcsmotorik_id").val(gcsMotorikId);
        $("#triaseform-hasil_gcs").val(nilaiGcs);
    }

    $(".btn-triage-option").bind("click", ({ currentTarget }) => {
        let triageRules = typeof configRules[$(currentTarget).data('triage_group')][$(currentTarget).val()] != 'undefined' ? configRules[$(currentTarget).data('triage_group')][$(currentTarget).val()] : null;
        if ($(currentTarget).hasClass('--selected')) {
            $(currentTarget).removeClass("btn-info --selected");
        } else {
            $(currentTarget).addClass("btn-info --selected");
            if ( triageRules != null ) {
                $.each( triageRules, (key, rules) => {
                    if (key == 'remove-all-except') {   
                        if ( rules == true) {
                            $(`[data-btn_key!='${$(currentTarget).val()}'][data-triage_group='${$(currentTarget).data('triage_group')}']`).removeClass('btn-info --selected')
                        } else {
                            let _elementException = `[data-triage_group='${$(currentTarget).data('triage_group')}'][data-btn_key!='${$(currentTarget).val()}']`
                            $.each( rules, (index, triageItem) => {
                                _elementException += `[data-btn_key!='${triageItem}']`
                            }) 
                            $(_elementException).removeClass('btn-info --selected')
                        }
                    } else if (key == 'allow-select-same-line') {
                        //just for make the options didnt catch by else triageRules condition
                    } else if (key == 'remove-not-sibling') {
                        $(currentTarget)
                            .closest(".btn-triage-group")
                            .find("button")
                            .not(currentTarget)
                            .not($(currentTarget).siblings())
                            .removeClass("btn-info --selected");
                    }else {
                        $.each(rules, (index, triageItem) => {
                            if (key == 'add') {
                                $(`[data-triage_group='${$(currentTarget).data('triage_group')}'][data-btn_key='${triageItem}']`).addClass('btn-info --selected')
                            } else if (key == 'remove') {
                                $(`[data-triage_group='${$(currentTarget).data('triage_group')}'][data-btn_key='${triageItem}']`).removeClass('btn-info --selected')
                            }
                        })
                    }
                })
            } else {
                $(currentTarget)
                    .closest(".btn-triage-group")
                    .find("button")
                    .not(currentTarget)
                    .not($(currentTarget).siblings())
                    .removeClass("btn-info --selected");
            }
        }

        const kategoriTriase = getKategoriTriase();
        updateRenderResultLabelTriase(kategoriTriase);
    });

    $(".btn-bed-option").bind("click", ({ currentTarget }) => {
        if ($(currentTarget).hasClass('--selected')) {
            $(currentTarget).removeClass("btn-info --selected");
            $("#triaseform-kamartempattidur_id").val("").trigger("change");
        } else {
            _editAble = $(currentTarget).hasClass('editable') ? true : false;
            $(currentTarget).addClass("btn-info --selected");
            $(currentTarget)
                .parent()
                .find("button")
                .not(currentTarget)
                .removeClass("btn-info --selected");
            $("#triaseform-kamartempattidur_id").val($(currentTarget).data("bed-id"));
            $('#bed-validation').hide();
        }
    });

    $("#btn-save-triage").bind("click", () => {
        // generate form data
        generateFormData()
        // Bed Validation
        if ($("#triaseform-kamartempattidur_id").val().length === 0) {
            $("#bed-validation").show();
            return false;
        }
        if(_editAble){
            confirmationDialog(_titleConfirmation, (confirm) => {
                if(confirm) {
                    $.ajax({
                        url : "/site/save-triage",
                        data: $("#form-triase").serializeArray(),
                        type: "POST",
                        confirmMessage: 'Apakah anda setuju untuk pasien ini ?',
                        success: function (res) {
                            let {data} = res;
                            docoNotification('success', 'Proses Berhasil', data.message);
                            $('#btn-reset-triage').trigger('click')
                            $("#bed-validation").hide();

                        },
                        error: function () {
                            docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                        }
                    });
                }
            })
        } else {
            $.ajax({
                url : "/site/save-triage",
                data: $("#form-triase").serializeArray(),
                type: "POST",
                confirmMessage: 'Apakah anda setuju untuk pasien ini ?',
                success: function (res) {
                    let {data} = res;
                    docoNotification('success', 'Proses Berhasil', data.message);
                    $('#btn-reset-triage').trigger('click')
                    $("#bed-validation").hide();
                },
                error: function () {
                    docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                }
            });
        }
   
    });

    $('#btn-doa').on('click', () => {
        // generate form data
        generateFormData(true)

        // Bed Validation
        if ($("#triaseform-kamartempattidur_id").val().length === 0) {
            $("#bed-validation").show();
            return false;
        }
        let _formData = $("#form-triase").serializeArray()
        $.each(_formData, (key, value) => {
            if ( $.inArray(_formData[key].name, ['_csrf', 'nip-scan', "TriaseForm[pegawai_id]", "TriaseForm[kelompokpegawai_id]", "TriaseForm[tgl_triase]", "TriaseForm[kamartempattidur_id]"]) < 0 ) {
                _formData[key].value = '';
            }
        })
        if(_editAble){
            confirmationDialog(_titleConfirmation, (confirm) => {
                if(confirm) {
                    $.ajax({
                        url : "/site/save-triage?is_doa=true",
                        data: _formData,
                        type: "POST",
                        success: function (res) {
                            let {data} = res;
                            docoNotification('success', 'Proses Berhasil', data.message);
                            $('#btn-reset-triage').trigger('click')
                            $("#bed-validation").hide();
                        },
                        error: function () {
                            docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                        }
                    });
                }
            })

        } else{
            $.ajax({
                url : "/site/save-triage?is_doa=true",
                data: _formData,
                type: "POST",
                success: function (res) {
                    let {data} = res;
                    docoNotification('success', 'Proses Berhasil', data.message);
                    $('#btn-reset-triage').trigger('click')
                    $("#bed-validation").hide();
                },
                error: function () {
                    docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                }
            });
        }
    })

    $("#btn-reset-triage").bind("click", () => {
        // $(`[data-bed-id="${$('#triaseform-kamartempattidur_id').val()}"]`).attr('disabled', true)
        $("#form-triase").find("input").not($("[name='_csrf']")).val("");
        $("#form-triase").find("button.--selected").each(function () {
            $(this).removeClass("btn-info --selected")
        });
        gcsEye = gcsVerbal = gcsMotorik = 0;
        $("#gcseyeForm,#gcsverbalForm,#gcsmotorikForm").val('').trigger('change');
        $("#kategori-triase, #waktu-respon, #observation-site").text("-").css("color", "black");
        $("#petugas-triase").text("-");
        $("#hasil-gcs").text(0);
        $(".btn-form").prop("disabled", true);
        $("#nip-scan").focus();
        $('#bed-validation').hide();
    })

    const generateFormData = (is_doa = false) => {
        let jalan_nafas = [], pernapasan = [], sirkulasi = [], disability = [];
        if ( !is_doa ) {
            $(".jalan_nafas-group").find('.--selected').each(function () {
                jalan_nafas.push($(this).val());
            })
            $(".pernapasan-group").find('.--selected').each(function () {
                pernapasan.push($(this).val());
            })
            $(".sirkulasi-group").find('.--selected').each(function () {
                sirkulasi.push($(this).val());
            })
            $(".disability-group").find('.--selected').each(function () {
                disability.push($(this).val());
            })
        }
        $("[type='hidden']").prop("disabled", false);
        // $("[name='_csrf']").prop("disabled", true);
        $("#triaseform-jalan_nafas").val(jalan_nafas.join(","))
        $("#triaseform-pernafasan").val(pernapasan.join(","))
        $("#triaseform-sirkulasi").val(sirkulasi.join(","))
        $("#triaseform-disability").val(disability.join(","))
        if(is_doa === true) {
            $('#triaseform-hasil_triase').val('')
            $('#triaseform-waktu_respon').val('')
            $('#triaseform-observation_site').val('')            
        }
    }
});

$("#nip-scan").on("change", function () {
    var data_pegawai;
    $.ajax({
        url: "/site/get-pegawai?nip=" + $("#nip-scan").val(),
        type: "GET",
        dataType: "json",
        beforeSend: () => {
            $(".nip-scan").after(loading_spinner);
        },
        success: function (response) {
            const {data} = response;
            if (!data) {
                docoNotification("warning", "Pegawai tidak ditemukan!", "Harap coba lagi.");
                $("#petugas-triase").text("-");
                $("#triaseform-pegawai_id, #triaseform-kelompokpegawai_id").val("").trigger("change");
                $("#nip-scan").focus();
                $(".btn-form").prop("disabled", true);
            } else {
                data_pegawai = data;
                $("#petugas-triase").text(data_pegawai.nama_pegawai);
                $("#triaseform-pegawai_id").val(data_pegawai.pegawai_id);
                $("#triaseform-kelompokpegawai_id").val(data_pegawai.kelompokpegawai_id);
                $(".btn-form").prop("disabled", false);
            }
        }
    })
    .done(function () {
        $(".spinner-text").remove();
    });
})

// date & time
function getCurrentDate(){
    var d = new Date();
    var date = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();

    return (("" + date).length < 2 ? "0" : "") + date + "/" + (("" + month).length < 2 ? "0" : "") + month + "/" + year;
}

function clock() {
    var d = new Date();
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var date = checkTime(d.getDate());
    var month = checkTime(d.getMonth() + 1);
    var year = d.getFullYear();
    var currentTime = hour +":"+ min +":"+ sec;

    // set time
    document.getElementById("tgl-triase").innerHTML = getCurrentDate() + '  ' + currentTime;
    $('input[name="TriaseForm[tgl_triase]"]').val(year + "-" + month + "-" + date + " " + hour + ":" + min + ":00");
}

function checkTime(i) {
    if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
    return i;
}

setInterval(clock, 1000);

async function updateKetersediaanBed() {
    let config = await $.getJSON("./../../json/setup.json")
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(config.ip + ':' + config.port);
    }
    console.log(socket);

    socket.on('ketersediaan-bed-dev', (message) => {
        const _data = $.parseJSON(message);
        if(Array.isArray(_data)) {
            for(let i in _data) {
                let data = _data[i];
                $(`.btn-bed-option[data-bed-id=${data.kamartempattidur_id}]`).css({'border-color':'','background-color':''}).attr({'title':'','disabled': data.status_isi});
            }
        } else {
            let data = _data;
            if(data.status_edit){
                    $(`.btn-bed-option[data-bed-id=${data.kamartempattidur_id}]`).css('border-color','#1ca189');
            }
            else{
            $(`.btn-bed-option[data-bed-id=${data.kamartempattidur_id}]`).attr('disabled', data.status_isi);
            }
        }
    });
}

updateKetersediaanBed();

function getKategoriTriase() {
    let valGcs = $("#triaseform-hasil_gcs").val();
    valGcs = parseInt(valGcs);
    const isEmptyValGcs = !valGcs || valGcs == 0;
    if (isEmptyValGcs) valGcs = null;
    let kategoriGcs = null;
    let prevVal = 0;
    Object.keys(configData.kesadaran).forEach((eachKey) => {
        let tempValKesadaran = configData.kesadaran[eachKey];
        tempValKesadaran = parseInt(tempValKesadaran);
        prevVal = parseInt(prevVal);
        if ( (valGcs <= tempValKesadaran) && (valGcs > prevVal) ){
            kategoriGcs = eachKey;
        }
        prevVal = tempValKesadaran;
    });
    const isResusitasi = $(".resusitasi-group").children("button.--selected").length > 0 || kategoriGcs == 'resusitasi';
    const isEmergent = $(".emergent-group").children("button.--selected").length > 0 || kategoriGcs == 'emergent';
    const isUrgent = $(".urgent-group").children("button.--selected").length > 0 || kategoriGcs == 'urgent';
    const isLessUrgent = $(".less_urgent-group").children("button.--selected").length > 0 || kategoriGcs == 'less_urgent';
    const isNoUrgent = $(".non_urgent-group").children("button.--selected").length > 0 || kategoriGcs == 'non_urgent';
    if (isResusitasi) {
        return 'resusitasi';
    } else if (isEmergent) {
        return 'emergent';
    } else if (isUrgent) {
        return 'urgent';
    } else if (isLessUrgent) {
        return 'less_urgent';
    } else if (isNoUrgent) {
        return 'non_urgent';
    } else {
        return null;
    }
}

function updateRenderResultLabelTriase(kategori) {
    let configWaktuRespon = [];
    let waktuResponse = null;
    let observationSite = null;
    let waktuResponseForm = null;
    let observationSiteForm = null;
    Object.keys(configData.waktu_respon).forEach((eachKey) => {
        configWaktuRespon.push(eachKey);
    })
    switch (kategori) {
        case 'resusitasi':
            Object.keys(configData.waktu_respon.resusitasi).map(function (key) {
                waktuResponse = configData.waktu_respon.resusitasi[key];
                waktuResponseForm = key;
            });
            Object.keys(configData.observation_site.resusitasi).map(function (key) {
                observationSite = configData.observation_site.resusitasi[key];
                observationSiteForm = key;
            });
            kategoriColor = "#F44336";
            break;
        case 'emergent':
            Object.keys(configData.waktu_respon.emergent).map(function (key) {
                waktuResponse = configData.waktu_respon.emergent[key];
                waktuResponseForm = key;
            });
            Object.keys(configData.observation_site.emergent).map(function (key) {
                observationSite = configData.observation_site.emergent[key];
                observationSiteForm = key;
            });
            kategoriColor = "#FC8338";
            break;
        case 'urgent':
            Object.keys(configData.waktu_respon.urgent).map(function (key) {
                waktuResponse = configData.waktu_respon.urgent[key];
                waktuResponseForm = key;
            });
            Object.keys(configData.observation_site.urgent).map(function (key) {
                observationSite = configData.observation_site.urgent[key];
                observationSiteForm = key;
            });
            kategoriColor = "gold";
            break;
        case 'less_urgent':
            Object.keys(configData.waktu_respon.less_urgent).map(function (key) {
                waktuResponse = configData.waktu_respon.less_urgent[key];
                waktuResponseForm = key;
            });
            Object.keys(configData.observation_site.less_urgent).map(function (key) {
                observationSite = configData.observation_site.less_urgent[key];
                observationSiteForm = key;
            });
            kategoriColor = "#33ff3b";
            break;
        case 'non_urgent':
            Object.keys(configData.waktu_respon.non_urgent).map(function (key) {
                waktuResponse = configData.waktu_respon.non_urgent[key];
                waktuResponseForm = key;
            });
            Object.keys(configData.observation_site.non_urgent).map(function (key) {
                observationSite = configData.observation_site.non_urgent[key];
                observationSiteForm = key;
            });
            kategoriColor = "#4CAF50";
            break;
        default:
            kategori = "-";
            waktuResponse = "-";
            kategoriColor = "black";
            $("#triaseform-waktu_respon").val("").trigger("change");
            break;
    }
    $("#kategori-triase").text(kategori.replace(/_/g, " ").toUpperCase()).css("color", kategoriColor);
    $("#waktu-respon").text(waktuResponse ?? '-').css("color", kategoriColor);
    $("#observation-site").text(observationSite ?? '-').css("color", kategoriColor);
    $("#triaseform-waktu_respon").val(waktuResponseForm);
    $("#triaseform-observation_site").val(observationSiteForm);
    $("#triaseform-hasil_triase").val(kategori != '-' ? kategori : null);
}