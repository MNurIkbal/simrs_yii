$('#clock1').clock({
	'dateFormat':'l, d F Y',
	'timeFormat':'H:i:s',
	'langSet':'id'
});
$('#default-antrian-btn').on('click',function(e){
	_this = $(this);
	_this.button('loading');
	$('#ambil-antrian-form-default').submit();
});
$('#ambil-antrian-form-default').on('beforeSubmit', function(e){
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: formData,
        success: function (res) {
        	var succMessage = 'Proses Berhasil!';
        	var succText = 'Antrian Dicetak';
        	var msg = res.response;
        	if(msg.text != undefined){
        		succText = msg.text;
        	}
        	if(msg.message != undefined){
        		succMessage = msg.message;
        	}
            new PNotify({
                title: succMessage,
                text: succText,
                addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                type: 'success'
            });
        },
        error: function (res) {
        	var errMessage = 'Gagal Diproses';
        	var errText = 'Terjadi Kesalahan';
            new PNotify({
                title: errMessage,
                text: errText,
                addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
                type: 'danger'
            });
        }
    }).done(function(){
    	btn_submit = $('#default-antrian-btn');
    	btn_submit.button('reset');
    });
}).on('submit', function(e){
    e.preventDefault();
});