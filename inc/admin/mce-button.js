
(function() {
	tinymce.PluginManager.add('af_footnotes', function( editor, url ) {
		editor.addButton( 'af_footnotes', {
			title: 'Add a Footnote',
			icon: 'af-footnotes-admin-button',
			onclick: function() {
				//if text is highlighted, wrap that text in a footnote
				//otherwise, show an editor to insert a footnote
				editor.focus();
				var content = editor.selection.getContent();
				if (content.length > 0) {
					if (content.indexOf('[af_footnote]') == -1 && content.indexOf('[/af_footnote]') == -1 &&
						content.indexOf('[af]') == -1 && content.indexOf('[/af]') == -1) {
						editor.selection.setContent('[af]' + content + '[/af]');
					} else if (content.indexOf('[af_footnote]') != -1 && content.indexOf('[/af_footnote]') != -1) {
						editor.selection.setContent(content.replace(/\[af\_footnote\]/, '').replace(/\[\/af\_footnote\]/, ''));
					} else if (content.indexOf('[af]') != -1 && content.indexOf('[/af]') != -1) {
						editor.selection.setContent(content.replace(/\[af\]/, '').replace(/\[\/af\]/, ''));
					} else {
						//we don't have a full tag in the selection, do nothing
					}
				} else {
					editor.windowManager.open( {
						title: 'Insert Footnote',
						body: [{
							type: 'textbox',
							name: 'footnote',
							label: 'Footnote'
						}],
						onsubmit: function( e ) {
							editor.insertContent( '[af]' + e.data.footnote + '[/af]');
						}
					});
				}
			}
	
		});
	});
	})();