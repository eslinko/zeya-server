<?php

namespace app\models;

use Yii;
use yii\base\Model;

class UploadFiles extends Model {
  private $folder_name;
  private $file_info;
  private $tmp_name;

  public function __construct($folder_name, $file_info = '', $tmp_name = '') {
    $this->folder_name = $folder_name;
    $this->file_info = $file_info;
    $this->tmp_name = $tmp_name;
  }

  public function createFolderForRecruit() {
    return \yii\helpers\FileHelper::createDirectory(Yii::getAlias('@webroot').'/upload/recruits/' . $this->folder_name, 0775, true);
  }

  public function path() {
    return  Yii::getAlias('@webroot').'/upload/recruits/'.$this->folder_name."/".$this->file_info['filename'].".".$this->file_info['extension'];
  }

  public function upload() {
    $this->createFolderForRecruit();
    return move_uploaded_file($this->tmp_name, $this->path());
  }

  public function removeFile() {
    if (file_exists($this->path())) {
      unlink($this->path());
    }
    return true;
  }
}