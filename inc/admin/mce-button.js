
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
					if (content.indexOf('[fn_footnote]') == -1 && content.indexOf('[/fn_footnote]') == -1 &&
						content.indexOf('[fn]') == -1 && content.indexOf('[/fn]') == -1) {
						editor.selection.setContent('[fn]' + content + '[/fn]');
					} else if (content.indexOf('[fn_footnote]') != -1 && content.indexOf('[/fn_footnote]') != -1) {
						editor.selection.setContent(content.replace(/\[fn\_footnote\]/, '').replace(/\[\/fn\_footnote\]/, ''));
					} else if (content.indexOf('[fn]') != -1 && content.indexOf('[/fn]') != -1) {
						editor.selection.setContent(content.replace(/\[fn\]/, '').replace(/\[\/fn\]/, ''));
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
							editor.insertContent( '[fn]' + e.data.footnote + '[/fn]');
						}
					});
				}
			}
	
		});
	});
	})();