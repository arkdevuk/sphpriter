<?php

use Garden\Cli\Cli;

include './vendor/autoload.php';


$cli = new Cli();

$cli->description('Create sprite sheet from group of PNGs')
    ->opt('folder:f', 'Folder containing PNGs', true)
    ->opt('output:o', 'Output file', true)
    ->opt('col:c', 'cols in sprite sheet.', true, 'integer')
    ;

// Parse and return cli args.
$args = $cli->parse($argv, true);

$folderInput = $args->getOpt('folder');
$output = $args->getOpt('output');
$cols = (int)$args->getOpt('col');

// CREATING THE STACK OF IMAGE
$files = array_diff(scandir($folderInput), array('.', '..'));
sort($files);
$stack = [];
foreach($files as $file){
    $fpath = $folderInput . DIRECTORY_SEPARATOR . $file;
    if(is_file($fpath) && !is_dir($fpath)){
        $stack[] = $fpath;
    }
}
unset($files);

if(count($stack) <= 0){
    exit('No image in queue');
}

// GETTING THE FIRST IMAGE DIMENSION
$image = new Imagick($stack[0]);
$d = $image->getImageGeometry();
$WIDTH = $d['width'];
$HEIGHT = $d['height'];
unset($image,$d);

// CREATING CANVAS
$canvas_w = $WIDTH*$cols;
$canvas_h = $HEIGHT*(ceil(count($stack)/$cols));

$canvas = new Imagick();
$canvas->newImage($canvas_w,$canvas_h, 'transparent');
$canvas->setImageFormat('png');

var_dump($canvas_w,$canvas_h);

echo 'CANVAS : '.$canvas_w.':'.$canvas_h."\n\n";


$composite = imagick::COMPOSITE_DEFAULT ;

$actualCol = 0;
$actualRow = 0;
$i = 0;
foreach($stack as $sprite){

    $spriteImagick = new Imagick($sprite);

    if($actualCol > ($cols-1)){
        $actualCol = 0;
        $actualRow++;
    }

    $x = $WIDTH*$actualCol;
    $y = $HEIGHT*$actualRow;
    $canvas->compositeImage($spriteImagick, $composite, $x, $y);

    echo "Image " . $i . " : {$x}:{$y}\n";

    unset($spriteImagick);
    $actualCol++;$i++;
}


$canvas->writeImage($output);














