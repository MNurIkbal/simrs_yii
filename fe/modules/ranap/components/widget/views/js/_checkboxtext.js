//author: Ardi Pratama

$(document).ready(function(){
	var checkboxId = "<?=$checkboxId?>";
	var checkboxName = "<?=$checkboxName?>";
	var textfieldId = "<?=$textfieldId?>";
	var textfieldName = "<?=$textfieldName?>";
	var dependsVal = "<?=$dependsVal?>";
	var classField = "checkboxtextwidgetfield-"+textfieldId;
	if(dependsVal != ''){
		$("."+classField).hide();
		if($("input."+classField).length){
			$("input."+classField).prop('disabled',true);
		}
	}
	$('#'+checkboxId+' input[name="'+checkboxName+'[]"]').change(function(){
		if($(this).val() == dependsVal && $(this).prop("checked") == true){
			$("."+classField).show();
			if($("input."+classField).length){
				$("input."+classField).prop('disabled',false);
			}
		}else if($(this).val() == dependsVal && $(this).prop("checked") == false){
			$("."+classField).hide();
			if($("input."+classField).length){
				$("input."+classField).prop('disabled',true);
			}
		}
		
	})
	
})