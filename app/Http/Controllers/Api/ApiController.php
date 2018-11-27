<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Throwable;
use App\User;
use App\Models\Company;

class ApiController extends Controller
{
    // Получаем список пользователей для выборки при смене владельца
    public function getUsers(Request $request)
    {
        $users = User::where('email', 'LIKE', '%'.$request->email.'%')->select(['email'])->get();
        return response()->json($users);
    }

    // Получение формы для изменения владельца
    public function changeOwner(Request $request)
    {
        $company = Company::findOrFail($request->company_id);
        $user = User::where('email', 'LIKE', '%'.$request->user.'%')->first();
        $company->user_id = $user->id;
        $company->save();
        try{
            return response()->json(['error' => 0]);
        } catch (Throwable $th) {
            return response()->json(['error' => 1, 'message' => $th]);
        }
    }
}
