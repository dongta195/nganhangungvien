<?php


namespace App\Helpers;

use App\Model\Experience;
use App\Model\ExperienceYears;
use App\Model\Job;
use App\Model\Level;
use App\Model\Province;
use App\Model\Salary;
use Lang;
use StringHelper;

class CandidateHelper {
	public static function uri($candidate) {
		$slug = StringHelper::uri ( $candidate->cv_title );
		return $slug . '_' . $candidate->id;
	}
	
	public static function language($point) {
		switch ($point) {
			case 1 :
				return "Tốt";
			case 2 :
				return "Khá";
			case 3 :
				return "Trung bình";
			case 4 :
				return "Kém";
			default:
				return "";
		}
	}
	
	public static function office($point) {
		switch ($point) {
			case 1 :
				return "Tốt";
			case 2 :
				return "Khá";
			case 3 :
				return "Trung bình";
			case 4 :
				return "Kém";
			default:
				return "";
		}
	}

	/**
	 * @return object
     */
	public static function getGraduationTypes()
	{
		return array(
			(object) [
				'id' => 1,
				'name' => 'Xuất sắc'
			],
			(object) [
				'id' => 2,
				'name' => 'Giỏi'
			],
			(object) [
				'id' => 3,
				'name' => 'Khá'
			],
			(object) [
				'id' => 4,
				'name' => 'Trung bình khá'
			],
			(object) [
				'id' => 5,
				'name' => 'Trung bình'
			]
		);
	}

	/**
	 * Get scale list
	 */
	public static function getScales()
	{
		return array(
			(object) [
				'id' => 1,
				'name' => 'Tốt'
			],
			(object) [
				'id' => 2,
				'name' => 'Khá'
			],
			(object) [
				'id' => 3,
				'name' => 'Trung bình'
			],
			(object) [
				'id' => 4,
				'name' => 'Kém'
			]
		);
	}

	public static function getParamsBySlug($slug){
		$params = [];

		$temps = explode('-', $slug);
		if(count($temps) > 0){
			$code = $temps[count($temps) - 1];
			$char = substr($code, 0, 1);
			$id = substr($code, 1);

			switch ($char){
				case 'c': //career
					$params['occupation'] = $id;
					break;
				case 'p': //province
					$params['province'] = $id;
					break;
				case 'e': //experience
					$params['yearOfExp'] = [$id];
					break;
				case 'd': //degree
					$params['degree'] = $id;
					break;
				case 's': //salary
					$params['salaryGrade'] = [$id];
					break;
			}
		}

		return $params;
	}

	public static function getCagegoryNameBySlug($slug){
		$temps = explode('-', $slug);
		if(count($temps) > 0){
			$code = $temps[count($temps) - 1];
			$char = substr($code, 0, 1);
			$id = substr($code, 1);

			switch ($char){
				case 'c': //career
					$category = Job::find($id);
					return "Ngành " . $category->name;
					break;
				case 'p': //province
					$category = Province::find($id);
					return "Tỉnh " . $category->name;
					break;
				case 'e': //experience
					$category = ExperienceYears::find($id);
					return "Kinh nghiệm " . $category->name;
					break;
				case 'd': //degree
					$category = Level::find($id);
					return "Trình độ " . $category->name;
					break;
				case 's': //salary
					$category = Salary::find($id);
					return "Lương " . $category->name;
					break;
			}
		}
	}

	public static function uriByCate($id, $title, $code){
		$code = $code . $id;

		$slug = StringHelper::uri ($title) . '-' . $code;
		return route('candidate.category', ['slug' => $slug]);
	}
}