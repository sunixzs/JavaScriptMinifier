# JavaScriptMinifier
A wrapper for Matthias Mullie's minify package

## Example usage

```
<?php
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
