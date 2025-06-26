<?php
if (isset($_FILES['files'])) {
    // Loop through all the uploaded files
    foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
        $fileExtension = pathinfo($_FILES['files']['name'][$index], PATHINFO_EXTENSION);
        $fileType = strtolower($fileExtension);
        $fileName0 = bin2hex(random_bytes(8)); 
        $fileName = $fileName0 . "." . $fileExtension;
        $targetFile = "uploads/" . $fileName;
        $convertedFile = "uploads/" . $fileName0 . ".png";
        $uploadOk = 1;

        // Handle file size limit (200MB)
        if ($_FILES['files']['size'][$index] > 200000000) {
            echo "<br>Sorry, your file is too large; (maximum 200Mb)";
            $uploadOk = 0;
        }

        // Check file type
        if ($fileType != "jpg" && $fileType != "jpeg") {
            echo "<br>Sorry, only JPG files allowed";
            $uploadOk = 0;
        }

        // If no errors, proceed with upload
        if ($uploadOk == 0) {
            echo "<br>File upload failed.";
            continue; // Skip the current file and continue with the next
        } else {
            // Move the uploaded file to the target location
            if (move_uploaded_file($_FILES['files']['tmp_name'][$index], $targetFile)) {
                echo "<br>File uploaded: " . $fileName;

                // Rename and convert the file to PNG (you can implement your conversion logic here)
                rename($targetFile, $convertedFile);

                // Check if the converted file exists
                if (file_exists($convertedFile)) {
                    // Trigger a download of the converted file using JavaScript
                    echo "<script>
                            var a = document.createElement('a');
                            a.href = '" . $convertedFile . "';
                            a.download = '" . basename($convertedFile) . "';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                          </script>";
                } else {
                    echo "<br>Error converting file: " . $fileName;
                }
            } else {
                echo "<br>Error uploading file: " . $_FILES['files']['name'][$index];
            }
        }
    }

    // Clean up: Delete files older than 10 seconds (JPG, JPEG, PNG)
    $files = scandir('uploads/');
    $currentTime = time();
    
    foreach ($files as $file) {
        $filePath = 'uploads/' . $file;
        // Check if the file is an image and older than 10 seconds
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
            $fileModificationTime = filemtime($filePath);
            if ($currentTime - $fileModificationTime > 10) {  // Check if file is older than 10 seconds
                if (is_file($filePath)) {
                    unlink($filePath); // Delete the file
                    echo "<br>Deleted old file: " . $file;
                }
            }
        }
    }
}
echo "<meta http-equiv='refresh' content='2;url=/index.php#multiple' />";
?>