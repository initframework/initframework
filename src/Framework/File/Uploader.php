<?php
namespace Providers;

class Uploader
{
   private $targetFile;
   private $fileSize;
   private $fileType;
   private $tmpFileDir;
   private $errors = [];
   private $mimeTypesExtensionsMap = [
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
      "audio/midi"=>".mid",
      "audio/midi"=>".midi",
      "audio/x-midi"=>".mid",
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
      "text/html"=>".htm",
      "text/html"=>".html",
      "text/plain"=>".txt",
      "text/css"=>".css",
      "text/csv"=>".csv",
      "text/calendar"=>".ics",
      "text/javascript"=>".js",
      "text/javascript"=>".mjs",
      "text/xml"=>".xml",
      "application/xhtml+xml"=>".xhtml",
      "appliction/php"=>".php",
      "application/pdf"=>".pdf",
      "application/json"=>".json",
      "application/ld+json"=>".jsonld",
      "application/x-csh"=>".csh",
      "application/x-sh"=>".sh",
      "application/octet-stream"=>".bin",
   ];

   public function __construct(string $fieldname, string $newFilename = "", string $storageDir = "/storage/")
   {
      if (isset($_FILES[$fieldname]) && isset($fieldname)) {
         // get the target filename
         $sFilename = ($newFilename == "") ? $_FILES[$fieldname]['name'] : $newFilename . "." . strtolower(pathinfo($_FILES[$fieldname]['name'],PATHINFO_EXTENSION));

         // get the filesize
         $this->fileSize = $_FILES[$fieldname]['size'];

         // get the filetype
         $this->fileType = $_FILES[$fieldname]['type'];

         // get tmp file dir
         $this->tmpFileDir = $_FILES[$fieldname]['tmp_name'];

         // set the target file directory
         $this->targetFile = $storageDir . $sFilename;

      } else {
         $this->errors[] = "No file chosen.";
      }
   }

   // @param fieldLabel it is used for error reporting
   public function validate(string $fieldLabel = "", array $fileTypes, int $maxsize = null, $minsize = null)
   {
      // get the preferred file types
      if (isset($fileTypes) == false) {
         // log error
         $this->errors[] = "$fieldLabel file types are required.";
      }

      // check for errors
      if ($maxsize != null) {
         if ($this->fileSize > $maxsize) {
            // log error
            $reqFileSize = floor($maxsize / 1024);
            $this->errors[] = "$fieldLabel filesize must be less than $reqFileSize" . "kb.";
         }
      }

      if ($minsize != null) {
         if ($this->fileSize < $minsize) {
            // log error
            $reqFileSize = floor($this->fileSize / 1024);
            $this->errors[] = "$fieldLabel filesize must be larger than $reqFileSize" . "kb.";
         }
      }

      // compute valid extensions
      $validExt = "";
      foreach ($fileTypes as $key ) {
         $validExt .= ", " . $this->mimeTypesExtensionsMap[$key] ;
      }
      $validExt = substr($validExt, 2, strlen($validExt));
      
      if (in_array($this->fileType,$fileTypes) == false) {
         // log error
         $this->errors[] = "$fieldLabel has invalid file type, only {$validExt} files are allowed.";
      }

   }

   public function upload() : bool
   {
      try {
         $realStorageDir = ".." . $this->targetFile;
         $moved = move_uploaded_file($this->tmpFileDir,$realStorageDir);
      }
      catch (Exception $e) {
         $this->errors[] = $e->getMessage();
      }
      finally {
         return $moved;
      }
   }

   public function getStorageDir() : string
   {
      return $this->targetFile;
   }

   public function errors()
   {
      return (array_count_values($this->errors) > 0) ? $this->errors : false;
   }

   /**
    * Scalable Vector Graphics (SVG) - .svg,
    * JPEG images - .jpeg .jpg,
    * Portable Network Graphics - .png,
    * Graphics Interchange Format (GIF) - .gif,
    * Windows OS/2 Bitmap Graphics - .bmp,
    * Icon format - .ico,
    * Tagged Image File Format (TIFF) - .tif .tiff,
    * WEBP image - .webp
    */
   public function ImageTypes() : array
   {
      $this->validExt = ".svg, .jpg, .png, .gif, .bmp, .ico, .tif, .tiff, .webp";
      return ["image/svg+xml","image/jpeg","image/png","image/gif","image/bmp","image/vnd.microsoft.icon","image/tiff","image/webp"];
   }

   /**
    * AVI: Audio Video Interleave - .avi,
    * MPEG Video - .mpeg,
    * OGG video - .ogv,
    * MPEG transport stream - .ts,
    * 3GPP audio/video container - .3gp,
    * WEBM video - .webm,
    * 3GPP2 audio/video container - .3g2
    */
   public function VideoTypes() : array
   {
      $this->validExt = ".avi, .mpeg, .ogv, .ts, .3gp, .webm, .3g2";
      return ["video/x-msvideo","video/mpeg","video/mp4","video/ogg","video/mp2t","video/3gpp","video/webm","video/3gpp2"];
   }

