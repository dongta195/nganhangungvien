<?php
namespace App\Repositories;

interface ITransactionRepo
{
    /**
     * Get transaction by user id
     * @param $userId user id
     * @param $start start record want to get
     * @param $limit number record get one time
     * @return mixed
     */
    function findByUserId($userId, $start, $limit);
}