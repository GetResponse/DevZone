var GR_jQuery = jQuery.noConflict();

function ShowHide(id)
{
	var element = GR_jQuery('#' + id);
	if ( element.is(':visible') )
	{ 
		element.hide();
	}	
	else if (element.is(':hidden') )
	{
		element.show();
	}
}

function AddCustom(clone_name)
{
	var parts = clone_name.split('_');
	if ( undefined != parts[2] && null != parts[2] )
	{
		var id = Math.round(new Date().getTime());
		var copy = GR_jQuery('.' + clone_name).clone(true);
	
		copy.show();
		copy.removeClass(clone_name);
		
		copy.find("input[id = 'name']").each(function(){
			$this = GR_jQuery(this); 
			$this.attr({'name': 'widget_getresponse[' + parts[2] + '][custom_' + id + '][name]', 'value': ''}).val('');
		});
		copy.find("input[id = 'value']").each(function(){
			$this = GR_jQuery(this); 
			$this.attr({'name': 'widget_getresponse[' + parts[2] + '][custom_' + id + '][value]', 'value': ''}).val('');
		});
		copy.find("input[id = 'hidden']").each(function(){
			$this = GR_jQuery(this); 
			$this.attr({'name': 'widget_getresponse[' + parts[2] + '][custom_' + id + '][hidden]', 'value': 'on'}).val('').removeAttr('checked');
			
		});
		
		copy.find("a[id = 'remove_custom']").each(function(){
			$this = GR_jQuery(this); 
			$this.show();
		});
	
		GR_jQuery('.' + clone_name).parent().append(copy);
	}
}

function RemoveCustom(clone_name)
{
	GR_jQuery('.' + clone_name).parent().children('div:last').remove();
}

function RemoveExistingCustom(element)
{
	GR_jQuery(element).parent().find("input[id = 'name']").each(function(){
		$this = GR_jQuery(this); 
		$this.attr({'value': ''}).val('');
	});
	GR_jQuery(element).parent().hide();
}