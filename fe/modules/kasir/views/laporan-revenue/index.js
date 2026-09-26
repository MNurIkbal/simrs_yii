var table;
var _dataKategori = [];

$.each(_listKategori, function( index, value ) {
    _dataKategori.push({
        id: index,
        text: value,
    });
});

$(document).ready(function(){
    var columns = [
        {
            title: "DESCRIPTIONS",
            data: "unit",
            searchable: false,
        },
    ];
    for(var i = 1; i <= 31; i++) {
        var k = (i < 10) ? "0" + i : "" + i;
        columns.push({
            title: "" + i,
            data: k,
            searchable: false,
            className: "text-right",
            render: $.fn.dataTable.render.number( ".", ",", 0, "" )
        });
    }
    table = $("#example").docoTabel({
        filter: true,
        ordering: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        paging: false,
        info: false,
        fixedColumns: {
            leftColumns: 1,
        },
        ajax: baseUrl+"kasir/laporan-revenue/get-data",
        columns: columns,
        createdRow : function(row, data) {
            var is_group = data.is_group;
            var is_kategori = data.is_kategori;
            var is_total = data.is_total;

            if(is_group) {
                $(row).addClass("bg-inverse");
            }

            if(is_kategori) {
                $(row).addClass("bg-yellow");
            }

            if(is_total) {
                $(row).addClass("bg-total");
            }

            if(data.code == "discount") {
                $(row).addClass("bg-discount");
            }
            else if(data.code == "total_revenue") {
                $(row).addClass("bg-total-revenue");
            }
            else if(data.code == "grandtotal") {
                $(row).addClass("bg-grand-total");
            }
        },
    });
    
    $(".dataTables_filter").hide();
    $(".startDate").height("1px");
    $(".endDate").height("1px");
    
    dateRangeHelper(".startDate",".endDate",".targetDate");
    var _jenis_periode = "date_range";
    if(typeof $("#filter_jenis_periode").val() == "undefined") {
        _jenis_periode = "date_range";
    }
    
    if(_jenis_periode == "date_range") {
        $(".filter_bulan_tahun").css("display", "none");
    }
    else {
        $(".filter_bulan_tahun").css("display", "block");
    }
    
    $("#filter_jenis_periode").on("change", function(e){
        var _val = $(this).val();
        if(_val == "bulan_tahun") {
            $(".div-date-range").css("display", "none");
            $(".filter_bulan_tahun").css("display", "block");
        }
        else {
            $(".div-date-range").css("display", "block");
            $(".filter_bulan_tahun").css("display", "none");
        }
    });

    $(".cari-revenue").on("click", function(event){
        event.preventDefault();
        _jenis_periode = $("#filter_jenis_periode").val();
        var _startDate = $(".startDate").val();
        var _endDate = $(".endDate").val();
        var _periode_tanggal = _startDate + " - " + _endDate;
        var _periode_bulan = $("#filter_bulan_tahun").val();
        var _kategori = $("#filter_kategori").val();
        var _unit = $(".selectUnit").val();

        _url = baseUrl+"kasir/laporan-revenue/get-data?range_tanggal="+ _periode_tanggal + "&range_bulan=" + _periode_bulan + "&jenis_periode=" + _jenis_periode + "&kategori=" + _kategori + "&unit=" + _unit
        
        table.ajax.url(_url).draw(false);
    });

    $(document).on("click", ".reset-revenue", function (event) {
        event.preventDefault()
        var parent = $(this).data("parent");
        $(".form-group").removeClass("has-error");
        $("span.help-block.error").remove();
        $("div.help-block.error").remove();

        var _startDate = $(".startDate").val();
        var _endDate = $(".endDate").val();
        var _periode_tanggal = _startDate + " - " + _endDate;
        
        if (typeof parent !== "undefined") {
            $(parent + " [type=reset]").click();
            $("#filter_jenis_periode").val("date_range").trigger("change");
            $("#filter_kategori").val(null).trigger("change");
            $("#filter_bulan_tahun").val(null).trigger("change");
            $(".selectUnit").empty().append(new Option()).trigger("change");
            var _kategori = $("#filter_kategori option:selected").val();
            var _unit = $(".selectUnit").val();
            var _periode_bulan = $("#filter_bulan_tahun").val();
            _jenis_periode = $("#filter_jenis_periode").val();

            _url = baseUrl+"kasir/laporan-revenue/get-data?range_tanggal="+ _periode_tanggal + "&range_bulan=" + _periode_bulan + "&jenis_periode=" + _jenis_periode + "&kategori=" + _kategori + "&unit=" + _unit

            $("#example").DataTable().ajax.url(_url).draw(false);
            $(parent + " .advancedFilterDo").click();
        } else {
            localStorage.clear();
            $(".advancedFilter [type=reset]").click();
            $(".advancedFilterDo").click();
        }
    });
    $(".startDate").on("change", function(){
        validasiBulan('startDate', 'startDate', 'endDate');
    });
    
    $(".endDate").on("change", function(){
        validasiBulan('endDate', 'startDate', 'endDate');
    });

    $("#filter_kategori").select2({
        placeholder: "-- Pilih Kategori --",
        allowClear: false,
        data: _dataKategori, 
    });

    function validasiBulan(activeClass, startClass, endClass)
    {
        var _end = $("." + endClass).val();
        var _start = $("." + startClass).val();
        var _expStart = _start.split("-");
        var _expEnd = _end.split("-");
        var _monthStart = _expStart[1];
        var _monthEnd = _expEnd[1];
        if(_monthStart != _monthEnd) {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.toLocaleString("default", { month: "short" });
            var yyyy = today.getFullYear();

            if(dd < 10) 
            {
                dd = "0" + dd;
            } 

            if(mm < 10) {
                mm = "0" + mm;
            }

            dd = (activeClass == "startDate") ? "01" : dd;
            var _today = dd + "-" + mm + "-" + yyyy;

            $("." + activeClass).val(_today);
            docoNotification("warning", "Perhatian!", "Range Tanggal tidak boleh beda Bulan!");
            $(".reset-revenue").trigger("click");
        }
    }

    $(document).on("click", ".excel-revenue", function(event){
        event.preventDefault();
        _jenis_periode = $("#filter_jenis_periode").val();
        var _startDate = $(".startDate").val();
        var _endDate = $(".endDate").val();
        var _periode_tanggal = _startDate + " - " + _endDate;
        var _periode_bulan = $("#filter_bulan_tahun").val();
        var _kategori = $("#filter_kategori").val();
        var _unit = $(".selectUnit").val();

        _url = baseUrl+"kasir/laporan-revenue/export-excel?range_tanggal="+ _periode_tanggal + "&range_bulan=" + _periode_bulan + "&jenis_periode=" + _jenis_periode + "&kategori=" + _kategori + "&unit=" + _unit
        
        // if($(this).attr('id') == 'detail-revenue'){
        //     _url = baseUrl+"kasir/laporan-revenue/export-excel-detail?range_tanggal="+ _periode_tanggal + "&range_bulan=" + _periode_bulan + "&jenis_periode=" + _jenis_periode + "&kategori=" + _kategori + "&unit=" + _unit
        // }
        window.open(_url);
    });

    $("#excel-bgprocess").unbind("click");
    $("#excel-bgprocess").on("click", function (event) {
        _jenis_periode = $("#filter_jenis_periode").val();
        var _startDate = $(".startDate").val();
        var _endDate = $(".endDate").val();
        var _periode_tanggal = _startDate + " - " + _endDate;
        var _periode_bulan = $("#filter_bulan_tahun").val();
        var _kategori = $("#filter_kategori").val();
        var _unit = $(".selectUnit").val();

        _url = encodeURI(baseUrl+"kasir/laporan-revenue/show-popup?range_tanggal="+ _periode_tanggal 
        + "&startDate=" + _startDate 
        + "&endDate=" + _endDate 
        + "&periode_tanggal=" + _jenis_periode 
        + "&periode_bulan=" + _periode_bulan 
        + "&kategori=" + _kategori 
        + "&unit=" + _unit 
        +"&")
        $(this).attr("data-url",_url);
    })
});