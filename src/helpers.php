<?php

use LaravelFtp\FTP;

function ftp($host, $user, $pass, $port = 21)
{
    return new Ftp($host, $user, $pass, $port);
}