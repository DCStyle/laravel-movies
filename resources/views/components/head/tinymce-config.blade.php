<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
	tinymce.init({
		selector: '#tinymce-editor',
		extended_valid_elements: 'script[src|async|defer|type|charset]',
		valid_elements: '*[*]',
		allow_script_urls: true,
		allow_html_in_named_anchor: true,
		verify_html: false,
		forced_root_block: false,
		remove_linebreaks: false,
		convert_newlines_to_brs: false,
		force_p_newlines: false,
		force_br_newlines: false,
		schema: 'html5',
		valid_children: '+body[script],+body[style]',
		content_css: false,
		remove_trailing_brs: false,
		custom_elements: 'script',
		cleanup: false,
		plugins: 'advlist anchor autolink charmap code codesample directionality emoticons fullscreen help image importcss insertdatetime link lists media nonbreaking pagebreak preview quickbars searchreplace table template visualblocks visualchars wordcount',
		toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align lineheight | checklist numlist bullist indent outdent | forecolor backcolor removeformat | link image media table codesample | charmap emoticons | fullscreen code preview',
		toolbar_mode: 'sliding',
		quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		contextmenu: 'link image table',
		height: 600,
		skin: 'oxide',
		menubar: false,
		branding: false,
		relative_urls: false,
		remove_script_host: false,
		convert_urls: false,
		document_base_url: '{{ config('app.url') }}',
		images_upload_handler: function(blobInfo, progress) {
			return new Promise((resolve, reject) => {
				const formData = new FormData();
				formData.append('image', blobInfo.blob(), blobInfo.filename());

				fetch('/images/upload', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					},
					body: formData
				})
					.then(res => res.json())
					.then(json => resolve(json.location))
					.catch(err => reject('Upload failed'));
			});
		},
		setup: function(editor) {
			const editorMode = document.getElementById('editor-mode');
			if (editorMode) {
                editorMode.addEventListener('change', function() {
                    if (this.checked) {
                        editor.execCommand('mceCodeEditor');
                    } else {
                        editor.execCommand('mceCodeEditor');
                    }
                });
            }
		}
	});
</script>