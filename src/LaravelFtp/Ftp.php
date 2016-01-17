<?php

namespace LaravelFtp;

use Illuminate\Support\Collection;

class FTP
{
    private $connection;

    /**
     * public function __construct
     *
     *
     * @param    string $host
     * @param    string $user
     * @param    string $pass
     * @return    boolean
     */
    public function __construct($host, $user, $pass, $port = 21)
    {
        if ($this->connection = @ftp_connect($host, ($port != 21) ? $port : 21)) {
            if (@ftp_login($this->connection, $user, $pass)) {
                ftp_pasv($this->connection, true);
                return true;
            }
        }
        return false;
    }

    /**
     * public function all
     *
     *
     * @param    string $directory
     * @return   string
     */
    public function all($directory = '')
    {
        return collect(ftp_nlist($this->connection, $directory));
    }

    /**
     * public function size
     *
     *
     * @param    string $file
     * @return   string
     */
    public function size($file = '')
    {
        return @ftp_size($this->connection, $file);
    }

    /**
     * public function get
     *
     *
     * @param    string $file
     * @return   string
     */
    public function get($file = '')
    {
        $tempHandle = fopen('php://temp', 'r+');
        $sizeFile = $this->size($file);
        if ($sizeFile > 512000) { // 512 000 KB
            return 'This file is too big to read, maximum filesize allowed to the browser: 512KB';
        } else {
            if (@ftp_fget($this->connection, $tempHandle, $file, FTP_ASCII, 0)) {
                rewind($tempHandle);
                $total = stream_get_contents($tempHandle);
                return $total;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * public function save
     *
     * @param $file
     * @param $content
     * @return bool
     */
    public function save($file, $content)
    {
        $tempHandle = fopen('php://temp', 'r+');
        fwrite($tempHandle, stripslashes($content));
        rewind($tempHandle);

        if (@ftp_fput($this->connection, $file, $tempHandle, FTP_ASCII, 0)) {
            return $this->get($file);
        } else {
            return false;
        }
    }

    /**
     * public function createFile
     *
     * @param $directory
     * @param $name
     * @return bool
     */
    public function createFile($directory, $name)
    {
        $temp = tmpfile();

        return @ftp_fput($this->connection, $directory . '/' . $name, $temp, FTP_ASCII);
    }

    /**
     * public function deleteFile
     *
     * @param $file
     * @return bool
     */
    public function deleteFile($file)
    {
        return @ftp_delete($this->connection, str_replace('../', '', $file));
    }

    /**
     * Public function createDirectory
     *
     * @param $directory
     * @return string
     */
    public function createDirectory($directory)
    {
        return @ftp_mkdir($this->connection, $directory);
    }

    /**
     * Public function deleteDirectory
     *
     * @param $directory
     * @return bool
     */
    public function deleteDirectory($directory)
    {
        if (!$directory) {
            return false;
        }

        $dst_dir = preg_replace('/\\/\$/', '', $directory);
        $ar_files = $this->all($dst_dir);

        if ($ar_files) {
            foreach ($ar_files as $st_file) {
                if (!empty($st_file)) {
                    $fl_file = $dst_dir . '/' . $st_file;

                    $getExtensions = pathinfo($fl_file);

                    if (!array_key_exists('extension', $getExtensions)) {
                        $this->deleteDirectory($fl_file); // Folder
                    } else {
                        $this->deleteFile($fl_file); // File
                    }
                }
            }

            $delete = @ftp_rmdir($this->connection, $dst_dir);

            if ($delete) {
                return true;
            }
        }

        return false;
    }

    /**
     * public function emptyDirectory
     *
     * @param $directory
     * @return bool
     */
    public function emptyDirectory($directory)
    {
        if (!$directory) {
            return false;
        }

        $dst_dir = preg_replace('/\\/\$/', '', $directory);
        $ar_files = $this->all($dst_dir);

        if ($ar_files) {
            foreach ($ar_files as $st_file) {
                if (!empty($st_file)) {
                    $fl_file = $dst_dir . '/' . $st_file;

                    $getExtensions = pathinfo($fl_file);

                    if (!array_key_exists('extension', $getExtensions)) {
                        $this->deleteDirectory($fl_file); // Folder
                    } else {
                        $this->deleteFile($fl_file); // File
                    }
                }
            }

            return true;
        }

        return false;
    }
}