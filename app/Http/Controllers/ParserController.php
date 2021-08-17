<?php

namespace App\Http\Controllers;


use App\Servises\MoviesService;
use App\Servises\ParserCsvService;
use Illuminate\Http\Request;
use App\Jobs\ParseNames;
use App\Jobs\ParseMoviesOld;
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

        ParseMoviesOld::dispatch($filePath);
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
        $moviesService->saveColectionRelates($data);

    }

    public function testParseNames()
    {

        $str1 = "(function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n][\"callbacks\"].push(arguments)};if(!w[n][\"callbacks\"]){w[n][\"callbacks\"]=[]}w[n][\"loaded\"]=false;if(typeof c!==\"object\"){c=[c]}w[n][\"counters\"]=c;for(var i=0;i<c.length;i+=1){p(c[i])}function p(cId){var a=d.getElementsByTagName(\"script\")[0],s=d.createElement(\"script\"),i=function(){a.parentNode.insertBefore(s,a)};s.type=\"text/javascript\";s.async=true;s.src=\"https://mod.calltouch.ru/init.js?id=\"+cId;if(w.opera==\"[object Opera]\"){d.addEventListener(\"DOMContentLoaded\",i,false)}else{i()}}})(window,document,\"ct\",\"f0rn56g6\");";
        $str2 = "(function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n][\"callbacks\"].push(arguments)};if(!w[n][\"callbacks\"]){w[n][\"callbacks\"]=[]}w[n][\"loaded\"]=false;if(typeof c!==\"object\"){c=[c]}w[n][\"counters\"]=c;for(var i=0;i<c.length;i+=1){p(c[i])}function p(cId){var a=d.getElementsByTagName(\"script\")[0],s=d.createElement(\"script\"),i=function(){a.parentNode.insertBefore(s,a)};s.type=\"text/javascript\";s.async=true;s.src=\"https://mod.calltouch.ru/init.js?id=\"+cId;if(w.opera==\"[object Opera]\"){d.addEventListener(\"DOMContentLoaded\",i,false)}else{i()}}})(window,document,\"ct\",\"f0rn56g6\");";

        if ($str1 == $str2) {
            echo "одинаковые строки";
        } else {
            echo "не одинаковые строки";
        }
    }

}
