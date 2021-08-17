<?php

namespace App\Jobs;

use App\Models\Casts;
use App\Models\ParseStat;
use App\Servises\CountryServices;
use App\Servises\NamesService;
use App\Servises\ParserCsvService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ParseNames implements ShouldQueue
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
    public function handle()
    {

        $pachName = 'dataCsv/names.csv';

        $filePath = '../storage/app/dataCsv/movies-2.csv';
        $file = new SplFileObject($filePath, 'r');

        $namesesService = new NamesService();
        while (!$file->eof()) {
            $data = $file->fgetcsv();

            if (!empty($data[0])) {
                $namesesService->addCollectionLies($data);
                continue;
            }
        }

        $data = $namesesService->toArray();
        $namesesService->setColectionRelates($data);

        $dataFile = Storage::disk('local')->readStream($pachName);

        $data = ParserCsvService::parse($dataFile);
        $size = sizeof($data);
        $europeNameCountries = CountryServices::getInstance()->getEuropeNameCountries();

        $countDb = 0;
        $data = array_slice($data, 0, 1000);
        foreach ($data as $k => $item) {

            $cast = Casts::where('imdb_name_id', $item['imdb_name_id'])->first();

            if (!$cast) {

                $countDb++;

            }

            unset($castsData);
        }

        $timeDiff = $timeStart->diff(Carbon::now())->format('%h hours, %i minutes, %s seconds');

        $parseStats = new ParseStat();
        $parseStats->file_stats = "$pachName - $size (string)";
        $parseStats->table_stats = "casts - $countDb (sring)";
        $parseStats->time_parse = "time work - $timeDiff";
        $parseStats->save();

    }
}
