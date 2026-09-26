/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-18 12:00:43
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-18 12:01:02
*/

	$(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
        $('.pickadate').val($(this).data('default'));
    });