$.ajaxSetup({
    cache: false
});

$.getJSON("./../../json/setup.json", function (config) {
    if (config.origin == "true" || config.origin) {
        console.log(config);
        var socket = io.connect(window.location.origin);
    } else {
        console.log(config);
        var socket = io.connect(`http://${config.ip}:${config.port}`);
    }

    socket.on(`update-radiologi-${config.name}`, function (data) {
        const payloadSocket = JSON.parse(data)
        if (typeof payloadSocket.flag == 'undefined' || (typeof payloadSocket.flag != 'undefined' && payloadSocket.flag == 'notification')) {
            const player = $("#audio-player");
            player[0].defaultPlaybackRate = 1
            player[0].src = `${window.location.origin}/media/sounds/NotificationFarmasi.mp3`
            player[0].play();
            payloadSocket.newNotification.map((notificationPayload) => {
                if(notificationPayload.is_integrasi) {
                    new PNotify({
                        title: 'Notifikasi Baru!',
                        text: `${notificationPayload.judulnotifikasi} ${notificationPayload.his_reg_no} - ${notificationPayload.isi_notifikasi}`,
                        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                        type: 'success'
                    })
                } else {
                    new PNotify({
                        title: 'Notifikasi Baru!',
                        text: `${notificationPayload.judulnotifikasi} - ${notificationPayload.isi_notifikasi}`,
                        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                        type: 'success'
                    })
                }
            })
        }
        $("#radiologi-notification-list").html('')
        let notification = {}
        payloadSocket.notificationBucket.map((eachNotification) => {
            try {
                notification = JSON.parse(eachNotification.additional_data)
            } catch (error) {
                notification = {}
            }
            if(notification.is_integrasi) {
                $("#radiologi-notification-list").append(`
                    <li class="${typeof eachNotification.is_read != 'undefined' && !eachNotification.is_read ? 'notification--unread' : ''} radiologi-expertise-notification" data-id="${eachNotification.notifikasi_id}" data-no="${notification.his_reg_no}">
                        <p class="notification-title"><i class="fa fa-flask"></i> ${notification.judulnotifikasi} ${notification.his_reg_no} : ${notification.old_test_name} (${notification.old_result}) Menjadi ${notification.test_name} (${notification.result})</p>
                        <p class="notification-date"><i class="fa fa-clock-o"></i>${moment(notification.created_date).locale('id').fromNow()}</p>
                    </li>
                `)
            } else {
                $("#radiologi-notification-list").append(`
                    <li class="${typeof eachNotification.is_read != 'undefined' && !eachNotification.is_read ? 'notification--unread' : ''} radiologi-expertise-notification" data-id="${eachNotification.notifikasi_id}" data-no="${notification.his_reg_no}">
                        <p class="notification-title"><i class="fa fa-flask"></i> ${notification.judulnotifikasi}</p>
                        <p class="notification-message">${notification.isi_notifikasi}</p>
                        <p class="notification-date"><i class="fa fa-clock-o"></i>${moment(notification.created_date).locale('id').fromNow()}</p>
                    </li>
                `)
            }

        })
        $("#radiologi-notification-total").html(payloadSocket.totalUnread)
    });
});

$(document).on('click', '.radiologi-expertise-notification', ({ currentTarget }) => {
    if ($(currentTarget).hasClass('notification--unread')) {
        $.ajax({
            url: `/radiologi/dashboard/read-notif?notifikasi_id=${$(currentTarget).data('id')}`,
            method: 'POST',
            success: () => {
                $(currentTarget).removeClass('notification--unread')
            }
        })
    }
    showApprove($(currentTarget).data('no'))
})

let tableHistoryRad;
const showHistory = (value, key = 'no') => {
    $("#modal-history-rad").modal({
        keyboard: false,
        backdrop: 'static'
    })
    if ($("#tb-history-rad").hasClass('dataTable')) {
        tableHistoryRad.destroy()
    }

    tableHistoryRad = $("#tb-history-rad").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[1, "asc"]],
        bSort: false,
        displayLength: 10,
        lengthChange: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        bPaginate: false,
        ajax: `/radiologi/informasi-pasien-rad/get-data?${key}=${value}`,
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Perubahan",
                data: "tgl_pemeriksaan",
                searchable: false
            },
            {
                title: "Hasil Sebelumnya",
                data: "prev_hasil",
                searchable: false
            },
            {
                title: "Hasil Update",
                data: "cur_hasil",
                searchable: false
            },
            {
                title: "Dirubah Oleh",
                data: "petugas_pemeriksaan",
                searchable: false
            },
        ],
        scrollCollapse: true
    });

    // Hide datatables filter form
    $(".dataTables_filter").hide();
}

const showApprove = (rujukan) => {
    $.ajax({
        type: "POST",
        url: "/radiologi/dashboard/pasien-notifikasi",
        data: {
            "no_rujukan": rujukan
        },
        success: function (response) {
          const {data} = response
          const pasienkirim_dec = data?.pasienkirimunitlain_dec 
          const status_penunjang = data?.status_penunjang
          // Sudah di Setujui
          if(status_penunjang == 471) {
            new PNotify({
                title: "Peringatan !",
                text: `Orderan dengan No ${rujukan} sudah di proses oleh ${data?.nama_pegawai} !`,
                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                type: "warning",
            });

            return false
          }

          if(status_penunjang == 472) {
            new PNotify({
                title: "Peringatan !",
                text: `Orderan dengan No ${rujukan} sudah dibatalkan oleh ${data?.nama_pegawai} !`,
                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                type: "warning",
            });

            return false
          }

          if(status_penunjang == 470) {
              showFormApproval(pasienkirim_dec)
          }
        }
    });
}

const showFormApproval = (id) => {
    window.location.href = `/radiologi/inf-pasien-rujukan-rad/form-aproval?id=${id}`;
}