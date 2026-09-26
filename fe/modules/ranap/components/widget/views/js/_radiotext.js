//author: Ardi Pratama

$(document).ready(function(){
	var radioId = "<?=$radioId?>";
	var radioName = "<?=$radioName?>";
	var textfieldId = "<?=$textfieldId?>";
	var textfieldName = "<?=$textfieldName?>";
	var dependsVal = "<?=$dependsVal?>";
	var classField = "radiotextwidgetfield-"+textfieldId;
	if(dependsVal != ''){
		$("."+classField).hide();
		if($("input."+classField).length){
			$("input."+classField).prop('disabled',true);
		}
	}
	$('input[type=radio][name="'+radioName+'"]').change(function(){
		if($(this).val() == dependsVal){
			$("."+classField).show();
			if($("input."+classField).length){
				$("input."+classField).prop('disabled',false);
			}
		}else if(dependsVal != ''){

			$("."+classField).hide();
			if($("input."+classField).length){
				$("input."+classField).prop('disabled',true);
			}
		}
	})
	
})