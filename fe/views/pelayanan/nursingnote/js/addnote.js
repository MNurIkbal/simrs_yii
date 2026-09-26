
    $(".jam_mulai").val(moment( new Date() ).format('HH:mm:00'))
    $(".pickadate").val( moment( new Date() ).format('DD/MM/YYYY'))
    $('.jam_mulai').timepicker({
        showMeridian: false,
        minuteStep: 5,
        defaultTime: false
    });

    $(".pickadate").pickadate({
        format: 'dd/mm/yyyy',
        min: [thn,bln,tgl],
        max: true,
        // onStart: function ()
        // {
        //     // console.log($(".pickadate").val())
        //     // var date = moment( $(".pickadate").val() ? $(".pickadate").val() : new Date() )
        //     // this.set('select', [date.year(), date.month() + 1, 24]);
        // },
    });
    
   $(document).ready(function(){
        let kebidanan = [];
        let keperawatan = [];
        var jenis_kegiatan = 0
        
  
        $("#btn-save-note").bind('click', () => {

            var dataselect = $('#nursingnoteform-kegiatan_perawat').select2("data")
            var values, index;
                values = $("#nursing-note-form").serializeArray();
                var a = 0
                for (index = 0; index < values.length; ++index) {
                    if (values[index].name == "NursingNoteForm[kegiatan_perawat][]") {
                        values[index].value = [dataselect[a].id,dataselect[a].text].join(',');
                        a = a + 1
                    }
                }

            $.ajax({
                url: $("#nursing-note-form").prop('action'),
                method: 'POST',
                data: values,
                success: (response) => {
                    const {data} = response
                    var aa = $("#nursing-note-form").serializeArray()
                    $('#tabel-nursing-note').DataTable().ajax.reload();
                    $("#nursing-note-form").parents('.modal').modal('hide')
                     docoNotification('success', 'Proses berhasil', 'Nursing Note berhasil disimpan')
                     return false;
                }
            })
        })
        $("#checkbox").on('change', function() {
            if ($(this).is(':checked')) {
              jenis_kegiatan = 1
            } else {
              jenis_kegiatan = 0
            }
        });
        $('#nursingnoteform-kegiatan_perawat').docoPaginationSelec2({
        placeholder : '-- Pilih Jenis Kegiatan --',
        tags : true,
        tokenSeparators: [',', '_'],
        multiple: true,
        _api : url+'/get-kegiatan-keperawatan',
            ajax: {
                data: function (params) {
                    return {
                        jenis_kegiatan: jenis_kegiatan,
                        q: params.term,
                        page: params.page || 1,
                    }
                },
                results: function (data, params) {
                    var more = (params.page * 15) < data.data.totalResult;
                    return { results: data.items, more: more };
                },
                processResults: function (res, params) {
                    var _array = [];
                    $.each(res.data.data, function (index, value) {
                        _array.push({
                            id:  index,
                            text: value,
                        })
                    });
                    return {
                        results: _array,
                       
                    }
                }
            }
        })
  
    })