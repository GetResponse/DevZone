$(function() {
// GetResponse Plugin
var api_key_element = $('input[name=configuration[MODULE_ORDER_TOTAL_GETRESPONSE_API_KEY]]');
var campaign_element = $('select[name=configuration[MODULE_ORDER_TOTAL_GETRESPONSE_CAMPAIGN]]');
$(campaign_element).after('<span class="rollling"></span>');
$(api_key_element).focusout(function() {
	var api_key = $(api_key_element).val();
			  $	.ajax({
					url : "modules.php?set=order_total&module=ot_getresponse&gr=get_campaign",
					beforeSend: function() { $('.rollling').html(' loading...') },
					data : {
						'api_key' : api_key
					},
					success : function(data) {
						$('.rollling').html('');
						var content ='';
						$.each(data, function(i, d) {
								content += '<option value="'+ d.text +'">'+ d.text +'</option>';
							});
						$(campaign_element).html(content);
					},
					type : "POST",
					async : false,
					dataType : "json"
				});
});
// GetResponse Plugin
});