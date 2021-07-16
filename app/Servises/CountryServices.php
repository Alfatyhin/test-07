<?php


namespace App\Servises;


use Illuminate\Support\Facades\Storage;

class CountryServices
{
    protected static $instance;

    public function __construct()
    {
    }
    private function __clone()
    {
    }
    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function getEuropeNameCountries()
    {
        $dataFile = Storage::disk('local')->readStream('dataCsv/Countries-Europe.csv');
        $data = ParserCsv::parse($dataFile);

        foreach ($data as $item) {
            $name = $item['name'];
            $countryNames[$name] = $name;
        }

        return $countryNames;
    }

}
