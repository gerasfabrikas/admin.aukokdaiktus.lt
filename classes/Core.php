<?php

class Core {

    /**
     * @var \PDO $db The database connection (PDO)
     */
    protected $db            = null;

    protected $uploadFileWriteToFilesystemErrors = array(
        0 => 'Nežinoma klaida. Bandykite dar kartą. Jeigu klaida kartojasi, susisiekite.',
        -1 => 'Įkelti bylos nepavyko. Galbūt interneto ryšio problema? Bandykite įkelti dar kartą. Jeigu klaida kartojasi, susisiekite.',
        -2 => 'Neteisingas įkeliamos bylos formatas. Jeigu klaida kartojasi ir nežinote kaip ją panaikinti, susisiekite.',
        -3 => 'Įkeliama byla yra per didelė.',
        -4 => 'Bylos įkelti nepavyko - serverio klaida nr.4. Bandykite įkelti dar kartą. Jeigu klaida kartojasi, susisiekite.',
        -5 => 'Bylos įkelti nepavyko - serverio klaida nr.5. Bandykite įkelti dar kartą. Jeigu klaida kartojasi, susisiekite.',
        -6 => 'Bylos įkelti nepavyko - serverio klaida nr.6. Bandykite įkelti dar kartą. Jeigu klaida kartojasi, susisiekite.',
        -7 => 'Bylos įkelti nepavyko - serverio klaida nr.7. Bandykite įkelti dar kartą. Jeigu klaida kartojasi, susisiekite.'
    );

    // Įmonės teisinės formos
    // Svarbu, kad reikšmės atitiktų indeksus! Pvz. Kitos įmonės visada == 5! Nieko nekeičiam čia! Jeigu reikia - papildom sąrašą ir tiek.
    protected $legalStatuses = array(
        1 => 'Akcinė bendrovė',
        2 => 'Uždaroji akcinė bendrovė',
        3 => 'Individuali įmonė',
        4 => 'Mažoji bendrija',
        5 => 'Kitos įmonės',
        6 => 'Valstybės/savivaldybės įmonė/įstaiga',
        7 => 'Nevyriausybinė organizacija',
        8 => 'Bendrija',
        9 => 'Bendruomenė',
        10 => 'Kitos organizacijos'
    );


    public function __construct(&$dbConnection = null) {
        if(!isset($dbConnection)) {
            $this->connectToDb();
        } else {
            $this->db = $dbConnection;
        }
    }

    public function get($propertyName) {
        if(isset($propertyName)) {
            return $this->$propertyName;
        }
        return null;
    }

