<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        if (\Session()->has('backUrl')) {
            \Session()->forget('backUrl');
        }

        $user = Auth::user();
        $bankAccount = $user->bankAccount;
        if ($bankAccount) {
            return view('web.bank_account.index', compact('bankAccount'));
        } else {
            return view('web.bank_account.create');
        }
    }

    public function edit()
    {
        $user = Auth::user();
        $bankAccount = $user->bankAccount;
        return view('web.bank_account.edit', compact('bankAccount'));
    }

    public function searchBankName(Request $request)
    {
        $backUrl = \URL::previous();
        $urlEdit = route('bank_account.edit');

        if ($backUrl == $urlEdit) {
            $request->session()->put('backUrl', $backUrl);
        }

        return view('web.bank_account.bank_name');
    }

    public function bankName(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $bankName = $request->bank_name;
        $infoBank = [
            'bank_code' => $request->bank_code,
            'bank_name' => $request->bank_name,
            'branch_code' => $request->branch_code,
            'branch_name' => $request->branch_name,
        ];

        $res = $client->request('GET', "https://bankcode-api.appspot.com/api/bank/JP?name=$bankName");
        $listResult = collect(json_decode($res->getBody()->getContents())->data);

        return view('web.bank_account.bank_name', compact('listResult', 'infoBank'));
    }

    public function searchBranchBankName(Request $request)
    {
        $backUrl = \URL::previous();
        $urlEdit = route('bank_account.edit');

        if ($backUrl == $urlEdit) {
            $request->session()->put('backUrl', $backUrl);
        }
        return view('web.bank_account.branch_bank_name');
    }

    public function branchBankName(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $bankName = $request->branch_name;
        $infoBank = [
            'bank_code' => $request->bank_code,
            'bank_name' => $request->bank_name,
            'branch_code' => $request->branch_code,
            'branch_name' => $request->branch_name,
        ];
        $res = $client->request('GET', "https://bankcode-api.appspot.com/api/bank/JP/0001?name=$bankName");
        $listResult = collect(json_decode($res->getBody()->getContents())->data);

        return view('web.bank_account.branch_bank_name', compact('listResult', 'infoBank'));
    }
}
