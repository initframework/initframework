<?php
namespace App\Services;
use Library\File\File;
use Library\Handler\IException;

class Upload
{

   private $validTypes = [
      // Image types
      "image/svg+xml"=>".svg",
      "image/jpeg"=>".jpg",
      "image/png"=>".png",
      "image/gif"=>".gif",
      "image/bmp"=>".bmp",
      "image/vnd.microsoft.icon"=>".ico",
      "image/tiff"=>".tiff",
      "image/webp"=>".webp",
      // Video types
      "video/x-msvideo"=>".avi",
      "video/mpeg"=>".mpeg",
      "video/mp4"=>".mp4",
      "video/ogg"=>".ogv",
      "video/mp2t"=>".ts",
      "video/3gpp"=>".3gp",
      "video/webm"=>".webm",
      "video/3gpp2"=>".3g2",
      // Audio types
      "audio/aac"=>".aac",
      "audio/wav"=>".wav",
      "audio/ogg"=>".oga",
      // "audio/midi"=>".mid",
      "audio/midi"=>".midi",
      // "audio/x-midi"=>".mid",
      "audio/x-midi"=>".midi",
      "audio/mpeg"=>".mpeg",
      "audio/mp3"=>".mp3",
      "audio/webm"=>".weba",
      "audio/3gpp"=>".3gp",
      "audio/3gpp2"=>".3gp2",
      // Zip types
      "application/x-7z-compressed"=>".7z",
      "application/zip"=>".zip",
      "application/x-tar"=>".tar",
      "application/x-rar-compressed"=>".rar",
      "application/java-archive"=>".jar",
      "application/gzip"=>".gz",
      "application/x-freearc"=>".arc",
      "application/x-bzip"=>".bz",
      "application/x-bzip2"=>".bz2",
      // Document types
      "application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>".docx",
      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>".xlsx",
      "application/vnd.ms-excel"=>".xls",
      "application/vnd.visio"=>".vsd",
      "application/vnd.openxmlformats-officedocument.presentationml.presentation"=>".pptx",
      "application/vnd.ms-powerpoint"=>".ppt",
      "application/vnd.oasis.opendocument.text"=>".odt",
      "application/vnd.oasis.opendocument.spreadsheet"=>".ods",
      "application/vnd.oasis.opendocument.presentation"=>".odp",
      "application/msword"=>".doc",
      "application/x-abiword"=>".abw",
      "application/vnd.apple.installer+xml"=>".mpkg",
      "application/epub+zip"=>".epub",
      "application/x-shockwave-flash"=>".swf",
      "application/vnd.mozilla.xul+xml"=>".xul",
      "application/ogg"=>".ogx",
      "application/vnd.amazon.ebook"=>".azw",
      // Font types
      "application/vnd.ms-fontobject"=>".eot",
      "font/otf"=>".otf",
      "font/ttf"=>".ttf",
      "font/woff"=>".woff",
      "font/woff2"=>".woff2",
      // Text types
      // "text/html"=>".htm",
      "text/html"=>".html",
      "text/plain"=>".txt",
      "text/css"=>".css",
      "text/csv"=>".csv",
      "text/calendar"=>".ics",
      "text/javascript"=>".js",
      // "text/javascript"=>".mjs",
      "text/xml"=>".xml",
      "application/xhtml+xml"=>".xhtml",
      "appliction/php"=>".php",
      "application/pdf"=>".pdf",
      "application/json"=>".json",
      "application/ld+json"=>".jsonld",
      "application/x-csh"=>".csh",
      "application/x-sh"=>".sh",
      "application/octet-stream"=>".bin"
   ];
   private $name;
   private $type;
   private $tmpfile;
   private $size;
   private $extension;
   private $field;
   private $errmsg;
   private $status = true;
   private $errors = [];
   private $uris = [];

   // Image types
   public function image(string $field, object $file, string $errmsg = "image not uploaded!", array $type = [ "image/svg+xml", "image/jpeg", "image/png", "image/gif", "image/bmp", "image/vnd.microsoft.icon", "image/tiff", "image/webp" ])
   {
      if ( $file != null && \in_array($file->type, $type) && \array_key_exists($file->type, $this->validTypes) ) {
         $this->name = $file->name;
         $this->type = $file->type;
         $this->tmpfile = $file->tmp_name;
         $this->size = $file->size;
         $this->extension = $this->validTypes[$file->type];
         $this->field = $field;
         $this->errmsg = $errmsg;
         $this->uris[$field] = '';
         $this->status = true;
      } else {
         $this->status = false;
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   public function max(int $size, string $unit = "Kb")
   {
      $size = $this->size($size, $unit);
      if ($this->status == false || $this->size > $size) {
         $this->errors[$this->field] = $this->errmsg;
      }
      return $this;
   }

   public function min(int $size, string $unit = "Kb")
   {
      $size = $this->size($size, $unit);
      if ($this->status == false || $this->size < $size) {
         $this->errors[$this->field] = $this->errmsg;
         $this->status = false;
      }
      return $this;
   }

   private function size(int $size, string $unit)
   {
      try {
         switch ($unit) {
            case 'Kb':
               $size = $size * 1024;
            break;
            case 'Mb':
               $size = $size * 1024 * 1024;
            break;
            case 'Gb':
               $size = $size * 1024 * 1024 * 1024;
            break;
            default:
               $size = $size;
               throw new IException("Invalid size unit!");
            break;
         }
      } catch (IException $ex) {
         $ex->handle();
      } finally {
         return $size;
      }
   }

   public function upload(string $path = "", string $filename = "")
   {
      try {
         if (is_dir(STORAGE_DIR . $path)) {
            if ($this->status = true) {
               $source = $this->tmpfile;
               $file = $filename == "" ? $this->name : $filename . $this->extension;
               $destination = STORAGE_DIR . $path . $file;
               $status = (new File())->upload($source, $destination);
               if ($status) {
                  $this->uris[$this->field] = STORAGE_PATH . $path . $file;
               } else {
                  $this->errors[$this->field] = $this->errmsg;
               }
            }
         } else {
            $this->errors[$this->field] = $this->errmsg;
            $this->status = false;
            throw new IException("$dir is not a directory!");
         }
      } catch (IException $ex) {
         $ex->handle();
      } finally {
         $this->reset();
         return $this;
      }
   }

   private function reset()
   {
      $this->name = '';
      $this->type = ''; #
      $this->tmpfile = '';
      $this->size = 0;
      $this->extension = '';
      $this->field = '';
      $this->errmsg = '';
      $this->status = true;
   }

   public function uri(string $field)
   {
      return $this->uris[$field];
   }

   public function errors()
   {
      return $this->errors;
   }

}