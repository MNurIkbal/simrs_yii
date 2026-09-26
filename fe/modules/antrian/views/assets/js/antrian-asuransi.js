$('#pasien-asuransi-content').click((e) => {
    e.preventDefault()

    console.log("hitto");
    let carabayarId = $('#pasien-asuransi-content').data('carabayar')
    let ruanganId = $('#pasien-asuransi-content').data('ruangan')
    let jenisantrianId = $('#pasien-asuransi-content').data('jenis-antrian')
    let antrian_jenis_id = (new URL(document.location)).searchParams.get('jenis_id');
    let payload = {
        'carabayar_id': carabayarId,
        'ruangan_id': ruanganId,
        'jenisantrian_id': jenisantrianId,
        "antrian_jenis_id": antrian_jenis_id,
    }

    $.ajax({
        url: '/antrian/dashboard/create-antrian-v2',
        type: 'POST',
        dataType: 'json',
        data: payload,
        beforeSend: function (data) {
        },
        success: function (res) {
            console.log(res)
            if(res?.response){
                if(res?.response?.data?.length == 0){
                    let errTitle = res.response.title
                    let errMessage = res.response.message
                    docoNotification('warning', errTitle, errMessage);
                }else{
                    let responseMessage = res.response.text
                    docoNotification('success', 'Proses Berhasil!', responseMessage);
                    let antrian_id = res?.response?.data?.antrian_id
                    let no_antrian = res?.response?.data?.no_antrian

                    $('#modal_backdrop').modal('hide');
                    $('#modal_status_pasien').modal('hide');
                    let data = {
                        "antrian_id": antrian_id,
                        "no_antrian": no_antrian,
                        "jenisantrian_id": jenisantrianId,
                        "nama_antrian": "Umum",
                        "antrian_jenis_id": antrian_jenis_id,
                    }
                    console.log(data)

                    $.ajax({
                        type: 'POST',
                        url: '/antrian/dashboard/cetak-antrian-v2',
                        timeout: (5 * 1000),
                        data: data,
                        success: function (res){
                            location.reload();
                            $.redirect("/antrian/dashboard/cetak-antrian-v2",{
                                antrian_id: antrian_id,
                                no_antrian: no_antrian,
                                jenisantrian_id: jenisantrianId,
                                nama_antrian: 'Umum',
                                antrian_jenis_id: antrian_jenis_id,
                            });
                        },
                        error: function (error) {
                        }
                    });
                }
            }
        },
        error: function(data){
            if(data?.responseJSON){
                let response = data?.responseJSON?.response
                let errorMessage = response.message
                docoNotification('warning','Perhatian!', errorMessage);
            }
        }
    });
})
