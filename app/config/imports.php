<?php

/**
 * Importeer cursussen naar de database
 *
 * @param [type] $files
 * @return void
 */
function importSheetToDB($files, $fieldname, $filename) {

  // Get file data
  // -------------
  $upload = $files[$fieldname]["name"];
  $extension = strtolower(pathinfo($upload, PATHINFO_EXTENSION));
  $filename = $filename . "." . $extension;
  $filetype = $files[$fieldname]["type"];
  $filesize = $files[$fieldname]["size"];
  $tempfile = $files[$fieldname]["tmp_name"];
  $filenameWithDirectory = DIR_IMPORT . $filename;

  // Exit als bestandstype niet toegelaten is
  // ----------------------------------------
  $allowedFileTypes = [
    'application/vnd.ms-excel',
    'text/xls',
    'text/xlsx',
    'text/csv',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
  ];

  if (!in_array($filetype, $allowedFileTypes)) {
    flash("Ongeldig bestandstype.", "danger");
    return redirect("/admin/dashboard-modules");
  }

  // Verwijder bestaande bestanden
  // -----------------------------
  if (file_exists($filenameWithDirectory)) {
    unlink($filenameWithDirectory);
  }

  // Sla bestand op
  // --------------
  move_uploaded_file($tempfile, $filenameWithDirectory);

  // Lees bestand
  // ------------
  $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filenameWithDirectory);
  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
  $spreadSheet = $reader->load($filenameWithDirectory);
  $sheet = $spreadSheet->getActiveSheet();
  $sheetArray = $sheet->toArray();
  $headerValues = $sheetArray[0];
  array_shift($sheetArray);
  $rowValues = $sheetArray;

  // Schrijf rijen naar database
  // ---------------------------
  $error = false;
  foreach ($rowValues as $row) {
    $importArray = MODEL_IMPORT_CURSUS;
    $headerModel = MODEL_HEADERS_CURSUS;
    for ($i = 0; $i < count($headerValues); $i++) {
      $headerValue = strtolower($headerValues[$i]);
      if (in_array($headerValue, array_keys($headerModel))) {
        $headerValue = $headerModel[$headerValue];
        $importArray[$headerValue] = $row[$i];
      }
    }
    $cursus = Cursus::findByCode($importArray["code"]);
    if (count($cursus) == 0) {
      // Cursus bestaat nog niet => INSERT
      $insertId = Cursus::insert($importArray);
      if (empty($insertId)) {
        $error = true;
      }
    } else {
      // Cursus bestaat al => UPDATE
      $update = Cursus::updateByCode($importArray["code"], $importArray);
      if ($update->errorCode() !== "00000") {
        $error = true;
      }
    }
  }

  // Verwijder opgeladen bestand
  // ---------------------------
  if (file_exists($filenameWithDirectory)) {
    unlink($filenameWithDirectory);
  }

  // Return
  return $error;
}
