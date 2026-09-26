var listSelected = [];

$(document).ready(function() {
    let canReDraw = false;

    table = $("#table-esign").docoTabel({
        filter: false,
        ordering: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: "/rm/esign/get-list",
        columns: [
            {
                width: '5%',
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    if(listSelected.includes(rowData.dokumen_sign_id)) {
                        return "<input type='checkbox' class='select-doc-sign' value='" + rowData.dokumen_sign_id + "' checked/>";
                    } else {
                        return "<input type='checkbox' class='select-doc-sign' value='" + rowData.dokumen_sign_id + "' />";
                    }
                }
            },
            {
                width: '5%',
                title: "No.",
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = table.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                width: '35%',
                title: "Nama Dokumen",
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    return rowData.type + "<br>" + convertDateByFormat(rowData.created_date, "d m y");
                }
            },
            {
                width: '40%',
                title: "Pasien",
                render : (data, rowElement, rowData, rowAdditionalData) => {
                    return "<b>" + rowData.nama_pasien + "</b><br>" + rowData.no_pendaftaran + " - " + rowData.no_rekam_medik;
                }
            },
            {
                width: '15%',
                render : (data, rowElement, rowData, rowAdditionalData) => {
                    return "<button class='btn btn-sm btn-info btn-preview-esign' data-url='/rm/esign/preview?name=" + rowData.filename +"'><b><i class=' fa fa-eye'></i></b></button>";
                }
            }
        ]
    });

    $(document).on('click', '.select-doc-sign', function(){
        let val = parseInt(this.value)
        if(this.checked) {
            if(!listSelected.includes(val)) {
                listSelected.push(val)
            }
        } else {
            if(listSelected.includes(val)) {
                let idx = listSelected.indexOf(val)
                listSelected.splice(idx, 1);
            }
        }
    })
    $(document).on('click', '.btn-preview-esign', function(){
        let dataBtn = $(this).data()
        $("#preview-content").attr('src', dataBtn.url)
        $('#modal-preview').modal({
            backdrop: 'static',
            keyboard: false
        })
    })
    $('#btn-sign').on('click', function() {
        if(listSelected.length > 0) {
            confirmationDialog("Anda yakin menandatangani " + listSelected.length + " Dokumen?",
                (isConfirm) => {
                if (isConfirm) {
                    signProccess(listSelected);
                }
            });
        } else {
            docoNotification("warning", "Peringatan", "Pilih dokumen!");
        }
    });

    $('#btn-sign-all').on('click', function() {
        confirmationDialog("Anda yakin menandatangani Semua Dokumen?",
            (isConfirm) => {
            if (isConfirm) {
                signProccess('all');
            }
        });
    });

    function signProccess(doc_id) {
        $.ajax({
            url: "/rm/esign/sign",
            method: "POST",
            contentType: "application/json",
            dataType: "json",
            data: JSON.stringify({
                doc_id: doc_id
            }),
            success: function(res) {
                if(typeof res.url != 'undefined') {
                    canReDraw = true;
                    $("#preview-content").attr('allow', "autoplay; camera; microphone")
                    $('#modal-preview').modal({
                        backdrop: 'static',
                        keyboard: false
                    })
                    $("#preview-content").attr('src', res.url + "&redirect_url=" +  window.location.origin + "/rm/esign/callback-tilaka" )
                    // window.open(res.url, '_blank')
                } else if(typeof res.message != 'undefined') {
                    docoNotification("error", "Gagal menandatangani", res.message);
                } else {
                    docoNotification("error", "Gagal menandatangani", "Proses tandatangan gagal");
                }
            },
            error: function(err) {
                docoNotification("error", "Gagal menandatangani", "Proses tandatangan gagal");
            }
        });
    }

    $('#modal-preview').on('hidden.bs.modal', function () {
        if(canReDraw) {
            window.location.reload();
        } else {
            $("#preview-content").attr('src','about:blank')
        }

    })
})