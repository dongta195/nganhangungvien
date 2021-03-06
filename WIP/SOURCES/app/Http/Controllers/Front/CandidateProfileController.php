<?php 

namespace App\Http\Controllers\Front;

use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Libs\Constants;
use App\Model\Transaction;
use App\Repositories\IConfigRepo;
use App\Repositories\IContactPersonRepo;
use App\Repositories\IEmployerRepo;
use App\Repositories\IProvinceRepo;
use App\Repositories\ISaveCvRepo;
use App\Repositories\ITransactionRepo;
use App\Repositories\IITLevelRepo;
use App\Repositories\ICandidateForeignLanguageRepo;
use App\Repositories\ICandidateCertificateRepo;
use App\Repositories\IExperienceRepo;
use Illuminate\Http\Request;
use App\Model\Candidate;
use App\Repositories\ICandidateRepo;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class CandidateProfileController extends BaseController {

	const DEACTIVE_STATUS = 0;
	private $employerRepo;
	private $saveCvRepo;
	private $transactionRepo;
    private $candidateForeignLanguageRepo;
    private $itLevelRepo;
    private $certificateRepo;
    private $exigencyRepo;
	private $experienceRepo;
	private $contactPersonRepo;

	public function __construct(
		IEmployerRepo $employerRepo,
		ISaveCvRepo $saveCvRepo,
		IProvinceRepo $provinceRepo,
		ICandidateRepo $candidateRepo,
		IConfigRepo $configRepo,
		ITransactionRepo $transactionRepo,
        IITLevelRepo $itLevelRepo,
		ICandidateForeignLanguageRepo $candidateForeignLanguageRepo,
        ICandidateCertificateRepo $certificateRepo,
        IExperienceRepo $experienceRepo,
        IContactPersonRepo $contactPersonRepo
	)
	{
		parent::__construct($candidateRepo, $provinceRepo, $configRepo);
		$this->employerRepo = $employerRepo;
		$this->saveCvRepo = $saveCvRepo;
		$this->transactionRepo = $transactionRepo;
        $this->itLevelRepo = $itLevelRepo;
        $this->candidateForeignLanguageRepo = $candidateForeignLanguageRepo;
        $this->certificateRepo = $certificateRepo;
        $this->experienceRepo = $experienceRepo;
        $this->contactPersonRepo = $contactPersonRepo;
	}

	/**
	 * Index page
	 *
	 * @return \Illuminate\View\View
	 */
	public function index(Request $request, $slug, $id)
	{
		$candidate = Candidate::find($id);
		if(!$candidate){
			return $this->errorView();
		}

		if ($candidate->status == self::DEACTIVE_STATUS) {
			return Redirect::route('home');
		}
		
		$dropdownData = $this->dropdownData();

		$candidatesData = $this->candidatesData();

		$user = $this->getCurrentUser();
		$countSavedCv = 0;
		$showContact = false;
		$transactionCost = "";

		if ($user) {
			if($user->user_type == 'employer'){
				$employer = $this->employerRepo->findEmployerInfoByUserId($user->id);
				$countSavedCv = $this->saveCvRepo->countSavedCv($employer->id, $candidate->id);

				if(UserHelper::isVip($employer)){
					$showContact = true;
				}else{
					$countTransactions = $this->transactionRepo->countTrans($employer->id, $candidate->id);
					if($countTransactions > 0){
						$showContact = true;
					}else{
						if ($candidate->experienceYears) {
							$transactionCost = UserHelper::getTransactionCost($candidate->experienceYears->code);
						}
					}
				}
			}else if($user->user_type == 'admin'){
				$showContact = true;
			}

		}

		//update count
		$candidate->view_total ++;
		$candidate->save();
		
		$sameData=[];
		$sameData['exp'] = $this->candidateRepo->sameExpStatistic($id);
		$sameData['lvl'] = $this->candidateRepo->sameLvlStatistic($id);
		$sameData['savedCv'] = $countSavedCv;

		$countData=[];
		$countData['all'] = $this->candidateRepo->countAllStatistic();
		$foreignLanguages = $this->candidateForeignLanguageRepo->getForeignLanguagesByCandidateId($candidate->id);
		$itLevels = $this->itLevelRepo->getITLevelsByCandidateId($candidate->id);
		$certificates = $this->certificateRepo->getCertificatesByCandidateId($candidate->id);
		$experiences = $this->experienceRepo->getExperiencesByCandidateId($candidate->id);
		$contactPersons = $this->contactPersonRepo->getContactPersonsByCandidateId($candidate->id);
		
		return view('front/profile/candidate')
				->with('candidate', $candidate)
				->with('dropdownData', $dropdownData)
				->with('candidatesData', $candidatesData)
				->with('linkYouTubeChanel', $this->linkYouTubeChanel)
				->with('sameData', $sameData)
				->with('showContact', $showContact)
				->with('transactionCost', $transactionCost)
				->with('foreignLanguages', $foreignLanguages)
				->with('itLevels', $itLevels)
				->with('certificates', $certificates)
				->with('experiences', $experiences)
				->with('contactPersons', $contactPersons)
				->with('countData', $countData);
	}

	public function viewContact(Request $request){
		$input = $request->all();
		$candiateId = $input['candidateId'];

		$candidate = Candidate::find($candiateId);
		if ($candidate->experienceYears) {
			$transactionCost = UserHelper::getTransactionCost($candidate->experienceYears->code);
		}

		$user = $this->getCurrentUser();
		$employer = $this->employerRepo->findEmployerInfoByUserId($user->id);
		
		$countTransactions = $this->transactionRepo->countTrans($employer->id, $candidate->id);

		if ($employer->vip == 1 || $countTransactions > 0 ) { // VIP hoac Da tra tien
			return response()->json([
				"data" => [
					"email"			=> $candidate->email,
					"phone_number"	=> $candidate->phone_number,
					"address"		=> $candidate->address,
					"attach_cv" 	=> $candidate->attach_cv ?
						Constants::GOOGLE_DOC_PATH . '?url=' . URL::to('/') . '/candidate/cv/' . $candidate->attach_cv . "?url=&embedded=true" : ""
				],
				"error" => 0
			]);
		}

		if($employer->balance >= $transactionCost){
			$employer->balance = $employer->balance - $transactionCost;

			//save transaction
			$trans = new Transaction();
			$trans->employer_id = $employer->id;
			$trans->candidate_id = $candidate->id;
			$trans->amount = $transactionCost;
			$trans->balance = $employer->balance;
			$trans->type = 1;
			$trans->payment_type = 3; //3-  Trừ tiền khi xem ứng viên
			$trans->save();

			$employer->save();

			return response()->json([
				"data" => [
					"email"			=> $candidate->email,
					"phone_number"	=> $candidate->phone_number,
					"address"		=> $candidate->address,
					"attach_cv" 	=> $candidate->attach_cv ?
						Constants::GOOGLE_DOC_PATH . '?url=' . URL::to('/') . '/candidate/cv/' . $candidate->attach_cv . "?url=&embedded=true" : ""
				],
				"error" => 0
			]);
		}else{
			return response()->json([
				"data" => [],
				"error" => 1
			]);
		}
	}
	
}