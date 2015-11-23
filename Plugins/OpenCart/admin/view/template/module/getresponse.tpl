<?php echo $header; ?>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      
     <table class="form">       
            <tr>
              <td><span class="required">*</span> <?php echo $entry_apikey; ?></td>
              <td><input id="gr_apikey" type="text" name="config_apikey" value="<?php echo $config_apikey; ?>" size="50" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_campaign; ?></td>
              <td><select id="gr_campaign" name="config_campaign">
                  <option value="">-- none --</option>
                </select></td>
            </tr>
            <tr>
            	
            	<td><?php echo $entry_export; ?></td>
            	<td><div class="buttons"><a id="gr_export" class="button"><?php echo $button_export; ?></a></div>
            	</td>
            </tr>
       	</table>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
<!--
$(function() {
	// variables
	var api_key_element = $('#gr_apikey');
	var campaign_element = $('#gr_campaign');
	var export_element = $('#gr_export');
	// jquery ajax export function
	function ajax_export() {
		var api_key = api_key_element.val();
		var campaign = campaign_element.val();
		  $	.ajax({
				url : "index.php?route=module/getresponse/export&token=<?php echo $token; ?>",
				beforeSend: function() { $('.gr_info').html(' Loading...') },
				data : {
					'api_key' : api_key,
					'campaign' : campaign
				},
				success : function(data) {
					$('.gr_info').html(data.response);
				},
				error: function (response) {
					$('.gr_info').html(' Ajax request error.');
				},
				type : "POST",
				async : false,
				dataType : "json"
			});
	}
	// jquery ajax get list of campaign
	function ajax_get_campaning() {
		var api_key = api_key_element.val();
		  $	.ajax({
				url : "index.php?route=module/getresponse/campaning&token=<?php echo $token; ?>",
				beforeSend: function() { $('.rollling').html(' Loading...') },
				data : {
					'api_key' : api_key
				},
				success : function(data) {
					$('.rollling').html('');
					var content ='';
					$.each(data, function(i, d) {
							content += '<option';
							if (d.text == "<?php echo $config_campaign; ?>") {
								content += ' selected="selected" ';
							}
							content += ' value="'+ d.text +'">'+ d.text +'</option>';
						});
					$(campaign_element).html(content);
					
				},
				error: function (response) {
					$('.rollling').html(' Ajax request error.');
				},
				type : "POST",
				async : false,
				dataType : "json"
			});
	}
	// js start settings
	$(campaign_element).after('<span class="rollling"></span>');
	$(export_element).after('<span class="gr_info"></span>');
	ajax_get_campaning();

	// actions
	$(api_key_element).focusout(function() {
		ajax_get_campaning();
	});		
	$(export_element).click(function() {
		ajax_export();
	});
});
//-->
</script> 

<?php echo $footer; ?>