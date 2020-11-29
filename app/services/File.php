<?php
namespace Services;

class File
{

   public function upload(string $tmpfile, string $destfile)
   {
      try {
         // $destfile = $destfile;
         if (file_exists($destfile)) {
            throw new \Exception("Error: $destfile already exists!");
         } else {
            return move_uploaded_file($tmpfile,$destfile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function wasUploaded(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return is_uploaded_file($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function size(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return filesize($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function type(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return filetype($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function lock(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return flock($storagefile, LOCK_SH);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function unlock(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return flock($storagefile, LOCK_SH);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function setPermission(string $file, int $permission = 0777)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            chmod($file, $permission);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function getPermission(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return fileperms($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function canWrite(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return is_writable($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function canExecute(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return is_executable($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function canRead(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return is_readable($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function stats(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return stat($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function move(string $src, string $dest)
   {
      try {
         $storagesrc = STORAGE_DIR . $src;
         $storagedest = STORAGE_DIR . $dest;
         if (!file_exists($storagesrc)) {
            throw new \Exception("Error: $src does not exist!");
         } else {
            copy($storagesrc, $storagedest);
            unlink($storagesrc);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function copy(string $src, string $dest)
   {
      try {
         $storagesrc = STORAGE_DIR . $src;
         $storagedest = STORAGE_DIR . $dest;
         if (!file_exists($storagesrc)) {
            throw new \Exception("Error: $src does not exist!");
         } else {
            copy($storagesrc, $storagedest);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
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
            throw new \Exception("Error: $file does not exist!");
         } else {
            unlink($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function owner(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return fileowner($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function lastAccessed(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return fileatime($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }

   public function lastModified(string $file)
   {
      try {
         $storagefile = STORAGE_DIR . $file;
         if (!file_exists($storagefile)) {
            throw new \Exception("Error: $file does not exist!");
         } else {
            return filemtime($storagefile);
         }
      } catch (\Throwable $e) {
         trigger_error($e->getMessage());
      }
   }
}
