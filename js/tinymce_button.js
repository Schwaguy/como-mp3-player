(function() {
    tinymce.PluginManager.add('comoVideoButton', function( editor, url ) {
        editor.addButton( 'comoVideoButton', {
            text: tinyMCE_video.button_name,
            icon: false,
            onclick: function() {
				
				var templateOptions = jQuery.parseJSON(tinyMCE_video.video_template_select_options);
				editor.windowManager.open( {
					title: tinyMCE_video.button_title,
					body: [
                        {
                            type: 'textbox',
                            name: 'id',
                            label: 'ID',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'class',
                            label: 'Class',
                            value: ''
                        },
						{
                            type   : 'checkbox',
                            name   : 'modal',
                            label  : 'Modal',
                            text   : 'Modal',
                            checked : false
                        },
						{
                            type: 'textbox',
                            name: 'modalid',
                            label: 'Modai ID',
                            value: ''
                        },
						{
                            type   : 'checkbox',
                            name   : 'controls',
                            label  : 'Controls',
                            text   : 'Show Controls',
                            checked : false
                        },
						{
                            type   : 'combobox',
                            name   : 'preload',
                            label  : 'Preload',
                            values : [
                                { text: 'auto', value: 'auto' },
                                { text: 'metadata', value: 'metadata' },
								{ text: 'none', value: 'none' }
                            ]
                        },
						{
                            type: 'textbox',
                            name: 'width',
                            label: 'Width',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'height',
                            label: 'Height',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'webm',
                            label: 'WEBM Video',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'ogv',
                            label: 'OVG Video',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'mp4',
                            label: 'MP4 Video',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'poster',
                            label: 'Poster Img',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'placeholder',
                            label: 'Placeholder Img',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'title',
                            label: 'Title',
                            value: ''
                        },
						{
                            type: 'textbox',
                            name: 'text',
                            label: 'Text',
                            value: ''
                        },
						{
                            type   : 'combobox',
                            name   : 'aspect',
                            label  : 'Aspect Ratio',
                            values : [
                                { text: 'Wide Screen', value: 'wide' },
                                { text: 'Full Screen', value: 'full' }
                            ]
                        },
						{
                            type   : 'checkbox',
                            name   : 'autoplay',
                            label  : 'Auto Play',
                            text   : 'Auto Play',
                            checked : false
                        },
						{
                            type   : 'checkbox',
                            name   : 'playsinline',
                            label  : 'Plays Inline',
                            text   : 'Plays Inline',
                            checked : false
                        },
						{
                            type   : 'checkbox',
                            name   : 'loop',
                            label  : 'Loop',
                            text   : 'Loop',
                            checked : false
                        },
						{
                            type   : 'checkbox',
                            name   : 'muted',
                            label  : 'Muted',
                            text   : 'Muted',
                            checked : false
                        },
						{
                            type   : 'listbox',
                            name   : 'template',
                            label  : 'Template',
                            values : templateOptions
                        },
                    ],
					
					//[video-bg id='' class='' controls='true/false' preload='' width='' height='' data-setup='' webm='full path' ogv='full path' mp4='full path' poster='full path' title='' text='' placeholder='full path' aspect='wide/full' template='']
					
                    onsubmit: function( e ) {
						
						var vidID = (e.data.id ? ' id='+e.data.id : '');
						var vidClass = (e.data.id ? ' class='+e.data.class : '');
						var modal = (e.data.modal ? ' modal=true' : '');
						var modalid = (e.data.modalid ? ' modalid='+e.data.modalid : '');
						var controls = (e.data.controls ? ' controls=true' : '');
						var preload = (e.data.preload ? ' preload='+e.data.preload : '');
						var width = (e.data.width ? ' width='+e.data.width : '');
						var height = (e.data.height ? ' height='+e.data.height : '');
						var webm = (e.data.webm ? ' webm='+e.data.webm : '');
						var ogv = (e.data.ogv ? ' ogv='+e.data.ogv : '');
						var mp4 = (e.data.mp4 ? ' mp4='+e.data.mp4 : '');
						var poster = (e.data.poster ? ' poster='+e.data.poster : '');
						var title = (e.data.title ? ' title=\''+e.data.title +'\'' : '');
						var text = (e.data.text ? ' text=\''+e.data.text +'\'' : '');
						var placeholder = (e.data.placeholder ? ' placeholder='+e.data.placeholder : '');
						var aspect = (e.data.aspect ? ' aspect='+e.data.aspect : '')
						var autoplay = (e.data.autoplay ? ' autoplay=true' : '');
						var playsinline = (e.data.playsinline ? ' playsinline=true' : '');
						var loop = (e.data.loop ? ' loop=true' : '');
						var muted = (e.data.muted ? ' muted=true' : '');
						var template = (e.data.template ? e.data.template : 'default');
						
                        editor.insertContent( '[como-mp3-player template='+ template + vidID + vidClass + modal + modalid + controls + preload + width + height + webm + ogv + mp4 + poster + title + text + placeholder + aspect + autoplay + playsinline + loop + muted +']');
                    }
                });
            },
        });
    });

})();