/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-17 11:42:53
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-17 11:46:17
*/
	$(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
        $('.pickadate').val($(this).data('default'));
    });