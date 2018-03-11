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

    public function addFiles($fileName) {
        if ($this->fileCounts >= $this->fileNumLimit) {
            $this->reopen();
        }

        if (func_num_args() > 1) {
            $localName = func_get_arg(1);
            $result = parent::addFile($fileName, $localName);
            if ($result) {
                $this->fileCounts++;
            }
            return $result;
        }
        $result = parent::addFile($fileName);
        if ($result) {
            $this->fileCounts++;
        }
        return $result;
    }
}
