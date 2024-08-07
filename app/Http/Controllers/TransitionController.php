<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transition;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransitionController extends Controller
{
    public function transfer(Request $request, $fromAccount, $toAccount)
    {
        try {
            $from = Account::findOrFail($fromAccount);
        } catch (ModelNotFoundException $e) {
            throw new BusinessException("Sender account ID $fromAccount not found", 201);
        }

        try {
            $to = Account::findOrFail($toAccount);
        } catch (ModelNotFoundException $e) {
            throw new BusinessException("Receiver account ID $toAccount not found", 201);
        }

        $amount = $request->input('amount');
        $senderbalance = $from->balance;
        if ($from->balance < $amount) {
            throw new BusinessException("Insufficient sender balance, you have only $senderbalance", 201);
        }

        sleep(1);
        $from->balance -= $amount;
        $to->balance += $amount;
        $from->save();
        $to->save();

        $transitionFrom = new Transition();
        $transitionFrom->amount = $amount;
        $transitionFrom->description = 'transfer';
        $transitionFrom->sender_id = $fromAccount;
        $transitionFrom->receiver_id = $toAccount;
        $transitionFrom->save();



        $transactionTime = $transitionFrom->created_at->toIso8601String();



        $fromUserId = $from->user ? $from->user->id : null;
        $fromUserName = $from->user ? $from->user->name : 'Unknown User';

        $toUserId = $to->user ? $to->user->id : null;
        $toUserName = $to->user ? $to->user->name : 'Unknown User';


        return response()->json([
            'message' => 'Transfer successful',
            'data' => [
                'transaction_id' => $transitionFrom->id,
                'senderAccount' => [
                    'account_id' => $from->id,
                    'user_id' => $from->user_id,
                    'bank_id' => $from->user->bank->id,
                    'balance' => $from->balance,
                    'User' => [
                        'user_id' => $fromUserId,
                        'name' => $fromUserName,
                    ],
                    'Bank' => [
                        'bank_id' => $from->user->bank->id,
                        'name' => $from->user->bank->name,
                    ],
                ],
                'receiverAccount' => [
                    'account_id' => $to->id,
                    'user_id' => $to->user_id,
                    'bank_id' => $to->user->bank->id,
                    'balance' => $to->balance,
                    'User' => [
                        'user_id' => $toUserId,
                        'name' => $toUserName,
                    ],
                    'Bank' => [
                        'bank_id' => $to->user->bank->id,
                        'name' => $to->user->bank->name,
                    ],
                ],
                'amount' => $amount,
                'transaction_time' => $transactionTime,
            ]
        ]);
    }


    public function deposit(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
        ]);
        try {
            $account = Account::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new BusinessException("Account ID $account not found", 201);
        }

        $account->balance += $validated['amount'];
        $account->save();

        $transition = new Transition();
        $transition->amount = $validated['amount'];
        $transition->description = 'deposit';
        $transition->sender_id = $id;
        $transition->receiver_id = $id; // Set receiver_id to the same account for deposit
        $transition->save();

        $amount = $request->input('amount');
        $transactionTime = $transition->created_at->toIso8601String();

        return response()->json([
            'message' => 'Deposit successful',
            'data' => [
                'transaction_id' => $transition->id,
                'senderAccount' => [
                    'account_id' => $account->id,
                    'user_id' => $account->user_id,
                    'bank_id' => $account->user->bank->id,
                    'balance' => $account->balance,
                    'User' => [
                        'user_id' => $account->user->id,
                        'name' => $account->user->name,
                    ],
                    'Bank' => [
                        'bank_id' => $account->user->bank->id,
                        'name' => $account->user->bank->name,
                    ],
                ],
                'amount' => $amount,
                'transaction_time' => $transactionTime
            ]
        ]);
    }

    public function withdraw(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
        ]);

        try {
            $account = Account::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new BusinessException("Account ID $account not found", 201);
        }
        if ($account->balance < $validated['amount']) {
            throw new BusinessException("Insufficient balance", 201);
        }

        $account->balance -= $validated['amount'];
        $account->save();

        $transition = new Transition();
        $transition->amount = $validated['amount'];
        $transition->description = 'withdraw';
        $transition->receiver_id = $id;
        $transition->save();

        $amount = $request->input('amount');
        $transactionTime = $transition->created_at->toIso8601String();

        return response()->json([
            'message' => 'Withdrawal successful',
            'data' => [
                'transaction_id' => $transition->id,
                'senderAccount' => [
                    'account_id' => $account->id,
                    'user_id' => $account->user_id,
                    'bank_id' => $account->user->bank->id,
                    'balance' => $account->balance,
                    'User' => [
                        'user_id' => $account->user->id,
                        'name' => $account->user->name,
                    ],
                    'Bank' => [
                        'bank_id' => $account->user->bank->id,
                        'name' => $account->user->bank->name,
                    ],
                ],
                'amount' => $amount,
                'transaction_time' => $transactionTime
            ]
        ]);
    }

    public function index()
    {
        $account = Transition::all();
    }
}
