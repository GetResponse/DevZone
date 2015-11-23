(function() {
	tinymce.create('tinymce.plugins.GrShortcodes', {

		init : function(ed, url) {
		},
		createControl : function(n, cm) {
			if (n=='GrShortcodes'){
				var mlb = cm.createListBox('GR Web Form Shortcode', {
					title : 'GR Web Form Shortcode',
					onselect : function(v) {
						if (tinyMCE.activeEditor.selection.getContent() == '' && v != null){
							var shortcode = '[grwebform url="' + v + '" css="on"/]';
							tinyMCE.activeEditor.selection.setContent( shortcode )
						}
					}
				});

				if (my_webforms != null && my_campaigns != null) {
					for (var i in my_webforms) {
						mlb.add(my_webforms[i].name + ' (' + my_campaigns[my_webforms[i].campaign].name + ')', my_webforms[i].url);
					}
				}
				else if (my_webforms != null) {
					for (var i in my_webforms) {
						mlb.add(my_webforms[i].name, my_webforms[i].url);
					}
				}
				else {
					mlb.add('No webforms', null);
				}

				return mlb;
			}
			return null;
		}
	});
	tinymce.PluginManager.add('GrShortcodes', tinymce.plugins.GrShortcodes);
})();