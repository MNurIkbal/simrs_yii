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
        const _token = "<?= $token ?>";
        const progress = $(".progress");
        const progressBar = $(".progress .progress-bar");
        const labelProgress = $(".label-progress");
        const labelPercent = $(".label-persentase");
        const btnDownload = $(".btn-download");

        progress.css("display", "none")
        btnDownload.css("display", "none")
        
        var showInfo = () => {
            setTimeout(() => {
                $(".populate-data").html(`mempersiapkan data ...`)
            }, 1000);
            setTimeout(() => {
                $(".populate-data").css("display", "none")
                progress.css("display", "block")
                $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> memproses data ... </p>`)
            }, 1000);
        }

        showInfo()

        async function updateProgressBar() {
            let config = await $.getJSON("./../../json/setup.json")
            if (config.origin == "true") {
                var socket = io.connect(window.location.origin);
            } else {
                var socket = io.connect(config.ip+':'+config.port);
            }

            const channel = `corporate:${config.name}:${_token}`
            socket.on(channel, (data) => {
                const _data = $.parseJSON(data);
                const { persentase, countData, no_order } = _data
                const progressPercent = parseFloat(((persentase / countData) * 100).toFixed(0));

                $(".progress .label-persentase").html(progressPercent)
                $(".progress .progress-bar").css("width", progressPercent +"%")
                    .attr("aria-valuenow", progressPercent)
                    .attr("aria-volume", progressPercent);
                
                $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses ... </i></p>`)
                if(persentase == countData) {
                    docoNotification('success', 'Berhasil', 'File berhasil di Proses.');
                    $('#modal_backdrop').modal('toggle');
                    window.open(`/mcu/lap-hasil-mcu/download-file?no_order=${no_order}`, '_blank')
                }
            });
        }
        updateProgressBar()
   });
</script>