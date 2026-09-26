/*
* @Author: ilhamsyah
* @Date:   2022-02-22 16:03:25
*/

var table;

$(document).ready(function() {
    table = $("#tb-lap-permintaan-makan").docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [[1, "desc"]],
        ajax: {
            url: baseUrl + "gizi/laporan-permintaan-makan/get-data-permintaan-makan-mhg",
        },
        columns: [
            {title: no, data: "no", searchable: false, orderable: false},
            {
                title: no_bed, data: "ruangan_nama",searchable: false,
                render: (data, rowElement, rowData) => {
                    return `                      
                    <p style="margin-bottom: 2px"> ${rowData.no_bed} </p>
                    <p> ${rowData.kelaspelayanan_nama}</p>
                    `
                }
            },

            {
                title: nama_pasien, data: "nama_pasien",searchable: false,
                render: (data, rowElement, rowData) => {
                    return `                      
                    <p style="margin-bottom: 2px"> ${rowData.no_rekam_medik} </p>
                    <p> ${rowData.nama_pasien}</p>
                    `
                }
            },

            {
                title: tanggal_lahir, data: "tanggal_lahir",searchable: false, orderable: false,
                render: (data, rowElement, rowData) => {
                    return `                      
                        <p style="margin-bottom: 2px"> ${rowData.tanggal_lahir == null ? '-' : moment(rowData.tanggal_lahir).format('DD-MM-YYYY')}</p>
                        <p> ${rowData.umur} </p>
                    `
                }
            },
            {
                title: tgl_admisi, data: "tgl_admisi",
                render: (data, rowElement, rowData) => {
                    return `                      
                    <p style="margin-bottom: 2px"> ${rowData.tgl_admisi} /</p>
                    <p> ${rowData.LOS} - ${rowData.dokter_dpjp}</p>
                    `
                }
            },
            // {title: diet, data: "menu_makan",searchable: false, orderable: false},
            {title: type_diet, data: "jenisdiet_nama",searchable: false, orderable: false},
            // {title: new_diet, data: "new_diet",searchable: false, orderable: false},
            {title: remark, data: "remark",searchable: false, orderable: false},
            {title: diagnosa, data: "diagnosa",searchable: false, orderable: false},
      
            {title: BF, data: "BF",searchable: false, orderable: false},
            {title: S1, data: "S1",searchable: false, orderable: false},
            {title: LN, data: "LN",searchable: false, orderable: false},
            {title: S2, data: "S2",searchable: false, orderable: false},
            {title: DN, data: "DN",searchable: false, orderable: false},
            {title: S3, data: "S3",searchable: false, orderable: false},
            {title: SP, data: "SP",searchable: false, orderable: false},
            {title: EX, data: "EX",searchable: false, orderable: false},
        ],
    });

    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(table , [
        [4, inputTanggal,'Tanggal Dirawat','&emsp;&emsp;&emsp;<i class="fa fa-info-circle" data-placement="right" data-toggle="tooltip" data-html="true" title="Laporan ini menampilkan pasien dirawat per tanggal yang telah ditentukan." aria-hidden="true"></i>'], 
    ]);


    dateRangeHelper(".startDate", false);

    $(".export-excel").on('click', function (event) {
        var col = table.data().count();
        if (col === 0) {
            docoNotification(
              'warning',
              'Terjadi Kesalahan',
              'Data Tidak Tersedia!'
            );
        } else {
            var url = window.location.origin;
            var target = $(this).attr('data-target');
            var datas = table.ajax.params();
            let wrapper = $(".filter-form");
            wrapper.find('.advancedFilter input').each(function () {
                var input = $(this);
                var index = input.attr('col-index');
                if (datas != null) {
                  if (typeof datas.columns[index] !== 'undefined') {
                    datas.columns[index].search.search = input.val();
                  }
                }
            });
            wrapper.find('.advancedFilter select').each(function () {
                var input = $(this);
                var index = input.attr('col-index');
                var data_id = input.val();
                var data_text = input.find('option:selected').text();
                if (datas != null) {
                  if (typeof datas.columns[index] !== 'undefined') {
                    datas.columns[index].search.search = data_id;
                    datas.columns[index].search.label = data_text;
                  }
                }
            });
            wrapper.find('.advancedFilter select[multiple]').each(function () {
                var input = $(this);
                var index = input.attr('col-index');
                var values = new Array();

                $.each(input.find('option:selected'), function (i, item) {
                  values.push($(item).val());
                });
            });
            window.open(url + target + $.param(datas));
        }
    });
});