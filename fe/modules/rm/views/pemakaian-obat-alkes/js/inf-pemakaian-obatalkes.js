/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-19 10:37:47
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-19 10:39:23
*/
	$(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
    });

    $(document).on("click", ".data-delete", function(e) {
	    e.preventDefault();
	    $(this).docoForm("delete",{
	    	additional: 'data-rm',
	        success : function (data) {
	            table.draw()
	        }
	    });
	    return false;
	});
