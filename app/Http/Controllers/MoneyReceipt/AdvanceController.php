<?php

namespace App\Http\Controllers\MoneyReceipt;

use App\Http\Controllers\Controller;
use App\Models\AdvanceMoneyReceipt;
use App\Traits\CodeGeneration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdvanceController extends Controller
{
    use CodeGeneration;

    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $query = AdvanceMoneyReceipt::join('Customer', 'Customer.CustomerCode', 'AdvanceMoneyReceipt.CustomerCode');
        if (Auth::user()->RoleId === 'customer') {
            $query->where('AdvanceMoneyReceipt.CustomerCode',Auth::user()->UserId);
        }
        if ($search !== '') {
            $query->where('AdvanceMoneyReceipt.MoneyRecNo','like','%'.$search.'%');
        }
        return $query->orderBy('CreatedAt', 'desc')
            ->select('AdvanceMoneyReceipt.MoneyRecNo', 'Customer.CustomerCode', 'Customer.CustomerName', 'AdvanceMoneyReceipt.Amount', 'AdvanceMoneyReceipt.CreatedAt', 'AdvanceMoneyReceipt.Active as InvoiceStatus')
            ->paginate($take);
    }

    public function getById($moneyRecNo)
    {
        $invoice = AdvanceMoneyReceipt::join('Customer as c','c.CustomerCode','AdvanceMoneyReceipt.CustomerCode')
            ->where('MoneyRecNo',$moneyRecNo)
            ->select('AdvanceMoneyReceipt.*','c.CustomerName')
            ->first();
        $invoices = DB::table('AdvanceMoneyReceiptDetails','AMR')
            ->join('AdvanceMoneyType as AT','AT.TypeId','AMR.AdvanceMoneyType')
            ->where('AMR.MoneyRecNo',$moneyRecNo)
            ->select('AMR.*','AT.TypeName')
            ->get();
        return response()->json([
            'invoice' => $invoice,
            'invoices' => $invoices
        ]);
    }
    public function getAdvanceTypes()
    {
        return response()->json([
            'types' => DB::table('AdvanceMoneyType')
                ->where('Active','Y')
                ->select('TypeId','TypeName',DB::raw("0 as Amount"))
                ->get()
        ]);
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'invoiceTo' => 'required|max:150',
            'invoicePhone' => 'required|max:18',
            'invoiceAddress' => 'required|max:200',
            'engineNo' => 'max:20',
            'frameNo' => 'max:50',
            'types' => 'required|array',
        ]);
        if (Auth::user()->RoleId !== 'customer') {
            return response()->json([
                'message' => 'This module is for customers.'
            ],400);
        }
        if (count($request->types) < 1) {
            return response()->json([
                'message' => 'No amount found!'
            ],400);
        }
        DB::beginTransaction();
        try {
            //GENERATE MONEY REC NO
            $moneyRecNo = $this->generateAdvanceMoneyReceipt(Auth::user()->UserId);
            //MASTER TABLE
            AdvanceMoneyReceipt::create([
                'MoneyRecNo' => $moneyRecNo,
                'CustomerCode' => Auth::user()->UserId,
                'InvoiceTo' => $request->invoiceTo,
                'InvoicePhone' => $request->invoicePhone,
                'InvoiceAddress' => $request->invoiceAddress,
                'EngineNo' => $request->engineNo,
                'FrameNo' => $request->frameNo,
                'Amount' => $request->amount,
                'CreatedAt' => Carbon::now()
            ]);
            //DETAILS TABLE
            foreach ($request->types as $type) {
                DB::table('AdvanceMoneyReceiptDetails')->insert([
                    'MoneyRecNo' => $moneyRecNo,
                    'AdvanceMoneyType' => $type['TypeId'],
                    'Amount' => $type['Amount']
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Money receipt generated successfully.'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return response()->json([
                'message' => 'Something went wrong!'
            ],500);
        }
    }
}
