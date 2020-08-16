<?php

namespace Core;

/** Control http status */
class Files
{
    private $files = [];

    private static function getExt($filename)
    {
        $ext = explode('.', $filename);
        return $ext[count($ext) - 1];
    }
    /**
     * Return all files
     */
    public final static function has($file)
    {
        if (isset($_FILES[$file])) {
            return true;
        }
        return false;
    }

    /**
     * get a file
     */
    public final static function getUploadedFile($file)
    {
        if (is_array($file)) {
            foreach ($file as $item) {
                if (self::has($item)) {
                    $_FILES[$file]['ext'] = self::getExt($_FILES[$file]['name']);
                    $temp[] = $_FILES[$file];
                }
            }
            return $temp;
        } else {
            if (self::has($file)) {
                $_FILES[$file]['ext'] = self::getExt($_FILES[$file]['name']);
                return $_FILES[$file];
            }
            return null;
        }
    }

    /**
     * Move a file to specify local
     */
    public final static function moveUploadedFile($file, $randomFileName = true, $randomFileNameLength = 0, $filename = '', $location = null)
    {
        if (self::has($file)) {
            $file = self::getUploadedFile($file);
            $path = ($location ? $location : PUBLIC_DIR);
            $filename = ($randomFileName ? Utilities::generateString(($randomFileNameLength > 0 ? $randomFileNameLength : 50)) . date('YmdHis') : ($filename != '' ? $filename : $file['name'])) . '.' . $file['ext'];
            $path = $path . DIRECTORY_SEPARATOR . $filename;
            if(move_uploaded_file($file["tmp_name"], $path)) {
                return $path;
            }
            return false;
        }
        return false;
    }
}
