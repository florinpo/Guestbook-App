<?php

namespace AppBundle\Utils\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadProcesor
{
  public function moveUploadedFile(UploadedFile $file, $uploadBasePath)
  {
    $originalName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();
    $filename = self::genUuid() . '_' . md5(microtime() . $originalName);
    $filename .= "." . $extension;

    $targetDir = $uploadBasePath;

    if (!is_dir($targetDir)) {
      $ret = mkdir($targetDir, umask(), true);
      if (!$ret) {
        throw new \RuntimeException("Could not create target directory to move temporary file into.");
      }
    }

    $file->move($targetDir, basename($filename));

    return str_replace($uploadBasePath . DIRECTORY_SEPARATOR, "", $filename);
  }

  /**
   * Generate Unique File Name for the File Upload
   *
   * @param int $len
   * @return string
   */
  public static function genUuid($len = 8) {
    $hex = md5(uniqid("", true));
    $pack = pack('H*', $hex);
    $tmp = base64_encode($pack);
    $uid = preg_replace("/[^A-Za-z0-9]/", "", $tmp);
    $len = max(4, min(128, $len));

    while (strlen($uid) < $len) {
      $uid .= self::genUuid(22);
    }

    $res = substr($uid, 0, $len);
    return $res;
  }
}