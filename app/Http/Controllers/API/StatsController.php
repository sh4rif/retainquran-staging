<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppLog;
use App\Providers\RouteServiceProvider;
use App\Models\Stats;
use App\Models\Card_history;
use App\Models\Card;
use App\Models\User;
use App\Models\Daily_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class StatsController extends Controller
{


    protected function valdator(array $data)
    {
        return Validator::make($data, [
            'usr_id'            =>  ['required'],
            'current_datetime'  =>  ['required']

        ]);
    }

    public function daily_data()
    {
        try{
            AppLog::create([
                'message' => "Cron Logs",
                'line_no' => 36,
                'code' => Carbon::now(),
                'log' => "Controller : Stats Controller - Method : daily_data",
                'ip' => 'CRON',
            ]);
        } catch (\Exception $exception){  }

        //DB::enableQueryLog();
        $users = User::all();
        $mytime = Carbon::now();
        $date   = $mytime->toDateString();

        foreach ($users as $user) {

            $user_id = $user->id;

            $total_cards = Card::select(DB::raw("count(card_id) as total"))
                ->where('usr_id', '=', $user_id)
                ->where(function ($query) {
                    $date = Carbon::now()->toDateString();
                    $query->whereNull('due_at')
                        ->orWhereDate('due_at', '=', $date)
                        ->orWhereDate('updated_at', '=', $date);
                })->first();

            empty($total_cards) ? $total_cards = 0 : $total_cards = $total_cards->total;

            $performed = Card::select(DB::raw("count(card_id) as performed"))
                ->where('usr_id', '=', $user_id)
                ->whereDate('updated_at', '=', $date)->first();
            empty($performed) ? $performed = 0 : $performed = $performed->performed;


            $not_performed = Card::select(DB::raw("count(card_id) as total"))
                ->where('usr_id', '=', $user_id)
                ->where(function ($query) {
                    $date = Carbon::now()->toDateString();
                    $query->whereNull('due_at')
                        ->orWhereDate('due_at', '=', $date);
                })->first();
            empty($not_performed) ? $not_performed = 0 : $not_performed = $not_performed->total;


            if (($total_cards == $performed) && ($not_performed == 0) && ($total_cards != 0)) {
                $status = "All Performed";
            } elseif (($total_cards != $performed) && ($not_performed != 0) && ($performed != 0) && ($total_cards != 0)) {
                $status = "Pending";
            } elseif (($total_cards == $not_performed) && ($performed == 0)) {
                $status = "Not Performed";
            } else {
                $status = "Pending";
            }

            $data = [
                'dd_usr_id'        => $user_id,
                'dd_total_cards'   => $total_cards,
                'dd_performed'     => $performed,
                'dd_not_performed' => $not_performed,
                'dd_status'        => $status,
                'dd_date'          => $date


            ];

            $insertData = Daily_data::create($data);
            if ($insertData) {
                echo "worked for " . $user_id . "<br>";
            } else {
                echo "error for " . $user_id . "<br>";
            }
        }
    }

    public function get_stats(Request $request)
    {
        DB::enableQueryLog();
        // total reviews
        // average reviews
        // longest streak
        // recent streak
        // highest reviews

        $data = $request->only('usr_id', 'current_datetime');
        $usr_id = $data['usr_id'];
        $current_datetime = $data['current_datetime'];

        $response = $this->valdator($data);

        if ($response->fails()) {
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ", $errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else {
            $stats['total_reviews'] = Card_history::where('usr_id', $usr_id)->count('uch_id');
            $count_id = Card_history::where('usr_id', $usr_id)
                ->groupBy('date')
                ->get(array(DB::raw('Date(created_at) as date, count(uch_id) as Count')));
            if ($count_id) {
                $created_at = Card_history::where('usr_id', $usr_id)
                    ->orderBy('created_at', 'asc')
                    ->first('created_at');
                $start_date =  date('Y-m-d H:i:s', strtotime($created_at->created_at));
                $s_date = Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->subDay();
                $lastPerformedAt = $created_at = Card_history::where('usr_id', $usr_id)
                    ->orderBy('created_at', 'desc')
                    ->first('created_at');
                $lastPerformedAt =  date('Y-m-d H:i:s', strtotime($lastPerformedAt->created_at));
                $num_of_days = $s_date->diffInDays($lastPerformedAt);

                if (!empty($stats['total_reviews']) && !empty($num_of_days)) {
                    $stats['avg_reviews'] = ceil($stats['total_reviews'] / $num_of_days);
                }

                foreach ($count_id as $key) {
                    $count[] = $key->Count;
                }

                if (!empty($count)) {
                    $stats['highest_reviews']   = collect($count)->max();
                }
            } else {
                $stats['avg_reviews']       = '0';
                $stats['highest_reviews']   = '0';
            }



            $query_ls = "SELECT COUNT(*) max_streak FROM 
                        (SELECT x.*, CASE WHEN @prev = date - INTERVAL 1 DAY THEN @i:=@i ELSE @i:=@i+1 END i, @prev:=date FROM 
                            ( SELECT DISTINCT DATE(created_at) as date FROM tbl_usr_card_history 
                                where usr_id = $usr_id 
                            ) x
                         JOIN ( SELECT @prev:=null,@i:=0 ) vars ORDER BY date
                         ) a 
                        GROUP BY i ORDER BY max_streak DESC LIMIT 1";



            $query_cs = "WITH t1 as(
                        SELECT usr_id, DATE(created_at) as created_at, COUNT(*) as count from tbl_usr_card_history where usr_id= " . $usr_id . " GROUP BY DATE(created_at) ORDER BY DATE(created_at) DESC)
                        SELECT MAX(streak * 1) AS streak FROM
                        (
                          SELECT Date(created_at), DATEDIFF(NOW(), Date(created_at)),
                          @streak := IF( DATEDIFF(NOW(), Date(created_at)) - @days_diff > 1, @streak,
                             IF(@days_diff := DATEDIFF(NOW(), Date(created_at)), @streak+1, @streak+1))  AS streak
                          FROM t1 CROSS JOIN (SELECT @streak := 0, @days_diff := -1) AS vars
                          WHERE Date(created_at) <= NOW()
                        ) AS t";

            $long    = DB::select(DB::raw($query_ls));
            $current = DB::select(DB::raw($query_cs));
            if ($long) {
                $stats['longest_streak'] = $long[0]->max_streak;
            } else {
                $stats['longest_streak'] = 0;
            }
            if ($current) {
                $stats['current_streak'] = $current[0]->streak;
            } else {
                $stats['current_streak'] = 0;
            }

            if ($stats['longest_streak'] != 0 && $stats['current_streak'] == 0) {
                $stats['current_streak'] = 1;
            }


            $res = ['result' => 'success', 'response' => $stats];
            return response()->json($res, 200);
        }
    }
    public function forecast_graph(Request $request)
    {
        $data = $request->only('usr_id', 'current_datetime');
        $usr_id = $data['usr_id'];
        $current_datetime = $data['current_datetime'];

        $response = $this->valdator($data);

        if ($response->fails()) {
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ", $errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else {
            $mytime    = Carbon::createFromFormat('Y-m-d H:i:s', $current_datetime);
            //$mytime    = Carbon::now();
            //$tomorrow  = Carbon::tomorrow();
            $now       = $mytime->toDateTimeString();
            $nowdate   = $mytime->toDateString();
            $tomorrow  = $mytime->addDay()->toDateString();
            $mytime    = Carbon::createFromFormat('Y-m-d H:i:s', $current_datetime);
            $predate   = $mytime->addMonth(1)->toDateTimeString();

            $stats['forecast'] = Card::where('usr_id', $usr_id)
                ->whereBetween('due_at', [$now, $predate])
                ->orderBy('date', 'desc')
                ->groupBy('date')
                ->get(array(DB::raw('Date(due_at) as date, count(card_id) as cards')));

            $total_cards = Card::where('usr_id', $usr_id)
                ->where('due_at', '>', $now)
                ->select(DB::raw('count(card_id) as cards'))->first();

            if ($total_cards->cards != 0) {
                $created_at = Card_history::where('usr_id', $usr_id)
                    ->whereDate('due_date', '>', $nowdate)
                    ->orderBy('due_date', 'desc')
                    ->first('due_date');
                if ($created_at) {
                    $last_date =  date('Y-m-d H:i:s', strtotime($created_at->due_date));
                    $mytime    = Carbon::createFromFormat('Y-m-d H:i:s', $current_datetime)->subDay();
                    $num_of_days = $mytime->diffInDays($last_date);

                    if (!empty($total_cards) && !empty($num_of_days)) {
                        $stats['avg_reviews'] = ceil($total_cards->cards / $num_of_days);
                    }
                    /*foreach ($stats['forecast'] as $card) {
                    $count[] = $card->cards;
                    }
                    if(!empty($count))
                    {
                        $stats['avg_reviews'] = ceil(collect($count)->avg());
                    }*/
                    $stats['total_cards'] = $total_cards->cards;
                }
            } else {
                $stats['avg_reviews'] = 0;
                $stats['total_cards'] = 0;
            }

            $query = "select count(card_id) as cards from `tbl_cards` where `usr_id` = " . $usr_id . " and Date(`due_at`) = '" . $tomorrow . "'";
            $due_tomorrow = DB::select(DB::raw($query));

            if ($due_tomorrow) {
                $stats['due_tomorrow'] = $due_tomorrow[0]->cards;
            } else {
                $stats['due_tomorrow'] = 0;
            }

            $res = ['result' => 'success', 'response' => $stats];
            return response()->json($res, 200);
        }
    }

    public function get_heat_map(Request $request)
    {
        //DB::enableQueryLog();
        $data = $request->only('usr_id', 'current_datetime');
        $usr_id = $data['usr_id'];
        $current_datetime = $data['current_datetime'];

        $response = $this->valdator($data);

        if ($response->fails()) {
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ", $errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else {
            $created_at = User::where('id', $usr_id)->first('created_at');
            $stats['start_date'] =  date('Y-m-d', strtotime($created_at->created_at));

            $start_date = Card_history::where('usr_id', $usr_id)->orderBy('created_at', 'asc')->first();
            if (isset($start_date)) {
                $start = date('Y-m-d', strtotime($start_date->created_at));
                $mytime         = Carbon::createFromFormat('Y-m-d H:i:s', $current_datetime);
                $now_date       = $mytime->toDateString();
                $now            = $mytime->toDateTimeString();
                $predate        = $mytime->subMonth(1)->toDateTimeString();
                $predate_date   = $mytime->toDateString();

                $today =  Card_history::select(DB::raw("count(Distinct(card_id)) as cards"))
                    ->where('usr_id', '=', $usr_id)
                    ->whereDate('created_at', $now_date)->first();
                //dd(DB::getQueryLog());

                // $today = DB::select(DB::raw($query_today));

                if ($today) {
                    $today_cards = $today->cards;
                } else {
                    $today_cards = 0;
                }

                $stats['today_cards'] = $today_cards;
                //debug($stats,1);
                $heatmap_progress = Card_history::where('tbl_usr_card_history.usr_id', $usr_id)
                    ->whereBetween(DB::raw('CAST(created_at as DATE)'), [$predate, $now])
                    ->orderBy('date', 'desc')
                    ->groupBy('date')
                    ->get(array(DB::raw('Date(created_at) as date, count(Distinct(card_id)) as cards')));

                $heatmap_month = Card_history::where('tbl_usr_card_history.usr_id', $usr_id)
                    ->whereBetween(DB::raw('CAST(created_at as DATE)'), [$predate, $now])
                    ->orderBy('date', 'desc')
                    ->groupBy('date')
                    ->get(array(DB::raw('Date(created_at) as date, count(uch_id) as cards')));


                $heatmap_all = Card_history::where('usr_id', $usr_id)
                    ->whereBetween(DB::raw('CAST(created_at as DATE)'), [$start, $now])
                    ->orderBy('date', 'desc')
                    ->groupBy('date')
                    ->get(array(DB::raw('Date(created_at) as date, count(uch_id) as cards')))->toArray();
                //debug($heatmap_all,1);

                $period = CarbonPeriod::create($start, $now);

                $data_of_dates = array();
                // Iterate over the 
                if ($heatmap_all) {
                    $heatmap_array = array_reverse($heatmap_all);
                    foreach ($period as  $date) {
                        $all_date = $date->format('Y-m-d');

                        foreach ($heatmap_array as $key => $hmall) {
                            if (in_array($all_date, $hmall)) {
                                $data_of_dates[] = ['date' => $all_date, 'cards' => $hmall['cards']];
                                unset($heatmap_array[$key]);
                            } else {
                                $data_of_dates[] = ['date' => $all_date, 'cards' => '0'];
                            }
                            break;
                        }
                    }
                }



                $heatmap_all = array_reverse($data_of_dates);

                $heatmap_colors_month = Daily_data::where('dd_usr_id', '=', $usr_id)
                    ->whereBetween('tbl_daily_data.dd_date', [$predate_date, $now_date])
                    ->orderBy('dd_date', 'desc')
                    ->select('dd_date', 'dd_status')->get();
                $heatmap_colors = Daily_data::where('dd_usr_id', '=', $usr_id)
                    ->orderBy('dd_date', 'desc')
                    ->select('dd_date', 'dd_status')->get();
                empty($heatmap_colors_month) ? $stats['heatmap_colors_month'] = array() : $stats['heatmap_colors_month'] =  $heatmap_colors_month;
                empty($heatmap_colors) ? $stats['heatmap_colors']   = array() : $stats['heatmap_colors'] =  $heatmap_colors;

                $stats['heatmap_month']     = $heatmap_month;
                $stats['heatmap_progress']  = $heatmap_progress;
                $stats['heatmap_all']       = $heatmap_all;
            } else {
                $stats['today_cards']           = 0;
                $stats['heatmap_progress']      = array();
                $stats['heatmap_month']         = array();
                $stats['heatmap_all']           = array();
                $stats['heatmap_colors_month']  = array();
                $stats['heatmap_colors']        = array();
            }




            $res = ['result' => 'success', 'response' => $stats];
            return response()->json($res, 200);
        }
    }

    public function get_colors(Request $request)
    {
        $data = $request->only('usr_id', 'current_datetime');
        $usr_id = $data['usr_id'];
        $current_datetime = $data['current_datetime'];

        $mytime         = Carbon::createFromFormat('Y-m-d H:i:s', $current_datetime);
        $now_date       = $mytime->toDateString();
        $predate_date   = $mytime->toDateString();


        $heatmap_colors_month = Daily_data::where('dd_usr_id', '=', $usr_id)
            ->whereBetween('tbl_daily_data.dd_date', [$predate_date, $now_date])
            ->orderBy('dd_date', 'desc')
            ->select('dd_date', 'dd_status')->get();
        $heatmap_colors = Daily_data::where('dd_usr_id', '=', $usr_id)
            ->orderBy('dd_date', 'desc')
            ->select('dd_date', 'dd_status')->get();
        empty($heatmap_colors_month) ? $stats['heatmap_colors_month'] = array() : $stats['heatmap_colors_month'] =  $heatmap_colors_month;
        empty($heatmap_colors) ? $stats['heatmap_colors']   = array() : $stats['heatmap_colors'] =  $heatmap_colors;

        $res = ['result' => 'success', 'response' => $stats];
        return response()->json($res, 200);
    }
}
