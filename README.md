# LaravelFtp
Laravel FTP client (or any other project that utilises has composer)

This project is just called LaravelFtp because I am holding this package to the conventions Laravel itself uses, they are great and easy.

It also utilises the collection package, files are returned as collection so you can use the Collection functions from Laravel.

# Usage

## Installation

Require this package with composer:

```
composer require dennissmink/laravel-ftp dev-master
```

## Usage

Initiate a new FTP client like so:

```
$ftp = ftp($host, $user, $pass, $optionalport);
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

*General file functions*
```
$ftp->all(); // Returns all the files of the users root files (Collection)
$ftp->all('folder'); // Returns all the files of the directory folder (Collection)

$ftp->get('filename.txt') // Returns the content of the file, can also be: get('directory/filename.txt')
$ftp->save('filename.txt', 'content file'); // Save file 'filename.txt' with content 'content file', returns content if success
```


*Create files/directories*
```
$ftp->createFile('filename.txt'); // Creates a file with the name 'filename.txt'
$ftp->createDirectory('directory name'); // Creates a directory 'directory name'
```

*Delete files/directories*
```
$ftp->deleteFile('filename.txt'); // Deletes a file with the name 'filename.txt'
$ftp->deleteDirectory('directory name'); // Removes a directory with the name 'directory name' (And its contents..)
$ftp->emptyDirectory('directory name'); // Emptys a directory but keeps the directory itself
```

## Links

Packagist: https://packagist.org/packages/dennissmink/laravel-ftp