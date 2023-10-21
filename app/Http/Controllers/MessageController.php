<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function index()
    {
        $message =  Message::all();
        return response()->json([
            'status' => 'success',
            'data' => $message
        ],200);
    }
}
