    $.ajax({
        url: '/dcms/profile/get-esign-status',
        type: 'GET',
        success: function(res) {
            $("[id^=esign-button-]").addClass('hide')
            console.log(res.data)
            for (var i = 0; i < res.data.length; i++) {
                $("#esign-button-"+i).removeClass('hide')
                $("#esign-button-"+i+" span").text(res.data[i].label)
                $("#esign-button-"+i).attr('href', res.data[i].url + "&redirect_url=" +  window.location.origin + '/dcms/profile/callback-status')
                $("#esign-button-"+i+" i").attr('class', res.data[i].icon)
            }
        }
    });