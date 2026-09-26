//author: Ardi Pratama

$(document).ready(function(){
	var componentId = "<?=$id?>";
	var limitlevel = 7;
	$('div#'+componentId).find('input[type=radio]').change(function(){
		if($(this).closest('div.dynamicfield-radio').length > 0){
			var com = $(this).closest('div.dynamicfield-radio');
			var comId = com.data('id');
			var comParent = com.data('parent');
			var valueChanged = $(this).val();
			if(com.closest('div.form-group').attr('class').match(/(dynamiclevel)-(\d+)/)){
				var mtch = com.closest('div.form-group').attr('class').match(/(dynamiclevel)-(\d+)/);
				var lvl = mtch[2];
				var nextlvl = 1+parseInt(lvl);
				$('div#'+componentId).find('div.dynamiclevel-'+nextlvl).each(function(){
					var compgroup = $(this);
					if($(this).find('.dynamicfieldclass').length >= 1){
						var compchild = $(this).find('.dynamicfieldclass');
						var compchildId = compchild.data('id');
						var compchildParent = compchild.data('parent');
						var compchildDependent = compchild.data('dependent');
						var compchildmatch = compchild.attr('class').match(/(dynamicfield)-(\w+)/);
						if(compchildmatch.length >= 2 && compchildParent == comId){
							if(compchildDependent == valueChanged){
								if(compgroup.hasClass('nohide') ==false){
									compgroup.toggleClass('hidden-level',false);
								}
							}else{
								if(compgroup.hasClass('nohide') ==false){
									compgroup.toggleClass('hidden-level',true);
								}
							}
						}else if(compchildParent != comId){
							if(compgroup.hasClass('nohide') ==false){
								compgroup.toggleClass('hidden-level',true);
							}
							var contlvl = 1+parseInt(nextlvl);
							do{
								$('div#'+componentId).find('div.dynamiclevel-'+contlvl).each(function(){
									var contgroup = $(this);
									if($(this).find('.dynamicfieldclass').length >= 1){
										var contchild = $(this).find('.dynamicfieldclass');
										var contchildId = contchild.data('id');
										var contchildParent = contchild.data('parent');
										var contchildDependent = contchild.data('dependent');
										var contchildmatch = contchild.attr('class').match(/(dynamicfield)-(\w+)/);
										
										if(contgroup.hasClass('nohide') ==false){
											contgroup.toggleClass('hidden-level',true);
										}
										
									}
								});
								contlvl++;
							} while(contlvl<=limitlevel);
						}
					}
				})

			}
			
		}
	});
})