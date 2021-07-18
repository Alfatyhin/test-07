<?php

namespace App\Http\Controllers;


use App\Servises\MoviesService;
use App\Servises\ParserCsvService;
use Illuminate\Http\Request;
use App\Jobs\ParseNames;
use App\Jobs\ParseMovies;
use Illuminate\Support\Facades\Storage;
use SplFileObject;

class ParserController extends Controller
{

    public function parseNames()
    {
        ParseNames::dispatch()->delay(now()->addMinutes(1));
    }

    public function parseMovies()
    {

        $filePath = '../storage/app/dataCsv/movies-2.csv';

        ParseMovies::dispatch($filePath);
    }

    // для вывода тестовой информации логики в браузере
    // эта логика будет в jobs
    public function testParseMovies()
    {

        $filePath = '../storage/app/dataCsv/movies-2.csv';
        $file = new SplFileObject($filePath, 'r');

        $moviesService = new MoviesService();
        while (!$file->eof()) {
            $data = $file->fgetcsv();

            if (!empty($data[0])) {
                $moviesService->addCollectionLies($data);
                continue;
            }
        }

        $data = $moviesService->toArray();
        $moviesService->setColectionRelates($data);
        // и потом чтото типа обновить данные ->seveCollection которая будет сохранять в базу

    }

}
