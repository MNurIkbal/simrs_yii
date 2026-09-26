var tbl_surat = $("#tabel-surat-keterangan").docoTabel({
    filter: false,
    sorting: [[0, "asc"]],
    displayLength: 10,
    processing: true,
    serverSide: true,
    ajax: baseUrl + modul + url + "/get-master-surat?pendaftaran_id=" + pendaftaran_id,
    columns: [
        {
            width: "1%",
            title: "No",
            data: 'urutan',
            searchable: false
        },
        {
            title: "Judul Surat",
            data: "judul_surat"
        },
        {
            width: "20%",
            title: "Aksi",
            data: "aksi",
            searchable: false,
            orderable: false,
        }
    ],
    drawCallback: function(settings) {
        let surat_keterangan_pasien_id = null;
        $('.btn-edit-surat').bind('click', function ({ currentTarget }) {
            const { type, href, wrapper, width, isEklaim } = $(currentTarget).data()
            let wrapperParents = wrapper.split(' ')[0]
            let konsulpoliIdSuratKeterangan = typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''; // konsulpoli_id variable global js
            $(wrapperParents).find('.modal-dialog').css('width', width)
            showLoader('Memuat Halaman...')
            $(wrapper).docoLoad({
                url: href.replace('#pendaftaran_id#', pendaftaran_id).replace('#konsulpoli_id#', konsulpoliIdSuratKeterangan)+"&is_eklaim="+isEklaim,
                dataType: 'html',
                success: function (data) {
                    hideLoader()
                    $(wrapper).parents('.modal').modal('show')
                },
                error: function () {
                    hideLoader()
                }
            })
        })
        
        $(".btn-print-surat").bind('click', function({ currentTarget }) {
            let url = $(this).attr('data-target').replace('#pendaftaran_id#', dec_pendaftaran_id);
            window.open(url, '_blank');
        })

        $(".btn-checklist-dokumen").bind('click', function({ currentTarget }) {
            const { suratKeteranganId } = $(currentTarget).data()

            if ($(currentTarget).prop('checked') == true) {
                $("#surat-keterangan-"+suratKeteranganId).data("isEklaim", 1)
                sendStatusEklaim(true, suratKeteranganId)
                return
            }  
            
            $("#surat-keterangan-"+suratKeteranganId).data("isEklaim", 0)
            sendStatusEklaim(false, suratKeteranganId)
        })

        function sendStatusEklaim(statusEklaim, suratKeteranganId) {
            $.ajax({
                type: "GET",
                url: baseUrl + modul + url + "/update-status-eklaim",
                data: {
                    status_eklaim: statusEklaim,
                    pendaftaran_id: pendaftaran_id,
                    surat_keterangan_id: suratKeteranganId
                },
                success: function (response) {
                    if(response?.meta?.code == 200) {
                        docoNotification('success', 'Proses Berhasil!', response?.data?.data?.message);
                    }
                },
                error: function(jqXhr) {
		        	docoNotification('error', "Oops Gagal", i18next.t(jqXhr.responseText));
                }
            });
        }
    }
});