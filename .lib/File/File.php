<?php
namespace Library\File;
use Library\Handler\IException;

class File
{

   public function upload(string $tmpfile, string $destfile)
   {
      try {
         // $destfile = $destfile;
         if (file_exists($destfile)) {
            throw new IException("Error: $destfile already exists!");
         } else {
            return move_uploaded_file($tmpfile,$destfile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function wasUploaded(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return is_uploaded_file($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function size(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return filesize($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function type(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return filetype($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function lock(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return flock($storagefile, LOCK_SH);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function unlock(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return flock($storagefile, LOCK_SH);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function setPermission(string $file, int $permission = 0777)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            chmod($file, $permission);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function getPermission(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return fileperms($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function canWrite(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return is_writable($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function canExecute(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return is_executable($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function canRead(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return is_readable($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function stats(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return stat($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function move(string $src, string $dest)
   {
      try {
         $storagesrc = STORAGE_DIR . $src;
         $storagedest = STORAGE_DIR . $dest;
         if (!file_exists($storagesrc)) {
            throw new IException("Error: $src does not exist!");
         } else {
            copy($storagesrc, $storagedest);
            unlink($storagesrc);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function copy(string $src, string $dest)
   {
      try {
         $storagesrc = STORAGE_DIR . $src;
         $storagedest = STORAGE_DIR . $dest;
         if (!file_exists($storagesrc)) {
            throw new IException("Error: $src does not exist!");
         } else {
            copy($storagesrc, $storagedest);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function exists(string $file)
   {
      $storagefile = STORAGE_DIR . $file;
      return file_exists($storagefile);
   }

   public function delete(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            unlink($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function owner(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return fileowner($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function lastAccessed(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return fileatime($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function lastModified(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new IException("Error: $file does not exist!");
         } else {
            return filemtime($storagefile);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }
}
