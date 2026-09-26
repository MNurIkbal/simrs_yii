var _menudietdata = []
$(document).ready( function() {
    $('#jenis-diet').on('change', function() {
        let _jenisdiet = $('#jenis-diet').val()
        if( _jenisdiet != '' ){
            $.ajax({
                url: `/rajal/pemeriksaan/get-menu-diet?jenisdiet_id=${_jenisdiet}`,
                beforeSend: function(request) {
                    $('#menu-diet').empty().attr('disabled', true);
                },
                success: function(result) {
                    const {data} = result
                    let _option = [
                        {
                            id: 0,
                            text: 'Pilih'
                        }
                    ]
                    if(data.length){
                        $.each(data, function(k,v) {
                            _option.push(v)
                        })
                    }
                    $('#menu-diet').select2({
                        disabled: false,
                        data: _option
                    })
                }
            })
        }else{
            $('#menu-diet').attr('disabled', true)
        }
    })
    $('#btn-add-diet').on('click', function(e) {
        e.preventDefault()
        if( $('#jenis-diet').val() == ''){
            $('#jenis-diet').closest('td').append(`<p style="color: red" class="permintaanmakan-validate">Jenis Diet Tidak Boleh Kosong</p>`)
        }
        if( $('#menu-diet').val() == '' || $('#menu-diet').val() == null){
            $('#menu-diet').closest('td').append(`<p style="color: red" class="permintaanmakan-validate">Menu Diet Tidak Boleh Kosong</p>`)
        }
        if( $('#waktu-diet').val() == ''){
            $('#waktu-diet').closest('td').append(`<p style="color: red" class="permintaanmakan-validate">Waktu Diet Tidak Boleh Kosong</p>`)
        }
        if( $('#qty-diet').val() == ''){
            $('#qty-diet').closest('td').append(`<p style="color: red" class="permintaanmakan-validate">Jumlah Tidak Boleh Kosong</p>`)
        }
        if( $('.permintaanmakan-validate').length ){
            setTimeout(() => {
                $('.permintaanmakan-validate').remove()
            }, 1500);
            return false
        }
        let _data = {
            jenisdiet_id:  $('#jenis-diet').val(),
            jenisdiet_nama:  $('#jenis-diet :selected').text(),
            waktu_diet:  $('#waktu-diet').val(),
            waktudiet_nama:  $('#waktu-diet :selected').text(),
            makanandiet_id:  $('#menu-diet').val(),
            menudiet_nama:  $('#menu-diet :selected').text(),
            jumlah: $('#qty-diet').val(),
            keterangan: $('#keterangan-diet').val(),
            daftartindakan_id: $('#menu-diet').select2('data')[0].daftartindakan_id
        }
        _menudietdata.push(_data)
        $('#jenis-diet').val('').trigger('change')
        $('#menu-diet').val('').trigger('change')
        $('#waktu-diet').val('').trigger('change')
        $('#qty-diet').val(null)
        $('#keterangan-diet').val(null)
        loadDataDiet()
    })
    $('#btn-save-permintaanmakan').bind('click', function() {
        if( !_menudietdata.length ){
            docoNotification('warning', 'Silahkan cek inputan', 'Belum ada data menu diet yang ditambahkan!')
            return false
        }
        confirmationDialog('Apakah anda yakin untuk menyimpan data ini ?', (confirm) => {
            if(confirm) {
                $.ajax({
                    url: '/rajal/pemeriksaan/permintaan-makan?id=' + pendaftaran_id,
                    method: "POST",
                    data: {
                        detaildiet: _menudietdata
                    },
                    beforeSend: function() {
                        showLoader()
                    },
                    success: function(response) {
                        docoNotification('success', 'Proses Berhasil!', 'Permintaan Makan Berhasil Dikirim Ke Unit Gizi!')
                        _menudietdata = [];
                        loadDataDiet();
                    },
                    error: function() {
                        hideLoader()
                    }
                })
            }
        })
    })
    const loadDataDiet = () => {
        let no = 0
        let _html = ""
        if( _menudietdata.length ){
            $.each(_menudietdata, function(k,v) {
                no++
                if(typeof v.jenisdiet_nama != 'undefined'){
                    _html += `
                        <tr>
                        <td>${no}</td>
                        <td>${v.jenisdiet_nama}</td>
                        <td>${v.menudiet_nama}</td>
                        <td>${v.waktudiet_nama}</td>
                        <td>${v.jumlah}</td>
                        <td>${v.keterangan}</td>
                        <td style="padding-bottom: 10px !important">
                            <button class="btn btn-danger btn-sm btn-delete-menudiet" data-key="${k}">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        </td>
                        </tr>
                    `
                }
            })
            $('#menu-diet-data tbody').html(_html)
            $('.btn-delete-menudiet').unbind()
            $('.btn-delete-menudiet').bind('click', ({delegateTarget}) => {
                let _key = $(delegateTarget).attr('data-key')
                _menudietdata.splice(_key, 1)
                loadDataDiet()
            })
        }else{
            $('#menu-diet-data tbody').html(`
                <tr>
                    <td colspan="7" style="padding: 10px !important" class="text-center">Belum Ada Permintaan Makan yang Ditambahkan</td>
                </tr>
            `)
        }
    }
})