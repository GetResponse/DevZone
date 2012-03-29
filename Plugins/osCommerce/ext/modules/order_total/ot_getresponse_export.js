$(function() {
// GetResponse Plugin
$('.infoBoxContent:last').parent().parent().parent().after('<div style="height: 60px; text-align:center; background-color: #DEE4E8;"><span class="export_btn">Export to campaign</span><span class="rollling"></span><div class="info"></div></div>');
$('.export_btn').button( { icons: { primary: "ui-icon-refresh" } });
$('.info').css("font-family","tahoma");
$('.info').css("font-size","11px");
$('.export_btn').click(function() {
	$	.ajax({
		url : "modules.php?set=order_total&module=ot_getresponse&gr=export",
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