   /**
    * AAC audio - .aac,
    * Waveform Audio Format - .wav,
    * OGG audio - .oga,
    * Musical Instrument Digital Interface (MIDI) - .mid .midi,
    * MP3 audio - .mp3,
    * WEBM audio - .weba,
    * 3GPP audio/video container - .3gp,
    * 3GPP2 audio/video container - .3g2
    */
   public function AudioTypes() : array
   {
      $this->validExt = ".aac, .wav, .oga, .mid, .midi, .mp3, .weba, .3gp, .3g2";
      return ["audio/aac","audio/wav","audio/ogg","audio/midi","audio/x-midi","audio/mpeg","audio/mp3","audio/webm","audio/3gpp","audio/3gpp2"];
   }

   /**
    * 7-zip archive - .7z,
    * ZIP archive - .zip,
    * Tape Archive (TAR) - .tar,
    * RAR archive - .rar,
    * Java Archive (JAR) - .jar,
    * GZip Compressed Archive - .gz,
    * Archive document (multiple files embedded) - .arc,
    * BZip archive - .bz,
    * BZip2 archive - .bz2,
    * Electronic publication (EPUB) - .epub
    */
   public function ArchiveTypes() : array
   {
      $this->validExt = ".7z, .zip, .tar, .rar, .jar, .gz, .arc, .bz, .bz2, .epub";
      return ["application/x-7z-compressed","application/zip","application/x-tar","application/x-rar-compressed","application/java-archive","application/gzip","application/x-freearc","application/x-bzip","application/x-bzip2","application/epub+zip"];
   }

   /**
    * Microsoft Word - .doc,
    * Microsoft Word (OpenXML) - .docx,
    * Microsoft Excel - .xls
    * Microsoft Excel (OpenXML) - .xlsx
    * Microsoft PowerPoint - .ptx
    * Microsoft PowerPoint (OpenXML) - .pptx,
    * Microsoft Visio - .vsd
    * OpenDocument text document - .odt,
    * OpenDocument spreadsheet document - .ods,
    * OpenDocument presentation document - .odp,
    * AbiWord document - .abw,
    * Apple Installer Package - .mpkg,
    * XML - .xml,
    * Electronic publication (EPUB) - .epub,
    * Small web format (SWF) or Adobe Flash document - .swf,
    * Adobe Portable Document Format (PDF) - .pdf,
    * XUL - .xul,
    * OGG - .ogx,
    * Amazon Kindle eBook format - .azw
    */
   public function DocumentTypes() : array
   {
      $this->validExt = ".doc, .docx, .xls, .xlsx, .ptx, .pptx, .vsd, .odt, .ods, .odp, .abw, .mpkg, .xml, .epub, .swf, .pdf, .xul, .ogx, .azw";
      return ["application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-excel","application/vnd.visio","application/vnd.openxmlformats-officedocument.presentationml.presentation","application/vnd.ms-powerpoint","application/vnd.oasis.opendocument.text","application/vnd.oasis.opendocument.spreadsheet","application/vnd.oasis.opendocument.presentation","application/msword","application/x-abiword","application/vnd.apple.installer+xml","application/xml","application/epub+zip","application/x-shockwave-flash","application/pdf","application/vnd.mozilla.xul+xml","application/ogg","application/vnd.amazon.ebook"];
   }

   /**
    * MS Embedded OpenType fonts - .eot,
    * OpenType font - .otf,
    * TrueType Font - .ttf,
    * Web Open Font Format (WOFF) - .woff,
    * Web Open Font Format (WOFF) - .woff2
    */
   public function FontTypes() : array
   {
      $this->validExt = ".eot, .otf, .ttf, .woff, .woff2";
      return ["application/vnd.ms-fontobject","font/otf","font/ttf","font/woff","font/woff2"];
   }

   /**
    * HyperText Markup Language (HTML) - .htm .html,
    * Text, (generally ASCII or ISO 8859-n) - .txt,
    * Cascading Style Sheets (CSS) - .css,
    * Comma-separated values (CSV) - .csv,
    * iCalendar format - .ics,
    * JavaScript - .js,
    * JavaScript module - .mjs,
    * XML - .xml,
    * XHTML - .xhtml,
    * Scalable Vector Graphics (SVG) - .svg,
    * Hypertext Preprocessor (Personal Home Page) - .php,
    * Adobe Portable Document Format (PDF) - .pdf,
    * JSON format - .json,
    * JSON-LD format - .jsonld,
    * C-Shell script - .csh,
    * Bourne shell script - .sh
    * Any kind of binary data - .bin
    */
   public function TextTypes() : array
   {
      $this->validExt = ".htm, .html, .txt, .css, .csv, .ics, .js, .mjs, .xml, .xhtml, .svg, .php, .pdf, .json, .jsonld, .csh, .sh, .bin";
      return ["text/html","text/plain","text/css","text/csv","text/calendar","text/javascript","text/xml","application/xhtml+xml","image/svg+xml","appliction/php","application/pdf","application/json","application/ld+json","application/x-csh","application/x-sh","application/octet-stream"];
   }

   public function delete(string $filename)
   {
      unlink(".." . $filename);
   }

}

?>