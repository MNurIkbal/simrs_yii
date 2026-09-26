/*
* @Author: rizqi_fitrianto
* @Date:   2018-08-09 10:02:11
* @Last Modified by:   rizqi_fitrianto
* @Last Modified time: 2018-09-05 13:52:50
*/

$('#tab-operasi').stepy({
    backLabel: 'Kembali <b><i class=\'fa fa-chevron-left\'></i></b>',
    nextLabel: 'Selanjutnya <b><i class=\'fa fa-chevron-right\'></i></b>',
    // titleClick: true,
    legend: false,
    duration: 200,
    transition: 'fade',
    select: function(index){
        loadContent(index)
    },
})
$(document).ready(function(){
    var _posisi = $('.posisi-tab').val()
    var _state = $('.state-tab').val()
    if(_state == 0){
        $('.stepy-navigator').addClass('hidden')
        $('#tab-operasi').stepy('step', _posisi) //ini biar default nampilin form intra operasi
    }else{
        $('#tab-operasi').stepy('step', 1) //ini biar default nampilin form intra operasi
    }
    $('#tab-operasi').find('.button-next').addClass('btn btn-xs btn-labeled btn-info');
    $('#tab-operasi').find('.button-back').addClass('btn btn-xs btn-labeled btn-info');
})

var loadContent = function(index) {
    var _steps = index - 1;
    var _obj = $( '#tab-operasi-step-'+ _steps ).find( '.content' );
    var _source = _obj.attr( 'data-source' )
    var _status = _obj.attr( 'data-status' )

    $(_obj).docoLoad({
        url: _source,
        dataType: 'html',
        success : function(data) {
            // $(" .select2 ").select2();
        }
    });

}