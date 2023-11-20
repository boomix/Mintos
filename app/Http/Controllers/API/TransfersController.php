<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use App\Models\Currencies;
use App\Models\Transfers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class TransfersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function accounts(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'client_id' => 'required|numeric|gt:-1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $accounts = Accounts::where('client_id', $input['client_id'])->get();

        foreach ($accounts as $account) {
            $account->currency = $account->currencyName();
        }

        return $this->sendResponse($accounts->toArray());
    }

    /**
     * Transfer balance from one account to another
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function transfer(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'account_id' => 'required|numeric|gt:-1',
            'target_account_id' => 'required|numeric|gt:-1',
            'amount' => 'required|numeric|gt:0',
            'currency' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // Check if both accounts exist
        $account = Accounts::where('id', (int)$input['account_id'])->first();
        if (!$account) {
            return $this->sendError('Account is not correct');
        }

        $targetAccount = Accounts::where('id', (int)$input['target_account_id'])->first();
        if (!$targetAccount) {
            return $this->sendError('Target account is not correct');
        }

        if ($account->id === $targetAccount->id) {
            return $this->sendError('You cant transfer money to same account');
        }

        // Get real currency ID
        $sendCurrency = Currencies::where('currency', $input['currency'])->first();
        if (!$sendCurrency) {
            return $this->sendError('Please provide correcy currency');
        }

        // Check if correct currency is sent to target account
        if ($targetAccount->currency_id !== $sendCurrency->id) {
            return $this->sendError('You can only send ' . $targetAccount->currencyName() . ' to this account');
        }

        $accountCurrency = Currencies::where('id', $account['currency_id'])->first();

        // Check if there is money for trasfer
        $usdInAccount = round($account->balance / $accountCurrency->rate, 2);
        $usdToSend = round($input['amount'] / $sendCurrency->rate, 2);

        if ($usdInAccount < $usdToSend) {
            return $this->sendError('There is not enough balance in account to transfer');
        }

        // Add trasfer logs
        Transfers::create([
            'account_id' => $account->id,
            'target_account_id' => $targetAccount->id,
            'amount' => $input['amount'],
            'transfered_at' => Carbon::now(),
        ]);

        // Trasfer money
        $usdToAccountCurrency = round($usdToSend * $accountCurrency->rate, 2);
        $newBalance = $account->balance - $usdToAccountCurrency;

        // Safe check, this should not execute
        if ($newBalance < 0.0) {
            return $this->sendError('There is not enough balance in account to transfer');
        }

        Accounts::where('id', $account->id)->update(['balance' => $newBalance]);


        // Add money to new account
        $newBalance = $targetAccount->balance + $input['amount'];
        Accounts::where('id', $targetAccount->id)->update(['balance' => $newBalance]);


        return $this->sendResponse([]);
    }

    /**
     * Show all transactions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function transactions(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'account_id' => 'required|numeric|gt:-1',
            'limit' => 'numeric|gt:0',
            'offset' => 'numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $limit = $input['limit'] ?? 10;
        $offset = $input['offset'] ?? 0;

        $transactions = Transfers::where('account_id', $input['account_id'])
            ->orderByDesc('id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return $this->sendResponse($transactions->toArray());
    }

    /**
     * Success response
     *
     * @param array $result
     * @return JsonResponse
     */
    private function sendResponse(array $result): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }

    /**
     * Return error response
     *
     * @param string $error
     * @param integer $code
     * @return JsonResponse
     */
    private function sendError(string $error, int $code = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}
