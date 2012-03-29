$(function() {
// GetResponse Plugin
$('.infoBoxContent:last').parent().parent().parent().after('<div style="height: 60px; text-align:center; background-color: #E7E6E0;"><input type="button" class="export_btn" name="export_btn" value="Export to campaign"><span class="rollling"></span><div class="info"></div></div>');
//$('.export_btn').button( { icons: { primary: "ui-icon-refresh" } });
$('.info').css("font-family","tahoma");
$('.info').css("font-size","11px");
$('.export_btn').click(function() {
	$	.ajax({
		url : "modules.php?set=ordertotal&module=ot_getresponse&gr=export",
		beforeSend: function() { $('.info').html('exporting...') },
		success : function(data) {
			//$('.rollling').html('');
			$('.info').html(data.response);
		},
		type : "POST",
		async : false,
		dataType : "json"
	});	
});
// GetResponse Plugin
});