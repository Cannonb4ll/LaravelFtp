# LaravelFtp
Laravel FTP wrapper for Laravel (or any other project that has composer)

This project is just called LaravelFtp because I am holding this package to the conventions Laravel itself uses, they are great and easy.

It also utilises the collection package, files are returned as collection so you can use the Collection function from Laravel.

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

> Display files
```
$ftp->all(); // Returns all the files of the users root files
$ftp->all('folder'); // Returns all the files of the directory folder

$ftp->get('filename.txt') // Returns the content of the file, can also be like: get('directory/filename.txt')

```


> Create files/directories
```
$ftp->createDirectory('directory name');
```

> Delete files/directories
```
$ftp->deleteDirectory('directory name');
```