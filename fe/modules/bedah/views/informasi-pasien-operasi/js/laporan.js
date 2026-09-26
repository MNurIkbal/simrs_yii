$(document).ready(() => {
	$('#laporanoperasiform-jam_masuk_rec').trigger('change')
	var _disableCetak = true;
	delete_i = '<i class="fa fa-trash"></i>'
	$.each(arrDokterOp, function (key, value) {
		$('.dokter_bedah').append(
			$(document.createElement('button')).prop({
				type: 'button',
				innerHTML: value.dokter_nama,
				class: 'btn btn-default space active-dok-op',
				id: key
			}),
			$(document.createElement('button')).prop({
				type: 'button',
				innerHTML: delete_i,
				class: 'btn btn-default delete-dokter',
				id: key+'_delete-dokter',
				value : value.dokter_id
			})
		);
		$('#' + key).attr('data-dokter-id', value.dokter_id);
		$('#' + key+'_delete-dokter').css('background-color', '#D1F2EB')
		$('#' + key+'_delete-dokter').css('border-color', '#04AA6D')
		$('#' + key+'_delete-dokter').attr('data-id', key)
		$('.delete-dokter').hide();
	});

	plus_button = '<i class="fa fa-plus"></i>'
	$('.dokter_bedah').append(
		$(document.createElement('button')).prop({
			type: 'button',
			innerHTML: plus_button,
			class: 'btn btn-default space tambah-laporan',
		})
	)

	$(".selectDokterBedah").select2InfinityScroll({
      url: "/bedah/informasi-pasien-operasi/get-filters?type=dokter_bedah&ruangan_id="+ruanganId,
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })

	$(".selectKategori").select2InfinityScroll({
      url: "/bedah/informasi-pasien-operasi/get-filters?type=kategori",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })

	$(".selectCaraPembiusan").select2InfinityScroll({
      url: "/bedah/informasi-pasien-operasi/get-filters?type=cara_pembiusan",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })

	$('.active-dok-op').on('click',function (e){
		$('.active-dok-op').each(function () {
			$(this).removeClass('active');
			$(this).css('background-color', '')
			$(this).css('border-color', '')
			$(this).prop('disabled', false);
		})
		$('.delete-dokter').each(function(){
			$(this).hide()
		})
		var laporanId = $(this).attr('id');
		var dokterId = $(this).data('dokter-id');
		if(typeof dokterId != 'undefined' && dokterId !== null){
			$().docoForm('click', {
				method : "GET",
				skipConfirm: true,
				skipSuccessNotif: true,
				url: baseUrl + 'bedah/informasi-pasien-operasi/get-data-tim?id=' + penunjangId + '&laporan_id=' + laporanId,
				success: function (response) {
					var _res = response.data;
					var _data = typeof _res.laporan_per_dokter != 'undefined' ? _res.laporan_per_dokter : []
					var _laporanId = _data.laporanoperasi_id;
					var _additionalData = _data.additional_data;
					var _is_kirimkepatologi = _data.is_kirimkepatologi
					_is_kirimkepatologi = (_is_kirimkepatologi === false) ? 0 : 1
	
					if(typeof _additionalData != 'undefined' && _additionalData !== null) {
						_additionalData = JSON.parse(_additionalData)
						var _asisten = _additionalData.asisten
						var _asistenInstrumen = _additionalData.asisten_instrumen
						var _dokterAnastesi = _additionalData.dokter_anastesi
						var _prosedurBedah = _additionalData.prosedur_bedah
	
						if(typeof _asisten != 'undefined' && _asisten !== null) {
							if($.isEmptyObject(_asisten) === false) {
								$('.selectAsisten').val(null).trigger('change')
								$.each(_asisten, function (key, value) {
									var optionsAsisten = new Option(value, key, true, true);
									$('.selectAsisten').append(optionsAsisten);
								});
							}
							else {
								$('.selectAsisten').val(null).trigger('change')
							}
						}
						if(typeof _asistenInstrumen != 'undefined' && _asistenInstrumen !== null) {
							if($.isEmptyObject(_asistenInstrumen) === false) {
								$('.selectAsistenInstrumen').val(null).trigger('change')
								$.each(_asistenInstrumen, function (key, value) {
									var optionsAsistenIns = new Option(value, key, true, true);
									$('.selectAsistenInstrumen').append(optionsAsistenIns);
								});
							}
							else {
								$('.selectAsistenInstrumen').val(null).trigger('change')
							}
						}
						if(typeof _dokterAnastesi != 'undefined' && _dokterAnastesi !== null) {
							if($.isEmptyObject(_dokterAnastesi) === false) {
								$('.selectDokterAnestesi').val(null).trigger('change')
								$.each(_dokterAnastesi, function (key, value) {
									var optionsDokterAnastesi = new Option(value, key, true, true);
									$('.selectDokterAnestesi').append(optionsDokterAnastesi);
								});
							}
							else {
								$('.selectDokterAnestesi').val(null).trigger('change')
							}
						}
						if(typeof _prosedurBedah != 'undefined' && _prosedurBedah !== null) {
							if($.isEmptyObject(_prosedurBedah) === false) {
								$('.selectProsedurBedah').val(null).trigger('change')
								$.each(_prosedurBedah, function (key, value) {
									var optionsProsedur = new Option(value, key, true, true);
									$('.selectProsedurBedah').append(optionsProsedur);
								});
							}
							else {
								$('.selectProsedurBedah').val(null).trigger('change')
							}
						}
					}
					if(typeof _data.dokter_id != 'undefined' && _data.dokter_id !== null) {
						var options = new Option(_data.dokter_bedah, _data.dokter_id, true, true);
						$('.selectDokterBedah').append(options);
					}
					if(typeof _data.kategori_operasi != 'undefined' && _data.kategori_operasi !== null) {
						var options = new Option(_data.kategori_nama, _data.kategori_operasi, true, true);
						$('.selectKategori').append(options).trigger('change');
					}
					if(typeof _data.cara_pembiusan != 'undefined' && _data.cara_pembiusan !== null) {
						var options = new Option(_data.cara_pembiusan_nama, _data.cara_pembiusan, true, true);
						$('.selectCaraPembiusan').append(options).trigger('change');
					}
					
					$('#laporanoperasiform-jam_masuk_rec').val(_data.mulai_operasi)
					$('#laporanoperasiform-jam_keluar_rec').val(_data.selesai_operasi)
					$('#lama-pembedahan').html(_data.lama_pembedahan)
					$('#laporanoperasiform-diagnosis_prabedah').val(_data.diagnosis_prabedah).trigger('change');
					$('#laporanoperasiform-diagnosis_paska_bedah').val(_data.diagnosis_paskabedah).trigger('change');
					$('#laporanoperasiform-posisi_pasien').val(_data.posisi_pasien).trigger('change');
					$('#laporanoperasiform-mulai_pembiusan').val(_data.mulai_pembiusan).trigger('change');
					$('#laporanoperasiform-selesai_pembiusan').val(_data.selesai_pembiusan).trigger('change');
					$('#laporanoperasiform-komplikasi').val(_data.komplikasi).trigger('change');
					$('#laporanoperasiform-perdarahan').val(_data.perdarahan).trigger('change');
					$('#laporanoperasiform-uraian_pembedahan').val(_data.uraian).trigger('change');
					$('#laporanoperasiform-asal_jaringan').val(_data.asal_jaringan).trigger('change');
					$('#laporanoperasiform-ukuran_implant').val(_data.ukuran_implant).trigger('change');
					$('#laporanoperasiform-jumlah_darah_masuk').val(_data.jumlah_darah_masuk).trigger('change');
					$('#laporanoperasiform-intruksi_post_operasi').val(_data.intruksi_post_operasi).trigger('change');
					$("#is_jaringan_dikirim_" + _is_kirimkepatologi).prop('checked', true);
					$('#laporanoperasi_id').val(laporanId);
	
					setTimeout(() => {
						$('.cetak-laporan').prop('disabled', false)
					}, 500);
	
					$('#' + laporanId).addClass('active')
					$('#' + laporanId).css('background-color', '#D1F2EB')
					$('#' + laporanId).css('border-color', '#04AA6D')
					$('#' + laporanId).prop('disabled', true);
					$('#' + laporanId +'_delete-dokter').show();
	
					var urlCetak = '/bedah/informasi-pasien-operasi/cetak?id=' + penunjangId + '&laporan_id=' + _laporanId
					$('.cetak-laporan').attr('data-target', urlCetak)
				},
			});
		} 
	})

	$('.tambah-laporan').on('click',function(e){
		$('.active-dok-op').each(function () {
			$(this).removeClass('active');
			$(this).css('background-color', '')
			$(this).css('border-color', '')
			$(this).prop('disabled', false);
		})
		$('.delete-dokter').each(function(){
			$(this).hide()
		})
		$('#laporanoperasiform-jam_masuk_rec').val(jam_masuk).trigger('change')
		$('#laporanoperasiform-jam_keluar_rec').val(jam_keluar).trigger('change')
		$('#laporanoperasiform-dokter_bedah').val("").trigger('change')
		$('#laporanoperasiform-asisten').val("").trigger('change')
		$('#laporanoperasiform-asisten_instrumen').val("").trigger('change')
		$('#laporanoperasiform-kategori_operasi').val("").trigger('change')
		$('#laporanoperasiform-diagnosis_prabedah').val("").trigger('change')
		$('#laporanoperasiform-diagnosis_paska_bedah').val("").trigger('change');
		$('#laporanoperasiform-prosedur_bedah').val("").trigger('change');
		$('#laporanoperasiform-posisi_pasien').val("").trigger('change');
		$('#laporanoperasiform-mulai_pembiusan').val("").trigger('change');
		$('#laporanoperasiform-selesai_pembiusan').val("").trigger('change');
		$('#laporanoperasiform-komplikasi').val("").trigger('change');
		$('#laporanoperasiform-cara_pembiusan').val("").trigger('change');
		$('#laporanoperasiform-dokter_anestesi').val("").trigger('change');
		$('#laporanoperasiform-perdarahan').val("").trigger('change');
		$('#laporanoperasiform-uraian_pembedahan').val("").trigger('change');
		$('#laporanoperasiform-asal_jaringan').val("").trigger('change');
		$('#laporanoperasiform-ukuran_implant').val("").trigger('change');
		$('#laporanoperasiform-jumlah_darah_masuk').val("").trigger('change');
		$('#laporanoperasiform-intruksi_post_operasi').val("").trigger('change');
		$("#is_jaringan_dikirim_0").prop('checked', true);
		$('#laporanoperasi_id').val('');
	})
	
	$('.delete-dokter').on('click',function(e){
		data_id = $(this).attr('data-id')
		dataDokterId = $(this).attr('value')
		add = $('#confirm-form').clone().removeClass('hidden');
        add.find('.input-pemakai').removeAttr('readonly');
        add.find('.input-pemakai').attr('value', '');
        add.find('.input-pemakai').attr('id', 'pemakai-validasi');
        add.find('.input-pemakai').attr('placeholder', 'Username');
        add.find('.input-sandi').attr('id', 'sandi-validasi');
        add = add.html();
		var header = 'Perhatian !'
		var message = 'Apakah anda yakin untuk menghapus laporan operasi ini ?' + add
		var label = {
			buttons: {
				'Yes': 'button-yes',
				'No': 'button-no'
			}, hidden:true
		};
		$.showQuestionDialog(header, message, label, function (reaction) {
				user = $('#pemakai-validasi').val();
                pass = $('#sandi-validasi').val();
			if (reaction == 'Yes') { 
				$().docoForm('click', {
					url: baseUrl + 'bedah/end-point/check-authorization',
					skipConfirm: true,
					skipSuccessNotif: true,
					data: {
						  nama_pemakai: user,
						  katakunci_pemakai: pass,
						  akses: 'delete-laporan-dokter',
					},
					success: function (data) {
					   setTimeout(function () {
						   showLoader()
					   }, 100);
					 $().docoForm('delete', {
					 	method : "GET",
					 	skipConfirm: true,
					 	skipSuccessNotif: true,
					 	url: baseUrl + 'bedah/informasi-pasien-operasi/delete-dokter?id=' + data_id + '&data_dokter_id=' + dataDokterId + '&penunjang_id=' + penunjangId,
					 	success: function (response) {
					 		location.reload();
					 	}
					 })
					}
				 })   
			} 
			if (reaction == 'No') {
				hideQuestionDialog();
				$('[data-popup="tooltip"]').tooltip();
			}
                  
		 }); 
	})	

	// if (laporanId !== '') {
	// 	_disableCetak = false;
	// }
	if ($('.btn-save-post').length) {
		$('.btn-save-post').remove()
	}
	var _posisi = $('.posisi-tab').val();
	
	$('.stepy-navigator').removeClass('hidden')
	if (_posisi == 3) {
		$('.stepy-navigator .btn-save-post').addClass('hidden')
	}
	$('.stepy-navigator').append('<button type="button" class="btn btn-xs btn-labeled btn-info submit-laporan"><b><i class="fa fa-floppy-o"></i></b> Simpan</button>')
	$('.stepy-navigator').append('<button type="button" class="btn btn-xs btn-labeled btn-info cetak-laporan"><b><i class="fa fa-print"></i></b> Cetak</button>')
	$('.stepy-navigator .cetak-laporan').prop('disabled', _disableCetak)

	$('.submit-laporan').on('click', () => {
		$().docoForm('click', {
			data: $('#form-laporan-operasi').serializeArray(),
			url: $('#form-laporan-operasi').attr('action'),
			beforeSend: () => {
				$('.submit-laporan').prop('disabled', true)
			},
			success: function (response) {
				location.reload();
			},
			error: () => {
				$('.submit-laporan').prop('disabled', false)
			}
		});
	})
	$('.cetak-laporan').unbind();
	$('.cetak-laporan').bind('click', function () {
		window.open($(this).attr('data-target'), '_blank')
	})

	$(document).on('change', '.selectDokterBedah', function(){
		var _data = $('.selectDokterBedah').select2('data');
		if(typeof _data != 'undefined' && _data !== null && _data[0].id != '') {
			var _id = _data[0].id;
			$().docoForm("click",{
				url : "/bedah/informasi-pasien-operasi/get-data-tim?id=" + penunjangId + '&dokter_id=' + _id,
				method : "GET",
				type : "json",
				skipConfirm: true,
				skipSuccessNotif: true,
				success : function (data) {
					var _res = data.data;
					var _data_tim = typeof _res.data_tim != 'undefined' ? _res.data_tim : []
					var _tindakan_diluar_bedah = typeof _res.tindakan_diluar_bedah != 'undefined' ? _res.tindakan_diluar_bedah : []
					var _tmp = _tmpTindakan = _tmpTindakanBedah = [];

					// Clear semua posisi tim sebelum diisi
					$('.selectAsisten').html('')
					$('.selectAsistenInstrumen').html('')
					$('.selectProsedurBedah').html('')
					$('.selectDokterAnestesi').html('')
					
					var _dataAsistenAnastesi = _dataAsistenBedah = [];
					if(typeof _data_tim[asisten_anastesi1] != 'undefined') {
						_dataAsistenAnastesi = _dataAsistenAnastesi.concat(_data_tim[asisten_anastesi1])
					}
					if(typeof _data_tim[asisten_anastesi2] != 'undefined') {
						_dataAsistenAnastesi = _dataAsistenAnastesi.concat(_data_tim[asisten_anastesi2])
					}
					if(typeof _data_tim[asisten_bedah1] != 'undefined') {
						_dataAsistenBedah = _dataAsistenBedah.concat(_data_tim[asisten_bedah1])
					}
					if(typeof _data_tim[asisten_bedah2] != 'undefined') {
						_dataAsistenBedah = _dataAsistenBedah.concat(_data_tim[asisten_bedah2])
					}

					
					if(typeof _dataAsistenAnastesi != 'undefined' && _dataAsistenAnastesi !== null) {
						$('.selectAsisten').val(null).trigger('change')
						$.each(_dataAsistenAnastesi, function (key, value) {
							var _pegawaiId = value.pegawai_id;
							var _pegawaiNama = value.dokter;
							var optionAsistenAnastesi = new Option(_pegawaiNama, _pegawaiId, true, true);
							$('.selectAsisten').append(optionAsistenAnastesi);
						});
					}

					if(typeof _dataAsistenBedah != 'undefined' && _dataAsistenBedah !== null) {
						$('.selectAsistenInstrumen').val(null).trigger('change')
						$.each(_dataAsistenBedah, function (key, value) {
							var _pegawaiId = value.pegawai_id;
							var _pegawaiNama = value.dokter;
							var optionAsistenBedah = new Option(_pegawaiNama, _pegawaiId, true, true);
							$('.selectAsistenInstrumen').append(optionAsistenBedah);
						});
					}

					if(typeof _data_tim[dokter_anastesi] != 'undefined' && _data_tim[dokter_anastesi] !== null) {
						$('.selectDokterAnestesi').val(null).trigger('change')
						$.each(_data_tim[dokter_anastesi], function (key, value) {
							var _pegawaiId = value.pegawai_id;
							var _pegawaiNama = value.dokter;
							if ($('.selectDokterAnestesi').find("option[value='" + _pegawaiId + "']").length) {
								$('.selectDokterAnestesi').val(_pegawaiId);
						  	} else { 
								var optionsDokterAnastesi = new Option(_pegawaiNama, _pegawaiId, true, true);
								$('.selectDokterAnestesi').append(optionsDokterAnastesi);
						  	} 
						});
					}

					$tindakanList = [];
					if(typeof _tindakan_diluar_bedah != 'undefined' && _tindakan_diluar_bedah !== null) {
						$.each(_tindakan_diluar_bedah, function (key, value) {
							var _tindakanId = value.daftartindakan_id;
							var _tindakanNama = value.daftartindakan_nama;
							var optionProsedurBedah = new Option(_tindakanNama, _tindakanId, true, true);
							$('.selectProsedurBedah').append(optionProsedurBedah);
						});
					}
				}
			});
		}
	})

	$(document).on('change', '#laporanoperasiform-jam_keluar_rec', function(){
		calculateLamaPembedahan()
	})

	$(document).on('change', '#laporanoperasiform-jam_masuk_rec', function(){
		calculateLamaPembedahan()
	})

	function calculateLamaPembedahan()
	{
		var start_time = $('#laporanoperasiform-jam_masuk_rec').val()
		var end_time = $('#laporanoperasiform-jam_keluar_rec').val()
		var _validStarTime = typeof start_time != 'undefined' && start_time !== '' ? true : false 
		var _validEndTime = typeof end_time != 'undefined' && end_time !== '' ? true : false
		if(_validStarTime && _validEndTime) {
			start_time = new Date(start_time)
			end_time = new Date(end_time)
			var diff = Math.abs(new Date(end_time) - new Date(start_time));
			var seconds = Math.floor(diff/1000);
			var minutes = Math.floor(seconds/60);
			seconds = seconds % 60;
			var hours = Math.floor(minutes/60);
			minutes = minutes % 60;
			var _interval = hours + ' Jam ' + minutes + ' Menit'
			$('#lama-pembedahan').html(_interval)
		}
	}
});
