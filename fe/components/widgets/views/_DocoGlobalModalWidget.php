<?php 
use yii\helpers\Html;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<center>
    <span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span>
</center>

<div class="modal-body">
    <div class="progress" style="display:none;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <span class="label-persentase">0</span>%
        </div>
    </div>
    <span class="help-block label-progress"></span>
</div>

<div class="modal-footer">
    <?= Html::button("<i class='fa fa-download'></i> Download", ['class' => 'btn btn-md bg-teal btn-download', 'style' => 'display:none;']) ?>
</div>

<script>
    $(document).ready(() => {
        const finalSyncUrl      = '<?= $url_sync ?>';
        const finalDownloadUrl  = '<?= $url_download ?>';
        const progress          = $(".progress");
        const progressBar       = $(".progress .progress-bar");
        const labelProgress     = $(".label-progress");
        const labelPercent      = $(".label-persentase");
        const btnDownload       = $(".btn-download");
        const btnExcel          = $('#data-export-excel-serconn')

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

        const showButton = (filename, messageProcess) => {
            return new Promise((resolve) => {
                setTimeout(() => {
                btnDownload.css("display", "block")
                .attr("href", filename)
                .prop("disabled", false);
                    $(".label-progress").html(
                        `<p style="font-size:16px;color:green;font-weight:bold;">${messageProcess}</p>`
                    );

                    btnDownload.off("click").on("click", function (e) {
                        e.preventDefault();
                        const button = $(this);

                        button.prop("disabled", true)
                            .css("opacity", "0.6")
                            .text("File sedang diunduh...");

                            window.open(`${finalDownloadUrl}&filename=${filename}`, "_blank");

                        setTimeout(() => {
                            $("#modal_backdrop").modal("hide");
                        }, 5000);
                    });

                    resolve();
                }, 1000);
            });

        };

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
                url : `${finalSyncUrl}`,
                success : function (data) {
                    let startNum = 5;
                    setPresentase(data.totalPerPage);
                    let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
                    $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${data.messageProcess}</p>`);
                    
                    socket.on(channel + data.unique_str, (message) => {
                        const _data = $.parseJSON(message);
                        const { status , messageProcess , filename, progress} = _data
                        if(status === 'finish' ) {
                            $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                            setPresentase(progress)
                            if (progress == 100) {
                                showButton(filename, messageProcess);
                            }
                        } else if (status === 'progress') {
                            setPresentase(progress)
                            $(".label-progress")
                                .html(`<p style="font-size:16px;font-weight:bold;">${messageProcess} </p>`)
                        } else if (status === 'error') {
                            docoNotification('error','Proses Gagal!', messageProcess)
                        } else {
                            setPresentase(data.totalPerPage)
                            $(".label-progress")
                                .html(`<p style="font-size:16px;font-weight:bold;">${data.messageProcess} </p>`)
                        }
                    });
                }
            });
        }

        updateProgressBar()
   });
</script>