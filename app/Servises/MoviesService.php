<?php


namespace App\Servises;


use App\Models\Castmovies;
use App\Models\Countries;
use App\Models\Genres;
use App\Models\Language;
use App\Models\Movies;
use App\Models\MoviesCasts;
use App\Models\MoviesCountries;
use App\Models\MoviesGenres;
use App\Models\NewMovies;
use App\Models\OldMovies;

class MoviesService extends ParserCsvService
{
    private $collectionGenresId = [];
    private $collectionLanguageId = [];
    private $collectionCountriesId = [];
    private $collectionCastsId = [];


    public function saveColectionRelates(array $data)
    {

        /////////////////////////////////////////////
        $name = 'language';
        $arrayData = array_column($data, $name);
        $this->addCollection($name, $arrayData);

        ////////////////////////////////////////////////
        $name = 'genre';
        $arrayData = array_column($data, $name);
        $this->addCollection($name, $arrayData);

        ////////////////////////////////////////////////
        $name = 'country';
        $arrayData = array_column($data, $name);
        $this->addCollection($name, $arrayData);

        ////////////////////////////////////////////////
        $name = 'director';
        $arrayData = array_column($data, $name);

        ////////////////////////////////////////////////
        $name = 'writer';
        $arrayData =  array_merge($arrayData, array_column($data, $name));

        ////////////////////////////////////////////////
        $name = 'actors';
        $arrayData = array_merge($arrayData, array_column($data, $name));
        $name = 'casts';
        $this->addCollection($name, $arrayData);

        ////////////////////////////////////////////////

        $this->setMoviesCollection($data);
    }

    public function setMoviesCollection(array $data) : self
    {
        foreach ($data as $item) {
            $itemData = [];
//            $itemData['imdb_title_id']        = $item['imdb_title_id'];
            $itemData['title']                = $item['title'];
            $itemData['year']                 = $item['year'];
            $itemData['duration']             = $item['duration'];
            $itemData['description']          = $item['description'];
            $itemData['avg_vote']             = $item['avg_vote'];
            $itemData['votes']                = $item['votes'];
            $itemData['reviews_from_users']   = $item['reviews_from_users'];
            $itemData['reviews_from_critics'] = $item['reviews_from_critics'];


            if ($item['avg_vote'] >= 8.0) {
                $itemData['is_top'] = true;
            }

            /////////////////////////////////////////////
            $name = 'language';
            $valueData = $this->getIntersectCollectionIds($name, $item[$name]);
            $itemData[$name] = json_encode($valueData);

            /////////////////////////////////////////////
            $name = 'genre';
            $genreIds = $this->getIntersectCollectionIds($name, $item[$name]);
            $itemData[$name] = json_encode($genreIds);

            /////////////////////////////////////////////
            $name = 'country';
            $countryIds = $this->getIntersectCollectionIds($name, $item[$name]);
            $data = self::parseCsvStringToArray($item[$name]);
            foreach ($data as $countryName) {
                if (CountryServices::is_usa($countryName)) {
                    $itemData['is_usa'] = true;
                }
                if (CountryServices::is_europe($countryName)) {
                    $itemData['is_europe'] = true;
                }
            }
            $itemData[$name] = json_encode($countryIds);

            /////////////////////////////////////////////
            $name = 'director';
            $valueData = $this->getIntersectCollectionIds($name, $item[$name]);
            $itemData[$name] = json_encode($valueData);
            $castIds = $valueData;

            /////////////////////////////////////////////
            $name = 'writer';
            $valueData = $this->getIntersectCollectionIds($name, $item[$name]);
            $itemData[$name] = json_encode($valueData);
            $castIds = array_merge($castIds, $valueData);

            /////////////////////////////////////////////
            $name = 'actors';
            $valueData = $this->getIntersectCollectionIds($name, $item[$name]);
            $itemData[$name] = json_encode($valueData);
            $castIds = array_merge($castIds, $valueData);

            ////////////////////////////////////////////////
            $movies = Movies::firstOrCreate(['imdb_title_id' => $item['imdb_title_id']], $itemData);

            ////////////////////////////////////////////////////
            if ($movies->year >= 1980) {
                NewMovies::firstOrCreate([
                    'move_id' => $movies->id,
                    'year'    => $movies->year
                ]);

            } else {
                OldMovies::firstOrCreate([
                    'move_id' => $movies->id,
                    'year'    => $movies->year
                ]);
            }
            //////////////////////////////////////////////
            foreach ($genreIds as $Id) {
                MoviesGenres::firstOrCreate([
                    'move_id'  => $movies->id,
                    'genre_id' => $Id,
                ]);
            }
            //////////////////////////////////////////////
            foreach ($countryIds as $Id) {
                MoviesCountries::firstOrCreate([
                    'move_id'  => $movies->id,
                    'country_id' => $Id,
                ]);
            }
            //////////////////////////////////////////////
            foreach ($castIds as $Id) {
                MoviesCasts::firstOrCreate([
                    'move_id'  => $movies->id,
                    'castmovies_id' => $Id,
                ]);
            }

        }


        return $this;
    }

