<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Translator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class TranslatorController extends Controller
{
    public function __construct()
     {
          $this->middleware('auth:api');
     }
     
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'language_id'    =>   ['required']

        ]);
    }

    public function get_translator_by_language(Request $request)
    {
        $data = $request->only('language_id');
        $response = $this->validator($data);

        if ($response->fails()){
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ", $errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else {
            $language_id = $data['language_id'];
            $trans = Translator::join('tbl_translations', 'tbl_translations.trans_id', '=', 'tbl_translators.trans_id')
                                ->select('tbl_translators.*', 'tbl_translations.*')
                                ->where('language_id', $language_id)
                                ->get();
            $res = ['result' => 'success', 'response' => $trans];
            return response()->json($res, 200);

        }
    }

    public function get_list_translators()
    {
        $all = Translator::join('tbl_translations', 'tbl_translations.trans_id', '=', 'tbl_translators.trans_id')
                                ->select('tbl_translators.*', 'tbl_translations.*')
                                ->get();
        if(isset($all))
        {
            $response['response'] = $all;
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
