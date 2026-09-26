//author: Ardi Pratama

$(document).ready(function(){
	var textfieldId = "<?=$textfieldId?>";
	var textfieldName = "<?=$textfieldName?>";
	$('#btntextpluswidget-'+textfieldId).click(function(){
		var parentEle = $(this).parent().parent().parent();
		var newEle = "<div class='input-group'>";
		var textfieldEle = $(this).closest("div.input-group").find("input[type='text']")[0];
		if($(textfieldEle).val().length >= 1){
			var newVal = $(textfieldEle).val();
			newEle +="<div class='form-control-static'>"+newVal+"</div>";
			newEle +="<input type='hidden' name='"+textfieldName+"[]' value='"+newVal+"'/>";
			newEle +="<span class='input-group-btn'><button type='button' class='btn btn-danger btntextpluswidget-remove'>-</button></span>"
			newEle +="</div>";
			$(newEle).prependTo(parentEle);
		}
	})	
	$('body').on('click', 'button.btntextpluswidget-remove', function() {
	    $(this).closest("div.input-group").remove();
	});
})