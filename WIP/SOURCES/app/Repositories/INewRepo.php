<?php namespace App\Repositories;

interface INewRepo
{
    /**
     * Search candidates by list of attributes
     * - Title
     * - Image
     * - Description
     * - Content
     * - Link
     * - password
     *
     * @param $params
     * @param $offset
     * @param $limit
     */

    public function search($name);

    public function all();

    public function findById($id);

}