<?php namespace App\Repositories;

use App\Model\SaveCv;
use Illuminate\Support\Facades\DB;

class SaveCvRepo implements ISaveCvRepo
{
    /**
     * {@inheritdoc}
     */
    public function getSavedCvByEmployerId($employerId, $start, $limit)
    {
        $query = SaveCv::join('candidate', 'save_cv.candidate_id', '=', 'candidate.id')
            ->where('save_cv.employer_id', '=', $employerId)
            ->select('save_cv.*', 'candidate.id as candidateId', 'candidate.full_name as candidateName', 'candidate.cv_title')
            ->orderBy('save_cv.created_at', 'desc')
            ->skip($start)
            ->take($limit)
            ->get();
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    function getSavedCvByCandidateAndEmployer($employerId, $candidateId)
    {
        $query = DB::table('save_cv')
            ->Where('employer_id', '=', $employerId)
            ->Where('candidate_id', '=', $candidateId)
            ->select('save_cv.*')
            ->first();
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function countSavedCv($employerId, $candidateId)
    {
        $query = DB::table('save_cv')
            ->Where('employer_id', '=', $employerId)
            ->Where('candidate_id', '=', $candidateId)
            ->count();
        return $query;
    }
}