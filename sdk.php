<?php // Download the SDKs as a zip file: sdk.php?p=android  or  sdk.php?p=flutter
// Source folders are inside /sdk, the same layout as the GitHub repository.
$map=['android'=>['tomatocoin-android-sdk',['tomatocoin-sdk'=>'tomatocoin-sdk','examples'=>'examples','README.md'=>'README.md','LICENSE'=>'LICENSE']],
      'flutter'=>['tomatocoin-flutter-sdk',['flutter/tomatocoin_sdk'=>'tomatocoin_sdk','LICENSE'=>'tomatocoin_sdk/LICENSE']],
      'repo'=>['tomatocoin-sdk-github',['.'=>'.']]];$p=(string)($_GET['p']??'');
if(!isset($map[$p])||!class_exists('ZipArchive')){http_response_code(isset($map[$p])?503:404);header('Content-Type: text/plain');exit(isset($map[$p])?'Zip is not available on this server. Download the sdk folder from the project files instead.':'Unknown download');}
[$name,$parts]=$map[$p];$base=realpath(__DIR__.'/sdk');if(!$base){http_response_code(404);exit('Not found');}
$tmp=tempnam(sys_get_temp_dir(),'tcsdk');$z=new ZipArchive();if($z->open($tmp,ZipArchive::OVERWRITE)!==true){http_response_code(500);exit('Could not create the file');}
foreach($parts as $src=>$dst){$path=realpath($base.'/'.$src);if(!$path||strpos($path,$base)!==0)continue;
 if(is_file($path)){$z->addFile($path,$name.'/'.$dst);continue;}
 foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,FilesystemIterator::SKIP_DOTS)) as $f){if($f->isFile()&&strpos($f->getPathname(),DIRECTORY_SEPARATOR.'build'.DIRECTORY_SEPARATOR)===false)$z->addFile($f->getPathname(),($dst==='.'?$name:$name.'/'.$dst).'/'.str_replace('\\','/',substr($f->getPathname(),strlen($path)+1)));}}
$z->close();header('Content-Type: application/zip');header('Content-Disposition: attachment; filename="'.$name.'.zip"');header('Content-Length: '.filesize($tmp));readfile($tmp);@unlink($tmp);
