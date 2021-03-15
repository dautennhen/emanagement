<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Invoice;
use App\ClientPayment;
use Carbon\Carbon;
use Session;
use App\PaymentGatewayCredentials;

class VNpayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $invoice = Invoice::find(\session()->get('invoice_id'));

            // dd($invoice);exit;

            // Save details in database and redirect to paypal
            $clientPayment = new ClientPayment();
       

        // try {
            /**Execute the payment **/
           
            /** dd($result);exit; /** DEBUG RESULT, remove it later **/
                // if ($result->getState() == 'approved') {

                    /** it's all right **/
                    /** Here Write your database logic like that insert record or value in database if you want **/
                    
                    $clientPayment->paid_on = Carbon::now();
                    $clientPayment->currency_id = $invoice->currency_id;
                    $clientPayment->amount = $invoice->total;
                    // dd($_GET['vnp_TransactionNo']);exit;
                    $clientPayment->transaction_id = $_GET['vnp_TransactionNo'];
                    $clientPayment->gateway = 'VNpay';
                    $clientPayment->status = 'complete';
        
                    $clientPayment->company_id = $invoice->company_id;
                    $clientPayment->invoice_id = $invoice->id;
                    $clientPayment->project_id = $invoice->project_id;
                    $clientPayment->save();
                    
                    $invoice->status = 'paid';
                    $invoice->save();
                    

                    Session::put('success','Payment success');
                    return redirect(route('client.invoices.show', session()->get('invoice_id')));
            // }
        // } catch (\Exception $ex) {
        //     Session::put('error','Payment failed');
        //     return redirect(route('client.invoices.show', session()->get('invoice_id')));
        // }

        // Session::put('error','Payment failed');

        // return redirect(route('client.invoices.index', session()->get('invoice_id')));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($invoiceId, Request $request)
    {
        
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($invoiceId,Request $request)
    {
        $invoice = Invoice::find($invoiceId);
        $credential = PaymentGatewayCredentials::withoutGlobalScope(CompanyScope::class)
        ->where('company_id', $invoice->company_id)
        ->first();
        
        // dd($invoice);
        session([
            'invoice_id' => $invoiceId,
        ]);
        session(['invoice_id' => $invoiceId]);
        session(['url_prev' => url()->previous()]);
        $vnp_TmnCode = $credential->vnp_TmnCode; //Mã website tại VNPAY 
        $vnp_HashSecret = $credential->vnp_HashSecret; //Chuỗi bí mật
        $vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('client.vnpay.index', session()->get('invoice_id'));
        $vnp_TxnRef = date("YmdHis").$invoice->company_id.$invoice->id; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán hóa đơn #".$invoice->invoice_number;//nội dung thanh toán
        $vnp_OrderType = 'billpayment';
        //  $request->input('amount') * 100;
        $vnp_Amount =$request->amount = $invoice->total*100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = request()->ip();
        

        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
           // $vnpSecureHash = md5($vnp_HashSecret . $hashdata);
            $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata);
            $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function return(Request $request)
    {
       
    }

    // public function handleGatewayCallback()
    // {
    //     $invoice = Invoice::find(\session()->get('invoice_id'));

    //         $invoice->status = 'paid';
    //         $invoice->save();
    //         // Save details in database and redirect to paypal
    //         $clientPayment = new ClientPayment();
    //         $clientPayment->currency_id = $invoice->currency_id;
    //         $clientPayment->amount = $invoice->total;

    //         $clientPayment->transaction_id = $paymentDetails['data']['reference'];
    //         $clientPayment->gateway = 'Paystack';
    //         $clientPayment->status = 'complete';

    //         $clientPayment->company_id = $invoice->company_id;
    //         $clientPayment->invoice_id = $invoice->id;
    //         $clientPayment->project_id = $invoice->project_id;
    //         $clientPayment->save();


    //     return redirect(route('client.invoices.show', session()->get('invoice_id')));
    // }
}
