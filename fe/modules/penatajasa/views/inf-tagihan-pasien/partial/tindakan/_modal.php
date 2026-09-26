<?php 
use yii\helpers\Html;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
<div class="modal-body">
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        <span class="label-persentase"></span>%</div>
    </div>
    <span class="help-block label-progress"></span>
</div>

<script>
    $(document).ready(() => {
        const progress = $(".progress");
        const progressBar = $(".progress .progress-bar");
        const labelProgress = $(".label-progress");
        const labelPercent = $(".label-persentase");
        const btnDownload = $(".btn-download");
        const btnExcel =  $('#data-export-excel-serconn')

        var closeModal = false;

        progress.css("display", "none")
        btnDownload.css("display", "none")

        $("#modal_backdrop").on("hidden.bs.modal", function () {
            closeModal = true;
        });
        
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

        const showButton = (filename) => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve(btnDownload.css("display", "block"))
                    resolve(btnDownload.attr("href", filename))
                    resolve($(".label-progress").html(`<p style="font-size:16px;color:green;font-weight:bold;">Berhasil Simpan Tindakan</p>`))
                }, 1000);
            })
        }

        const setPresentase = function(progress) {
            $(".progress .label-persentase").html(progress)
            $(".progress .progress-bar").css("width", progress +"%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
        }

        async function updateProgressBar() {
            let _form = $('#tindakan-form').serializeArray();
            var pendaftaranId =<?=$pendaftaranId ?>; 
            var uniqueString = <?= json_encode($randString); ?>;
            _form.push({name: 'unique_string', value: uniqueString});
            let config = await $.getJSON("./../../json/setup.json")
            if (config.origin == "true") {
                var socket = io.connect(window.location.origin);
            } else {
                var socket = io.connect(config.ip+':'+config.port);
            }
            const channel = `integrasi-kasir:`
            await showInfo()
            $.ajax({
                url : `/penatajasa/inf-tagihan-pasien/save-tindakan?pendaftaran_id=${pendaftaranId}`,
                withoutLoading:true,
                data: _form, // Send the form data
                type: 'POST', // Ensure the data is sent via POST
                beforeSend: function(){
                    let startNum = 0;
                    let totalData, totalObat, totalTindakan, countProgress = 0;
                    var modalCloset = false;
                    setPresentase(startNum)
                    $(".label-progress")
                            .html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data </p>`);
                    socket.on(channel + uniqueString, (message) => {
                        const _data = $.parseJSON(message);
                        let { status , messageProcess , filename, progress} = _data
                        
                        if (_data.hasOwnProperty('messageType') && _data.messageType == 'total_data') {
                            totalTindakan = _data?.tindakan;
                            totalObat = _data?.obat;
                            totalData = totalTindakan + totalObat;
                            messageProcess = 'Sedang mempersiapkan data ..';
                        }

                        if(status != 'failed') {
                            if (_data.hasOwnProperty('isTindakanProcessFinished')) {
                                countProgress++;
                                messageProcess = 'Sedang mempersiapkan data ('+countProgress+'/'+totalData+')';
                            } else if (_data.hasOwnProperty('isBmhpProcessFinished')) {
                                countProgress += totalObat;
                                messageProcess = 'Sedang mempersiapkan data ('+countProgress+'/'+totalData+')';
                            }
                        }

                        $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                        if(status == 'finish') {
                            setPresentase(Math.round(countProgress / totalData * 100));
                            if ((countProgress / totalData * 100) == 100) {
                                table.draw();
                                if(!modalCloset){
                                    setTimeout(function(){
                                        $("#modal_riwayat").modal('toggle');
                                        $("#modal_backdrop").modal('toggle');
                                    }, 1000);
                                    modalCloset = true;
                                }

                             }
                        } else if (status == 'failed') {
                            docoNotification('error','Proses Gagal!', messageProcess)
                        }
                    });
                },
                success : function (data) {



                },
                error : function (data) {
                    $("#modal_riwayat").modal('toggle');
                    var jsonResponse = JSON.parse(data.responseText);
                    var errorMessage = jsonResponse.response.text;
                    docoNotification('error','Proses Gagal!', errorMessage)
                },
            });
        }

        updateProgressBar()
   });
</script>
