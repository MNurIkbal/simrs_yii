
function bindingRegionBpjs(objectElement) {
    // object element must be 3 element -> province,city,district
    const { province, city, district } = objectElement
    const defaultValue = [
        {
            id: '',
            text: '--Pilih--'
        }
    ]
    if (!province.hasClass('select2-hidden-accessible')) {
        $.ajax({
            url: '/api/bpjs/referensi-provinsi',
            success: (res) => {
                const sourceList = []
                res.data.map((item) => {
                    sourceList.push({
                        id: item.kode,
                        text: item.nama
                    })
                })
                province.select2({
                    placeholder: 'Pilih Provinsi',
                    data: sourceList
                });
            },
            error: (res) => {
                docoNotification('warning', 'Terjadi Kesalahan!', res.responseJSON.message)
            }
        })
    } else {
        province.val(null).trigger('change')
    }
    province.bind('change', () => {
        if (province.val() !== '' && province.val() !== null) {
            $.ajax({
                url: '/api/bpjs/referensi-kabupaten',
                method: 'GET',
                data: {
                    kode_propinsi: province.val()
                },
                success: (res) => {
                    refreshOptionSelect2(city, res.data, {
                        id: 'kode',
                        text: 'nama'
                    })
                    city.val('').trigger('change')
                    city.prop('disabled', false)
                    resetDropdownRegion(district)
                },
                error: (res) => {
                    docoNotification('warning', 'Terjadi Kesalahan!', res.responseJSON.message)
                }
            })
        } else {
            city.val('').trigger('change')
            city.prop('disabled', true)
        }
    })
    city.bind('change', () => {
        if (city.val() !== '' && city.val() !== null) {
            $.ajax({
                url: '/api/bpjs/referensi-kecamatan',
                method: 'GET',
                data: {
                    kode_kabupaten: city.val()
                },
                success: (res) => {
                    refreshOptionSelect2(district, res.data, {
                        id: 'kode',
                        text: 'nama'
                    })
                    district.val('').trigger('change')

                    district.prop('disabled', false)
                },
                error: (res) => {
                    docoNotification('warning', 'Terjadi Kesalahan!', res.responseJSON.message)
                }
            })
        } else {
            district.val('').trigger('change')
            district.prop('disabled', true)
        }
    })
    district.bind('change', () => {
        if (district.val() !== '' && district.val() !== null) {
            district.prop('disabled', false)
        }
    })
    if (province.val() == null || province.val() == '') {
        city.prop('disabled', true)
    }
    if (city.val() == null || city.val() == '') {
        district.prop('disabled', true)
    }
    province.on('select2:close', ({ delegateTarget }) => {
        $(delegateTarget).focus()
    })
    city.on('select2:close', ({ delegateTarget }) => {
        $(delegateTarget).focus()
    })
    district.on('select2:close', ({ delegateTarget }) => {
        $(delegateTarget).focus()
    })
}
