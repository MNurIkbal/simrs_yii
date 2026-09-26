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
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-download'> Download</i>", ['class' => 'btn bg-teal btn-download']) ?>
</div>

<script>
    $(document).ready(() => {
        var _randString = <?= "'$randString'" ?>;
        var _filename = <?= "'$filename'" ?>;
        const progress = $(".progress");
        const progressBar = $(".progress .progress-bar");
        const labelProgress = $(".label-progress");
        const labelPercent = $(".label-persentase");
        const btnDownload = $(".btn-download");

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

        const setPresentase = function(progress) {
            $(".progress .label-persentase").html(progress)
            $(".progress .progress-bar").css("width", progress +"%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
        }

        async function updateProgressBar() {
            let config = await $.getJSON("./../../json/setup.json")
            if (config.origin == "true") {
                var socket = io.connect(window.location.origin);
            } else {
                var socket = io.connect(config.ip+':'+config.port);
            }
            const channel = `export-excel:`
            await showInfo()
            
            $.ajax({
                url : '/kasir/laporan-data-jurnal/process-sync-excel?randString=<?= $randString ?>',
                success : function () {
                    let startNum = 5
                    setPresentase(startNum)
                    let totalProgres = 100;
                    $(".label-progress")
                            .html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data</p>`);

                    socket.on(channel + _randString, (messages) => {
                        const _data = $.parseJSON(messages);
                        const { key, status , messageProcess , progress} = _data
                        console.log(_data)
                        if(status == 'finish') {
                            $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                            setPresentase(progress)
                            if (progress == 100) {
                                showButton(key)
                            }
                        } else if (status == 'finish') {
                            docoNotification('error','Proses Gagal!', messageProcess)
                        } else {
                            startNum++
                            setPresentase(progress)
                            var currentProcess = (startNum-5);
                            $(".label-progress")
                                .html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data</p>`)
                        }
                    });

                }
            });
        }
        
        const showButton = (key) => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve(btnDownload.css("display", "block"))
                    // resolve(btnDownload.attr("href", filename))
                    resolve($(".label-progress").html(`<p style="font-size:16px;color:green;font-weight:bold;">File berhasil di proses</p>`))
                    resolve(btnDownload.on("click", () => {
                        $('#modal_backdrop').modal('toggle');
                        window.open(`/kasir/laporan-data-jurnal/download-excel?key=` + key + `&filename=` + _filename, '_blank')
                    }))
                }, 1000);
            })
        }
        
        setTimeout(() => {
            updateProgressBar()
        }, 1000);
   });
</script>
