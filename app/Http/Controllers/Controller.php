<?php

namespace App\Http\Controllers;

use App\Models\ParseStat;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $parseStats = false;

        $parseStats = ParseStat::all()->sortDesc()->toArray();

        foreach ($parseStats as $k => $item) {
            $parseStats[$k]['created_at'] = Carbon::parse($item['created_at'])->format('Y-m-d H-m-i');
        }

        return view('index', [
            'parseStats' => $parseStats
        ]);
    }
}
