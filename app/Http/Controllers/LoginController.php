<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email    = $request->email;
        $password = $request->password;
        // [VULNERABLE HERE] — raw SQL with direct string concatenation; no bindings, no escaping
        // SQLi payload: Email = ' OR '1'='1' --   → logs in as first user without knowing any password
        # SELECT * FROM users WHERE email = '' OR '1'='1' # ' AND password = 'anything'
        # SELECT * FROM users WHERE email = 'phat3022005@gmail.com'#' AND password = 'anything'
        // $sql   = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        // $users = DB::select($sql);

        // if (!empty($users)) {
        //     $user = $users[0];
        //     Auth::loginUsingId($user->id);
        //     return redirect('/dashboard');
        // }

        // [FIXED - ELOQUENT] Uses query builder parameter binding under the hood (no raw SQL concatenation).
        $user = User::where('email', $email)
            ->where('password', $password)
            ->first();
         if ($user) {
            Auth::loginUsingId($user->id);
            return redirect('/dashboard');
        }


        

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
