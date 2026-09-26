$("#form-kala-satu").on("submit", function(event) {
    $(this).docoForm("submit", {
        success : function(response) {
            $("#btn-cetak-keadaan-umum").removeAttr("disabled");
            $("#partograf-wizard-head-1").click();
        }
    });
});

$("#btn-simpan-kaladua").on("click", function(event) {
    event.preventDefault();
    var submit = true;
    var gawatJanin = $('input[type=radio][name="KalaDuaForm\\[k2_gawatjanin\\]"]:checked', "#form-kala-dua").val();
    var distosiaBahu = $('input[type=radio][name="KalaDuaForm\\[k2_distosiabahu\\]"]:checked', "#form-kala-dua").val();

    if (gawatJanin == 1) {
        $('#detail-tindakanjanin input').each(function () {
            if (this.value == '') {
                docoNotification("error", "Terjadi Kesalahan.", "Ada tindakan janin yang belum diisi.");
                submit = false;
                return false;
            }
        });
    }

    if (distosiaBahu == 1) {
        $('#detail-tindakandistosia input').each(function () {
            if (this.value == '') {
                docoNotification("error", "Terjadi Kesalahan.", "Ada tindakan distosia bahu yang belum diisi.");
                submit = false;
                return false;
            }
        });
    }

    if (submit == false) {
        event.stopPropagation();
        return false;
    }

    $(this).docoForm("click", {
        url: '/ranap/pemeriksaan-rawat-inap/kala-dua?id='+pendaftaran_id,
        data: $("#form-kala-dua").serializeArray(),
        success : function(response) {
            $("#btn-cetak-keadaan-umum").removeAttr("disabled");
            $("#partograf-wizard-head-3").click();
        }
    });
});

$('input[type=radio][name="KalaDuaForm\\[k2_episitomi\\]"]').change(function(event) {
    var k2_episitomi = $(this).val();

    if (k2_episitomi == 0) {
        $("#kaladuaform-k2_indikasi").attr("readonly", "readonly");
        $("#kaladuaform-k2_indikasi").val("");
    } else {
        $("#kaladuaform-k2_indikasi").removeAttr("readonly");
    }
});

$('input[type=radio][name="KalaDuaForm\\[k2_gawatjanin\\]"]').change(function(event) {
    var k2_gawatjanin = $(this).val();

    if (k2_gawatjanin == 0) {
        $("div#detail-tindakanjanin").empty();

        var html = "";
        html = html +
        "<div class='row'>"+
            "<div class='col-sm-10'>"+
                "<input type='text' class='form-control' name='k2_tindakanjanin[]'>"+
            "</div>"+
            "<div class='col-sm-1'>"+
                "<button type='button' class='btn btn-success' onclick='addTindakanJanin(this)'><b><i class='fa fa-plus'></i></b></button>"+
            "</div>"+
            "<div class='col-sm-1 remove-tindakanjanin hidden'>"+
                "<button type='button' class='btn btn-danger' onclick='removeTindakanJanin(this)'><b><i class='fa fa-trash'></i></b></button>"+
            "</div>"+
        "</div>";

        $("div#detail-tindakanjanin").append(html);
        $(".field-kaladuaform-k2_tindakanjanin").addClass("hidden");
    } else {
        $(".field-kaladuaform-k2_tindakanjanin").removeClass("hidden");
    }
});

$('input[type=radio][name="KalaDuaForm\\[k2_distosiabahu\\]"]').change(function(event) {
    var k2_distosiabahu = $(this).val();

    if (k2_distosiabahu == 0) {
        $("div#detail-tindakandistosia").empty();

        var html = "";
        html = html +
        "<div class='row'>"+
            "<div class='col-sm-10'>"+
                "<input type='text' class='form-control' name='k2_tindakandistosia[]'>"+
            "</div>"+
            "<div class='col-sm-1'>"+
                "<button type='button' class='btn btn-success' onclick='addTindakanDistosia(this)'><b><i class='fa fa-plus'></i></b></button>"+
            "</div>"+
            "<div class='col-sm-1 remove-tindakandistosia hidden'>"+
                "<button type='button' class='btn btn-danger' onclick='removeTindakanDistosia(this)'><b><i class='fa fa-trash'></i></b></button>"+
            "</div>"+
        "</div>";

        $("div#detail-tindakandistosia").append(html);
        $(".field-kaladuaform-k2_tindakandistosia").addClass("hidden");
    } else {
        $(".field-kaladuaform-k2_tindakandistosia").removeClass("hidden");
    }
});

function addTindakanJanin() {
    var add = true;
    $('#detail-tindakanjanin input').each(function () {
        if (this.value == '') {
            add = false;
        }
    });

    if (add) {
        var html = "";

        var row = $("#detail-tindakanjanin").closest('.row');
        row.find("div.remove-tindakanjanin").removeClass("hidden");

        html = html +
        "<div class='row' style='margin-top:10px;'>"+
            "<div class='col-sm-10'>"+
                "<input type='text' class='form-control' name='k2_tindakanjanin[]'>"+
            "</div>"+
            "<div class='col-sm-1'>"+
                "<button type='button' class='btn btn-success' onclick='addTindakanJanin(this)'><b><i class='fa fa-plus'></i></b></button>"+
            "</div>"+
            "<div class='col-sm-1 remove-tindakanjanin'>"+
                "<button type='button' class='btn btn-danger' onclick='removeTindakanJanin(this)'><b><i class='fa fa-trash'></i></b></button>"+
            "</div>"+
        "</div>";

        $("div#detail-tindakanjanin").append(html);
    } else {
        docoNotification("error", "Terjadi Kesalahan.", "Ada tindakan janin yang belum diisi.");
    }
}

function removeTindakanJanin(element) {
    $(element).parent().parent().remove();

    var count = document.querySelectorAll('#detail-tindakanjanin > .row');

    if (count.length == 1) {
        var row = $("#detail-tindakanjanin").closest('.row');
        row.find("div.remove-tindakanjanin").addClass("hidden");
    }
}

function addTindakanDistosia() {
    var add = true;
    $('#detail-tindakandistosia input').each(function () {
        if (this.value == '') {
            add = false;
        }
    });

    if (add) {
        var html = "";

        var row = $("#detail-tindakandistosia").closest('.row');
        row.find("div.remove-tindakandistosia").removeClass("hidden");

        html = html +
        "<div class='row' style='margin-top:10px;'>"+
            "<div class='col-sm-10'>"+
                "<input type='text' class='form-control' name='k2_tindakandistosia[]'>"+
            "</div>"+
            "<div class='col-sm-1'>"+
                "<button type='button' class='btn btn-success' onclick='addTindakanDistosia(this)'><b><i class='fa fa-plus'></i></b></button>"+
            "</div>"+
            "<div class='col-sm-1 remove-tindakandistosia'>"+
                "<button type='button' class='btn btn-danger' onclick='removeTindakanDistosia(this)'><b><i class='fa fa-trash'></i></b></button>"+
            "</div>"+
        "</div>";

        $("div#detail-tindakandistosia").append(html);
    } else {
        docoNotification("error", "Terjadi Kesalahan.", "Ada tindakan distosia bahu yang belum diisi.");
    }
}

function removeTindakanDistosia(element) {
    $(element).parent().parent().remove();

    var count = document.querySelectorAll('#detail-tindakandistosia > .row');

    if (count.length == 1) {
        var row = $("#detail-tindakandistosia").closest('.row');
        row.find("div.remove-tindakandistosia").addClass("hidden");
    }
}
