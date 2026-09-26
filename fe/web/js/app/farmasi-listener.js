$.ajaxSetup({
    cache: false,
});

$.getJSON("./../../json/setup.json", function(config) {
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(`http://${config.ip}:${config.port}`);
    }

    socket.on(`order-farmasi-${config.name}`, function(data) {
        const payloadSocket = JSON.parse(data);
        if (
            typeof payloadSocket.flag == "undefined" ||
            (typeof payloadSocket.flag != "undefined" &&
                payloadSocket.flag == "notification")
        ) {
            const player = $("#audio-player");
            player[0].defaultPlaybackRate = 1;
            player[0].src = `${window.location.origin}/media/sounds/NotificationFarmasi.mp3`;
            player[0].play();
            var notificationPayload = payloadSocket.newNotification;
            new PNotify({
                title: 'Notifikasi Baru!',
                text: `Terdapat reseptur baru dari dokter <strong>${notificationPayload.nama_pegawai}</strong> dengan
                        No Reseptur: <strong>${notificationPayload.noresep}</strong>
                        di ruangan <strong>${notificationPayload.ruangan_tujuan}</strong>`,
                addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                type: 'success'
            })

            if(window.location.pathname == '/apotek/informasi-reseptur') {
                // table.draw();
            }
        }

        $("#farmasi-notification-list").html("");
        let notification = {}
        payloadSocket.notificationBucket.map((eachNotification) => {
            try {
                notification = JSON.parse(eachNotification.additional_data);
            } catch (error) {
                notification = {};
            }

            $("#farmasi-notification-list").append(`
                <li class="${typeof eachNotification.is_read != 'undefined' && !eachNotification.is_read ? 'farmasi-notif-unread' : ''} farmasi-notif-item" data-id="${eachNotification.notifikasi_id}" data-rid="${notification.enc_reseptur_id}" data-noresep="${notification.noresep}">
                    <p class="notification-title"><i class="fa fa-medkit"></i>
                        ${eachNotification.judulnotifikasi}
                    </p>
                    <p class="notification-message">
                        ${eachNotification.isi_notifikasi}
                    </p>
                    <p class="notification-date"><i class="fa fa-clock-o"></i>
                        ${moment(eachNotification.created_date).locale('id').fromNow()}
                    </p>
                </li>
            `)
        })
        $("#farmasi-notification-total").html(payloadSocket.totalUnread);
    });
});

$(document).on('click', '.farmasi-notif-item', ({ currentTarget }) => {
    var reseptur_id = $(currentTarget).data('rid');
    var noresep = $(currentTarget).data('noresep');
    if ($(currentTarget).hasClass('farmasi-notif-unread')) {
        $.ajax({
            url: `/apotek/dashboard/read-notif?notifikasi_id=${$(currentTarget).data('id')}`,
            method: 'POST',
            success: () => {
                $(currentTarget).removeClass('farmasi-notif-unread');
            }
        })
    }
    window.location.replace(`/apotek/informasi-reseptur/view-notif?reseptur_id=${reseptur_id}`);
});

$(document).on('click', '.clear-notif-farmasi', ({ }) => {
    $.ajax({
        url: `/apotek/dashboard/clear-notifications?judulnotifikasi=Farmasi`,
        method: 'GET',
        success: () => {
            console.log("hello")
        }
    })
});

$('.farmasi_scroll').on('scroll', function() {
    var height = ($(this).scrollTop() + $(this).innerHeight());
    var scroll_height = ($(this)[0].scrollHeight - 300);
    if(height >= scroll_height) {
        $('.clear-notif-farmasi').html('Memuat data...');
        var last_id = $(".farmasi-notif-item:last").attr("data-id");
        loadMoreData(last_id);
    }
});


function loadMoreData(last_id) {
    $.ajax({
        url: '/apotek/dashboard/load-more-notif?last_id='+last_id+'&limit=10',
        method: 'GET',
        success: (data) => {
            $('.clear-notif-farmasi').html('Bersihkan Notifikasi');
            var raw_data = JSON.parse(data);
            var html = ``;
            for (var i = 0; i < raw_data.length; i++) {
                var is_read = (raw_data[i].is_read == true) ? `` : `farmasi-notif-unread `;
                var additional_data = JSON.parse(raw_data[i].additional_data);
                html += `
                    <li class="`+is_read+`farmasi-notif-item" 
                        data-id="`+raw_data[i].notifikasi_id+`" 
                        data-rid="`+additional_data.enc_reseptur_id+`" 
                        data-noresep="`+additional_data.noresep+`">

                        <p class="notification-title"><i class="fa fa-medkit"></i>
                            `+raw_data[i].judulnotifikasi+`
                        </p>
                        <p class="notification-message">
                            `+raw_data[i].isi_notifikasi+`
                        </p>
                        <p class="notification-date" data-date="`+raw_data[i].tglnotifikasi+`">
                            <i class="fa fa-clock-o"></i>Memuat ...
                        </p>
                    </li>
                `;
            }

            $('#farmasi-notification-list').append(html);
        }, 
        error: () => {
            console.log("Failed to get notifications");
        }
    });
}