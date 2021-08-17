<?php

namespace App\Jobs;

use App\Servises\MoviesService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SplFileObject;

class ParseMovies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $service;

    /**
     * Create a new job instance.
     *
     * @param App\Models\Podcast $podcast
     * @return void
     */
    public function __construct(MoviesService $service)
    {
        $this->service = $service;
    }

    /**
     * @param array $row
     * @return InputService
     */
    public function getData(string $filePath): InputService
    {
        $file = new SplFileObject($filePath, 'r');

        while (!$file->eof()) {
            yield $file->fgetcsv();
        }
    }

    public function handle(string $filePath)
    {
        foreach ($this->getData as $value) {
            /* @var InputService $value */
            if ($value->isValid()) {
                $this->service->addCollectionLies($value);
            }
        }
    }
}
