<?php

use Illuminate\Support\Facades\File;

foreach (File::allFiles(__DIR__.'/vendor') as $routeFile) {
    require $routeFile->getPathname();
}


