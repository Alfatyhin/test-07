<?php

namespace App\Http\Controllers;

use App\Models\Castmovies;
use App\Models\Casts;
use App\Models\Countries;
use App\Models\Genres;
use App\Models\Language;
use App\Models\Movies;
use App\Models\MoviesCasts;
use App\Models\MoviesCountries;
use App\Models\MoviesGenres;
use App\Models\NewMovies;
use App\Models\OldMovies;
use App\Models\ParseStat;
use App\Servises\CountryServices;
use App\Servises\ParserCsv;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParserController extends Controller
{

    public function parseNames()
    {
        set_time_limit(600000);

        $timeStart = Carbon::now();

        $pachName = 'dataCsv/names.csv';
        $dataFile = Storage::disk('local')->readStream($pachName);

        $data = ParserCsv::parse($dataFile);
        $size = sizeof($data);
        $europeNameCountries = CountryServices::getInstance()->getEuropeNameCountries();

        $countDb = 0;
        foreach ($data as $k => $item) {

            $cast = Casts::where('imdb_name_id', $item['imdb_name_id'])->first();

            if (!$cast) {

                $castsData['imdb_name_id'] = $item['imdb_name_id'];
                $castsData['name'] = $item['name'];
                $castsData['height'] = $item['height'];
                $castsData['bio'] = $item['bio'];
                $castsData['date_of_birth'] = $item['date_of_birth'];
                $castsData['place_of_birth'] = $item['place_of_birth'];
                $castsData['children'] = $item['children'];

                if (preg_match('/USA/', $item['place_of_birth'])) {
                    $castsData['is_usa'] = true;
                } else {
                    $arrayPlace = explode(' ', $item['place_of_birth']);
                    $arrayIntest = array_intersect($arrayPlace, $europeNameCountries);

                    if ($arrayIntest) {
                        $castsData['is_europe'] = true;
                    }
                }

                $cast = Casts::create($castsData);
                $countDb++;

            }

            unset($castsData);
        }

        $timeDiff = $timeStart->diff(Carbon::now())->format('%i minutes, %s seconds');

        $parseStats = new ParseStat();
        $parseStats->file_stats = "$pachName - $size (string)";
        $parseStats->table_stats = "casts - $countDb (sring)";
        $parseStats->time_parse = "time work - $timeDiff";
        $parseStats->save();

        echo "<pre>";
        var_dump($parseStats->toArray());
    }

    public function parseMovies()
    {
        set_time_limit(600000);

        $timeStart = Carbon::now();

        $pachName = 'dataCsv/movies.csv';
        $dataFile = Storage::disk('local')->readStream($pachName);

        $data = ParserCsv::parse($dataFile);
        $size = sizeof($data);
        $europeNameCountries = CountryServices::getInstance()->getEuropeNameCountries();


        $data = array_slice($data, 0, 1000);
        $castmoveData = [];
        $genreData = [];
        $languageData = [];
        $countriesData = [];
        $countGenre = 0;
        $countMovies = 0;
        $countCountries = 0;
        $countLanguages = 0;
        $countMoviesGenres = 0;
        $countOldMovies = 0;
        $countNewMovies = 0;
        $countCastmovies = 0;
        $countMoviesCasts = 0;
        $countMoviesCountries = 0;
        foreach ($data as $k => $item) {

            $movies = Movies::where('imdb_title_id', $item['imdb_title_id'])->first();

            if (!$movies) {
                // создаем модель movies
                $movies = new Movies();
                $movies->imdb_title_id = $item['imdb_title_id'];
                $movies->title = $item['title'];
                $movies->year = $item['year'];
                $movies->duration = $item['duration'];
                $movies->description = $item['description'];
                $movies->avg_vote = $item['avg_vote'];
                $movies->votes = $item['votes'];
                $movies->reviews_from_users = $item['reviews_from_users'];
                $movies->reviews_from_critics = $item['reviews_from_critics'];


                if ($item['avg_vote'] >= 8.0) {
                    $movies->is_top = true;
                }

                $movies->save();
                $countMovies++;

                /////////////////////////////////////////
                /// countries
                $countryName = trim($item['country']);

                if (empty($countriesData[$countryName])) {

                    $country = Countries::where('name', $countryName)->first();
                    if (!$country) {
                        $country = new Countries();
                        $country->name = $countryName;

                        if ($countryName == 'USA') {
                            $country->is_usa = true;
                        } elseif (isset($europeNameCountries[$countryName])) {
                            $country->is_europe = true;
                        }

                        $country->save();
                        $countCountries++;
                    }
                    $moviesCountry = new MoviesCountries();
                    $moviesCountry->move_id = $movies->id;
                    $moviesCountry->country_id = $country->id;
                    $countMoviesCountries++;

                    $countriesData[$countryName] = $country;

                } else {
                    $country = $countriesData[$countryName];
                }

                ///////////////////////////////
                $movies->country = $country->id;

                if ($country->is_usa) {
                    $movies->is_usa = true;
                } elseif ($country->is_europe) {
                    $movies->is_europe = true;
                }


                /////////////////////////////////////////
                /// таблицы old new movies
                if ($movies->year >= 1980) {
                    $newMovies = new NewMovies();
                    $newMovies->move_id = $movies->id;
                    $newMovies->year = $movies->year;
                    $newMovies->save();
                    $countNewMovies++;
                } else {
                    $oldMovies = new OldMovies();
                    $oldMovies->move_id = $movies->id;
                    $oldMovies->year = $movies->year;
                    $oldMovies->save();
                    $countOldMovies++;
                }


                ////////////////////////////////////////
                /// language
                if (empty($item['language'])) {
                    $lang = 'None';
                } else {
                    $lang = $item['language'];
                }

                if (empty($languageData[$lang])) {
                    $language = Language::where('name', $lang)->first();
                    if (!$language) {
                        $language = new Language();
                        $language->name = $lang;
                        $language->save();
                        $countLanguages++;

                    }
                    $languageData[$lang] = $language;

                } else {
                    $language = $languageData[$lang];
                }

                $movies->language = $language->id;


                ///////////////////////////////////////////////////
                /// genres, movies-genres
                $genreArray = explode(',', $item['genre']);

                // получаем id жанров и попутно создаем записи в таблице жанров если их нет
                foreach ($genreArray as $genreName) {

                    $genreName = trim($genreName);

                    if (empty($genreData[$genreName])) {
                        $genre = Genres::where('name', $genreName)->first();
                        if (!$genre) {
                            $genre = new Genres();
                            $genre->name = $genreName;
                            $genre->save();
                            $countGenre++;
                        }

                        $genreData[$genreName] = $genre;

                    } else {
                        $genre = $genreData[$genreName];
                    }
                    $moveGenreArray[] = $genre->id;

                    $moviesGenre = new MoviesGenres();
                    $moviesGenre->genre_id = $genre->id;
                    $moviesGenre->move_id = $movies->id;
                    $moviesGenre->save();
                    $countMoviesGenres++;
                }
                $genreIds = json_encode($moveGenreArray);
                $movies->genre = $genreIds;
                unset($moveGenreArray);


                //////////////////////////////////////////////////////
                /// movies-casts
                /// не понятно есть ли связь с таблицей casts или надо делать отдельную,
                ///  так как в casts может быть несколько человек с таким именем
                /// и не понятно которого надо вписывать
                /// поэтому сделана для этого другая таблица castmovies

                if (empty($castmoveData[$item['director']])) {
                    $castmovies = Castmovies::where('name', $item['director'])->first();
                    if (!$castmovies) {
                        $castmovies = new Castmovies();
                        $castmovies->name = $item['director'];
                        $castmovies->save();
                        $countCastmovies++;
                    }
                    $castmoveData[$item['director']] = $castmovies;

                } else {
                    $castmovies = $castmoveData[$item['director']];
                }
                $movies->director = $castmovies->id;

                $moviesCast = new MoviesCasts();
                $moviesCast->move_id = $movies->id;
                $moviesCast->castmovies_id = $castmovies->id;
                $countMoviesCasts++;


                if (empty($castmoveData[$item['writer']])) {
                    $castmovies = Castmovies::where('name', $item['writer'])->first();
                    if (!$castmovies) {
                        $castmovies = new Castmovies();
                        $castmovies->name = $item['writer'];
                        $castmovies->save();
                        $countCastmovies++;
                    }
                    $castmoveData[$item['writer']] = $castmovies;

                } else {
                    $castmovies = $castmoveData[$item['writer']];
                }
                $movies->writer = $castmovies->id;

                $moviesCast = new MoviesCasts();
                $moviesCast->move_id = $movies->id;
                $moviesCast->castmovies_id = $castmovies->id;
                $countMoviesCasts++;

                $castmoviesArray = explode(',', $item['actors']);
                foreach ($castmoviesArray as $castName) {

                    $castName = trim($castName);

                    if (empty($castmoveData[$castName])) {

                        $castmovies = Castmovies::where('name', $castName)->first();
                        if (!$castmovies) {
                            $castmovies = new Castmovies();
                            $castmovies->name = $castName;
                            $castmovies->save();
                            $countCastmovies++;
                        }
                        $castmoveData[$castName] = $castmovies;

                    } else {
                        $castmovies = $castmoveData[$castName];
                    }
                    $actorsArray[] = $castmovies->id;

                    $moviesCast = new MoviesCasts();
                    $moviesCast->move_id = $movies->id;
                    $moviesCast->castmovies_id = $castmovies->id;
                    $countMoviesCasts++;
                }
                $movies->actors = json_encode($actorsArray);
                $movies->save();
                unset($actorsArray);

            }



        }

        $timeDiff = $timeStart->diff(Carbon::now())->format('%H hours, %i minutes, %s seconds');

        $countAll = $countGenre + $countMovies + $countOldMovies
            + $countNewMovies + $countCountries + $countLanguages
            + $countMoviesGenres + $countCastmovies + $countMoviesCasts
            + $countMoviesCountries;
        echo "<pre>";
        $parseStats = new ParseStat();
        $parseStats->file_stats = "$pachName - $size (string)";
        $parseStats->table_stats = "genre - $countGenre (sring), \n"
            . "movies - $countMovies (string), \n"
            . "old_movies - $countOldMovies (string), \n"
            . "new_movies - $countNewMovies (string), \n"
            . "countries - $countCountries (string), \n"
            . "language - $countLanguages (string), \n"
            . "movies_genre - $countMoviesGenres (string), \n"
            . "castmovies - $countCastmovies (string), \n"
            . "movies_casts - $countMoviesCasts (string), \n"
            . "movies_countries - $countMoviesCountries (string), \n"
            . "all table - $countAll (string), \n";
        $parseStats->time_parse = "time work - $timeDiff";
        $parseStats->save();

        var_dump($parseStats->toArray());
    }

}
