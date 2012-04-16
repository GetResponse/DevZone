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
            <?php /* wylaczone funkcje ze wzgledu na brak api
            <tr>
              <td><?php echo $entry_enable_module; ?></td>
              <td><select id="gr_enable_module" name="config_enable_module">
                  <?php if ($config_enable_module == 1) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            */?>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_apikey; ?></td>
              <td><input id="gr_apikey" type="text" name="config_apikey" value="<?php echo $config_apikey; ?>" size="50" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_campaign; ?></td>
              <td><select id="gr_campaign" name="config_campaign">
                  <option value="">--- none ---</option>
                </select></td>
            </tr>
            <?php /* wylaczone funkcje ze wzgledu na brak api
            <tr>
              <td><?php echo $entry_register_integration; ?></td>
              <td><?php if ($config_register_integration) { ?>
                <input type="radio" name="config_register_integration" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_register_integration" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_register_integration" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_register_integration" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_guest_integration; ?></td>
              <td><?php if ($config_guest_integration) { ?>
                <input type="radio" name="config_guest_integration" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_guest_integration" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_guest_integration" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_guest_integration" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
             */?>
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
	// zmienne
	var api_key_element = $('#gr_apikey');
	var campaign_element = $('#gr_campaign');
	var export_element = $('#gr_export');
	//var enable_module_element = $('#gr_enable_module'); /* wylaczone funkcje ze wzgledu na brak api */
	// funkcja exportu ajaxem
	function ajax_export() {
		var api_key = api_key_element.val();
		var campaign = campaign_element.val();
		  $	.ajax({
				url : "index.php?route=module/getresponse/export&token=<?php echo $token; ?>",
				beforeSend: function() { $('.gr_info').html(' Loading...') },
				data : {
					'api_key' : api_key,
					'campaign' : campaign,
				},
				success : function(data) {
					$('.gr_info').html(data.response);				
				},
				type : "POST",
				async : false,
				dataType : "json"
			});
	}
	// funkcja sciagniecia listy kampani ajaxem
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
				type : "POST",
				async : false,
				dataType : "json"
			});
	}
	/* wylaczone funkcje ze wzgledu na brak api 
	// wygaszanie inputow i selectow
	function enable_disable_module() {
		$('.rollling, .gr_info').html('');
		if ($(enable_module_element).val()==1) {
			console.log('guziki dzialaja');
			$('input[type=radio], #gr_apikey, #gr_campaign, #gr_export').prop('disabled', false);
			$(export_element).bind('click', function() {
					ajax_export();
			});
		} else if ($(enable_module_element).val()==0) {
			console.log('guziki wylaczone');
			$('input[type=radio], #gr_apikey, #gr_campaign, #gr_export').prop('disabled', true);
			$(export_element).unbind();
		}		
	}
	*/
	// ustawienia startowe:
	$(campaign_element).after('<span class="rollling"></span>');
	$(export_element).after('<span class="gr_info"></span>');
	ajax_get_campaning();
	//enable_disable_module();	/* wylaczone funkcje ze wzgledu na brak api */

	// ustawienia po jakiej akcji
	$(api_key_element).focusout(function() {
		ajax_get_campaning();
	});		
	$(export_element).click(function() {
		ajax_export();
	});
	
	/* wylaczone funkcje ze wzgledu na brak api 
	$(enable_module_element).change(function() {
		enable_disable_module();
	});
	*/
});
//-->
</script> 

<?php echo $footer; ?>