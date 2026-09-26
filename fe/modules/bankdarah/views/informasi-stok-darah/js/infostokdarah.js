var table;

var cur_date = "";
$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: true,
        columnDefs: [
            {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }
        ],
        select: {
            style:    "os",
            selector: "tr"
        },
        sorting: [[2, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"bankdarah/informasi-stok-darah/get-data",
        columns : [
        { // 0
            title: "",
            data: null,
            defaultContent: "",
            searchable: false,
            orderable: false,
            width: "10%"
        },
        { // 1
            title: "No.",
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        { // 2
            title: "Jenis Darah",
            data: "jenisdarah_nama",
            name: "jenisdarah_id"
        },
        { // 3
            title: "Golongan Darah",
            data: "golongandarah",
            name: "golongandarah_id"
        },
        { // 4
            title: "Rhesus",
            data: "rhesus",
            searchable: false
        },
        { // 5
            title: "Tanggal Kadaluarsa",
            data: "tgl_kadaluarsa"
        },
        { // 6
            title: "Suhu Penyimpanan (c)",
            data: "suhu_penyimpanan",
            searchable: false
        },
        { // 7
            title: "Stok Tersedia",
            data: "qty_tersedia",
            searchable: false
        },
        ]
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
            [
                5,
                '<div class="input-group"><input type="text" id="rangeDemoStart" value="'+cur_date+'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'+cur_date+'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>'
            ],
            [
                2,
                "<div class='form-group'><select id='list_jenisdarah' name='jenisdarah_id'></div>"
            ],
            [
                3,
                "<div class='form-group'><select id='list_goldar' name='golongandarah_id'></div>"
            ]
        ],
        {
            2:0,
            3:1,
            5:2,
        }
    );

    dateRangeHelper(".startDate",".endDate",".targetDate");

    $("#list_jenisdarah").select2({
        data: _data_jd,
        allowClear: true
    });

    $("#list_goldar").select2({
        data: _data_goldar,
        allowClear: true
    });

    $(".data-reset").click(function(){
        $('#list_jenisdarah').val("------ Pilih ------").trigger('change');
        $('#list_goldar').val("------ Pilih ------").trigger('change');
        $(".startDate").val(null);
        $(".endDate").val(null);
        $(".targetDate").val(null);
    });
});

