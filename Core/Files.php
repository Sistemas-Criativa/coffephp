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
    public final static function has($file, array $allowedExtensions = [])
    {
        if (isset($_FILES[$file])) {
            if (count($allowedExtensions) > 0) {
                return in_array(strtolower(self::getExt($_FILES[$file]['name'])), $allowedExtensions);
            }
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
            $path = ($location ? PUBLIC_DIR . DIRECTORY_SEPARATOR . $location : PUBLIC_DIR);
            $filename = ($randomFileName ? Utilities::generateString(($randomFileNameLength > 0 ? $randomFileNameLength : 50)) . date('YmdHis') : ($filename != '' ? $filename : $file['name'])) . '.' . $file['ext'];
            if(!file_exists($path)) {
                mkdir($path);
                $blankFile = fopen($path . DIRECTORY_SEPARATOR . "index.html", "w");
                fclose($blankFile);
            }
            $path = $path . DIRECTORY_SEPARATOR . $filename;
            if (move_uploaded_file($file["tmp_name"], $path)) {
                return ["complete" => $path, "relative" => ($location ? $location . DIRECTORY_SEPARATOR . $filename : $filename), "url" => route()['root'] . "/" .str_replace("\\", "/", ($location ? $location . DIRECTORY_SEPARATOR . $filename : $filename))];
            }
            return false;
        }
        return false;
    }
}
