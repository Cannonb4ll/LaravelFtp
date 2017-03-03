<?php

use LaravelFtp\FTP;

function ftp($host, $user, $pass, $port = 21)
{
    return new FTP($host, $user, $pass, $port);
}