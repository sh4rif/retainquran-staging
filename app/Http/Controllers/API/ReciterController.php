<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Reciter;
use App\Models\Verse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ReciterController extends Controller
{
	public function validator($data)
	{
		return Validator::make($data, [

			'reciter_id' => ['required'],
			'surah_id' => ['required']

		]);
	}

	public function validator_country($data)
	{
		return Validator::make($data, [

			'reciter_country' => ['required']

		]);
	}

	public function get_reciter_by_surah(Request $request)
	{

		$data = $request->only('reciter_id', 'surah_id');
		$response = $this->validator($data);

		if ($response->fails()) {
			$result = 'failed';
			$errors = $response->errors()->all();
			$err_str = implode(" ", $errors);
			$res = ['result' => $result, 'response' => $err_str];
			return response()->json($res, 200);
		} else {
			// checking db for data
			$surah_id = $data['surah_id'];
			$filedata = Reciter::find($data['reciter_id']);
			if ($filedata) {
				$surah_id = $data['surah_id'];
				$file_data = $filedata->toArray();
				//get path of local directory
				$dir = $file_data['rec_dir_name'];
				$dir_path = "https://storage.googleapis.com/retain-quran/Reciters/";
				$getverses = Verse::where('surah_id', $surah_id)->get()->toArray();
				if ($getverses) {
					$surah = sprintf("%03s", $surah_id);
					foreach ($getverses as $verse) {
						$ayah = sprintf("%03s", $verse['verse_number']);
						$file_name = $surah . $ayah . ".mp3";
						$url['path'] = $dir_path . $file_name;
						$url['verse_id'] = $verse['verse_id'];
						$path[] = $url;
					}
					$result = 'success';
					$res = ['result' => $result, 'response' => $path];
					return response()->json($res, 200);
				} else {
					$result = 'failed';
					$res = ['result' => $result, 'response' => 'There is no file in the Directiry'];
					return response()->json($res, 200);
				}
			} else {
				$result = 'failed';
				$res = ['result' => $result, 'response' => 'There is no record in the Database'];
				return response()->json($res, 200);
			}
		}
	}

	public function get_all_reciters(Request $request)
	{
		$all = Reciter::all();
		if (isset($all)) {
			$response['response'] = $all;
			$response['result'] = 'success';
			return response()->json($response);
		} else {
			$response['response'] = "There is no record in Table";
			$response['result'] = 'failed';
			return response()->json($response);
		}
	}

	public function get_all_reciters_by_country(Request $request)
	{
		$data = $request->only('reciter_country');
		$response = $this->validator_country($data);

		if ($response->fails()) {
			$result = 'failed';
			$errors = $response->errors()->all();
			$err_str = implode(" ", $errors);
			$res = ['result' => $result, 'response' => $err_str];
			return response()->json($res, 200);
		} else {
			$country = $data['reciter_country'];
			$all_by_country = Reciter::where('reciter_country', 'like', '%' . $country . '%')->get()->toArray();
			//debug($all_by_country,1);
			if (isset($all_by_country)) {
				$res['response'] = $all_by_country;
				$res['result'] = 'success';
				return response()->json($res);
			} else {
				$res['response'] = "There is no record in Table";
				$res['result'] = 'failed';
				return response()->json($res);
			}
		}
	}

	public function GetAudioSizeByReciter(Request $request)
	{
		$data = $request->all();

		$response = Validator::make($data, [
			'reciterId' => 'required'
		]);

		if (!($response->fails())) {

			$dataArray = DB::table('surah_audio_size')
				->select('*')
				->where('reciter_id', '=', $data['reciterId'])
				->get();

			if (count($dataArray) == 0) {
				$dataArray = DB::table('surah_audio_size')
					->select('*')
					->where('reciter_id', '=', 1)
					->get();
			}

			if (count($dataArray) > 0) {
				$responseData = array();
				foreach ($dataArray as $data) {
					$responseData = json_decode($data->size_info);
				}
				$responseArray = [
					'result' => 'success',
					'message' => 'record found',
					'data' => $responseData
				];
			} else {
				$responseArray = [
					'result' => 'success',
					'message' => 'No Record found',
					'data' => $dataArray
				];
			}
		} else {
			$responseArray = [
				'result' => 'failed',
				'message' => $response->errors()->all()
			];
		}

		return response()->json($responseArray);
	}
}