    public function setCollectionCastsId(array $data) : self
    {
        $this->collectionCastsId = $data;

        return $this;
    }

    public function setCollectionLanguageId(array $data) : self
    {
        $this->collectionLanguageId = $data;

        return $this;
    }

    public function setCollectionGenresId(array $data) : self
    {
        $this->collectionGenresId = $data;

        return $this;
    }

    public function setCollectionCountriesId(array $data) : self
    {
        $this->collectionCountriesId = $data;

        return $this;
    }

    public function addCollection(string $colectionName, array $data) : self
    {

        $colectionData = self::getSubArrayUnicue($data);

        switch ($colectionName) {

            case 'casts' :
                foreach ($colectionData as $name) {
                    $model = Castmovies::updateOrCreate([
                        'name' => $name
                    ]);
                    $collectionDataId[$model->id] = $name;
                }
                $this->setCollectionCastsId($collectionDataId);
                break;

            case 'language' :
                foreach ($colectionData as $name) {
                    $model = Language::updateOrCreate([
                        'name' => $name
                    ]);
                    $collectionDataId[$model->id] = $name;
                }
                $this->setCollectionLanguageId($collectionDataId);
                break;

            case 'genre' :
                foreach ($colectionData as $name) {

                    $model = Genres::updateOrCreate([
                        'name' => $name
                    ]);
                    $collectionDataId[$model->id] = $name;
                }
                $this->setCollectionGenresId($collectionDataId);
                break;

            case 'country' :
                foreach ($colectionData as $name) {

                    if (CountryServices::is_usa($name)) {
                        $dataCountry['is_usa'] = true;
                    }
                    if (CountryServices::is_europe($name)) {
                        $dataCountry['is_europe'] = true;
                    }

                    $dataCountry['name'] = $name;
                    $model = Countries::updateOrCreate($dataCountry);
                    $collectionDataId[$model->id] = $name;
                    unset($dataCountry);
                }
                $this->setCollectionCountriesId($collectionDataId);
                break;

        }

        return $this;
    }

    public function getIntersectCollectionIds(string $colectionName, string $str) : array
    {
        switch ($colectionName) {

            case 'actors' :
            case 'writer' :
            case 'director' :
                $collectionDataId = $this->collectionCastsId;
                break;

            case 'language' :
                $collectionDataId = $this->collectionLanguageId;
                break;

            case 'genre' :
                $collectionDataId = $this->collectionGenresId;
                break;

            case 'country' :
                $collectionDataId = $this->collectionCountriesId;
                break;
        }

        $data = self::parseCsvStringToArray($str);
        $result = array_intersect($collectionDataId, $data);
        $keyIds = array_keys($result);

        return $keyIds;
    }

}
