$("#btn-back").on("click", function(event) {
    $("#modal_backdrop").modal('toggle');
 });
 $(document).ready(function () {
     _tableObat = $('#tmp-kontrak').DataTable( {
         data: {},
         scrollX: true,
         columns: [
            {
                title: "No",
                data: "nomer",
                orderable: false
            },
            {
                title: "Nomor Kontrak Supplier", 
                data: "kontraksupplier_no",
            },
            {
                title: "Tgl Berlaku (DD/MM/YYYY)", 
                data: "tgl_berlaku",
            },
            {
                title: "Kode Supplier", 
                data: "kode_supplier",
            },
            {
                title: "Nama Supplier", 
                data: "supplier",
            },
            {
                title: "Payterm", 
                data: "payterm",
            },
            {
                title: "PPN (%)", 
                data: "persen_ppn",
            },
            {
                title: "Contact Person", 
                data: "contact_person",
            },
            {
                title: "Kode Obat", 
                data: "kode_obat",
            },
            {
                title: "Nama Obat", 
                data: "nama_obat",
            },
            {
                title: "Harga Order (Rp)", 
                data: "harga",
                class : "text-right",
            },
            {
                title: "Pengurang", 
                data: "diskon",
                class : "text-right",
            },
            {
                title: "Qty Min", 
                data: "qty_min",
                class : "text-right",
            },
            {
                title: "Total Harga (Rp)", 
                data: "total_harga",
                class : "text-right",
            },
            {
                title: "Keterangan Gagal", 
                data: "keterangan",
            },
         ],
     });
 });
 
 $("#file-upload").change(function(event) {
     event.preventDefault();
     let button = this;
     let data = new FormData();
     let dataPost = $("#form-upload").serializeArray();
     let getLink = $('.lihat_file').attr('href');
     
     data.append("UploadForm[upload_file]", $("#file-upload")[0].files[0]);
     $('.myprogress').css('width', '0');
     $('.msg').text('');

     $.ajax({
         type: "post",
         dataType: false, // what to expect back from the PHP script, if anything
         cache: false,
         contentType: false,
         processData: false,
         url: "/pengadaan/kontrak-supplier/upload",
         data: data,
         beforeSend: function (request, res) {
         },
         xhr: function (res) {
             var xhr = new window.XMLHttpRequest();
             xhr.upload.addEventListener("progress", function (evt) {
                 if (evt.lengthComputable) {
                     var percentComplete = evt.loaded / evt.total;
                     percentComplete = parseInt(percentComplete * 100);
                     $('.myprogress').text(percentComplete + '%');
                     $('.myprogress').css('width', percentComplete + '%');
                 }
             }, false);
             return xhr;
         },
         success: function (res) {
             $("#file-upload").val('');
             // $('.msg').text(res.response.file);
             $("#file-upload").removeAttr("disabled");
             var data = res.response.data;
             if (res.response.status == 422) {
                 failedProgressBar();
                 docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File gagal di upload"));
                 tableBlank();
             } else {
                 _tableObat.destroy();
                 _tableObat = $('#tmp-kontrak').DataTable( {
                     data: data,
                     scrollX: true,
                     columns: [
                        {
                            title: "No",
                            data: "nomer",
                            orderable: false
                        },
                        {
                            title: "Nomor Kontrak Supplier", 
                            data: "kontraksupplier_no",
                        },
                        {
                            title: "Tgl Berlaku (DD/MM/YYYY)", 
                            data: "tgl_berlaku",
                        },
                        {
                            title: "Kode Supplier", 
                            data: "kode_supplier",
                        },
                        {
                            title: "Nama Supplier", 
                            data: "supplier",
                        },
                        {
                            title: "Payterm", 
                            data: "payterm",
                        },
                        {
                            title: "PPN (%)", 
                            data: "persen_ppn",
                        },
                        {
                            title: "Contact Person", 
                            data: "contact_person",
                        },
                        {
                            title: "Kode Obat", 
                            data: "kode_obat",
                        },
                        {
                            title: "Nama Obat", 
                            data: "nama_obat",
                        },
                        {
                            title: "Harga Order (Rp)", 
                            data: "harga",
                            class : "text-right",
                        },
                        {
                            title: "Pengurang", 
                            data: "diskon",
                            class : "text-right",
                        },
                        {
                            title: "Qty Min", 
                            data: "qty_min",
                            class : "text-right",
                        },
                        {
                            title: "Total Harga (Rp)", 
                            data: "total_harga",
                            class : "text-right",
                        },
                        {
                            title: "Keterangan Gagal", 
                            data: "keterangan",
                        },
                     ],
                     "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                         if ((aData.status == false)) {
                             $(nRow).css("background", "#F6C1C1");
                         } else {
                             $(nRow).css("background-color", "fff");
                         }
                     }
                 });
                 let nama_file = res.response.file;
                 $("#label-file").html(nama_file);
                 $(".lihat_file").attr('data-file', nama_file);
                 docoNotification("success", i18next.t("Proses Berhasil"), i18next.t("File Berhasil di upload."));
             }
         },
         error: function(res) {
             failedProgressBar();
             docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File yang di upload tidak sesuai dengan format contoh, xls dan xlsx"));
             tableBlank();
         }
     });
 })

 function failedProgressBar() {
     $("#label-file").html('Pilih Berkas');
     $(".lihat_file").attr('data-file', 'Pilih Berkas');
     $(".myprogress").css("width","0%");
     $(".myprogress").html("0%")
 }

 $("#upload-kontrak-supplier").on("click", function(event) {
     event.preventDefault();
     var data = $("#upload-kontrak-supplier-form").serializeArray();
     $(this).docoForm('click',{
        url: '/pengadaan/kontrak-supplier/upload-kontrak-supplier',
        data: data,
        success : function(res) {
            var response = res.data;
            let message = [];
            if (response.data != undefined) {
                var data = response.data
                for (const [key, value] of Object.entries(data)) {
                    var x = key+' = '+value
                    message.push(x);
                }
            }
            (new PNotify({
                title: "Berhasil di Simpan",
                text: "<strong>" + message.join( "<br />" ) + "</strong>",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: true,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true,
                    buttons: [
                        {
                            text: "Ya",
                            addClass: "btn btn-xs btn-success",
                        },
                        {
                            text: 'Tidak',
                            addClass: 'btn btn-xs btn-danger hidden',
                        }
                    ]
                },
                history: {
                    history: false
                }
            })).get().on("pnotify.confirm", function () {
                table.draw();
                $("#modal_backdrop").modal('toggle');
            }).on('pnotify.cancel', function() {
            });
        },
        error: function(res) {
            var response = res.responseJSON.data;
            let message = [];
            if (response.data != undefined) {
                var data = response.data
                for (const [key, value] of Object.entries(data)) {
                    message.push(value);
                }
                docoNotification("error", "Proses Gagal", message.join( "<br />" ))
            } else {
                docoNotification("error", response.data.meta.title, response.message)
            }
        }
     });
 });

 function tableBlank() {
     _tableObat.clear();
     _tableObat.destroy();
     _tableObat = $('#tmp-kontrak').DataTable( {
         data: {},
         scrollX: true,
         columns: [
            {
                title: "No",
                data: "nomer",
                orderable: false
            },
            {
                title: "Nomor Kontrak Supplier", 
                data: "kontraksupplier_no",
            },
            {
                title: "Tgl Berlaku (DD/MM/YYYY)", 
                data: "tgl_berlaku",
            },
            {
                title: "Kode Supplier", 
                data: "kode_supplier",
            },
            {
                title: "Nama Supplier", 
                data: "supplier",
            },
            {
                title: "Payterm", 
                data: "payterm",
            },
            {
                title: "PPN (%)", 
                data: "persen_ppn",
            },
            {
                title: "Contact Person", 
                data: "contact_person",
            },
            {
                title: "Kode Obat", 
                data: "kode_obat",
            },
            {
                title: "Nama Obat", 
                data: "nama_obat",
            },
            {
                title: "Harga Order (Rp)", 
                data: "harga",
                class : "text-right",
            },
            {
                title: "Pengurang",
                data: "diskon",
                class : "text-right",
            },
            {
                title: "Qty Min", 
                data: "qty_min",
                class : "text-right",
            },
            {
                title: "Total Harga (Rp)", 
                data: "total_harga",
                class : "text-right",
            },
            {
                title: "Keterangan Gagal", 
                data: "keterangan",
            },
         ],
     });
 }