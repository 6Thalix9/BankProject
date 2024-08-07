<?php
namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use OpenTelemetry\APİ\Trace as APİ;
use OpenTelemetry\Context\Context;
use OpenTelemetry\SDK\Trace\TraceProvider;


class AccountController extends Controller
{
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'account_number' => 'required|string|unique:accounts',
            'balance' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $account = Account::create($request->all());
            return response()->json($account, 201);
        } catch(\Exception $e){
            throw new BusinessException(['message' => 'Account creation failed'], 500);
        }

        
    }

    
    public function index()
    {
        try{
            $accounts = Account::all();
            return response()->json($accounts);
        }catch(\Exception $e){
            return response()->json(['error' => 'Failed to retrieve accounts'], 500);
        }
    }

    
    public function show($id)
    {
        
        try{
            $account = Account::find($id);
            if (!$account) {
                throw new BusinessException(['message' => 'Account not found'], 201);
            }
            return response()->json($account);
        }
        catch(\Exception $e){
            return response()->json(['error' => 'Failed to retrieve account'], 500);
        }
    }

    
    public function update(Request $request, $id)
    {
        $account = Account::find($id);

        if (!$account) {
            throw new BusinessException(['message' => 'Account not found'], 201);
        }

        $account->update($request->all());

        return response()->json($account);
    }

    
    public function destroy($id)
    {
        $account = Account::find($id);

        if (!$account) {
            throw new BusinessException(['message' => 'Account not found'], 201);
        }

        $account->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }

    public function transactionHistory($accountId){

        $account = Account::find($accountId);

        if(!$account){
            return response()->json(['message' => 'Account not found'], 404);
        }

        $transitions = $account->transitions()->get();

        return response()->json($transitions);
    }
    
}