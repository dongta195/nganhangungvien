<?php namespace App\Repositories;

use App\Helpers\DateTimeHelper;
use App\Model\Candidate;
use DB;

class CandidateRepo implements ICandidateRepo {

    /**
     * Search candidates by list of attributes
     * - Title of CV
     * - Occupation
     * - Province
     * - Salary Grade
     * - Degree
     * - Year of experience
     * - Sex
     * - Foreign language
     * - Upate time
     *
     * @param $params
     * @param int $pageSize
     * @param string $orderBy
     * @return
     */
    public function search($params, $pageSize = 10, $orderBy = 'updated_at') {
        $query = Candidate::select();

        if(isset($params['title']) && $params['title']){
            $query = $query->where('cv_title', 'like', "%" . $params['title'] . "%");
        }

        if(isset($params['occupation']) && $params['occupation']){
            $query = $query->whereHas('expectJobs', function($q) use ($params)
            {
                $q->where('job_id', '=', $params['occupation']);
            });
        }

        if(isset($params['province']) && $params['province']){
            $query = $query->whereHas('expectAddresses', function($q) use ($params)
            {
                $q->where('province_id', '=', $params['province']);
            });
        }

        if(isset($params['salaryGrade']) && $params['salaryGrade']){
            $query = $query->whereIn('expect_salary', $params['salaryGrade']);
        }

        if(isset($params['degree']) && $params['degree']){
            $query = $query->where('level', '=', $params['degree']);
        }

        if (!empty($params['yearOfexp']) && !empty($params['yearOfexp'][0])) {
            $query = $query->whereIn('experience_years', $params['yearOfexp']);
        }

        if(isset($params['sex']) && $params['sex'] !== "" && $params['sex'] !== null){
            $query = $query->where('sex', '=', $params['sex']);
        }

        if(isset($params['employment_status']) && $params['employment_status']){
            $query = $query->where('employment_status', '=', $params['employment_status']);
        }

        if(isset($params['rank']) && $params['rank']){
            $query = $query->where('expect_rank', '=', $params['rank']);
        }

        if(isset($params['language']) && $params['language']){
            $query = $query->whereHas('foreignLanguages', function($q) use ($params)
            {
                $q->where('language_id', '=', $params['language']);
            });
        }

        if(isset($params['timeUpdate']) && $params['timeUpdate']){
            $date = DateTimeHelper::getDateFromNow($params['timeUpdate']);
            $date = date("Y-m-d", $date);

            $query = $query->where('updated_at', '>=', $date);
        }

        if(isset($params['status']) && $params['status'] !== "" && $params['status'] !== null){
            $query = $query->where('status', '=', $params['status']);
        }

        //$query = $query->where('status', '=', 1);

        $query = $query->orderBy($orderBy, 'desc');
        //die($query->toSql());
        return $query->paginate($pageSize);

    }

