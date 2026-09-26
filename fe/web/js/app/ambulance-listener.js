$.ajaxSetup({
    cache: false
});

$.getJSON("./../../json/setup.json", function (config) {
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(`http://${config.ip}:${config.port}`);
    }

    socket.on(`order-ambulan-${config.name}`, function (data) {
        const payloadSocket = JSON.parse(data)
        if (typeof payloadSocket.flag == 'undefined' || (typeof payloadSocket.flag != 'undefined' && payloadSocket.flag == 'notification')) {
            const player = $("#audio-player");
            player[0].defaultPlaybackRate = 1
            player[0].src = `${window.location.origin}/media/sounds/emergency.mp3`
            player[0].play();
            docoNotification('success', 'Notifikasi baru!', payloadSocket.message)
        }
        $("#ambulance-notification-list").html('')
        payloadSocket.notificationBucket.map((notification) => {
            $("#ambulance-notification-list").append(`
                <li>
                    <p class="notification-title"><i class="fa fa-ambulance"></i>Pemesanan Ambulan untuk No Polisi ${notification.no_polisi} dengan No Pemesanan : ${notification.no_pesanambulan}</p>
                    <p class="notification-date"><i class="fa fa-clock-o"></i>${moment(notification.tgl_pesanambulan).locale('id').fromNow()}</p>
                </li>
            `)
        })
        $("#ambulance-notification-total").html(payloadSocket.totalNotProcess)
    });
});
