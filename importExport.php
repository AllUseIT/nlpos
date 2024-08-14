<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV</title>
    <link rel="stylesheet" href="./styles/styles.css"> <!-- Link your CSS file -->
</head>
<body>
    <div>
        <h2 class="login-h2">Import</h2>
    </div>
    <div class="import-body">
        <div class="upload-box">
            <h2 class="upload-title">Upload Products</h2>
            <div class="upload-dropzone" onclick="document.getElementById('fileToUpload1').click()">
                <input type="file" id="fileToUpload1" class="file-input" onchange="displayFileName('fileToUpload1', 'fileName1')">
                <p>Drag & drop or click to upload your file</p>
                <span id="fileName1"></span> <!-- Display file name here -->
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar1">0%</div>
            </div>
            <div class="upload-actions">
                <button onclick="uploadFile('fileToUpload1', 'progressBar1', './API/product-upload.php')" class="upload-btn">Upload</button>
            </div>
        </div>

        <div class="upload-box">
            <h2 class="upload-title">Upload Montana Stock</h2>
            <div class="upload-dropzone" onclick="document.getElementById('fileToUpload2').click()">
                <input type="file" id="fileToUpload2" class="file-input" onchange="displayFileName('fileToUpload2', 'fileName2')">
                <p>Drag & drop or click to upload your file</p>
                <span id="fileName2"></span> <!-- Display file name here -->
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar2">0%</div>
            </div>
            <div class="upload-actions">
                <button onclick="uploadFile('fileToUpload2', 'progressBar2', './API/montana_import.php')" class="upload-btn">Upload</button>
            </div>
        </div>

        <div class="upload-box">
            <h2 class="upload-title">Upload Zambezi File</h2>
            <div class="upload-dropzone" onclick="document.getElementById('fileToUpload3').click()">
                <input type="file" id="fileToUpload3" class="file-input" onchange="displayFileName('fileToUpload3', 'fileName3')">
                <p>Drag & drop or click to upload your file</p>
                <span id="fileName3"></span> <!-- Display file name here -->
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar3">0%</div>
            </div>
            <div class="upload-actions">
                <button onclick="uploadFile('fileToUpload3', 'progressBar3', './API/zambezi_import.php')" class="upload-btn">Upload</button>
            </div>
        </div>

        <div class="upload-box">
            <h2 class="upload-title">Upload Centurion File</h2>
            <div class="upload-dropzone" onclick="document.getElementById('fileToUpload4').click()">
                <input type="file" id="fileToUpload4" class="file-input" onchange="displayFileName('fileToUpload4', 'fileName4')">
                <p>Drag & drop or click to upload your file</p>
                <span id="fileName4"></span> <!-- Display file name here -->
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar4">0%</div>
            </div>
            <div class="upload-actions">
                <button onclick="uploadFile('fileToUpload4', 'progressBar4', './API/centurion_import.php')" class="upload-btn">Upload</button>
            </div>
        </div>

        <div class="upload-box">
            <h2 class="upload-title">Upload Daspoort File</h2>
            <div class="upload-dropzone" onclick="document.getElementById('fileToUpload5').click()">
                <input type="file" id="fileToUpload5" class="file-input" onchange="displayFileName('fileToUpload5', 'fileName5')">
                <p>Drag & drop or click to upload your file</p>
                <span id="fileName5"></span> <!-- Display file name here -->
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar5">0%</div>
            </div>
            <div class="upload-actions">
                <button onclick="uploadFile('fileToUpload5', 'progressBar5', './API/daspoort_import.php')" class="upload-btn">Upload</button>
            </div>
        </div>

        <div class="upload-box">
            <h2 class="upload-title">Upload Menlyn File</h2>
            <div class="upload-dropzone" onclick="document.getElementById('fileToUpload6').click()">
                <input type="file" id="fileToUpload6" class="file-input" onchange="displayFileName('fileToUpload6', 'fileName6')">
                <p>Drag & drop or click to upload your file</p>
                <span id="fileName6"></span> <!-- Display file name here -->
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar6">0%</div>
            </div>
            <div class="upload-actions">
                <button onclick="uploadFile('fileToUpload6', 'progressBar6', './API/menlyn_import.php')" class="upload-btn">Upload</button>
            </div>
        </div>
    </div>
    <div>
        <h2 class="login-h2">Export</h2>
    </div>

    <!-- Error Popup -->
    <div class="main-error-popup" id="errorPopup">
        <div class="popup-error-content">
            <span class="popup-error-close" onclick="closeErrorPopup()">×</span>
            <h2>Error Information</h2>
            <div id="errorList" class="error-list">
                <!-- Error messages will be dynamically added here -->
            </div>
        </div>
    </div>

    <script>
    function displayFileName(fileInputId, fileNameId) {
        var fileName = document.getElementById(fileInputId).files[0].name;
        document.getElementById(fileNameId).textContent = 'File selected: ' + fileName;
    }

    function uploadFile(fileInputId, progressBarId, uploadUrl) {
        var fileInput = document.getElementById(fileInputId);
        var file = fileInput.files[0];
        var formData = new FormData();
        formData.append('fileToUpload', file);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', uploadUrl, true);

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                var percentComplete = (e.loaded / e.total) * 100;
                document.getElementById(progressBarId).style.width = percentComplete + '%';
                document.getElementById(progressBarId).textContent = percentComplete.toFixed(2) + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.error) {
                    displayErrorPopup(response.error_info);
                } else {
                    alert('Upload successful.'); // Optionally, handle success message
                }
            } else {
                alert('Error occurred during upload. Please try again.');
            }
        };

        xhr.onerror = function() {
            alert('Error occurred during upload.');
        };

        xhr.send(formData);
    }

    function displayErrorPopup(errorInfo) {
        var errorPopup = document.getElementById('errorPopup');
        if (!errorPopup) {
            console.error('Error: Error popup element not found.');
            return;
        }

        var errorList = document.getElementById('errorList');
        errorList.innerHTML = ''; // Clear previous errors

        if (Array.isArray(errorInfo)) {
            errorInfo.forEach(function(errorMsg) {
                var errorItem = document.createElement('div');
                errorItem.classList.add('result-item');
                errorItem.classList.add('error');
                errorItem.textContent = errorMsg;
                errorList.appendChild(errorItem);
            });
        } else {
            var errorItem = document.createElement('div');
            errorItem.classList.add('result-item');
            errorItem.classList.add('error');
            errorItem.textContent = errorInfo;
            errorList.appendChild(errorItem);
        }

        errorPopup.style.display = 'flex'; // Show error popup
    }

    function closeErrorPopup() {
        var errorPopup = document.getElementById('errorPopup');
        if (errorPopup) {
            errorPopup.style.display = 'none'; // Hide error popup
        } else {
            console.error('Error: Error popup element not found.');
        }
    }
    </script>

</body>
</html>
