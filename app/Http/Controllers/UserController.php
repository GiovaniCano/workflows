<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile()
    {
        return view('user.profile');
    }

    public function destroy()
    {
        DB::transaction(function() {
            $user = auth()->user();

            $images = $user->images->pluck('name')->toArray();

            Storage::delete($images);

            $user->delete();
        });
        return to_route('register');
    }

    // public function payment()
    // {
    //     return view('user.payment');
    // }
}
