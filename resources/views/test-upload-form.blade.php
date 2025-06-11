<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Test File Upload</h4>
                    </div>
                    <div class="card-body">
                        <form id="upload-form" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="test_file" class="form-label">Select File to Upload</label>
                                <input type="file" class="form-control" id="test_file" name="test_file" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                        
                        <div class="mt-4">
                            <h5>Results:</h5>
                            <div id="results" class="border p-3 rounded bg-light">
                                <p class="text-muted">Upload a file to see results...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Upload Troubleshooting</h4>
                    </div>
                    <div class="card-body">
                        <p>This page helps diagnose file upload issues in the chat application.</p>
                        <p>Key things to check:</p>
                        <ul>
                            <li>Storage directory permissions</li>
                            <li>Proper form encoding (multipart/form-data)</li>
                            <li>Request validation</li>
                            <li>File size limits</li>
                        </ul>
                        
                        <div class="alert alert-info">
                            <strong>Note:</strong> If uploads work here but not in the chat, check the chat controller's file handling logic.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#upload-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const $results = $('#results');
                
                $results.html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Uploading...</p></div>');
                
                $.ajax({
                    url: '/test-upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        let html = '<div class="alert alert-success">File uploaded successfully!</div>';
                        html += '<h6>File Details:</h6>';
                        html += '<ul>';
                        html += `<li><strong>Name:</strong> ${response.file_info.original_name}</li>`;
                        html += `<li><strong>Type:</strong> ${response.file_info.mime_type}</li>`;
                        html += `<li><strong>Size:</strong> ${formatFileSize(response.file_info.size)}</li>`;
                        html += `<li><strong>Path:</strong> ${response.file_info.path}</li>`;
                        
                        if (response.file_info.url) {
                            html += `<li><strong>URL:</strong> <a href="${response.file_info.url}" target="_blank">${response.file_info.url}</a></li>`;
                        }
                        
                        html += '</ul>';
                        
                        $results.html(html);
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred during upload.';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {}
                        
                        $results.html(`<div class="alert alert-danger">${errorMessage}</div><pre class="bg-dark text-white p-3 mt-3">${xhr.responseText}</pre>`);
                    }
                });
            });
            
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        });
    </script>
</body>
</html> 