<?php


namespace App\Servises;


use App\Models\Casts;

class NamesService extends ParserCsvService
{
    public function setCollection(array $data)
    {
        foreach ($data as $item) {

            $castsData['imdb_name_id'] = $item['imdb_name_id'];
            $castsData['name'] = $item['name'];
            $castsData['height'] = $item['height'];
            $castsData['bio'] = $item['bio'];
            $castsData['date_of_birth'] = $item['date_of_birth'];
            $castsData['place_of_birth'] = $item['place_of_birth'];
            $castsData['children'] = $item['children'];

            if (CountryServices::is_usa($item['place_of_birth'])) {
                $castsData['is_usa'] = true;
            } else {
                $arrayPlace = ParserCsvService::parseCsvStringToArray($item['place_of_birth']);

                foreach ($arrayPlace as $placeName) {
                    if (CountryServices::is_europe($placeName)) {
                        $castsData['is_europe'] = true;
                    }
                }

            }

            $cast = Casts::create($castsData);
        }

    }
}
