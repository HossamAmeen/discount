<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
class ClientController extends BackEndController
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }
}
