let autoObat = $('.autoObat');
let id_obat = $('.id_obat');
let apotek = { list_obat: {} };

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

        var _id_obat = id_obat.val();
        $(".autoObat").val(_id_obat).trigger('change');
    }
})