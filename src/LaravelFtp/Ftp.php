<?php

namespace LaravelFtp;

class FTP
{
    private $connection;

    /**
     * public function __construct
     *
     *
     * @param    string    $host
     * @param    string    $user
     * @param    string    $pass
     * @return    boolean
     */
    public function __construct($host, $user, $pass, $port = 21)
    {
        if($this->connection = @ftp_connect($host, ($port != 21 )? $port : 21)){
            if( @ftp_login( $this->connection, $user, $pass )){
                ftp_pasv( $this->connection, true );
                return true;
            }
        }
        return false;
    }

    /**
     * public function ls
     *
     *
     * @param    string    $directory
     * @return   string
     */
    public function all( $directory = '' ){
        return ftp_nlist( $this->connection, $directory );
    }

    /**
     * public function size
     *
     *
     * @param    string    $file
     * @return   string
     */
    public function size( $file = '' ){
        return @ftp_size( $this->connection, $file );
    }

    /**
     * public function get
     *
     *
     * @param    string    $file
     * @return   string
     */
    public function get( $file = '' ){
        $tempHandle = fopen('php://temp', 'r+');
        $sizeFile = $this->size( $file );
        if( $sizeFile > 512000 ){ // 512 000 KB
            return "This file is too big to read, maximum filesize allowed to the browser: 512KB";
        }else{
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

    public function deleteFile( $pathFile ){
        return @ftp_delete($this->connection, str_replace("../", "", $pathFile ) );
    }

    public function createDirectory( $directory ){
        return @ftp_mkdir( $this->connection, $directory );
    }

    public function removeDirectory( $directory ){
        return @ftp_rmdir($this->connection, $directory );
    }
}