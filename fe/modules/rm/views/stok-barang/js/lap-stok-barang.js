/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-19 13:38:16
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-19 13:38:25
*/

	$(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
        $('.pickadate').val($(this).data('default'));
    });