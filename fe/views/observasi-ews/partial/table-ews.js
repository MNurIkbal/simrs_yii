$(document).ready(function() {
    var tableEws = $("#observasiEws").DataTable({
        ordering: false,
        scrollX: true,
        searching: false,
        fixedHeader: true,
        paging: false,
        lengthChange: false,
        info: false,
        initComplete: function() {
            var container = $("#observasiEws_wrapper .dataTables_scroll");
            container.scrollLeft(container[0].scrollWidth); // auto ke kanan
        },
        language: {
            emptyTable: "Belum Ada data EWS" // Ubah sesuai kebutuhan
        }
    });

    $(".select2-data").select2();

    tableEws.on("init.dt", function() {
        alert(22);
    });

    $("#observasiEws_wrapper .dataTables_scroll").off("scroll").on("scroll", function () {
        if ($(this).scrollLeft() === 0) {
            console.log("scroll left");
            $('#scroll-load').trigger('click');
        }
    });

    $(document).off('click', '.update-ews-date').on('click', '.update-ews-date', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if ($(this).hasClass('processing')) {
            return false;
        }
        $(this).addClass('processing');
        
        let date = $(this).data('date');
        let ewsId = $(this).data('ews-id');
        let jenisEwsId = $('.selectJenisEws').val();
        
        if (!jenisEwsId) {
            docoNotification('warning', 'Peringatan', 'Jenis EWS Belum di Pilih.');
            $(this).removeClass('processing');
            return false;
        }

        openUpdateEwsModal(date, ewsId, jenisEwsId);
        
        setTimeout(() => {
            $(this).removeClass('processing');
        }, 1000);
    });
});

function openUpdateEwsModal(date, ewsId, jenisEwsId) {
    if ($('#modal_backdrop_ews').hasClass('in') || $('#modal_backdrop_ews').hasClass('show')) {
        return false;
    }
    
    let jenisEws = $('.selectJenisEws').find(":selected").text().toLowerCase();
    
    let baseUrl = `/${modul}${url}/input-ews?pendaftaran_id=${pendaftaran_id}`;
    let updateUrl = new URL(baseUrl, window.location.origin);
    updateUrl.searchParams.set('tanggal_ews', date);
    updateUrl.searchParams.set('ews_id', ewsId);
    updateUrl.searchParams.set('jenisews_id', jenisEwsId);
    updateUrl.searchParams.set('jenisews', jenisEws);

    $('.temp-update-ews-button').remove();

    let tempButton = $('<button>')
        .addClass('temp-update-ews-button')
        .attr('data-toggle', 'modal')
        .attr('data-target', '#modal_backdrop_ews')
        .attr('data-width', '75%')
        .attr('action', updateUrl.toString())
        .css('display', 'none');
    
    $('body').append(tempButton);
    tempButton.trigger('click');
    
    setTimeout(() => {
        tempButton.remove();
    }, 100);
}
