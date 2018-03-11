<?php
class zlibFile extends ZipArchive{
    private $fileCounts = 0;
    private $fileName;
    private $fileNumLimit = 100;

    public function __construct($fileName = '') {
        $this->fileName = $fileName;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getFileCounts() {
        return $this->fileCounts;
    }

    public function getFileNumLimit() {
        return $this->fileNumLimit;
    }

    public function open($fileName = '', $flags = null) {
        if ($fileName != '') {
            $this->fileName = $fileName;
        }

        return parent::open($this->fileName, $flags);
    }

    public function reopen() {
        $thisFileName = $this->fileName;
        if (!$this->close()) {
            return false;
        }

        return parent::open($thisFileName, self::CREATE);
    }

    public function setFileNumLimit($fileNumLimit = 200) {
        if (empty($fileNumLimit) || !is_int($fileNumLimit) || $fileNumLimit < 1) {
            return false;
        }
        $this->fileNumLimit = $fileNumLimit;
        return true;
    }

    public function close() {
        $this->fileName = null;
        $this->fileCounts = 0;
        return parent::close();
    }

    public function addFiles($fileName, $localName = NULL, $start = 0, $length = 0) {
        if ($this->fileCounts >= $this->fileNumLimit) {
            $this->reopen();
        }

        if (is_null($localName)) {
            $localName = $fileName;
        }

        if ($length) {
            $content = file_get_contents($fileName, 0, null, $start);
        } else {
            $content = file_get_contents($fileName, 0, null, $start, $length);
        }

        $result = parent::addFromString($localName, $content);

        if ($result) {
            $this->fileCounts++;
        }
        return $result;
    }
}
