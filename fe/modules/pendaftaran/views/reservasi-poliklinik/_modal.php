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
        let columns = [];
        var _tipe = <?= "'$tipe'" ?>;
        var _randString = <?= "'$randString'" ?>;
        const progress = $(".progress");
        const progressBar = $(".progress .progress-bar");
        const labelProgress = $(".label-progress");
        const labelPercent = $(".label-persentase");

        var closeModal = false;

        table.settings().init().columns.map((val) => {
            if (val.data && val.data != "rowNum" && val.visible != false) {
                if (val.data == "info_pasien") {
                    columns.push(JSON.stringify({
                        title: "No. Pendaftaran",
                        data: 'no_pendaftaranol'
                    }))

                    columns.push(JSON.stringify({
                        title: "No. Rekam Medik",
                        data: 'no_rekam_medik'
                    }))

                    columns.push(JSON.stringify({
                        title: "Nama Depan",
                        data: 'nama_depan'
                    }))

                    columns.push(JSON.stringify({
                        title: "Nama Pasien",
                        data: 'nama_pasien'
                    }))

                    columns.push(JSON.stringify({
                        title: "Jenis Kelamin",
                        data: 'jk'
                    }))
                } else if (val.data == "poli_dokter") {
                    columns.push(JSON.stringify({
                        title: "Poli",
                        data: 'ruangan_nama'
                    }))

                    columns.push(JSON.stringify({
                        title: "Dokter",
                        data: 'nama_pegawai'
                    }))
                } else if (val.data == "carabayar_penjamin") {
                    columns.push(JSON.stringify({
                        title: "Cara Bayar",
                        data: 'carabayar_nama'
                    }))

                    columns.push(JSON.stringify({
                        title: "Penjamin",
                        data: 'penjamin_nama'
                    }))
                } else {
                    columns.push(JSON.stringify({
                        title: val.title,
                        data: val.data
                    }))
                }
            }
        }) 

        progress.css("display", "none")

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
                url : '/pendaftaran/reservasi-poliklinik/process-sync?randString=<?= $randString ?>&tipe=<?= $tipe ?>&columns='+columns,
                success : function (data) {
                let startNum = 5
                setPresentase(startNum)
                let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
                $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data 
                <i> (0/${data.countData}) </i> data </p>`);
                socket.on(channel + data.unique_str, (message) => {
                    const _data = $.parseJSON(message);
                    const { status , messageProcess , filename, progress} = _data
                    if(status == 'finish') {
                        $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                        setPresentase(progress)
                        if (progress == 100) {
                            window.open(`/pendaftaran/reservasi-poliklinik/download-file?fileName=${filename}&tipe=${_tipe}`, '_blank')
                            $("#modal_backdrop").modal("toggle")
                        }
                    } else if (status == 'finish') {
                        docoNotification('error','Proses Gagal!', messageProcess)
                    } else {
                        startNum++
                        setPresentase(Math.ceil((startNum/totalProgres) * 100))
                        var currentProcess = (startNum-5);
                        $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data <i> (${currentProcess}/${data.countData}) </i> data </p>`)
                    }
                });
                }
            });
        }

        updateProgressBar()
    });
</script>
