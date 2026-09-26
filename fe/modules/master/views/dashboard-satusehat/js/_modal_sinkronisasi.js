$(() => {
    const progress = $(".progress");
    const progressBar = $(".progress .progress-bar");
    const labelProgress = $(".label-progress");
    const labelPercent = $(".progress .label-persentase");
 
    progress.css("display", "none")
 
    const setPresentase = function (progress) {
        labelPercent.html(progress)
        progressBar.css("width", progress + "%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
    }

    $(document).on('change', '.pilih_sync', function(){
        var val = $(this).val();
        var textJumlah = "Jumlah Data"+$(this).parent('label').text()+" yang belum terintegrasi ke Satu Sehat "+"("+list_jumlah_data[val]+")";
        $('.info-select-master').text(textJumlah);
    });
    
    $(".btn-sync-satusehat").on("click", function (event) {
        event.preventDefault();
        var _data = $("#satusehat-sinkronisasi-form").serializeArray();
        var _url = $("#satusehat-sinkronisasi-form").attr('action');
        var $btn = $(this)
        $btn.attr('label', $btn.text()).css("font-weight", "bold").html(`<i class="fa fa-circle-o fa-spin"></i> Sedang di proses ....`).animate({ disabled: true })
    
        $().docoForm("click", {
            url: _url,
            data: _data,
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function (data) {
                var data = data.data;
                if ($('.progress-bar').hasClass('bg-danger')) {
                    $('.progress-bar').removeClass('bg-danger');
                }
                var countData = data.countData;
                var randString = data.unique_str;
                var totalPerPage = data.totalPerPage;
                let startNum = 5
                setPresentase(startNum)
                let totalProgres = parseInt(startNum) + parseInt(totalPerPage) + 20;
                labelProgress.html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data <i> (0/${countData}) </i> data </p>`);

                async function updateProgress() {
                    let config = await $.getJSON("./../../json/setup.json")
                    const socket = (config.origin == "true") ? io.connect(window.location.origin) : io.connect(config.ip + ':' + config.port)
                    const channel = `syncDataSatusehat:${randString}`
                    progress.css("display", "block")
                    labelProgress.html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`)

                    socket.on(channel, (message) => {
                        const _data = $.parseJSON(message);
                        const { status, messageProcess, progress } = _data
                        if (status == 'finish') {
                            labelProgress.html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                            setPresentase(progress)
                            if (progress == 100) {
                                $btn.html(`<i class="fa fa-paper-plane"></i> Sync`).animate({ disabled: false })
                                table.draw();
                            }
                        }
                        else if (status == 'failed') {
                            $btn.html(`<i class="fa fa-paper-plane"></i> Sync`).animate({ disabled: false })
                            docoNotification('error', 'Terjadi Kesalahan', messageProcess)
                            $('.progress-bar').addClass('bg-danger');
                            labelProgress.html(`<p style="font-size:16px;font-weight:bold;"> <i> Gagal memproses data, silahkan mencoba lagi. <i></p>`)
                        }
                        else {
                            startNum++
                            setPresentase(Math.ceil((startNum / totalProgres) * 100))
                            var currentProcess = (startNum - 5);
                            labelProgress
                                .html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data <i> (${currentProcess}/${countData}) </i> data </p>`)
                        }
                    })
                }
                updateProgress()
            },
            error: function(res) {
                var res = res.responseJSON;
                if (typeof res.metadata != "undefined" && res.metadata.status == 422) {
                    docoNotification('error', i18next.t('Proses Gagal'), i18next.t("Data source belum dipilih!"));
                }
                $btn.html(`<i class="fa fa-paper-plane"></i> Sync`).animate({ disabled: false })
            }
        });
    });

    $(".btn-kembali").on("click", function (event){
        table.draw();
    })
});