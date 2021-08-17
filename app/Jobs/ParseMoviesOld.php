<?php

namespace App\Jobs;

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
use App\Models\ParseStat;
use App\Servises\CountryServices;
use App\Servises\MoviesService;
use App\Servises\ParserCsvService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SplFileObject;

class ParseMoviesOld implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    private function arraySetValueIndex($name, $array)
    {
        foreach ($array as $item) {
            $data[][$name] = $item;
        }

        return $data;
    }


    public function handle(string $filePath)
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
        $moviesService->saveColectionRelates($data);


    }
}
