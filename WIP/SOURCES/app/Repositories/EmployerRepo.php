<?php namespace App\Repositories;

use App\Model\Employer;
use DB;

class EmployerRepo implements IEmployerRepo
{

    /**
     * {@inheritDoc}
     */
    public function search($params, $pageSize = 10)
    {
        /*$query = Employer::join('user', 'employer.user_id', '=', 'user.id')
            ->leftJoin('province', 'employer.province_id', '=', 'province.id')
            ->leftJoin('company_size', 'employer.company_size', '=', 'company_size.id')
            ->select('employer.id', 'employer.company_name', 'employer.phone', 'employer.contact_person',
                'employer.status', 'user.username', 'employer.created_at', 'employer.vip');

        if ($keyword) {
            $query = $query->where('company_name', 'LIKE', '%' . $keyword . '%')
                ->orwhere('phone', 'LIKE', '%' . $keyword . '%')
                ->orwhere('username', 'LIKE', '%' . $keyword . '%');
        }

        return $query->orderBy('created_at', 'desc')->get();*/

        $query = Employer::select();

        if(isset($params['company_name']) && $params['company_name']){
            $query = $query->where('company_name', 'like', "%" . $params['company_name'] . "%");
        }

        if(isset($params['company_address']) && $params['company_address']){
            $query = $query->where('company_address', 'like', "%" . $params['company_address'] . "%");
        }

        if(isset($params['province']) && $params['province']){
            $query = $query->where('province_id', '=', $params['province']);
        }

        if(isset($params['contact']) && $params['contact']){
            $query = $query->where('contact_person', 'like', "%" . $params['contact'] . "%")
                    ->orWhere('contact_phone', 'like', "%" . $params['contact'] . "%")
                    ->orWhere('contact_email', 'like', "%" . $params['contact'] . "%");
        }

        if(isset($params['status']) && $params['status'] !== "" && $params['status'] !== null){
            $query = $query->where('status', '=', $params['status']);
        }

        if(isset($params['vip']) && $params['vip'] !== "" && $params['vip'] !== null){
            $query = $query->where('vip', '=', $params['vip']);
        }


        return $query->paginate($pageSize);
    }

    /**
     * {@inheritDoc}
     */
    public function findById($id)
    {
        $query = Employer::join('user', 'employer.user_id', '=', 'user.id')
            ->leftJoin('province', 'employer.province_id', '=', 'province.id')
            ->leftJoin('company_size', 'employer.company_size', '=', 'company_size.id')
            ->where('employer.id', '=', $id)
            ->select('employer.*', 'user.image', 'province.name as provinceName', 'company_size.name as companySize')
            ->first();

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function updateStatus($id, $status)
    {
        $employer = Employer::find($id);
        if (!$employer) {
            return false;
        }
        $employer->status = $status;
        $employer->save();
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function findByUserId($userId)
    {
        $query = Employer::join('user', 'employer.user_id', '=', 'user.id')
            ->leftJoin('province', 'employer.province_id', '=', 'province.id')
            ->leftJoin('company_size', 'employer.company_size', '=', 'company_size.id')
            ->where('user.id', '=', $userId)
            ->select('employer.*', 'user.id as userId', 'user.email as userEmail', 'user.image', 'province.name as provinceName', 'company_size.name as companySize')
            ->first();

        return $query;
    }

    /**
     * Find employer info by user id
     *
     * @param int $userId
     * @return Employer|null
     */
    public function findEmployerInfoByUserId($userId)
    {
        $query = Employer::join('user', 'employer.user_id', '=', 'user.id')
            ->where('user.id', '=', $userId)
            ->select('employer.*')
            ->first();

        return $query;
    }

    /**
     * Increase balance after payment
     *
     * @param int $userId
     * @param int $balance
     * {@inheritDoc}
     */
    public function increaseBalanceAfterPayment($userId, $balance)
    {
        $employer = Employer::where('user_id', '=', $userId)->first();

        if (!$employer) {
            return false;
        }

        $oldBalance = isset($employer->balance) ? $employer->balance : 0;
        $employer->balance = $oldBalance + $balance;
        $employer->save();

        return $employer;
    }

    /**
     * Set employer is VIP
     *
     * @param int $id
     * @param boolean $vip
     * {@inheritDoc}
     */
    public function setVip($id, $vip, $expire_vip)
    {
        $employer = Employer::find($id);
        if (!$employer) {
            return false;
        }
        $employer->vip = $vip;
        $employer->expire_vip = $expire_vip;
//        if ($vip == 1) {
//            $employer->expire_vip = date('Y-m-d h:m:s', strtotime("+" . $intervalTime . " months", time()));
//        } else {
//            $employer->expire_vip = date('Y-m-d h:m:s', time());
//        }
        $employer->save();

        return true;
    }
}