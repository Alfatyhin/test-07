<?php


namespace App\Servises;


use Illuminate\Support\Facades\Storage;
use SplFileObject;

class CountryServices extends ParserCsvService
{

    public static function getEuropeNameCountries() : array
    {

        $filePath = '../storage/app/dataCsv/Countries-Europe.csv';
        $file = new SplFileObject($filePath, 'r');

        $csvService = new ParserCsvService();
        while (!$file->eof()) {
            $data = $file->fgetcsv();

            if (!empty($data[0])) {
                $csvService->addCollectionLies($data);
                continue;
            }
        }
        $data = $csvService->toArray();

        $name = 'name';
        $arrayData = array_column($data, $name);

        return $arrayData;
    }

    public static function is_usa(string $str) : bool
    {
        if (preg_match('/USA/', $str)) {
            return true;
        } else {
            return false;
        }
    }

    public static function is_europe(string $str) : bool
    {
        $europeCountries = array_flip(self::getEuropeNameCountries());

        if (isset($europeCountries[$str])) {
            return true;
        } else {
            return false;
        }
    }

}
