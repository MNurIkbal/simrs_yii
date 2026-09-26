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
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        <span class="label-persentase"></span>%</div>
    </div>
    <span class="help-block label-progress"></span>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-download'> Download</i>", ['class' => 'btn bg-teal btn-download']) ?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		const progress = $('.progress');
		const progressBar = $('.progress .progress-bar');
		const labelProgress = $('.label-progress');
		const labelPercent = $('.label-persentase');
		const btnDownload = $('.btn-download');
		const btnExcel = $('#data-export-excel-bg');

		var closeModal = false;

		progress.css('display', 'none');
		btnDownload.css('display', 'none');

		$('#modal_backdrop').on('hidden.bs.modal', function() {
			closeModal = true;
		});

		const showInfo = () => {
			return new Promise((resolve) => {
				setTimeout(() => {
					resolve($('.populate-data').html(`Sedang Menyiapkan data ....`))
				}, 1000);

				setTimeout(() => {
					resolve($('.populate-data').css('display', 'none'))
					resolve(progress.css('display', 'block'))
					resolve($('.label-progress').html(`
						<p style="font-size: 16px; font-weight: bold;">Menyiapkan data ....</p>
						`))
				}, 2000);
			})
		}

		const showButton = (filename) => {
			return new Promise((resolve) => {
				setTimeout(() => {
					resolve(btnDownload.css('display', 'block'))
					resolve(btnDownload.attr('href', filename))
					resolve($(".label-progress").html(`<p style="font-size:16px;color:green;font-weight:bold;">File berhasil di proses</p>`))
					resolve(btnDownload.unbind())
					resolve(btnDownload.bind('click', () => {
						window.open(`/apotek/pemusnahan-obat/download-excel?filename=${filename}`, '_blank')
						$('#modal_backdrop').modal('toggle');
					}))
				}, 1000);
			})
		}

		const setPresentase = function(progress) {
			$('.progress .label-persentase').html(progress);
			$('.progress .progress-bar').css('width', progress + '%').attr('aria-valuenow', progress).attr('aria-volume', progress);
		}

		async function updateProgressBar() {
			let config = await $.getJSON('./../../json/setup.json')
			if(config.origin == 'true') {
				var socket = io.connect(window.location.origin);
			} else {
				var socket = io.connect(config.ip+':'+config.port);
			}

			const channel = `export-excel:`
			await showInfo()

			$.ajax({
				url: '/apotek/pemusnahan-obat/proses-sync-excel?randomStr=<?= $randomStr ?>',
				success: function(data) {
					let startNum = 5
					setPresentase(startNum)
					let progress = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
					$(".label-progress")
					        .html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data 
					                        <i> (0/${data.countData}) </i> data </p>`);
					socket.on(channel + data.randomStr, (message) => {
						const _data = $.parseJSON(message);
						const { status, messageProcess, filename, progress} = _data;

						if(status == 'finish') {
							$(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
							setPresentase(progress)
							if(progress == 100) {
								showButton(filename)
							}
						} else if(status == 'finish') {
							docoNotification('error', 'Proses Gagal !', messageProcess)
						} else {
							startNum++
							setPresentase(Math.ceil((startNum/progress) * 100))
							var currentProses = (startNum - 5)
							$(".label-progress")
							    .html(`<p style="font-size:16px;font-weight:bold;">
							                Sedang memproses Data <i> (${currentProses}/${data.countData}) </i> data </p>`)
						}
					});
				}
			});
		}

		updateProgressBar()
	});
</script>