(function() {
	tinymce.PluginManager.add('GrShortcodes', function(editor, url) {

		function getValues() {
			var wf = [];

			if (my_webforms != null) {
				for (var i in my_webforms) {

					var webforms = {};
					var webform_name = (my_campaigns != null) ? my_webforms[i].name + ' (' + my_campaigns[my_webforms[i].campaign].name + ')' : my_webforms[i].name;

					webforms.text = webform_name;
					webforms.url = my_webforms[i].url;

					wf.push(webforms);
				}
			}
			else {
				return [{text:"No web forms", url:null}];
			}
			return wf;
		}

		editor.addButton('GrShortcodes', {
			type: 'listbox',
			title: 'GetResponse Web form integration',
			text: 'GR Web form',
			values: getValues(),
			onselect: function(v) {
				if (v.control.settings.url != null && v.control.settings.text != 'No web forms') {
					var shortcode = '[grwebform url="' + v.control.settings.url + '" css="on"/]';
					editor.insertContent(shortcode);
				}
			}
		});
	});

})();