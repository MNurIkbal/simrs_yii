function populateTableDrugSupport(data) {
	if (data !== null && $('#table-beforeleavingdrugsupport tbody > tr#drugsupport-' + data.obatalkes_id).length === 0) {
		$('#table-beforeleavingdrugsupport tbody').append(
			'<tr id="drugsupport-' + data.obatalkes_id + '">' +
			'<td>' + data.obatalkes_nama + '</td>' +
			'<td>' + data.dose + '</td>' +
			'<td>' + data.time_delivery + '</td>' +
	            '<td width="50">' +
		            '<a class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>' +
		            '<input type="hidden" class="obatalkes_id" value="' + data.obatalkes_id + '" />' +
		            '<input type="hidden" class="dose" value="' + data.dose + '" />' +
		            '<input type="hidden" class="time_delivery" value="' + data.time_delivery + '" />' +
		            '<input type="hidden" class="obatalkes_nama" value="' + data.obatalkes_nama + '" />' +
		            '<input type="hidden" class="anestesipostoprdrugsupport_id" value="' + (data.anestesipostoprdrugsupport_id === undefined ? '' : data.anestesipostoprdrugsupport_id) + '" />' +
	            '</td>' +
			'</tr>'
		);
	}
	if ($('#table-beforeleavingdrugsupport tbody > tr:not(.empty-row)').length === 0) {
		$('#table-beforeleavingdrugsupport tbody').html(
			'<tr class="empty-row"><td colspan="4" class="text-center">Belum ada data yang ditambahkan</td></tr>'
		);
	} else {
		$('#table-beforeleavingdrugsupport tbody > tr.empty-row').remove();
		$('#table-beforeleavingdrugsupport tbody > tr:not(.empty-row)').each(function(index, element) {
			$('.obatalkes_id', element).attr('name', 'BeforeLeavingForm[drugsupports][' + index + '][obatalkes_id]');
			$('.obatalkes_nama', element).attr('name', 'BeforeLeavingForm[drugsupports][' + index + '][obatalkes_nama]');
			$('.dose', element).attr('name', 'BeforeLeavingForm[drugsupports][' + index + '][dose]');
			$('.time_delivery', element).attr('name', 'BeforeLeavingForm[drugsupports][' + index + '][time_delivery]');
			$('.anestesipostoprdrugsupport_id', element).attr('name', 'BeforeLeavingForm[drugsupports][' + index + '][anestesikondisipasiendrugsupport_id]');
		});
	}
}

$('#component-beforeleaving').on('click', '#table-beforeleavingdrugsupport .btn-remove', function(e) {
	e.preventDefault;
	$(this).closest('tr').remove();
	populateTableDrugSupport(null);
});

$('#component-beforeleaving').on('change', '.calculation', function() {
	var results = {};
	$('#component-beforeleaving .calculation').each(function() {
		if (results[$(this).val()] === undefined) {
			results[$(this).val()] = 0;
		}
		if ($(this).is(':checked')) {
			results[$(this).val()] = results[$(this).val()] + parseInt($(this).closest('tr').find('.score-value').html());
		}
	});
	for (var i in results) {
		$('#totalscore-' + i).html(results[i]);
	}
});

$('#component-beforeleaving').on("click", "#btn-save-beforeleaving", function () {
	var urlAction = $(this).closest('#component-beforeleaving').data('action');
    $().docoForm("click", {
        // skipConfirm: false,
        // skipConfirmMessage: true,
        data: $("#component-beforeleaving *").serializeArray(),
        url: urlAction,
        success: function (response) {
        	$('#table-beforeleavingdrugsupport tbody').html('');
            for (var i in response.item.drugsupports) {
				populateTableDrugSupport(response.item.drugsupports[i]);
            }
			populateTableDrugSupport(null);
        }
    });
});

$(".beforeleavingusetimepicker").timepicker({
    showMeridian: false
});

$(document).on('click', "#save-button-before-leaving", function() {
	var data = {};
	var serializeArray = $('#modal-before-leaving').serializeArray();
	for (var i in serializeArray) {
		data[serializeArray[i].name] = serializeArray[i].value;
	}
	if (data.obatalkes_nama.length <= 0 || data.dose.length <= 0) {
		$("#modal_backdrop").modal("hide");
		return false;
	}else{
		populateTableDrugSupport(data);
		$("#modal_backdrop").modal("hide");
	}
});

// trigger default

$('#component-beforeleaving .calculation:eq(0)').trigger('change');

populateTableDrugSupport(null);
