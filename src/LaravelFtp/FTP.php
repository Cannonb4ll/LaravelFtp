<?php

namespace LaravelFtp;

class FTP
{
    private $connection;
    private $mode;

    /**
     * public function __construct
     *
     *
     * @param    string $host
     * @param    string $user
     * @param    string $pass
     * @param int       $port
     * @param int       $mode
     *
     * @throws \Exception
     */
    public function __construct($host, $user, $pass, $port = 21, $mode = FTP_ASCII)
    {
        $this->mode = $mode;

        if ($this->connection = @ftp_connect($host, ($port != 21) ? $port : 21)) {

            try {
                @ftp_login($this->connection, $user, $pass);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            ftp_pasv($this->connection, true);
            return true;

        }

        throw new \Exception('Unable to establish connection to FTP server');
    }

    /**
     * public function all
     *
     *
     * @param    string $directory
     *
     * @return   string
     */
    public function all($directory = '')
    {
        $files = @ftp_nlist($this->connection, $directory);

        if (!$files) {
            $files = [];
        }

        return collect($files);
    }

    /**
     * public function size
     *
     *
     * @param    string $file
     *
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
     *
     * @return   string
     */
    public function get($file = '')
    {
        $tempHandle = fopen('php://temp', 'r+');
        $sizeFile = $this->size($file);
        if ($sizeFile > 512000) { // 512 000 KB
            return 'This file is too big to read, maximum filesize allowed to the browser: 512KB';
        } else {
            if (@ftp_fget($this->connection, $tempHandle, $file, $this->mode, 0)) {
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
     * public function download
     *
     *
     * @param string $localFile
     * @param string $remoteFile
     * @param int    $maxSize
     *
     * @return string
     */
    public function download($localFile = '', $remoteFile = '', $maxSize = 512000)
    {
        $sizeFile = $this->size($remoteFile);
        if ($sizeFile > $maxSize) { // 512 000 KB
            return 'This file is too big to read, maximum filesize allowed to the browser: 512KB';
        }

        if (@ftp_get($this->connection, $localFile, $remoteFile, $this->mode, 0)) {
            return true;
        }

        return false;
    }

    /**
     * public function save
     *
     * @param $file
     * @param $content
     *
     * @return bool
     */
    public function save($file, $content)
    {
        $tempHandle = fopen('php://temp', 'r+');
        fwrite($tempHandle, $content);
        rewind($tempHandle);

        if (@ftp_fput($this->connection, $file, $tempHandle, $this->mode, 0)) {
            return $this->get($file);
        } else {
            return false;
        }
    }

    /**
     * public function rename
     *
     * @param $old
     * @param $new
     *
     * @return bool
     */
    public function rename($old, $new)
    {
        return @ftp_rename($this->connection, $old, $new);
    }

    /**
     * public function directory
     *
     * @return string
     */
    public function directory()
    {
        return ftp_pwd($this->connection);
    }

    /**
     * public function createFile
     *
     * @param $directory
     * @param $name
     *
     * @return bool
     */
    public function createFile($directory, $name)
    {
        $temp = tmpfile();

        return @ftp_fput($this->connection, $directory . '/' . $name, $temp, $this->mode);
    }

    /**
     * public function deleteFile
     *
     * @param $file
     *
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
     *
     * @return boolean
     * @throws \Exception
     */
    public function createDirectory($directory)
    {
        @ftp_mkdir($this->connection, $directory);

        return true;
    }

    /**
     * Check if $directory actually is a directory
     *
     * @param $directory
     *
     * @return bool
     */
    public function isDirectory($directory)
    {
        if (ftp_nlist($this->connection, $directory)) {
            return true;
        }

        return false;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function createDirectoryRecursive($path)
    {
        $dir = explode("/", $path);
        $path = "";
        $ret = true;

        for ($i = 0; $i < count($dir); $i++) {
            $path .= "/" . $dir[$i];
            if (!@ftp_chdir($this->connection, $path)) {
                @ftp_chdir($this->connection, "/");
                if (!@ftp_mkdir($this->connection, $path)) {
                    $ret = false;
                    break;
                }
            }
        }
        return $ret;
    }

    /**
     * Public function deleteDirectory
     *
     * @param $directory
     *
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
     *
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

    /**
     * public function uploadFile
     *
     *
     * @param    string $fileToUpload
     * @param    string $fileUrl
     *
     * @return   boolean
     */
    public function uploadFile($fileToUpload, $fileUrl)
    {
        if (@ftp_put($this->connection, $fileToUpload, $fileUrl, $this->mode)) {
            return true;
        } else {
            return false;
        }
    }

}
