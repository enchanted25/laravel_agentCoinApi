<?php

namespace App\Http\Controllers;



use App\Models\Coin;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use function PHPUnit\Framework\returnSelf;

class CoinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Coin::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $fields = $request->validate([
            'sender' => 'required|String',
            'receiver' => 'required|String',
            'balance' => 'required|integer'
        ]);


        //get authenticated user
        $userId = request()->user()->id;
        $userBalance = request()->user()->balance;
        $userRole = request()->user()->role;


        //get from input
        $inputBalance = $request->input('balance');
        $inputSender = $request->input('sender');
        $inputReceiver = $request->input('receiver');

        // testing
        //============================

        // $test = DB::table('users')->where('id', 2)->increment('balance', $inputBalance);
        // dd($test);

        //================

        $agentFalseCondition = $userId == 2 && $inputReceiver == $inputSender;

        $agentFalseCondition2 = $userId == 2 && $inputReceiver == ($inputSender = "masteragent");

        // rules for user topup
        if (!$userId) {
            return response([
                'message' => 'unauthorized'
            ], 401);
        } elseif ($agentFalseCondition || $agentFalseCondition2) {
            return response([
                'message' => 'you are not allowed to do that!'
            ], 401);
        } elseif ($userId == 2 && (float)$inputBalance > (float)$userBalance) {
            return response([
                'message' => "you're balance is not enough!"
            ], 401);
        }

        Coin::create([
            'sender' => $fields['sender'],
            'receiver' => $fields['receiver'],
            'balance' => $fields['balance']
        ]);

        //update user balance
        if ($userId == 1 && ($inputSender == 'masteragent' && $inputReceiver == 'agent')) {
            // $test = DB::table('users')->where('id', 2)->increment('balance', $inputBalance);
            //kenapa gak perlu increment agent udah otomatis menambah balance agent?

            DB::table('users')->where('role', $userRole)->decrement('balance', $inputBalance);

            return response([
                'message' => "Topup Success",
            ], 200);
        } elseif ($inputSender == 'masteragent' && $inputReceiver == 'masteragent') {

            DB::table('users')->where('role', $userRole)
                ->increment(
                    'balance',
                    $inputBalance
                );

            return response([
                'message' => "Balance updated",
            ], 200);
        } elseif ($userId == 2) {

            DB::table('users')->where('role', $userRole)
                ->decrement(
                    'balance',
                    $inputBalance
                );

            return response([
                'message' => "Topup to Customer Success",
            ], 200);
        }
    }




    public function show($id)
    {
        return Coin::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $coin = Coin::find($id);
        // $coin->update($request->all());
        // return $coin;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Coin::destroy($id);
    }
}
