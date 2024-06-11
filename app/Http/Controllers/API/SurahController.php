<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Surah;
use DB;

class SurahController extends Controller
{
    public function __construct()
     {
          $this->middleware('auth:api');
     }
     
    public function getAllSurah()
    {

        $all_surah = Surah::join('tbl_verses', 'tbl_verses.surah_id', '=', 'tbl_surah.surah_id')
                            ->groupBy('surah_id')
                            ->select(DB::raw("tbl_surah.*, group_concat(tbl_verses.verse_id) as verse_id, group_concat(tbl_verses.verse_number) as verse_number"))
                            ->get();
        if(isset($all_surah))
        {
            $response['response'] = $all_surah;
            $response['result'] = 'success';
            return response()->json($response);

        }
        else
        {
            $response['response'] = "There is no record in Table";
            $response['result'] = 'failed';
            return response()->json($response);
        }
        
    }

}
