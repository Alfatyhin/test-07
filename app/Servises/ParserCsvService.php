<?php


namespace App\Servises;


class ParserCsvService
{

    private $headers;
    private $collectionLines = [];

    public function __construct()
    {

    }

    public function addCollectionLies(array $data) : self // типизация - то чо должна возвращать функция??
    {
        if (!$this->headers) {
            $this->headers = $data;
        } else {
            $collection = $this->collectionLines;
            $collection[] = $data;
            $this->collectionLines = $collection;

        }

        return $this;
    }

    public function toArray() : array
    {
        foreach ($this->collectionLines as $item) {
            $itemData[] = array_combine($this->headers, $item);
        }

        return $itemData;
    }

    public static function parseCsvStringToArray(string $str) : array
    {
        if (empty($str)) {
            $str = 'None';
        }
        $str = str_replace(', ', ',', $str);
        $data = explode(',', $str);

        return $data;
    }

    public static function getSubArrayUnicue(array $data) : array
    {
        $valueData = array_unique($data);

        foreach ($valueData as $str) {

            if (empty($str)) {
                $str = 'None';
            }

            $strArray = self::parseCsvStringToArray($str);
            foreach ($strArray as $valName) {
                $valName = trim($valName);
                $subData[] = $valName;
            }
        }
        $subData = array_unique($subData);

        return $subData;
    }

    public function getCollectionLines() : array
    {
        return $this->collectionLines;
    }
    public function getHeaders()
    {
        return $this->headers;
    }



}
