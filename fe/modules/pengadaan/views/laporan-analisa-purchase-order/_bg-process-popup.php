<?php 
use yii\helpers\Html;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body" id="progress">
    <p class="text-center populate-data populate-data" style="font-size: 14px; font-weight: bold;">&nbsp;</p>
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        <span class="label-persentase"></span>%</div>
    </div>
    <span class="help-block label-progress">&nbsp;</span>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-download'> Download</i>", ['class' => 'btn bg-teal btn-download']) ?>
</div>

<script>
    $(document).ready(() => {
        const progress = $("#progress .progress");
        const progressBar = $("#progress .progress-bar");
        const labelProgress = $("#progress .label-progress");
        const labelPercent = $("#progress .label-persentase");
        const btnDownload = $(".btn-download");
        const btnExcel =  $('#data-export-serconn')

        var closeModal = false;

        progress.css("display", "none")
        btnDownload.css("display", "none")

        $("#modal_backdrop").on("hidden.bs.modal", function () {
            closeModal = true;
        });
        
        const showInfo = () => {
            return new Promise((resolve) => {
                // setTimeout(() => {
                //     resolve($(".populate-data").html(`mempersiapkan data ...`))
                // }, 1000);
                setTimeout(() => {
                    resolve($(".populate-data").css("display", "none"))
                    resolve(progress.css("display", "block"))
                    resolve($("#progress .label-progress").html(`<p style="font-size:14px;font-weight:bold;"> ... </p>`))
                }, 1000);
            })
        }

        const showButton = (filename) => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve(btnDownload.css("display", "block"))
                    resolve(btnDownload.attr("href", filename))
                    resolve($("#progress .label-progress").html(`<p style="font-size:14px;color:green;font-weight:bold;">File berhasil di proses</p>`))
                    resolve(btnDownload.unbind());
                    resolve(btnDownload.bind("click", () => {
                        window.open(`<?= $url ?>/download-file-<?= $fileType ?>?filename=${filename}`, '_blank')
                        $("#modal_backdrop").modal('toggle');
                    }))
                }, 1000);
            })
        }

        const setPresentase = function(progress) {
            $("#progress .label-persentase").html(progress)
            $("#progress .progress-bar").css("width", progress +"%")
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

            const channel = 'export-<?= $fileType ?>:'
            await showInfo()
            
            $.ajax({
                url : '<?= $url ?>/process-sync-<?= $fileType ?>?randString=<?= $randString ?>',
                success : function (data) {
                    let startNum = 5
                    setPresentase(startNum)
                    let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
                    $("#progress .label-progress").html(`<p style="font-size:14px;font-weight:bold;"> Sedang memproses Data </p>`);

                    socket.on(channel + data.randString, (message) => {
                        const _data = $.parseJSON(message);
                        const { status ,messageProcess ,filename ,progress } = _data
                        if(status == 'finish') {
                            $("#progress .label-progress").html(`<p style="font-size:14px;font-weight:bold;">${messageProcess}</p>`)
                            setPresentase(progress)
                            if (progress == 100) {
                                showButton(filename)
                            }
                        } else if (status == 'finish') {
                            docoNotification('error','Proses Gagal!', messageProcess)
                        } else {
                            startNum++
                            setPresentase(Math.ceil((startNum/totalProgres) * 100))
                            var currentProcess = (startNum-5);
                            $("#progress .label-progress").html(`<p style="font-size:14px;font-weight:bold;"> Sedang memproses Data </p>`)
                        }
                    });

                }
            });
        }

        updateProgressBar()
   });
</script>