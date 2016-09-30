<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Importer;


interface ImporterInterface
{

    /**
     * @param $file
     * @param int $pid
     * @return mixed
     */
    public function importFromFile($file, $pid = 0);

    /**
     * @param $url
     * @param int $pid
     * @return mixed
     */
    public function importFromUrl($url, $pid = 0);

    /**
     * @param $content
     * @param int $pid
     * @return mixed
     */
    public function import($content, $pid = 0);

}