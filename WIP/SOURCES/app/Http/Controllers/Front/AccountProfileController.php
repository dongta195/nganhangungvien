<?php

namespace App\Http\Controllers\Front;

use App\Helpers\FileHelper;
use App\Http\Requests;
use App\Http\Response;
use App\Repositories\ICandidateRepo;
use App\Repositories\ICompanySizeRepo;
use App\Repositories\IConfigRepo;
use App\Repositories\IEmployerRepo;
use App\Repositories\IProvinceRepo;
use App\Repositories\IUserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Libs\Constants;
use Validator;
use Auth;

class AccountProfileController extends BaseController
{
    private $employerRepo;
    private $userRepo;
    private $companySizeRepo;

    public function __construct(
        IEmployerRepo $employerRepo,
        IUserRepo $userRepo,
        IProvinceRepo $provinceRepo,
        ICandidateRepo $candidateRepo,
        ICompanySizeRepo $companySizeRepo,
        IConfigRepo $configRepo
    )
    {
        parent::__construct($candidateRepo, $provinceRepo, $configRepo);
        $this->employerRepo = $employerRepo;
        $this->userRepo = $userRepo;
        $this->companySizeRepo = $companySizeRepo;
        $this->middleware('auth');
    }

    /**
     * Get user profile
     * @param Request $request
     * @return view
     */
    public function manageAccountProfile(Request $request)
    {
        if ($request->isMethod('get')) {
            $user = $this->getCurrentUser();
            $employer = $this->employerRepo->findByUserId($user->id);
            if (!$employer) {
                return $this->errorView();
            }

            $provinces = $this->provinceRepo->getSortedList();
            $companySizes = $this->companySizeRepo->all();
            return view('front.account.employer_profile')
                ->with('employer', $employer)
                ->with('provinces', $provinces)
                ->with('linkYouTubeChanel', $this->linkYouTubeChanel)
                ->with('companySizes', $companySizes);
        }

        return view('front/account/employer_profile')->with('linkYouTubeChanel', $this->linkYouTubeChanel);
    }

    /**
     * Change account password
     * @param Request $request
     * @throws Exception
     */

    /**
     * Change company information
     * @param Request $request
     * @return mixed
     * @throws Exception
     */

    public function changeAccountPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            try {
                $validator = $this->validateChangePassword($input);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->messages()]);
                }
            } catch (\Exception $e) {
                throw new Exception($e);
            }
            $userId = Auth::user()->id;

            $user = $this->userRepo->findById($userId);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy thông tin đăng nhập']);
            }
            $oldPassword = $input['oldPassword'];
            if (!Hash::check("$oldPassword",  "$user->password")) {
                return response()->json(['status' => false, 'message' => 'Mật khẩu cũ không đúng']);
            }
            // save password
            $user->password = Hash::make($input['newPassword']);
            $user->save();

            return response()->json(['status' => true, 'message' => 'Đổi mật khẩu thành công']);
        }
    }
    public function changeCompanyInformation(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            try {
                $validator = $this->validateChangeCompanyInformation($input);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->messages()]);
                }
            } catch (\Exception $e) {
                throw new Exception($e);
            }
            // handle file
            $companyImgPath = FileHelper::getCompanyImgPath();
            $imageName = FileHelper::getNewFileName();

            $user = $this->getCurrentUser();
            if (!empty($request->file('logo'))) {
                $imgExtension = $request->file('logo')->getClientOriginalExtension();
                $request->file('logo')->move($companyImgPath, $imageName . '.' . $imgExtension);
                // save image for user
                $user->image = FileHelper::getCompanyRelativePath() . $imageName . '.' . $imgExtension;
                $user->save();
            }

            // get employer
            $employerId = $input['employer_id'];
            $employer = $this->employerRepo->findById($employerId);
            if (!$employer) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy thông tin nhà tuyển dụng']);
            }
            $employer->company_name = $input['company_name'];
            $employer->company_size = $input['company_size'];
            $employer->phone = $input['company_phone'];
            $employer->company_description = $input['company_description'];
            $employer->company_address = $input['company_address'];
            $employer->province_id = $input['province_id'];
            $employer->website = $input['website'];
            $employer->save();

            // create image url
            $imgUrl = URL::asset('assets/image/default.png');
            if (!empty($user->image)) {
                $imgUrl = URL::to('/') . $user->image;
            }
            return response()->json(['status' => true, 'message' => 'Cập nhật thông tin nhà tuyển dụng thành công',
                'img' => $imgUrl]);
        }
    }

    /**
     * Change employer contact person information
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function changeEmployerContactPersonInfo(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->all();
            try {
                $validator = $this->validateContactPersonInformation($input);

                if ($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->messages()]);
                }
            } catch (\Exception $e) {
                throw new Exception($e);
            }
            $employerId = $input['employer_id'];
            $employer = $this->employerRepo->findById($employerId);
            if (!$employer) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy thông tin nhà tuyển dụng']);
            }
            $employer->contact_person = $input['contact_name'];
            $employer->contact_phone = $input['contact_phone'];
            $employer->contact_email = $input['contact_email'];
            $employer->save();

            return response()->json(['status' => true, 'message' => 'Cập nhật thông tin nhà tuyển dụng thành công']);
        }
    }

    /**
     * Validate change password data
     * @param $data
     * @return mixed
     */
    private function validateChangePassword($data)
    {
        return Validator::make($data, [
            'oldPassword' => 'required',
            'newPassword' => 'required'
        ]);
    }

    /**
     * Validate change company information
     * @param $data
     */
    private function validateChangeCompanyInformation($data)
    {
        return Validator::make($data, [
            'employer_id' => 'required',
            'company_name' => 'required',
            'company_size' => 'required',
            'company_phone' => 'required',
            'company_description' => 'required',
            'company_address' => 'required',
            'province_id' => 'required'
        ]);
    }

    private function validateContactPersonInformation($data)
    {
        return Validator::make($data, [
            'contact_name' => 'required',
            'contact_phone' => 'required',
            'contact_email' => 'required'
        ]);
    }
}