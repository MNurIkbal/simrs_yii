$(document).ready(() => {
    progress.css("display", "none")
    btnDownload.css("display", "none")
    $("#jenis_laporan").val(1145).trigger('change')

    const showInfo = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve($(".populate-data").html(`mempersiapkan data ...`))
            }, 1000);
            setTimeout(() => {
                resolve($(".populate-data").css("display", "none"))
                resolve(progress.css("display", "block"))
                resolve($(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
            }, 2000);
        })
    }

    const setPresentase = function(progress) {
        setTimeout(() => {
            $(".progress .label-persentase").html(progress)
            $(".progress .progress-bar").css("width", progress +"%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
        }, 2500);
    }

    async function updateProgressBar() {
        let config = await $.getJSON("./../../json/setup.json")
        if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
        } else {
            var socket = io.connect(config.ip+':'+config.port);
        }

        const channel = `sensus-harian-rajal:`
        await showInfo()
        var tgl = $('.startDate').val()+' - '+$('.endDate').val();
        let _data = {
            jenis_laporan: $('#jenis_laporan').val(),
            tgl_pendaftaran: tgl,
        }
        
        $.ajax({
            url : '/rm/lap-sensus-harian-pasien-rajal/get-data-serconn?randString='+randString,
            data: _data,
            success : function (data) {
                
                socket.on(channel + data.randString, (message) => {
                    const _data = $.parseJSON(message);
                    const { status , messageProcess , filename, progress} = _data
                    if(status == 'update') {
                        let valProgress = 30
                        setPresentase(valProgress)
                    } else {
                        let valProgress = 100
                        setPresentase(valProgress)
                        $('#cari').prop('disabled', false);
                        setTimeout(() => {
                            $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> Berhasil menyiapkan data</p>`)
                            if(draw > 0) {
                                table.clear().draw()
                                table.rows.add(_data.data); 
                                table.columns.adjust().draw(); 
                            } else {
                                table = $('#lap-sensus-harian-rajal').docoTabel({
                                    data: _data.data,
                                    scrollX: true,
                                    destroy: true,
                                    searching: false,
                                    paging: false,
                                    sorting: false,
                                    displayLength: 100,
                                    processing: false,
                                    serverSide: false,
                                    ordering: false,
                                    lengthChange: false,
                                    columns: _data.header.columns,
                                });
                                draw++;
                            }
                            contentData.css("display", "");
                        }, 2500);
        
                        setTimeout(() => {
                            setPresentase(0)
                            $('.progress').css("display", "none");
                            $(".label-progress").html("")
                            table.columns.adjust().draw(); 
                        }, 4000);
                    }
                });

            }
        });
    }

    $('#jenis_laporan').on('change', function (e) {
        var _jnsLap = $(this).val();
        // if (_jnsLap != '726') {
        //     $('#export-excel').prop ("disabled", true);
        // } else {
        //     $('#export-excel').prop ("disabled", false);
        // }
    })

    $('#cari').on('click', function (e) {
        e.preventDefault();

        if (!$("#jenis_laporan").val()) {
            docoNotification("error","Pencarian Gagal !","Jenis pendaftaran harus di pilih");
            return false;
        }

        setTimeout(() => {
            updateProgressBar()
        }, 1000);
        $('#cari').prop('disabled', true);
    })

    $(document).on('click', '#reset', function (e) {
        if(draw > 0) {
            table.clear().draw();
        }
        setPresentase(0);
        $("#jenis_laporan").val("").trigger('change')
        contentData.css("display", "none");
        progress.css("display", "none");
        $(".label-progress").html("");
        resetDate();
        $('#cari').prop('disabled', false);
    })

    const resetDate = function () {
        let today = moment().format('DD-MMM-YYYY');
        $(".startDate").val(today)
        $(".endDate").val(today)
        $(".targetDate").val("")
    }

    $('#export-excel').on('click', function (e) {
        if(table !== undefined) {
            var col = table.data().count();
            if (col === 0) {
                docoNotification(
                    'warning',
                    'Terjadi Kesalahan',
                    'Data Tidak Tersedia!'
                );
            } else {
                e.preventDefault();
                if (!$("#jenis_laporan").val()) {
                    docoNotification("error", "Export Excel Gagal !", "Jenis pendaftaran harus di pilih");
                    return false;
                }

                var tgl = $('.startDate').val() + ' - ' + $('.endDate').val();
                let _data = {
                    jenis_laporan: $('#jenis_laporan').val(),
                    tgl_pendaftaran: tgl,
                }
                var _param = $.param(_data);

                $(this).attr("action", "/rm/lap-sensus-harian-pasien-rajal/show-popup-excel?" + _param);
                $(this).attr("data-target", "#modal_backdrop");
                $(this).attr("data-width", "75%");
                $(this).attr("data-toggle", "modal");
            }   
        } else {
            docoNotification(
                'warning',
                'Terjadi Kesalahan',
                'Data Tidak Tersedia!'
            );
        }
    })
});