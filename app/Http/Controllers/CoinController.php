<?php

namespace App\Http\Controllers;



use App\Models\Coin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Rules\allowedSender;



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
        //tinggal rule receiver dan lainnya
        $fields = $request->validate([
            'sender' => new allowedSender,
            'receiver' => 'required|string',
            'balance' => 'required|integer'
        ]);

        //get authenticated user
        $userId = request()->user()->id;
        $userBalance = request()->user()->balance;
        $userRole = request()->user()->role;


        //get from input
        $inputBalance = $request->input('balance');
        $inputReceiver = $request->input('receiver');

        $agentFalseCondition = $userId == 2 && $inputReceiver == "masteragent";

        $agentFalseCondition2 = $userId == 2 && $inputReceiver == "agent";

        // rules for user topup
        if (!$userId) {
            return response([
                'message' => 'unauthorized'
            ], 401);
        } elseif ($agentFalseCondition || $agentFalseCondition2) {
            return response([
                'message' => 'Not allowed!'
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

        //update user balance after topup
        if ($userId == 1 && $inputReceiver == 'agent') { // masteragent topup to agent

            DB::table('users')->where('role', $userRole)->decrement('balance', $inputBalance);

            $agentRole = User::where('role', 'agent')->first();
            $agentRole->increment('balance', $inputBalance);

            return response([
                'message' => "Topup Success",
            ], 200);
        } elseif ($userId == 1 && $inputReceiver !== ("masteragent" || "agent")) { //masteragent topup to customer
            DB::table('users')->where('role', $userRole)->decrement('balance', $inputBalance);

            return response([
                'message' => "Topup Success",
            ], 200);
        } elseif ($userId == 1 && $inputReceiver == 'masteragent') {

            DB::table('users')->where('role', $userRole)
                ->increment(
                    'balance',
                    $inputBalance
                );

            return response([
                'message' => "Balance updated",
            ], 200);
        } elseif ($userId == 2) { //agent topup to customer

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

    public function show()
    {
        //code
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //code..
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //code
    }
}
