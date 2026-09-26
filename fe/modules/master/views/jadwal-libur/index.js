const urlGetData = '/master/jadwal-libur/get-data'
const urlUpdateData = '/master/jadwal-libur/update?id='
const urlDeleteData = '/master/jadwal-libur/delete?id='

$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev, title, next today',
            center: '',
            right: ''
        },
        schedulerLicenseKey: 'nesimrs',
        refetchResourcesOnNavigate : true,
        locale : 'id',
        initialView: 'dayGridMonth',
        aspectRatio: 2,
        defaultDate: moment().format('YYYY-MM-DD'),
        eventLimit: true,
        events: function (start, end, tz, callback) {
            $.ajax({
                url : urlGetData,
                type: 'POST',
                dataType: 'json',
                data: {
                    start: start.format(),
                    end: end.format()
                },
                success : function (data) {
                    var eventsList = [];
                    var i = 0;
                    while (i < data.length) {
                        eventsList.push({
                            title: data[i].ket_libur,
                            start: data[i].tgl_libur,
                            description: data[i].ket_libur,
                            is_liburnasional: data[i].is_liburnasional,
                            id: data[i].jadwallibur_id,
                            // url: '/master/jadwal-libur/update?id='+data[i].jadwallibur_id
                            // end: endDate
                        });
            
                        i++
                    }
                    callback(eventsList);
                },
                error : function (data) {
                    return false;
                }
            });
        },
        views: {
            dayGrid: {
                eventLimit: 5
            }
        },
        eventClick: function (event, jsEvent, view) { 
            console.log(event);
            $('#modalTitle').html(event.title);
            initPickDate('#tgl_libur',event.start._i);
            $('#jadwalliburform-ket_libur').html(event.description);
            $('#jadwallibur_id').val(event.id);
            $('#jadwallibur_id_delete').val(event.id);
            $('#jadwalliburform-is_liburnasional').prop("checked", event.is_liburnasional);
            // $('#jadwallibur-form-update').attr('action',event.url)
            $('#calendarModal').modal();
        },
        // eventRender: function(event, element) {
        //     element.append( "<span class='closeon'>X</span>" );
        //     element.find(".closeon").click(function() {
        //         $('#calendar').fullCalendar('removeEvents',event._id);
        //     });
        // }
    });
    setTimeout(function(){
        $('.fc-license-message').addClass('hidden');
    },500);
    
});

function initPickDate(inputId, startDate) {
    let pickDate = $(inputId).pickadate({
        format: 'dd mmm yyyy',
        formatSubmit: 'yyyy-mm-dd'
    });
    pickDate.pickadate('picker').set('min',false)
    pickDate.pickadate('picker').set('select', startDate, {format: 'yyyy-mm-dd'})
}

$('#calendarModal').on('hidden.bs.modal', function () {
    // location.reload();
    $('#tgl_libur').pickadate('picker').stop();
    $(this).find('form').trigger('reset');
})

$('.btn-update').on('click', function(e){
    e.preventDefault();
    $().docoForm('click',{
        url     : urlUpdateData+$('#jadwallibur_id').val(),
        data    : $('#jadwallibur-form-update').serializeArray(),
        skipConfirm: true,
        success : function(data) {
            // $('#calendarModal').modal('toggle');
            location.reload();
        } 
    });
});

$('.btn-hapus').on('click', function(e){
    $('#calendarModalDelete').modal()
});

$('.btn-save-delete').on('click', function(e){
    e.preventDefault();
    $().docoForm('click',{
        url     : urlDeleteData+$('#jadwallibur_id').val(),
        data    : $('#batal-form').serializeArray(),
        type: 'POST',
        skipConfirm: true,
        success : function(data) {
            location.reload();

        } 
    });
});

$('.data-reset').on('click', function(e){
    location.reload();
});