    /**
     * Checks if database connection is opened and open it if not
     */
    protected function connectToDb()
    {
        // connection already opened
        if ($this->db != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                $this->db = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME.';charset=utf8', DB_USER, DB_PASS);
                return true;
                // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = $this->lang['Database error'];
                return false;
            }
        }
    }

    /**
     * Upload the fil eto filesystem, db
     *
     * @param null|int $userId
     * @param null|string $basenameOfFileToRemove      filename to remove (PATHINFO_BASENAME)
     * @param string $webRootUrl                       e.g.: http://aukokdaiktus.lt/
     * @return bool|void
     */
    public function uploadFile($userId = null, $basenameOfFileToRemove = null, $webRootUrl = '', $tableName = 'users') {

        $fileCase = 'user_thumb';
        $allowedMimeTypes = array('image/jpeg', 'image/png');
        $maxAllowedFileSize = 5 * 1024 * 1024; // 5 MB
        $defaultChmodForDir = 0777;
        $basePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $relPathToUploadFilesTo = str_replace('{USER_ID}', $userId, ('uploads' . DIRECTORY_SEPARATOR . $tableName . DIRECTORY_SEPARATOR . '{USER_ID}' . DIRECTORY_SEPARATOR . $fileCase . DIRECTORY_SEPARATOR));

        if(!$userId) {
            return false; // Cannot continue if no user to proceed for
        }

        $uploaded = $this->uploadFileWriteToFilesystem($userId, ($basePath . $relPathToUploadFilesTo), $basenameOfFileToRemove, $defaultChmodForDir, $fileCase, $maxAllowedFileSize, $allowedMimeTypes);
        if(!is_array($uploaded) || !isset($uploaded['fileThumb'])){
            // File upload failed
            $stringToWriteToDb = (string)$uploaded; // aka. saving error code.
        } else {
            $stringToWriteToDb = $uploaded['fileThumb']; // drop the root path
            $stringToWriteToDb = str_replace($basePath, '', $stringToWriteToDb); // drop the root path
            $stringToWriteToDb = str_replace('\\', '/', $stringToWriteToDb); // change os separators to url separators
            if(!$webRootUrl) {
                $stringToWriteToDb = '/' . $stringToWriteToDb; // add / in front to make it 'url that is relative to the root'
            } else {
                $stringToWriteToDb = $webRootUrl . $stringToWriteToDb; // add webroot url in front to make it 'absolute image url'
            }
        }

        $this->uploadFileWriteToDb($userId, $stringToWriteToDb, $tableName);

        if(isset($uploaded)) {
            return $uploaded;
        }

    }


    protected function uploadFileWriteToDb($userId, $userThumbNewValue, $tableName = 'users') {

        if(!$userId) {
            return false;
        }

        $q = 'UPDATE `' . ($tableName) . '` SET `user_thumb` = :userThumb WHERE  `user_id` = :userId';
        $PdoStatement = $this->db->prepare($q);
        $PdoStatement->bindValue(':userThumb', $userThumbNewValue, PDO::PARAM_STR | PDO::PARAM_INT);
        $PdoStatement->bindValue(':userId', $userId, PDO::PARAM_INT);

        return $PdoStatement->execute();

    }


    protected function uploadFileWriteToFilesystem($userId, $dirToUploadUserFiles, $basenameOfFileToRemove = null, $defaultChmodForDir = 0755, $fileCase = 'user_thumb', $maxAllowedFileSize = '1048576', $allowedMimeTypes = array('image/jpeg', 'image/png'))
    {

        // No user id = no user to care about = have nothing to do...
        if(!$userId) {
            return 0;
        }

        // No temp file
        if (!$_FILES[$fileCase]['tmp_name']) {
            return -1;
        }

        $upFilenameExt = strtolower(pathinfo($_FILES[$fileCase]["name"], PATHINFO_EXTENSION));
        $upFilename = time(true) . str_replace('.', '_', uniqid('_', true)) . '.' . $upFilenameExt;
        $upTempFile = $_FILES[$fileCase]["tmp_name"];
        $mimeType = mime_content_type($upTempFile);

        $dirNative = $dirToUploadUserFiles . 'native' . DIRECTORY_SEPARATOR;
        $fileNativeToRemove = $dirToUploadUserFiles . 'native' . DIRECTORY_SEPARATOR . $basenameOfFileToRemove;
        $fileNative = $dirNative . $upFilename;
        $dirThumb = $dirToUploadUserFiles . 'thumb' . DIRECTORY_SEPARATOR;
        $fileThumbToRemove = $dirToUploadUserFiles . 'thumb' . DIRECTORY_SEPARATOR . $basenameOfFileToRemove;
        $fileThumb = $dirThumb . $upFilename;



        // Check mime type
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return -2;
        }

        // Check file size (bigger than allowed)
        if($_FILES[$fileCase]["size"] > $maxAllowedFileSize) {
            return -3;
        }

        // Create dir for native file to save to
        if(!is_dir($dirNative)) {
            mkdir($dirNative, $defaultChmodForDir, true);
        }

        // Check if dir was created successfully for native file to save to
        if(!is_dir($dirNative)) {
            return -4;
        }

        // Move from temp to native dir
        if(!move_uploaded_file($upTempFile, $fileNative)) {
            return -5;
        }

        // Create dir for thumb file to save to

        if(!is_dir($dirThumb)) {
            mkdir($dirThumb, $defaultChmodForDir, true);
        }

        // Check if dir was created successfully for thumb file to save to
        if(!is_dir($dirThumb)) {
            return -6;
        }

        // Create thumb & move thumb
        if(!thumb($fileNative, $fileThumb, 60, 60, $upFilenameExt)) {
            return -7;
        }

        if(is_file($fileNativeToRemove)) {
            unlink($fileNativeToRemove);
        }
        if(is_file($fileThumbToRemove)) {
            unlink($fileThumbToRemove);
        }

        return array(
            'fileNative' => $fileNative,
            'fileThumb' => $fileThumb,
        );

    }

}