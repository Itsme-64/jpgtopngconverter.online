<?php
if(isset($_FILES['fileToUpload'])) {
	$fileExtension = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
    $fileType = strtolower($fileExtension);
    $fileName0 = bin2hex(random_bytes(8)); 
    $fileName = $fileName0 . "." . $fileExtension;
    $targetFile = "uploads/" . $fileName;
	$convertedFile = "uploads/" . $fileName0 . ".png";
	$uploadOk = 1;
    ob_start();

    if (file_exists($targetFile)) {
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 100000000) {
        echo "<br>Sorry, your file is too large; (maximum 100Mb)";
        $uploadOk = 0;
        return;
    }

    if($fileType != "jpg" && $fileType != "jpeg") {
        echo "<br>Sorry, only JPG files allowed";
        $uploadOk = 0;
        exit;
    }

    if ($uploadOk == 0) {
        exit;
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo "<br>Thank you for using this jpg to png converter! :)) file uploaded";
			rename($targetFile, $convertedFile);
			if (headers_sent()) {
				echo "<br> HTTP header has already been sent.";
				return false;
			}
			$output = ob_get_clean();

			if (file_exists($convertedFile)) {
				// Set headers for file download
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="'.basename($convertedFile).'"');
				header('Content-Length: ' . filesize($convertedFile));
			
				// Read the file and output its contents
				readfile($convertedFile);
                usleep(10);
                unlink($convertedFile);
			
				exit;
            } else {
                echo "<br>Error uploading file.";
            }
        }
    }
}
?>