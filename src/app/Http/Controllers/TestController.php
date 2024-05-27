<?php

namespace App\Http\Controllers;

use App\Repositories\Profile\Interface\ProfileRepositoryInterface;

class TestController extends BaseController
{
    public function __invoke(ProfileRepositoryInterface $profileRepository)
    {
        $testModel = $profileRepository->create([
            'client_id' => 1
        ]);
        $profileRepository->update($testModel,[
            'client_id' => 2
        ],['client_id']);
    }
}
