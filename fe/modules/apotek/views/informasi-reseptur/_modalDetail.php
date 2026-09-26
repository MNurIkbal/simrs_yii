<?php 

use yii\helpers\Html;
?>
<style>
    button .close{
        font-size: 3rem;
    }

    .modal-header .close{
        top: 30%;
    }
</style>

<div class="modal-header">
    <button type="button" class="close close_modal_serahkan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
<div class="modal-body">
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <span class="label-persentase"></span>%
        </div>
    </div>
    <span class="help-block label-progress"></span>
    <br>
    <div class="result-process"></div>
</div>
<div class="modal-footer">
</div>

<script>
    $(document).ready(() => {
        const progress = $(".progress");
        const progressBar = $(".progress .progress-bar");
        const labelProgress = $(".label-progress");
        const labelPercent = $(".label-persentase");
        const btnDownload = $(".btn-download");
        const btnExcel =  $('#data-export-excel-serconn');
        const resultProcess = $('.result-process');
        var no_resep = "<?= $yiiRestfulParams['nomor'] ?>";
        no_resep = no_resep.split(',').sort();
        var responSerahkanObat = [];
        let fixRequest = 0;
        let fixProcess = 0;

        var closeModal = false;

        progress.css("display", "none")
        btnDownload.css("display", "none")

        $("#modal_backdrop").on("hidden.bs.modal", function () {
            closeModal = true;
        });

        $('.close_modal_serahkan').on('click', function() {
            table.ajax.reload(null,false);
            $('#btn-serahkan-bgprocess').attr('disabled', true);
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

        const showButton = (message) => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve($(".label-progress").html(`<p style="font-size:16px;color:green;font-weight:bold;">Resep berhasil di proses</p>`))
                    resolve(resultProcess.html( parsingResponse(message) ))
                }, 1000);
            })
        }

        const setPresentase = function(progress) {
            $(".progress").css("display", "block");
            $(".progress .label-persentase").html(progress)
            $(".progress .progress-bar").css("width", progress +"%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
        }

        const updatePresentase = function(upVal) {
            let currVal = $(".progress-bar").attr("aria-valuenow")
            setPresentase(+currVal + upVal)
        }

        /*
        const parsingResponse = function(response) {
            var html = ``;
            var result = response;

            html += `<table width="100%" class="table table-striped table-condensed table-hover no-footer">`;
            html += `<tr id="header" style="background-color: #37474f; border-color: #37474f; color: #ffffff;">`;
            html += `<th>Nomor Resep</th>`;
            html += `<th>Status</th>`;
            html += `<th>Keterangan</th>`;
            html += `</tr>`;

            for (var i = 0; i < result.length; i++) {
                var label = (result[i].status_update.error_code != 200) ? `<span class="badge badge-danger">Gagal</span>` : `<span class="badge badge-success">Sukses</span>`;
                var msg = (result[i].is_success != 0) ? result[i].status_update.msg : result[i].message;

                html += `<tr>`;
                html += `<td>`+result[i].nomor_resep+`</td>`;
                html += `<td>`+label+`</td>`;
                html += `<td>`+msg+`</td>`;
                html += `</tr>`;
            }

            html += `</table>`;

            return html;
        }
        */

        const drawTabel = function() {
            var html = ``;

            html += `<table width="100%" class="table table-striped table-condensed table-hover no-footer">`;
            html += `<tr id="header" style="background-color: #37474f; border-color: #37474f; color: #ffffff;">`;
            html += `<th>Nomor Resep</th>`;
            html += `<th>Status</th>`;
            html += `<th>Keterangan</th>`;
            html += `</tr>`;
            html += `<tbody id="body_table">`;

            html += `<tr id="status_proses">`;
            html += `<td colspan="3" align="center">Sedang Memproses...</td>`;
            html += `</tr>`;

            html += `</tbody>`;
            html += `</table>`;

            $('.result-process').html(html);
        }

        const bodyTabel = function(result) {
            $('#status_proses').remove();

            var html = ``;
            var label = `<span class="badge badge-danger">Menunggu Diproses</span>`;
            var msg = result.message;

            html += `<tr>`;
            html += `<td class="nomer-`+result.nomor_resep+`">`+result.nomor_resep+`</td>`;
            html += `<td class="status-`+result.nomor_resep+`">`+label+`</td>`;
            html += `<td class="msg-`+result.nomor_resep+`">`+msg+`</td>`;
            html += `</tr>`;

            $('#body_table').append(html);
        }

        async function ajxRequest(nomor) {
            updatePresentase(fixRequest)
            /* harusnya menggunakan fetch bukan ajax */
            return $.ajax({
                url : '/apotek/informasi-reseptur/proses-serahkan-obat-multiple?no_resep=' + nomor
            });
        }

        async function keyToPromise(key) {
            data_return = {
                nomor_resep: key,
                is_success: 1,
                message: '-'
            };
            bodyTabel(data_return);
            return new Promise((resolve, reject) => {
                ajxRequest(key)
                    .then(result => {
                        let data = result.data;
                        let meta = result.meta;
                        data_return = {
                            nomor_resep: key,
                            is_success: 1,
                            message: (meta.code != 200) ? data.message : result.message,
                            error_code: meta.code
                        };
                        responSerahkanObat.push(data_return);
                        updateStatusSerahkan(data_return);
                        resolve(result);
                    })
                    .catch(error => {
                        console.log(error);
                        data_return = {
                            nomor_resep: key,
                            is_success: 0,
                            message: 'Resep ' + key + ' Gagal di Proses' + (error.statusText != undefined ? ' (' + error.statusText + ')' : ''),
                            error_code: error.status != undefined ? error.status : 422
                        };
                        responSerahkanObat.push(data_return);
                        updateStatusSerahkan(data_return);
                        reject(error);
                    });
                });
        }

        async function updateSerahkanObat() {
            let progress = 100;
            let jml_resep = no_resep.length;
            let percentResep = Math.floor(progress/jml_resep);
            fixRequest = Math.floor(percentResep/3);
            fixProcess = percentResep-fixRequest;
            let sumPercentResep = percentResep * jml_resep;
            let selisihPercentResep = progress - sumPercentResep;

            let percentProcess = selisihPercentResep;
            drawTabel();
            setPresentase(percentProcess);

            /* options #01 using promise all */
            // Promise.all(no_resep.map(keyToPromise))
            //     .then(result => {
            //         // console.log({state: 'succed', data: result});
            //     })
            //     .catch(error => {
            //         // console.log({state: 'error', data: error});
            //     });

            /* options #02 using for each loop */
            for (var i = 0; i < no_resep.length; i++) {
                try {
                    data_return = {
                        nomor_resep: no_resep[i],
                        is_success: 1,
                        message: '-'
                    };
                    bodyTabel(data_return);
                    ajaxSerahObat(no_resep[i]);
                } catch(err) {
                    console.log(no_resep[i] + ' ' + err);
                }
            }

            $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> Sedang memproses ... </p>`);
            $('.close_modal_serahkan').hide();
        }

        const ajaxSerahObat = async function(resepData) {
            var data_return;

            $.ajax({
                url : '/apotek/informasi-reseptur/proses-serahkan-obat-multiple?no_resep=' + resepData,
                success : function (data) {
                    data_return = {
                        nomor_resep: resepData,
                        is_success: 1,
                        message: (data.meta.code != 200) ? data.data.message : data.message,
                        error_code: data.meta.code
                    };

                    updatePresentase(fixRequest);
                    responSerahkanObat.push(data_return);

                    updateStatusSerahkan(data_return);
                },
                error: function(err) {
                    data_return = {
                        nomor_resep: resepData,
                        is_success: 0,
                        message: 'Resep ' + resepData + ' Gagal di Proses',
                        error_code: 422
                    };

                    updatePresentase(fixRequest);
                    responSerahkanObat.push(data_return);

                    updateStatusSerahkan(data_return);
                }
            });

            return data_return;
        }

        updateSerahkanObat();

        const updateStatusSerahkan = function(data) {
            var label = (data.error_code != 200) ? `<span class="badge badge-danger">Gagal</span>` : `<span class="badge badge-success">Sukses</span>`;
            $('.status-'+data.nomor_resep).html(label);
            $('.msg-'+data.nomor_resep).text(data.message);

            if(responSerahkanObat.length == no_resep.length) {
                $(".label-progress").html(`<p style="font-size:16px;color:green;font-weight:bold;">Resep berhasil di proses</p>`);
                $('.close_modal_serahkan').show();
            }
            updatePresentase(fixProcess);
        }

        /*
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
                url : '/apotek/informasi-reseptur/proses-sync-serah-obat?randString=<?= $randString ?>',
                success : function (data) {
                    let startNum = 5
                    setPresentase(startNum)
                    let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
                    $(".label-progress")
                            .html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data 
                                            <i> (0/${data.countData}) </i> data </p>`);
                    socket.on(channel + data.randString, (message) => {
                        const _data = $.parseJSON(message);
                        const { 
                            status , 
                            messageProcess , 
                            filename, 
                            progress
                        } = _data
                        
                        if(status == 'finish') {
                            $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                            setPresentase(progress)
                            if (progress == 100) {
                                showButton(_data)
                            }
                        } else if (status == 'finish') {
                            docoNotification('error','Proses Gagal!', messageProcess)
                        } else {
                            startNum++
                            setPresentase(Math.ceil((startNum/totalProgres) * 100))
                            var currentProcess = (startNum-5);
                            $(".label-progress")
                                .html(`<p style="font-size:16px;font-weight:bold;">
                                            Sedang memproses Data <i> (${currentProcess}/${data.countData}) </i> data </p>`)
                        }
                    });

                }
            });
        }
        */

        //updateProgressBar()
   });
</script>
