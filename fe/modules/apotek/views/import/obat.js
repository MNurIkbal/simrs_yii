$('document').ready(function(){

	$('#btn-upload').on('click',function(){
		$('#import-obat-form').submit();
	});

	$("#import-obat-form").submit(function(event) {
	    event.preventDefault();

	    var formData = new FormData(this);
	    $(this).docoForm("submit", {
	        dataType: false,
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: formData,
	        method: 'post',
	        isUpload: true,
	        skipSuccessNotif: true,
	        success: function(data) {
	        	var metadata = data.metadata;
	        	var response = data.response;
	        	if(metadata.status == 206) {
	        		docoNotification('warning', i18next.t(response.message), i18next.t(response.text));
	        		$("#import-obat-form")[0].reset();
	        	} else if(metadata.status == 200) {
	        		docoNotification('success', i18next.t(response.message), i18next.t(response.text));
	        		$("#import-obat-form")[0].reset();
	        	} else {
		        	docoNotification('error', i18next.t(response.message), i18next.t(response.text));
	        	}
	        },
	        error: function(data) {
	        	var response = data.responseJSON;
	        	docoNotification('error', i18next.t(response.name), i18next.t(response.message));
	        }
	    });

	});
});