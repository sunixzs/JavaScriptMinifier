<?php
namespace Sunixzs\JavaScriptMinifier;

use MatthiasMullie\Minify;

class JavaScriptMinifier
{
    /**
     * JavaScript-Files to minify
     *
     * @var array
     */
    protected $files = [];

    /**
     * Undocumented variable
     *
     * @var integer
     */
    protected $totalFilesize = 0;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    protected $totalMinifiedFilesize = 0;

    /**
     * adds a file to minify
     *
     * @param string $filepath
     * @param string $minifiedFile
     * @throws Exception if file was not found
     * @return void
     */
    public function addFile($filepath, $minifiedFile = "")
    {
        if (!is_file($filepath)) {
            throw new Exception("File " . $filepath . " not found!");
        }
        if ($minifiedFile) {
            $this->files[$minifiedFile] = array($filepath);
        } else {
            $this->files[] = array($filepath);
        }
    }

    /**
     * adds a file collection to minify
     *
     * @param array $filesToMinify
     * @param string $minifiedFile
     * @throws Exception if a file was not found
     * @return void
     */
    public function addFileCollection($filesToMinify, $minifiedFile)
    {
        foreach ($filesToMinify as $fileToMinify) {
            if (!is_file($fileToMinify)) {
                throw new Exception("File " . $fileToMinify . " not found!");
            }
            $this->files[$minifiedFile] = $filesToMinify;
        }
    }

    /**
     * Converts bytes to a human readable value
     *
     * @param integer $bytes
     * @param integer $decimals
     * @return void
     */
    protected function humanFilesize($bytes, $decimals = 3)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        $unit = @$sz[$factor];
        if ($unit !== "B") {
            $unit .= "B";
        } else {
            $decimals = 0;
        }
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $unit;
    }

    /**
     * returns days, hours, minutes, seconds in a human readable form.
     *
     * @param integer|float $seconds
     * @return void
     */
    protected function humanReadableSeconds($seconds)
    {
        $secondsInt = (integer) $seconds;
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$secondsInt");
        if ($secondsInt > 3600 * 24) {
            return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
        } elseif ($secondsInt > 3600) {
            return $dtF->diff($dtT)->format('%h hours, %i minutes and %s seconds');
        } elseif ($secondsInt > 60) {
            return $dtF->diff($dtT)->format('%i minutes and %s seconds');
        } elseif ($secondsInt > 2) {
            return $dtF->diff($dtT)->format('%s seconds');
        } else {
            $milliseconds = substr(strstr($seconds, '.'), 0, 4);
            return $dtF->diff($dtT)->format('%s') . "$milliseconds seconds";
        }
    }

    /**
     * Undocumented function
     *
     * @param string $str
     * @param integer $length
     * @return void
     */
    protected function shortenedName($str, $length = 40)
    {
        return strlen($str) > $length ? "..." . substr($str, ($length - 3) * -1) : $str;
    }

    /**
     * @var array
     */
    protected $out = [];

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function initOut()
    {
        $this->out = [];
    }

    /**
     * Undocumented function
     *
     * @param string $str
     * @return void
     */
    protected function addOut($str)
    {
        $this->out[] = $str;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function out()
    {
        echo implode(PHP_EOL, $this->out);
    }

    /**
     * Processes all files and writes the minified version to disk
     *
     * @return void
     */
    public function minify()
    {
        $time_start = microtime(true);
        $this->initOut();
        foreach ($this->files as $minifiedFile => $files) {

            // define output file
            if (!is_string($minifiedFile) || is_numeric($minifiedFile)) {
                $minifiedFile = substr($files[0], 0, -3) . ".min.js";
            }

            $collectionFilesize = 0;

            $f = 0;
            foreach ($files as $file) {
                if ($f === 0) {
                    $minifier = new Minify\JS($file);
                } else {
                    $minifier->add($file);
                }

                $filesize = filesize($file);
                $collectionFilesize += $filesize;
                $this->totalFilesize += $filesize;

                $filesizeHuman = $this->humanFilesize($filesize);
                $fileShortened = $this->shortenedName($file, 70);

                $filenum = str_replace('~', ' ', str_pad((string) ($f + 1), 6, "~", STR_PAD_LEFT));

                $this->addOut(PHP_EOL . "file ${filenum} ${fileShortened} (${filesizeHuman})");

                $f++;
            }
            
            $minifier->minify($minifiedFile);

            // get filesizes
            $minifiedFilesize = filesize($minifiedFile);
            
            // add them to total
            $this->totalMinifiedFilesize += $minifiedFilesize;

            // create human readable values
            $collectionFilesizeHuman = $this->humanFilesize($collectionFilesize);
            $minifiedFilesizeHuman = $this->humanFilesize($minifiedFilesize);

            // calculate percent and saved size
            $savedPercent = number_format(100 / $collectionFilesize * ($collectionFilesize - $minifiedFilesize), 2);
            $savedFilesizeHuman = $this->humanFilesize($collectionFilesize - $minifiedFilesize);

            // we don't need the full path. Shorten the path at the beginning
            $minifiedFileShortened = $this->shortenedName($minifiedFile, 70);

            // show the result
            $this->addOut(PHP_EOL . "minified to ${minifiedFileShortened} (${minifiedFilesizeHuman})");
            $this->addOut("saved ${savedPercent}% (${collectionFilesizeHuman} - ${savedFilesizeHuman} = ${minifiedFilesizeHuman})");
        }

        // calculate some totals
        $savedTotalPercent = number_format(100 / $this->totalFilesize * ($this->totalFilesize - $this->totalMinifiedFilesize), 2);
        $totalFilesizeHuman = $this->humanFilesize($this->totalFilesize);
        $savedTotalFilesizeHuman = $this->humanFilesize($this->totalFilesize - $this->totalMinifiedFilesize);
        $totalMinifiedFilesizeHuman = $this->humanFilesize($this->totalMinifiedFilesize);

        // calculate saved edge time ()
        $savedEdgeTime = ($this->totalFilesize - $this->totalMinifiedFilesize) * 8 / 1024 / 120;
        $savedEdgeTimeHuman = $this->humanReadableSeconds($savedEdgeTime);

        // calculate saved 3G time
        $saved3GTime = ($this->totalFilesize - $this->totalMinifiedFilesize) * 8 / 384 / 1024;
        $saved3GTimeHuman = $this->humanReadableSeconds($saved3GTime);
        
        // ... and show them
        $this->addOut("overall saved ${savedTotalPercent}% (${totalFilesizeHuman} - ${savedTotalFilesizeHuman} = ${totalMinifiedFilesizeHuman})");

        $time_end = microtime(true);

        $time = number_format($time_end - $time_start, 3);
 
        $this->addOut(PHP_EOL . "minifying took ${time} seconds");
        $this->addOut("...and saved ${savedEdgeTimeHuman} with edge");
        $this->addOut("...and saved ${saved3GTimeHuman} with 3G" . PHP_EOL);

        $this->out();
    }
}
