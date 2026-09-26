
$(document).ready(() => {
    progress.css("display", "none")
    $("#jenis_laporan").val(1145).trigger('change')

    const showInfo = () => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve($(".populate-data-index").html(`mempersiapkan data ...`))
            }, 1000);
            setTimeout(() => {
                resolve($(".populate-data-index").css("display", "none"))
                resolve(progress.css("display", "block"))
                resolve($(".label-progress-index").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
            }, 2000);
        })
    }

    const setPresentase = function(progress) {
        setTimeout(() => {
            $(".progress-index .label-persentase-index").html(progress)
            $(".progress-index .progress-bar").css("width", progress +"%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
        }, 2500);
    }

    async function updateProgressBar() {
        let config = await $.getJSON("./../../json/setup.json")
        
        if (config.origin == "true") {
            
            var socket = io.connect(window.location.origin);
        } else {
            var socket = io.connect(config.ip+':'+config.port);
        }

        const channel = `kunjungan-rawat-inap:`
        await showInfo()
        var tgl = $('.startDate').val()+' - '+$('.endDate').val();
        let _data = {
          "advance_filter":{  tgl_pendaftaran: tgl,
            carabayar_id:$('#carabayar_id').val(),
            penjamin_id:$('#penjamin_id').val(),
            ruangan_id:$('#ruangan_id').val(),
            nosep:$('#nosep').val(),
            kamar_id:$('#kamar_id').val(),
            tempattidur_id:$('#tempattidur_id').val(),
            nosep:$('#nosep').val(),
            pegawai_id:$('#pegawai_id').val()},
        }
        
        $.ajax({
            url : '/pendaftaran/kunjungan-rawat-inap/get-data-serconn',
            data: _data,
            success : function (data) {
                
                
                socket.on(channel + data.randString, (message) => {
                    
                    const _data = $.parseJSON(message);
                    
                    const { status , messageProcess , filename, progress} = _data
                    if(status == 'update') {
                        let valProgress = 30
                        setPresentase(valProgress)
                    } else {
                        let valProgress = 100
                        setPresentase(valProgress)
                        $('#cari').prop('disabled', false);
                        setTimeout(() => {
                            $(".label-progress-index").html(`<p style="font-size:16px;font-weight:bold;"> Berhasil menyiapkan data</p>`)
                            if(draw > 0) {
                                table.clear().draw()
                                table.rows.add(_data.data); 
                                table.columns.adjust().draw(); 
                            } else {
                                
                                
                                table = $('#laporan').DataTable({
                                    data:  _data.data,
                                    scrollX: true,
                                    destroy: true,
                                    searching: false,
                                    paging: false,
                                    sorting: false,
                                    displayLength: 100,
                                    processing: false,
                                    serverSide: false,
                                    ordering: false,
                                    lengthChange: false,
                                    columns: _data.header.columns,
                                });
                                
                                draw++;
                                
                            }
                            contentData.css("display", "");
                        }, 2500);
                        setTimeout(() => {
                            setPresentase(0)
                            $('.progress-index').css("display", "none");
                            $(".label-progress-index").html("")
                            table.columns.adjust().draw(); 
                        }, 4000);
                    }
                });

            }
        });
    }

    $('#penjamin_id').on('depdrop:afterChange', function(){
        var selectedValue = $('#carabayar_id').val();
        if (selectedValue == null || selectedValue == '' || selectedValue == undefined) {
            $(this).val('').trigger('change');
            $(this).prop('disabled', true);
        } else {
            $(this).prop('disabled', false);
        }
    })

    $('#kamar_id').on('depdrop:afterChange', function(){
        var selectedValue = $('#ruangan_id').val();
        if (selectedValue == null || selectedValue == '' || selectedValue == undefined) {
            $(this).val('').trigger('change');
            $(this).prop('disabled', true);
        } else {
            $(this).prop('disabled', false);
        }
    })

    $('#tempattidur_id').on('depdrop:afterChange', function(){
        var selectedValue = $('#kamar_id').val();
        if (selectedValue == null || selectedValue == '' || selectedValue == undefined) {
            $(this).val('').trigger('change');
            $(this).prop('disabled', true);
        } else {
            $(this).prop('disabled', false);
        }
    })

        $(document).on("click","#export-excel", function(e){
            e.preventDefault();


        })

        $(document).on('click', '#reset', function (e) {
            $('.select2').select2();
            $('.select2').val('').trigger('change');
            $('#penjamin_id').prop('disabled', true);
            $('#kamar_id').prop('disabled', true);
            $('#tempattidur_id').prop('disabled', true);
            $('.startDate').val('');
            $('.endDate').val('');
            if(draw > 0) {
                table.clear().draw();
            }
            setPresentase(0);
            contentData.css("display", "none");
            progress.css("display", "none");
            $(".label-progress-index").html("");
            $('#cari').prop('disabled', false);
        })
        $(document).on('click','#cari', function (e) {
            e.preventDefault();
    
            setTimeout(() => {
                var tgl = $('.startDate').val()+' - '+$('.endDate').val();
                let _data = {
                  "advance_filter":{  tgl_pendaftaran: tgl,
                    carabayar_id:$('#carabayar_id').val(),
                    penjamin_id:$('#penjamin_id').val(),
                    ruangan_id:$('#ruangan_id').val(),
                    kamar_id:$('#kamar_id').val(),
                    nosep:$('#nosep').val(),
                    tempattidur_id:$('#tempattidur_id').val(),
                    pegawai_id:$('#pegawai_id').val()},
                }
                var _param = $.param(_data);

                $("#export-excel").attr("action","/pendaftaran/kunjungan-rawat-inap/show-popup?tipe=excel&"+_param);
                $("#export-excel").attr("data-target","#modal_backdrop");
                $("#export-excel").attr("data-width","75%");
                $("#export-excel").attr("data-toggle","modal");

                updateProgressBar()
            }, 1000);
            // $('#cari').prop('disabled', true);
        })
});