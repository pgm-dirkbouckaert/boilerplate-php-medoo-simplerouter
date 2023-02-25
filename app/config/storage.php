<?php


/**
 * Upload files
 *
 * @param [array] $files (= $_FILES of post)
 * @return [array] $arrFileNames (names of uploaded files, without storage directory)
 */
function uploadFiles($files, $fieldname) {

  $arrFileNames = [];
  if (gettype($files[$fieldname]["name"]) == "array") {
    foreach ($files[$fieldname]["name"] as $index => $upload) {
      $extension = strtolower(pathinfo($upload, PATHINFO_EXTENSION));
      $filename = time() . "." . $extension;
      $filetype = $files[$fieldname]["type"][$index];
      $filesize = $files[$fieldname]["size"][$index];
      $tempfile = $files[$fieldname]["tmp_name"][$index];
      $filenameWithDirectory = DIR_UPLOAD . $filename;
      array_push($arrFileNames, $filename);
      move_uploaded_file($tempfile, $filenameWithDirectory);
    }
  } elseif (gettype($files[$fieldname]["name"]) == "string") {
    $extension = strtolower(pathinfo($files[$fieldname]["name"], PATHINFO_EXTENSION));
    $filename = time() . "." . $extension;
    $filetype = $files[$fieldname]["type"];
    $filesize = $files[$fieldname]["size"];
    $tempfile = $files[$fieldname]["tmp_name"];
    $filenameWithDirectory = DIR_UPLOAD . $filename;
    array_push($arrFileNames, $filename);
    move_uploaded_file($tempfile, $filenameWithDirectory);
  }
  // dd($arrFileNames);
  return $arrFileNames;
}


/**
 * Delete files
 *
 * @param [array] $arrFileNames
 * @param [string] $targetDir
 * @return void
 */
function deleteFiles($arrFileNames, $targetDir) {
  foreach ($arrFileNames as $file) {
    $fullpath = $targetDir . $file;
    if (file_exists($fullpath)) {
      unlink($fullpath);
    }
  }
}



/**
 * Download a file
 *
 * @param [string] $fileName
 * @return void
 */
function downloadFile($fileName) {

  // Bron: https://www.w3docs.com/snippets/php/automatic-download-file.html

  // Get parameter
  $fileName = urldecode($_REQUEST["file"]); // Decode URL-encoded string

  // Check if the file name includes illegal characters using regular expression
  if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $fileName)) {
    $filepath = DIR_UPLOAD . $fileName;
    // Process download
    if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filepath));
      flush(); // Flush system output buffer
      readfile($filepath);
      die();
    } else {
      http_response_code(404);
      die();
    }
  } else {
    die("Ongeldige betandsnaam!");
  }
}
