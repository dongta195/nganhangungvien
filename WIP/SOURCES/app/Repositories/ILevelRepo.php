<?php namespace App\Repositories;

interface ILevelRepo
{
    public function all();

    public function filter($name);
}
