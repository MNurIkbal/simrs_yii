$.ajaxSetup({
    cache: false
});

$.getJSON("./../../json/setup.json", function (config) {
    if (config.origin == "true") {
        var socket = io.connect(window.location.origin);
    } else {
        var socket = io.connect(`http://${config.ip}:${config.port}`);
    }

    socket.on(`new-notification-${config.name}`, function (data) {
        const payloadSocket = JSON.parse(data)
        if (payloadSocket.users.indexOf(bucketNotification.user_id) >= 0) {
            updateNotificationBucket(JSON.parse(data))
            $("#refresh-notification-btn").trigger('click')
        }
    });
    socket.on(`permintaan-makan-notification-${config.name}`, function (data) {
        const payloadSocket = JSON.parse(data)
        if (payloadSocket.users.indexOf(bucketNotification.user_id) >= 0 && typeof payloadSocket.newNotification.modul_id != 'undefined' && moduleID == payloadSocket.newNotification.modul_id) {
            updateNotificationBucket(JSON.parse(data))
        }
    });
    socket.on(`pembantaran-notification-${config.name}`, function (data) {
        const payloadSocket = JSON.parse(data)
        if (payloadSocket.users.indexOf(bucketNotification.user_id) >= 0 && typeof payloadSocket.newNotification.modul_id != 'undefined' && moduleID == payloadSocket.newNotification.modul_id) {
            updateNotificationBucket(payloadSocket)
        }
    });
    socket.on(`update-notification-${config.name}`, function (data) {
        const payloadSocket = JSON.parse(data)
        if ((typeof bucketNotification.user_id != 'undefined' && payloadSocket.userId == bucketNotification.user_id) || (typeof payloadSocket.users != 'undefined' && payloadSocket.users.indexOf(bucketNotification.user_id) >= 0)) {
            updateNotificationBucket(payloadSocket, false)
        }
    });
});

const updateNotificationBucket = (payloadSocket, withNotif = true, callbackNewNotification = null) => {
    if (typeof payloadSocket.users != 'undefined') {
        if (withNotif) {
            if (typeof payloadSocket.newNotification.filter_ruangan === 'undefined' || payloadSocket.newNotification.filter_ruangan === ruangan_index) {
                let doconotif = docoNotification('success', 'Notifikasi baru!', payloadSocket.newNotification.message)
                try {
                    if(doconotif != undefined) {
                        const player = $("#audio-player");
                        player[0].defaultPlaybackRate = 1
                        player[0].src = `${window.location.origin}/media/sounds/NotificationRM.mp3`
                        player[0].play();
                    }
                } catch (error) {
                    console.error(`PLAYER ERROR : ${error}`)
                }
                if (callbackNewNotification != null && typeof callbackNewNotification == 'function') {
                    callbackNewNotification(payloadSocket.newNotification)
                }
            }
            // bucketNotification.records.unshift(payloadSocket.newNotification)
        }
        // bucketNotification.totalUnread = bucketNotification.totalUnread + 1
        $("#refresh-notification-btn").show()
        bucketNotification.sync = true
    } else {
        $("#refresh-notification-btn").hide()
        bucketNotification.sync = false
        $("#notification-list").html('')
        payloadSocket.notificationBucket.map((notification) => {
            $("#notification-list").append(`
                <li class="${typeof notification.is_read != 'undefined' && !notification.is_read ? 'notification--unread' : ''} general-notification" data-id="${notification.notifikasi_id}">
                    <p class="notification-title"><i class="fa fa-bell"></i>&nbsp;${notification.modul_nama}</p>
                    <p class="notification-message">${notification.isi_notifikasi}</p>
                    <p class="notification-date"><i class="fa fa-clock-o"></i>${moment(notification.tglnotifikasi).locale('id').fromNow()}</p>
                </li>
            `)
        })
        bucketNotification = {
            records: payloadSocket.notificationBucket,
            totalUnread: payloadSocket.totalUnread,
            user_id: bucketNotification.user_id
        }
    }
    $("#notification-total").html(bucketNotification.totalUnread)
    localStorage.setItem('notifications', JSON.stringify(bucketNotification))
}

let bucketNotification = {sync: false}
$(() => {
    if (typeof notifications != 'undefined' && localStorage.getItem('notifications') == null) {
        localStorage.setItem('notifications', JSON.stringify(notifications))
    }
    try {
        bucketNotification = localStorage.getItem('notifications') != null ? JSON.parse(localStorage.getItem('notifications')) : {}
    } catch (error) {
        bucketNotification = {}
    }
    if (bucketNotification.sync) {
        $("#refresh-notification-btn").show()
    } else {
        $("#refresh-notification-btn").hide()
    }
    $("#notification-list").html('')
    if (typeof bucketNotification.records != 'undefined' && bucketNotification.records.length > 0) {
        bucketNotification.records.map((notification) => {
            $("#notification-list").append(`
                <li class="${typeof notification.is_read != 'undefined' && !notification.is_read ? 'notification--unread' : ''} general-notification" data-id="${notification.notifikasi_id}" data-type="${notification.type}">
                    <p class="notification-title"><i class="fa fa-bell"></i>&nbsp;${notification.modul_nama}</p>
                    <p class="notification-message">${notification.isi_notifikasi}</p>
                    <p class="notification-date"><i class="fa fa-clock-o"></i>${moment(notification.tglnotifikasi).locale('id').fromNow()}</p>
                </li>
            `)
        })
        $("#notification-total").html(bucketNotification.totalUnread)
    } else {
        $("#notification-list").append(`
            <li>
                <p class="notification-title text-center">Belum ada notifikasi</p>
            </li>
        `)
        $("#notification-list").parent().css('overflow-y', 'hidden')
        $("#notification-total").html(0)
        $("#all-notification-btn").hide()
    }
})
let dataNotification = {}

$(document).on('click', '.general-notification', (e) => {
    const { currentTarget } = e
    e.preventDefault()
    if ($(currentTarget).hasClass('notification--unread')) {
        $.ajax({
            url: '/site/read-notification',
            data: {
                notifikasi_id: $(currentTarget).data('id')
            },
            method: 'POST',
            success: () => {
                $(currentTarget).removeClass('notification--unread')
                if($(currentTarget).data('type') == 'dok-sign-notif') {
                    window.location.href = urlEsign;
                }
            }
        })
    } else {
        if($(currentTarget).data('type') == 'dok-sign-notif') {
            window.location.href = urlEsign;
        }
    }
})

$("#refresh-notification-btn").bind('click', () => {
    $.ajax({
        url: '/site/refresh-notification',
        method: 'POST',
        success: () => {
        }
    })
})
