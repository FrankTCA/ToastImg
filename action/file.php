<?php
class File {
    private $id = null;
    private $fileName = null;
    private $access_id = null;
    private $file_size = null;
    private $mime_type = null;
    private $timestamp = null;
    private $aka = null;

    public function set_vars($i, $f, $a, $s, $m, $t) {
        $this->id = $i;
        $this->fileName = $f;
        $this->access_id = $a;
        $this->file_size = $s;
        $this->mime_type = $m;
        $this->timestamp = $t;
    }

    public function set_aka($a) {
        $this->aka = $a;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_file_name() {
        return $this->fileName;
    }

    public function get_access_id() {
        return $this->access_id;
    }

    public function get_file_size() {
        return $this->file_size;
    }

    public function get_mime_type() {
        return $this->mime_type;
    }

    public function get_timestamp() {
        return $this->timestamp;
    }

    public function get_aka() {
        return $this->aka;
    }
}
