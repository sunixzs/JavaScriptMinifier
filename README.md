# JavaScriptMinifier
A wrapper for Matthias Mullie's minify package

## Example usage

```
<?php
// filename MinifyJS.php
header('Content-Type: text/html; charset=utf-8');

// include autoloader
require_once __DIR__ .'/vendor/autoload.php';

// base folder for your stuff working in
define("DIR", __DIR__ . "/"); 

// make instance
$minifier = new Sunixzs\JavaScriptMinifier\Wrapper();

// example adding single file with auto suffix .min.js
// JavaScript/app.js will be minified to JavaScript/app.min.js
$minifier->addFile(
    // source file
    DIR . "JavaScript/app.js"
);

// example adding single file without auto suffix
// vendor/Company/Tool/src/tool.js will be minified to
// JavaScript/CompanyTool.min.js
$minifier->addFile(
    // source file
    DIR . "vendor/Company/Tool/src/tool.js",
    // target file
    "JavaScript/CompanyTool.min.js"
);

// example adding more than a single file
// which will be minified to one output file
$minifier->addFileCollection(
    // source files
    [
        DIR . "JavaScript/LIB.js",
        DIR . "JavaScript/LIB.Dom.js",
        DIR . "JavaScript/LIB.MyExtension.js",
    ],
    // target file
    DIR . "JavaScript/LIB.min.js"
);

// start minifying
$minifier->minify();
```

The output is something like this:

```
Computername:dir username$ php MinifyJS.php
- package 1
file    1.1 ...path/to/your/working/dir/JavaScript/app.js (10.000KB)
minified to .../to/your/working/dir/JavaScript/app.min.js (4.000KB)
saved 50.00% (10.0000KB - 6.000KB = 4.000KB)
- package 2
file    2.1 ...orking/dir/vendor/Company/Tool/src/tool.js (16.479KB)
minified to .../working/dir/JavaScript/CompanyTool.min.js (7.170KB)
saved 56.49% (16.479KB - 9.310KB = 7.170KB)
- package 3
file    3.1 ...path/to/your/working/dir/JavaScript/LIB.js (7.144KB)
file    3.2 .../to/your/working/dir/JavaScript/LIB.Dom.js (1.005KB)
file    3.3 .../working/dir/JavaScript/LIB.MyExtension.js (13.798KB)
minified to .../to/your/working/dir/JavaScript/LIB.min.js (15.008KB)
saved 31.61% (21.947KB - 6.939KB = 15.008KB)
- total:
overall saved 45.94% (48.426KB - 22.248KB = 26.178KB)
- stats:
minifying took 0.054 seconds
...and saved 2 seconds with edge
...and saved 0.776 seconds with 3G
```

## composer

Set something like this in your main composer.json:

```
{
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "sunixzs/JavaScriptMinifier",
                "version": "master",
                "source": {
                    "url": "https://github.com/sunixzs/JavaScriptMinifier.git",
                    "type": "git",
                    "reference": "master"
                }
            }
        }
    ],
    "require": {
        "matthiasmullie/minify": "1.3.60",
        "sunixzs/JavaScriptMinifier": "master@dev",
    },
    "autoload": {
        "psr-4": {
            "Sunixzs\\JavaScriptMinifier\\": "vendor/sunixzs/JavaScriptMinifier/src/"
        }
    }
}
```
