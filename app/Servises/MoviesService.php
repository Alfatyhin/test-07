<?php


namespace App\Servises;


class MoviesService extends ParserCsvService
{
    private $collectionLanguage = [];

    public function setColectionRelates(array $data)
    {

        $langData = array_column($data, 'language');
        $data = self::getSubArrayUnicue($langData);

        $this->addCollectionLanguage($data);

    }

    public function addCollectionLanguage(array $data) : self
    {
        $this->collectionLanguage = $data;

        return $this;
    }


}
