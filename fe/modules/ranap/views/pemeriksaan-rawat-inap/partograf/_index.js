var methodLoadingPanelPartograf = {
	_createContainer:function(){
		return '<div class=\"loading-panel-partograf\">'+
					'<div class=\"text-center\">'+
						'<i class=\"icon-spinner4 spinner position-center\"></i>'+
					'</div>'+
				'</div>';
	},
	show: function(element){
		element.prepend(methodLoadingPanelPartograf._createContainer)
	},
	clear: function(element){
		element.find('.loading-panel-partograf').remove();
	}
}
$.fn.loadingPanelPartograf = function(method) {

	var _this = $(this);
	return methodLoadingPanelPartograf[method](this);
};

$(function(){
	$.fn.stepy.defaults.legend = false;
    $.fn.stepy.defaults.transition = 'fade';
    $.fn.stepy.defaults.duration = 150;
    $.fn.stepy.defaults.backLabel = 'Kembali <b><i class=\'fa fa-chevron-left\'></i></b>';
    $.fn.stepy.defaults.nextLabel = 'Selanjutnya <b><i class=\'fa fa-chevron-right\'></i></b>';
    $.fn.stepy.defaults.duration = 150;
    $('#partograf-wizard').stepy({
        titleClick: true,
        select: function(index){
        	var tabIndex = index-1;
        	var tabContent = $(this).find('fieldset#partograf-wizard-step-'+tabIndex).find('.content');
        	var tabName = $(this).find('fieldset#partograf-wizard-step-'+tabIndex).data('name');
        	var tabUrl = $(this).find('fieldset#partograf-wizard-step-'+tabIndex).data('url');
        	var urlContent = 'content-partograf';
        	if(tabUrl != undefined){
        		urlContent = tabUrl;
        	}
        	tabContent.docoLoad({
        		url:'/ranap/pemeriksaan-rawat-inap/'+urlContent+'?trace=1&id='+pendaftaran_id+'&idx='+tabIndex+'&tabname='+tabName,
        		dataType:'html',
        		success:function(data){
                    $('.select2').select2()
        		}
        	});
        }
    });
    $('#partograf-wizard').stepy('step',1);
    $('#partograf-wizard').find('.button-next').prop('hidden',true);
    $('#partograf-wizard').find('.button-back').prop('hidden',true);

    $(document).on("click", ".btn-selanjutnya", function(event) {
        var index = parseInt($(this).data("index"));

        if (index < 6) {
            index = index + 1;
            var header = 'Perhatian !';
            var message = 'Apakah anda yakin untuk kembali ke halaman selanjutnya ?';
            var label = {
                buttons: {
                    'Yes': 'button-yes',
                    'No': 'button-no'
                }
            };
            $.showQuestionDialog(header, message, label, function(reaction) {
                if (reaction == 'Yes') {
                    $("#partograf-wizard-head-"+index).click();
                } 
            });
        }
    });

    $(document).on("click", ".btn-sebelumnya", function(event) {
        var index = parseInt($(this).data("index"));

        if (index > 0) {
            index = index - 1;
            var header = 'Perhatian !';
            var message = 'Apakah anda yakin untuk kembali ke halaman sebelumnya ?';
            var label = {
                buttons: {
                    'Yes': 'button-yes',
                    'No': 'button-no'
                }
            };
            $.showQuestionDialog(header, message, label, function(reaction) {
                if (reaction == 'Yes') {
                    $("#partograf-wizard-head-"+index).click();
                } 
            });

        }
    });

    $(document).on("click", "#btn-cetak-keadaan-umum", function() {
        let url = window.location.origin;
        let target = $(this).attr('data-target');
        window.open(url+target);
    });


});