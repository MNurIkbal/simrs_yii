/* 
    Author : Randy Vianda Putra (aweutist)
*/

let autoObat = $('.autoObat');
let id_obat = $('.id_obat');
let autoKasus = $('.autoKasus');
let id_kasus = $('.id_kasus');
let apotek = { list_obat: {}, list_kasus: {} };



$.ajax({
    url: '/apotek/obat-alkes-kasus/get-data-ajax',
    type: 'json',
    success: function(res) {
        let data = [];
        let response = res.data_obat;
        for (var i in response) {
            data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_namalain});
            apotek.list_obat[response[i].obatalkes_id] = response[i];
        }

        autoObat.select2({
            data: data,
            type: "GET",
            quietMillis: 50,
            minimumInputLength: 2,
        })
        
        autoObat.change(function (e) {
            var id = $(this).val();
            var selected = apotek.list_obat[id];
            if (typeof selected !== 'undefined') {
                id_obat.val(selected.obatalkes_id);
            }
        });


        let data_kasus = [];
        let response_kasus = res.data_kasus;
        for (var i in response_kasus) {
            data_kasus.push({ id: response_kasus[i].jeniskasuspenyakit_id, text: response_kasus[i].jeniskasuspenyakit_nama });
            apotek.list_kasus[response_kasus[i].jeniskasuspenyakit_id] = response_kasus[i];
        }

        autoKasus.select2({
            data: data_kasus,
            type: "GET",
            quietMillis: 50,
            minimumInputLength: 2,
        })
        
        autoKasus.change(function (e) {
            var id = $(this).val();
            var selected = apotek.list_kasus[id];
            if (typeof selected !== 'undefined') {
                id_kasus.val(selected.jeniskasuspenyakit_id);
            }
        });
        var _id_obat = id_obat.val();
        var _id_kasus = id_kasus.val();
        $(".autoObat").val(_id_obat).trigger('change');
        $(".autoKasus").val(_id_kasus).trigger('change');
    }
})

// Event Ready
$(document).ready(function () {
    $('body').tooltip({ selector: '[data-tooltip=tooltip]' })
    

    var table = $('#example').docoTabel({
        bInfo: false,
        bLengthChange: false,
        columns: [
            { data: 'rowNum', name: 'rowNum' },
            { data: 'obatalkes.obatalkes_namalain', name: 'obatalkes_m.obatalkes_namalain' },
            { data: 'jeniskasuspenyakit.jeniskasuspenyakit_nama', name: 'jeniskasuspenyakit_m.jeniskasuspenyakit_nama' },
            { data: 'aksi', name: 'aksi' }
        ],
        colNoOrder: [0, 3]
    });

    $('.reset').click(function(e) {
        $('#mySelect2').val(null).trigger('change');
        
    })

    $("#ajax-form").docoForm("submit", {
        success: function (data) {
            // if (data.status == 201)
                this.formInput[0].reset();
                table.reload();
                // $(".select2").select2("val", "");
            
        }
    });

    $("#update-form").docoForm("submit", {
        success: function (data) {
            document.location.href = "/apotek/obat-alkes-kasus/index"
        }
    });

    $(document).on('click', '.delete', function (event) {
        event.preventDefault();
        $(this).docoForm('delete', {
            success: function (data) {
                table.reload()
            }
        });
    })

    

});