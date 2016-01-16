# LaravelFtp
Laravel FTP wrapper for Laravel (or any other project that has composer)

This project is just called LaravelFtp because I am holding this package to the conventions Laravel itself uses, they are great and easy.

# Usage

## Installation

Require this package with composer:

```
composer require dennissmink/laravel-ftp dev-master
```

## Usage

Initiate a new FTP client like so:

```
$ftp = new Ftp($host, $user, $pass, $optionalport);
```

Then, check if your connection was succesfull:

```
if($ftp){
    // Do your stuff here, connection has been made.
}else{
    // Connection failed
}   
```

**Methods**

```
$ftp->all(); // Returns all the files of the users root files
$ftp->all('folder'); // Returns all the files of the directory folder
```