    public function careerStatistic() {
        // Hiện tại chỉ dựa vào ngành nghề hiện tại, sau này có thể bổ sung thống kê theo ngành nghề liên quan
        $results = DB::select( DB::raw("SELECT id, name, CASE WHEN num_candidates IS NULL THEN 0 ELSE num_candidates END 
                    AS num_candidates FROM job j LEFT JOIN 
                    (SELECT job_id, COUNT(*) AS num_candidates FROM candidate_expect_job cej LEFT JOIN (SELECT status, id FROM candidate) ca ON cej.candidate_id = ca.id WHERE ca.status = 1 GROUP BY job_id) 
                    c ON j.id = c.job_id ORDER BY num_candidates DESC") );
        return $results;
    }

    public function experienceYearsStatistic() {
        $results = DB::select( DB::raw("SELECT id, name, CASE WHEN num_candidates IS NULL THEN 0 ELSE num_candidates END AS num_candidates FROM experience_years p LEFT JOIN (SELECT experience_years, COUNT(*) AS num_candidates FROM candidate WHERE status = 1 GROUP BY experience_years) c ON p.id = c.experience_years") );
        return $results;
    }

    public function levelsStatistic() {
        $results = DB::select( DB::raw("SELECT id, name, CASE WHEN num_candidates IS NULL THEN 0 ELSE num_candidates END AS num_candidates FROM level p LEFT JOIN (SELECT level, COUNT(*) AS num_candidates FROM candidate WHERE status = 1 GROUP BY level) c ON p.id = c.level ORDER BY id") );
        return $results;
    }

    public function salariesStatistic() {
        $results = DB::select( DB::raw("SELECT id, name, CASE WHEN num_candidates IS NULL THEN 0 ELSE num_candidates END AS num_candidates FROM salary p LEFT JOIN (SELECT expect_salary, COUNT(*) AS num_candidates FROM candidate WHERE status = 1 GROUP BY expect_salary) c ON p.id = c.expect_salary ORDER BY id") );
        return $results;
    }

    public function provinceStatistic() {
        $results = DB::select( DB::raw("SELECT id, name, CASE WHEN num_candidates IS NULL THEN 0 ELSE num_candidates END AS num_candidates 
                                        FROM province p 
                                        LEFT JOIN (SELECT province_id, COUNT(*) AS num_candidates FROM candidate_expect_address cea LEFT JOIN (SELECT status, id FROM candidate) ca ON cea.candidate_id = ca.id WHERE ca.status = 1 GROUP BY province_id) c ON p.id = c.province_id
                                        ORDER BY num_candidates DESC") );
        return $results;
    } 

    public function candidateStatistic() {
        $results = Candidate::leftJoin('experience_years','candidate.experience_years','=','experience_years.id')
                ->leftJoin('salary','candidate.expect_salary','=','salary.id')
                ->select('candidate.id', 'full_name','cv_title','experience_years.name as exp_years', 'salary.name as salary','candidate.updated_at as updated')
                ->where('status', '=', 1)
                ->orderBy('updated','decs')
                ->take(20)
                ->get();
        return $results;
    }

    public function bestViewStatistic() {  
        $results = Candidate::leftJoin('experience_years','candidate.experience_years','=','experience_years.id')
                ->leftJoin('salary','candidate.expect_salary','=','salary.id')
                ->select('candidate.id', 'view_total','full_name','cv_title','experience_years.name as exp_years', 'salary.name as salary', 'candidate.updated_at as updated')
                ->where('status', '=', 1)
                ->orderBy('view_total','decs')
                ->take(20)
                ->get();
        return $results;
    }

    public function countAllStatistic()
    {
        $results = DB::table('candidate')
            ->where('full_name','<>',' ')
            ->where('status','=', 1)
            ->count();
        return $results;

    }
    public function countNewStatistic()
    {   
        $date = date('Y-m-d');
        $prev_date = date('Y-m-d', strtotime($date .' -1 day'));
        $results = DB::table('candidate')
                ->where('updated_at', '>=', $prev_date.' 00:00:00')
                ->where('status','=', 1)
                ->count();
        return $results;
    }
    public function countRecentStatistic()
    {   
        $date = date('Y-m-d');
        $prev_month = date('Y-m-d', strtotime($date .' -1 month'));
        $results = DB::table('candidate')
                ->where('updated_at', '>=', $prev_month.' 00:00:00')
                ->where('status','=', 1)
                ->count();
        return $results;
    }

    public function sameExpStatistic($id)
    {
        $candidate = Candidate::find($id);
        if(!$candidate->experience_years){
            return NULL;
        }
        $results = Candidate::Where('experience_years','=',"$candidate->experience_years")
                ->Where('candidate.id','<>',$id)
                ->where('status','=', 1)
                ->take(20)
                ->get();

        return $results;
    }
    
    public function sameLvlStatistic($id)
    {
        $candidate = Candidate::find($id);
        if(!$candidate->current_rank){
            return NULL;
        }
        $results = Candidate::Where('current_rank','=',"$candidate->current_rank")
                ->Where('candidate.id','<>',$id)
                ->where('status','=', 1)
                ->take(20)
                ->get();
        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function updateStatus($id, $status)
    {
        $candidate = Candidate::find($id);
        if (!$candidate) {
            return false;
        }
        $candidate->status = $status;
        $candidate->save();
        return true;
    }
}