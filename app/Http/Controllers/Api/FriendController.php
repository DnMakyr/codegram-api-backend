<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FriendController extends Controller
{

    public function suggest(){
        $authUser = auth()->user()->id;
        $suggests = User::with('profile')->where('id', '!=', $authUser)->get();
        return $suggests;
    }
    // public functiuon addFriend(){
        
    // }
}
