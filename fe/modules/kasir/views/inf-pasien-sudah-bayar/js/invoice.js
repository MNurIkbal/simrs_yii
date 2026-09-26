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
 
    $('#penjamin_id').prop('disabled', true);
    $('.jenis_invoice').on('change', function () {
        if ($(this).val() == 3 && listPenjamin.length != 0 && groupcarabayar_id != groupUmum) {
            $('#penjamin_id').prop('disabled', false);
        }
        else {
            $('#penjamin_id').prop('disabled', true);
        }
    })
 
    $(".btn-cetak-invoice").on("click", function (event) {
        if(typeof isBgprocess != "undefined" && isBgprocess == true){
            var _data = $("#invoice-form").serializeArray();
            var $btn = $(this)
            $btn.attr('label', $btn.text()).css("color", "black").css("font-weight", "bold").html(`<i class="fa fa-circle-o fa-spin"></i> Sedang di proses ....`).animate({ disabled: true })
    
            $().docoForm("click", {
                url: $("#invoice-form").attr('action'),
                data: _data,
                skipConfirm: true,
                skipSuccessNotif: true,
                success: function (data) {
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
                        const channel = `invoice:${randString}`
                        progress.css("display", "block")
                        labelProgress.html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`)
    
                        socket.on(channel, (message) => {
                            const _data = $.parseJSON(message);
                            const { status, messageProcess, filename, progress } = _data
                            if (status == 'finish') {
                                labelProgress.html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                                setPresentase(progress)
                                if (progress == 100) {
                                    $btn.html(`<i class="fa fa-print"></i> Cetak`).animate({ disabled: false })
                                    window.open(`/kasir/pembayaran-tagihan/download-invoice?fileName=${filename}`, '_blank')
                                }
                            }
                            else if (status == 'failed') {
                                $btn.html(`<i class="fa fa-print"></i> Cetak`).animate({ disabled: false })
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
                }
            });

        }else{
            event.preventDefault();
            var kelompok = null;
            var tableData = table.row(".selected").data();
            var penjualanresep_id = null;

            if(typeof tableData !== "undefined") {
                var pendaftaran_id = tableData.pendaftaran_id;
                var pembayaran_id = tableData.pembayaran_id;
                var penjualanresep_id = tableData.penjualanresep_id;
                let jenis_invoice = $("#invoice-form input[type='radio']:checked").val();
                let penjamin_id = $('#penjamin_id').val();

                if(penjualanresep_id != null) {
                    kelompok = PASIEN_ALKES;
                }

                var url = "/kasir/pembayaran-tagihan/cetak-invoice?id=" + pendaftaran_id + "&invoice_id=" + pembayaran_id + "&kelompok=" + kelompok + "&jenis_invoice=" + jenis_invoice+ "&penjamin=" + penjamin_id;
                window.open(url);
            }
            else {
                if(_pembayaranId) {
                    let pembayaran_id = _pembayaranId;
                    let jenis_invoice = $("#invoice-form input[type]:checked").val();
                    let penjamin_id = $("#penjamin_id").val();
                    var url = "/kasir/pembayaran-tagihan/cetak-invoice?invoice_id=" + pembayaran_id + "&kelompok=" + kelompok + "&jenis_invoice=" + jenis_invoice + "&penjamin=" + penjamin_id;
                    window.open(url);
                } else {
                    docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
                }
            }
        }
    });
 })
 
 