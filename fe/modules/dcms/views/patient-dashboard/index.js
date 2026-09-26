$(document).ready( function() {
    loadData();

    setInterval(() => {
        loadData()
    }, 60000);
} )

var loadData = () => {
    $.ajax({
        url: '/dcms/patient-dashboard/get-rekap',
        method: 'get',
        success: function(response) {
            const {data} = response
            $.each( data, (k,v) => {
                $(`#${v.tipe}`).text(v.total)
            })
        }
    })
}