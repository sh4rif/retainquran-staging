<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Excel;

class TranslationController extends Controller
{
    public function __construct()
     {
          $this->middleware('auth:api');
     }

     public function get_disk_storage()
     {
        $path = Storage_path();
        $df = round(disk_free_space($path) / 1024 / 1024 );
        print("Free space: $df MB");
     }

    
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'trans_id'    =>   ['required']

        ]);
    }

    protected function validator_surah(array $data)
    {
        return Validator::make($data, [
            'trans_id'    =>   ['required'],
            'surah_id'    =>   ['required']

        ]);
    }

    public function get_translation(Request $request)
    {

        $data = $request->only('trans_id');
        $response = $this->validator($data);

        if ($response->fails()) {
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ", $errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else
        {
            // checking db for data
            $filedata = Translation::find($data['trans_id']);
            if($filedata)
            {
                $file_data = $filedata->toArray();
                $path = "https://storage.googleapis.com/retain-quran/translations/". $file_data['translation_file_name'];
;

                if(file_exists($path))
                {
                    $data = Excel::toArray(new TranslationController,$path); // reading the file 
                    foreach($data[0] as $key => $value)
                    {
                        $insert_data[] = array(
                            'id'  => $value['0'],
                            'sura'   => $value['1'],
                            'ayat'   => $value['2'],
                            'translation'    => $value['3']
                        );
                    }
                    $result = 'success';
                    $res = ['result' => $result, 'response' => $insert_data];
                    return response()->json($res, 200);
                }else
                {
                    $result = 'failed';
                    $res = ['result' => $result, 'response' => 'There is no file in the Directiry'];
                    return response()->json($res, 200);
                }
            }
            else
            {
               $result = 'failed';
               $res = ['result' => $result, 'response' => 'There is no record in the Database'];
               return response()->json($res, 200);
           }
       }
    }



   public function get_translation_by_surah(Request $request)
    {

        $data = $request->only('trans_id','surah_id');
        $response = $this->validator_surah($data);

        if ($response->fails()) {
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ", $errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else
        {
            // checking db for data
            $filedata = Translation::find($data['trans_id']);
            if($filedata)
            {
                $file_data = $filedata->toArray();
                //get path of local directory
                $file_path = $file_data['translation_dir_name'] . '/' . $file_data['translation_file_name'];
                $path = Storage_path($file_path); 
                $surah_id = $data['surah_id'];
                
                if(file_exists($path))
                {
                    $data = Excel::toArray(new TranslationController,$path); // reading the file 
                    foreach($data[0] as $key => $value)
                    {
                        if($value['1'] == $surah_id)
                        {
                            $insert_data[] = array(
                            'id'  => $value['0'],
                            'sura'   => $value['1'],
                            'ayat'   => $value['2'],
                            'translation'    => $value['3']
                        );
                        }
                        
                    }
                    $result = 'success';
                    $res = ['result' => $result, 'response' => $insert_data];
                    return response()->json($res, 200);
                }else
                {
                    $result = 'failed';
                    $res = ['result' => $result, 'response' => 'There is no file in the Directiry'];
                    return response()->json($res, 200);
                }
            }
            else
            {
               $result = 'failed';
               $res = ['result' => $result, 'response' => 'There is no record in the Database'];
               return response()->json($res, 200);
           }
       }
    }

   public function get_list_translations()
    {
        $all = Translation::all();
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

    public function get_list_by_translator(Request $request)
    {
        $data = $request->only('trans_id');
        $response = $this->validator($data);

        if ($response->fails()) {
            $result = 'failed';
            $errors = $response->errors()->all();
            $err_str = implode(" ", $errors);
            $res = ['result' => $result, 'response' => $err_str];
            return response()->json($res, 200);
        } else
        {
        $all = Translation::find($data['trans_id']);
        if(isset($all))
        {
            $res['response'] = $all;
            $res['result'] = 'success';
            return response()->json($res);

        }
        else
        {
            $res['response'] = "There is no record in Table";
            $res['result'] = 'failed';
            return response()->json($res);
        }
    }
    }


}
