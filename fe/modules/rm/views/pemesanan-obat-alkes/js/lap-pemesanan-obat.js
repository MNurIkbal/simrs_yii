/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-18 09:57:53
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-18 09:57:57
*/


	$(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
        $('.pickadate').val($(this).data('default'));
    });
