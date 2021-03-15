<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Package;
use App\VNpayInvoice;
use App\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Notifications\CompanyUpdatedPlan;
use App\User;
use Illuminate\Support\Facades\Notification;
use App\StripeSetting;
use App\Traits\StripeSettings;

class VNpayController extends Controller
{
    use StripeSettings;
    public function redirectToGateway(Request $request)
    {
        $stripe = StripeSetting::first();
        $package = Package::find($request->plan_id);
        $request->first_name = $request->name;
        $request->email = $request->vnpayEmail;
        $request->orderID = '1';
        $request->amount = $package->{$request->type.'_price'};
        $request->quantity = '1';
        // $request->reference = $paystack->genTranxRef();
        $vnp_TmnCode =$stripe->vnp_TmnCode; //Mã website tại VNPAY 
        $vnp_HashSecret = $request->key = $stripe->vnp_HashSecret;
        $request->plan = $package->{'vnpay_'.$request->type.'_plan_id'};
        $vnp_Locale =$request->currency = 'vn';
        session([
            'package_id' => $package->id,
            'package_type' => $request->type,
            'package_amount' => $package->{$request->type.'_price'},
        ]);
       
        $vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('admin.payments.vnpay.callback');
        $vnp_TxnRef = date("YmdHis"); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán hóa đơn phí dich vụ";
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $package->{$request->type.'_price'} * 100;
        $vnp_IpAddr = request()->ip();
        $vnp_BankCode='';

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

    public function handleGatewayCallback()
    {
        // if($paymentDetails['status']) {
            $company = Company::findOrFail(company()->id);
            $vnpayInvoices = new VNpayInvoice();

            $vnpayInvoices->company_id = company()->id;
            $vnpayInvoices->package_id = Session::get('package_id');
            // $vnpayInvoices->transaction_id = $paymentDetails['data']['reference'];
            $vnpayInvoices->amount = Session::get('package_amount');
            $vnpayInvoices->pay_date = Carbon::now()->format('Y-m-d');

            $packageType = Session::get('package_type');

            if($packageType == 'monthly') {
                $vnpayInvoices->next_pay_date = Carbon::now()->addMonth()->format('Y-m-d');
            } else {
                $vnpayInvoices->next_pay_date = Carbon::now()->addYear()->format('Y-m-d');
            }
            
            $vnpayInvoices->save();

            $company->package_id = $vnpayInvoices->package_id;
            $company->package_type = ($packageType == 'annual') ? 'annual' : 'monthly';
            $company->status = 'active';
            $company->licence_expire_on = null;
            $company->save();

            //send superadmin notification
            $generatedBy = User::allSuperAdmin();
            Notification::send($generatedBy, new CompanyUpdatedPlan($company, $company->package_id));
        // }

        return redirect(route('admin.billing.packages'));
    }
}
