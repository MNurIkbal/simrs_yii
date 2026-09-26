
$(" .select2 ").select2();
function clock_dok() {
    var d = new Date();
    var hour = checkTimeDok(d.getHours());
    var min = checkTimeDok(d.getMinutes());
    var sec = checkTimeDok(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour +":"+ min +":"+ sec;
    var getmont = parseInt(d.getMonth()) +1;
    var month = checkTimeDok(getmont);
    var date = checkTimeDok(d.getDate());

    var realtime = d.getFullYear()+"-"+month+"-"+date+" "+hour+":"+min+":"+sec;
    $('#asesmenmedisrdform-tgl_asesmen').val( realtime );
    $('.tgl_implementasi').html( realtime );
}
function checkTimeDok(i) {
    if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
    return i;
}

if (!$('#asesmenmedisrdform-asesmenmedisrd_id').val()) {
    setInterval(clock_dok, 1000);
}

$(document).ready(function(){
    var opsi = detailBagianTubuh[$('.bagian-tubuh').val()];
    $('.bagian-tubuh-detail').append(populateOpsi(opsi));

    $('#btn-save-asesmen-dokter').on('click',function(e){
	    e.preventDefault();
        var asesmen = $('#form-asesmen-dokter').serializeArray();
        
	    var arr = []
	    arr[0] = {name: 'anatomi', value: JSON.stringify(tmpData)}
        var alldata = $.merge(asesmen, arr);
        
        $(this).docoForm('click',{
            data: alldata,
            url: "/igd/pemeriksaan-igd/simpan-asesmen-dokter",
            success: function(){
                // location.reload();
                // var btn_cetak = $('#cetak-resume-medis');
                // if(btn_cetak.hasClass('hidden')){
                //     btn_cetak.removeClass('hidden');
                // }
            }
        });
    });

    $('#btn-print-asesmen-dokter').on('click',function(e){
        e.preventDefault();
        var url="/igd/pemeriksaan-igd/cetak-pdf-asesmen-dokter?id="+pendaftaran_id;
        window.open(url, '_blank');
    });
})
var populateOpsi = function(opsi){
    var txtopsi = '';
    $.each(opsi, function(k,v){
        txtopsi += '<option value="'+k+'">'+v+'</option>'
    })
    return txtopsi
}
/*----------  Gcs start  ----------*/
var nilaiGcsEye = 0
var nilaiGcsVerbal = 0
var nilaiGcsMotorik = 0
var nilaiGcs = 0
var hitungGcs = function () {
    // nilaiGcsEye = parseInt($(".gcs_eye").find(':selected').attr('data-nilai'));
    // nilaiGcsVerbal = parseInt($(".gcs_verbal").find(':selected').attr('data-nilai'));
    // nilaiGcsMotorik = parseInt($(".gcs_motorik").find(':selected').attr('data-nilai'));
    // nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik;
    // $('#asesmenmedisrdform-jumlah_gcs').val(isNaN(nilaiGcs) ? '' : nilaiGcs);
    let nilaiGcs = nilaiGcsEye + nilaiGcsVerbal + nilaiGcsMotorik;
    $('.nilai_gcs').val(nilaiGcs);
    let hasil;
    var is_kapitis = $('#asesmenmedisrdform-is_kapitis').is(":checked");
    $.each(dataGcs, function(key, value){
        if(nilaiGcs >= value['gcs_nilaimin'] && nilaiGcs <= value['gcs_nilaimax'] && value['is_kapitis'] == is_kapitis){
            $('.hasil_gcs').val(value.gcs_nama)
            return false
        }
    })

}

$('#asesmenmedisrdform-is_kapitis').on("change", function(e) {
    hitungGcs();
});

$(".gcs_eye").on("change", function(e) { 
    nilaiGcsEye = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});
$(".gcs_verbal").on("change", function(e) { 
    nilaiGcsVerbal = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});
$(".gcs_motorik").on("change", function(e) { 
    nilaiGcsMotorik = isNaN(parseInt($(this).find(':selected').attr('data-nilai'))) ? 0 : parseInt($(this).find(':selected').attr('data-nilai'))
    hitungGcs()
});

/*----------  Gcs end  ----------*/
/*----------  Anatomi tubuh start  ----------*/
var sumbuX = 0;
var sumbuY = 0;



$(window).keydown(function(e){
    if (e.keyCode == 27) {
        $('.add-caption').val('');
        $('.bagian-tubuh').val('');
        $('.bagian-tubuh-detail').find('option').remove();
        $('.tag').attr({
            style : 'display:none;',
        });
        $('.tag').data('show',1);
        return false;
    }
    if (e.keyCode == 13) {
        e.preventDefault();
    }
});

$('.image-frame').on('click', function(event) {
    event.preventDefault();
    var posX = (event.pageX - $(this).offset().left),
        posY = (event.pageY - $(this).offset().top) - 10,
        tag = $('.tag');
    sumbuX = posX;
    sumbuY = posY;
    if (tag.data('show') != 1) {
        tag.attr({
            style : 'display:none;',
        });
        tag.data('show',1);
    } else {
        tag.attr({
            style : 'top: '+ (posY + 20) +'px; left: '+ posX +'px;width:500px;z-index:3;position:absolute;'
        });
        tag.data('show',2);
    }
});

var addCaption = function(e) {
    e.preventDefault();
    var date = new Date()
    var m = (date.getMonth()+1)
    var d = (date.getDate())
    var now = date.getFullYear() + '-' + ((''+m).length<2 ? '0' : '') + m + '-' + ((''+d).length<2 ? '0' : '') + d + ' ' + date.getHours() + ':' + date.getMinutes() + ':' +date.getSeconds()
    var tabel = $('.tabel-anggotatubuh');
    var _contentParent = $(this).closest('.well-sm');
    var valBagian = $('.bagian-tubuh').val();
    var valBagianDetail = $('.bagian-tubuh-detail').val();
    if (e.keyCode == 13 || e.keyCode == 27) {
        if (e.keyCode == 13 && (/[\w\d]+/.test($(this).val())) && valBagian != '') {
            var bagian = typeof bagianTubuh[valBagian] != 'undefined' ? bagianTubuh[valBagian] : '-';
            var bagianDetail = typeof detailBagianTubuh[valBagian][valBagianDetail] != 'undefined' ? detailBagianTubuh[valBagian][valBagianDetail] : '-';
            tmpData[counter] = {
                counters : counter + 1,
                bagian : bagian,
                bagianDetail : bagianDetail,
                bagiantubuh_id : valBagian,
                bagiantubuhdetail_id : valBagianDetail,
                koordinat_y : sumbuY,
                koordinat_x : sumbuX,
                created_date: now,
                catatan_tubuh : $(this).val()
            }
            addRow();
            counter++;
        }
        $(this).val('');
        $('.bagian-tubuh').val('');
        $('.bagian-tubuh-detail').val('');
        $('.tag').attr({
            style : 'display:none;',
        });
        $('.tag').data('show',1);
        return false;
    }
}

var addRow = function () {
    var _tabel = $('.tabel-anggotatubuh');
    if (_tabel.find('tbody > tr').length == 1) {
        $('.default-row').hide();
    }
    var clone  = $('.default-row').clone();
    _tabel.find('tbody').html('<tr class=\"default-row\" style=\"display:none\">'+ clone.html() + '</tr>');
    var i = 1;
    if (Object.keys(tmpData).length == 0) {
        _tabel.find('tbody').html('<tr class=\"text-center\"><td colspan="6">Data Kosong</td></tr>');
    }
    $.each(tmpData, function (key,items) {
        // $('.image-frame').append('<div style=\"top: '+ items.koordinat_y+'px; left: '+ items.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ items.counters +'\"><span class=\"badge bg-warning-400\">'+ items.counters +'</span></div>');
        $('.image-frame').append('<div style=\"top: '+ items.koordinat_y+'px; left: '+ items.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image counter-'+ items.counters +'\"><span class=\"badge bg-warning-400\">'+ i +'</span></div>');
        var html = '';
        html += '<tr>';
        // html += '<td>'+ items.counters +'</td>';
        html += '<td>'+ i +'</td>';
        html += '<td>'+ items.created_date +'</td>';
        html += '<td>'+ items.bagian +'</td>';
        html += '<td>'+ ( (items.bagianDetail != null) ? items.bagianDetail : '-' ) +'</td>';
        html += '<td>'+ items.catatan_tubuh +'</td>';
        html += '<td><button style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item\" data-counter=\"'+ items.counters +'\">'+
                    '<i class=\"fa fa-trash\"></i></button></td>';
        html += '</tr>';
        _tabel.find('tbody').append(html);

        i++;
    })
}

var delRow = function (event) {
    event.preventDefault();
    var _this = $(this);
    // console.log(_this); return;
    // var _counter = _this.data('counter') + 1;
    var _counter = _this.data('counter');
    var _trParent = _this.closest('tr');
    var _tabel = $('.tabel-anggotatubuh');
    _trParent.remove();
    var index = -1;

    $.each(tmpData, function (key, item) {
        if (item.counters == _counter) {
            index = key;
        }
    });

    delete tmpData[index];
    // delete tmpData[_counter];
    

    // $.each(tmpData, function (key, items) {
    //     if (_counter < key) {
    //         // delete tmpData[key];
    //         tmpData[(key - 1)] = items;
    //         tmpData[(key - 1)]['counters'] = items.counters - 1;
    //     }
    // });

    // $('.counter-' + _counter).remove();
    $('.tag-image').remove();
    addRow();
    if (_tabel.find('tbody > tr').length == 1) {
        $('.default-row').show();
    }
    // counter--;
}

$(document).on('click','.hapus-item', delRow);

$('.add-caption').on('keyup',addCaption);
var saveAnatomi = function (data) {
    let res = data;
    let pemeriksaanfisik_id = res.response['pemeriksaanfisik_id'] ? res.response['pemeriksaanfisik_id'] : null;
    let url = "/ranap/pemeriksaan-rawat-inap/save-anatomi";

    if (Object.keys(tmpData).length) {
        $.ajax({
            type : 'POST',
            dataType : 'json',
            url : url,
            data : {
                data:tmpData,
                pendaftaran_id : $('.pendaftaran_id').val(),
                pasien_id : $('.pasien_id').val(),
                pemeriksaanfisik_id : pemeriksaanfisik_id,
            },
            error : function (data) {
                console.log(data);
            }
        });
    } else {
        alert('Anatomi Harus Di isi');
    }
};

$('.bagian-tubuh').on('change', function(){
    var opsi = detailBagianTubuh[$('.bagian-tubuh').val()]
    $('.bagian-tubuh-detail').find('option').remove().end().append(populateOpsi(opsi))
})