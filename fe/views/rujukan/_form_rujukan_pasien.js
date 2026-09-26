var resepturTable = null;
var resepturData = null;

$(document).ready(function(){
    $('#rujukanpulangform-rujukan_dituju').focus();
    $('#rujukanpulangform-pegawai_kode_bpjs').select2();
	$('#rujukanpulangform-pegawai_kode_bpjs').trigger('change');
	$('#rujukanpulangform-diagnosa_prb').select2();
    $('#rujukanpulangform-diagnosa_prb').on('change', function(e) {
        console.log('er')
        $('#rujukanpulangform-diagnosa_keluar').val($('#rujukanpulangform-diagnosa_prb').select2('data')[0].text)
    });
	if(typeof $('#pasienpulangform-is_prb') != 'undefined' && $('#pasienpulangform-is_prb').is(":checked")) {
        $('#rujukanpulangform-pegawai_kode_bpjs').prop('disabled', false);
        $('.field-rujukanpulangform-pegawai_nama').addClass('hidden')
        $('.field-rujukanpulangform-pegawai_kode_bpjs').removeClass('hidden')
        $('.field-rujukanpulangform-diagnosa_keluar').addClass('hidden')
        $('.field-rujukanpulangform-diagnosa_prb').removeClass('hidden')
		$('.reseptur.hidden').removeClass('hidden');
		loadDataReseptur();
	}  else {
        $('.field-rujukanpulangform-pegawai_nama').removeClass('hidden')
        $('.field-rujukanpulangform-pegawai_kode_bpjs').addClass('hidden')
        $('.field-rujukanpulangform-diagnosa_keluar').removeClass('hidden')
        $('.field-rujukanpulangform-diagnosa_prb').addClass('hidden')
    }
    $('.jam_rujukan').timepicker({
        showMeridian: false,
        minuteStep: 5,
        defaultTime: false
    });
});

function loadDataReseptur() {
	if(resepturTable == null) {
        $.ajax({
            url: "/rajal/pemeriksaan/get-data-obat-prb",
            method: "GET",
            data: {
                pendaftaran_id: pendaftaran_id
            },
            success: function(response) {
                resepturData = response.data
            }
        })

        resepturTable = $('#tabel-reseptur').docoTabel({
            cacheFilter: false,	
            filter: false,
            info: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollY: false,
            scrollX: true,
            ordering: false,
            ajax: {
                url: `/rajal/pemeriksaan/get-data-obat-prb`,
                data: {
                	pendaftaran_id: pendaftaran_id
                }
            },
            columns: [
                {
                    orderable: false,
                    render: (data, rowElement, rowData, rowAdditionalData) => {
                        var tableInfo = resepturTable.page.info()
                        return tableInfo.start + rowAdditionalData.row + 1
                    }
                },
                {
                    data: 'rke',
                    orderable: false,
                    render: (data, rowElement, rowData, rowAdditionalData) => {
                        return data == null ? '-' : data
                    }
                },
                {
                    data: 'obatalkes_nama',
                    orderable: false,
                },
                {
                    data: 'signa_nama',
                    orderable: false,
                },
                {
                    data: 'qty_reseptur',
                    orderable: false,
                },
            ],
            rowCallback: (rowElement, data) => {
                if (data.is_bpjs) {
                    $(rowElement).css('background-color', '#ffc0cb')
                }
            },    
        })		
	} else {
        resepturTable.draw();
    }
}