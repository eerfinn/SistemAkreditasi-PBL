<script>
    // Gunakan TinyMCE Self-hosted (tidak perlu API key)
    // Pastikan file tinymce.min.js sudah ada di folder public/assets/vendor/tinymce
    var script = document.createElement('script');
    script.src = "{{ asset('assets/vendor/tinymce/tinymce.min.js') }}";
    script.onload = function() {
        tinymce.init({
            selector: '{{ $selector }}',
            height: {{ $height }},
            plugins: 'code table lists link image charmap preview anchor searchreplace visualblocks fullscreen insertdatetime media help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | table | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            branding: false,
            promotion: false,
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    };
    document.head.appendChild(script);
</script>
