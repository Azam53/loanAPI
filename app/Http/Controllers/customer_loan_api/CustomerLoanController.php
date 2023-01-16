<?php

namespace App\Http\Controllers\customer_loan_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CustomerLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show($id)
    {
        //
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

    public function get_api_token(Request $request)
    {
        $api_token = env('LOAN_API_TOKEN');
        echo $api_token;
        exit;
    }

    public function create_customer_loan(Request $request)
    {
        $header_api_key = !empty($request->header('loan-api-key')) ? $request->header('loan-api-key') : '';

        $data = array(
            "status" => "0",
            "message" => "Something went wrong"
        );

        if(empty($header_api_key)){
            $data = [
                "status" => "0",
                'message' => "Please provide api key to access this API",
            ];
        }else{
            $request_data = $request->all();
            $customer_id = !empty($request_data['customer_id']) ? $request_data['customer_id'] : 0;
            $loan_name = !empty($request_data['loan_name']) ? $request_data['loan_name'] : 'Customer Loan';
            $loan_amount = !empty($request_data['loan_amount']) ? $request_data['loan_amount'] : '';
            $loan_description = !empty($request_data['loan_description']) ? $request_data['loan_description'] : '';

            if(empty($customer_id) || empty($loan_amount)){
                $data = [
                    "status" => "0",
                    'message' => "Please provide customer id and loan amount",
                ];
            }else{
                DB::table('customer_loans')->insert(
                    array(
                        'customer_id' => $customer_id,
                        'loan_amount' => $loan_amount,
                        'loan_name' => $loan_name,
                        'loan_description' => $loan_description
                    )
                );

                $data = [
                    "status" => "200",
                    "message" => "Loan created successfully",
                ];
            }
        }

        return response()->json($data);
    }

    public function get_loan_list(Request $request)
    {
        $header_api_key = !empty($request->header('loan-api-key')) ? $request->header('loan-api-key') : '';

        $data = array(
            "status" => "0",
            "message" => "Something went wrong",
            "details" => array(),
        );

        if(empty($header_api_key)){
            $data = [
                "status" => "0",
                'message' => "Please provide api key to access this API",
                "details" => array(),
            ];
        }else{
            $request_data = $request->all();
            $customer_id = !empty($request_data['customer_id']) ? $request_data['customer_id'] : 0;
            if(!empty($customer_id)){
                $loan_list = DB::table('customer_loans')->where('customer_id', '=', $customer_id)->get();
            }else{
                $loan_list = DB::table('customer_loans')->get();
            }

            $loan_list = json_decode(json_encode($loan_list), TRUE);

            $data = [
                "status" => "200",
                "message" => "Loan list display successfully",
                "details" => $loan_list,
            ];
        }
        
        return response()->json($data);
    }

    public function create_customer_loan_request(Request $request)
    {
        $header_api_key = !empty($request->header('loan-api-key')) ? $request->header('loan-api-key') : '';

        $data = array(
            "status" => "0",
            "message" => "Something went wrong"
        );

        if(empty($header_api_key)){
            $data = [
                "status" => "0",
                'message' => "Please provide api key to access this API",
            ];
        }else{
            $request_data = $request->all();
            $customer_id = !empty($request_data['customer_id']) ? $request_data['customer_id'] : 0;
            $loan_id = !empty($request_data['loan_id']) ? $request_data['loan_id'] : 0;
            $loan_amount = !empty($request_data['loan_amount']) ? floatval($request_data['loan_amount']) : '';
            $term_duration = !empty($request_data['term_duration']) ? intval($request_data['term_duration']) : '';
            $loan_applied_date = !empty($request_data['loan_applied_date']) ? date('Y-m-d', strtotime($request_data['loan_applied_date'])) : '';

            if(empty($customer_id) || empty($loan_id) || empty($loan_amount) || empty($term_duration) || empty($loan_applied_date) ){
                $data = [
                    "status" => "0",
                    'message' => "Please provide customer id, loan id, loan amount, term duration and loan applied date fields value",
                ];
            }else{
                DB::table('customer_loan_requests')->insert(
                    array(
                        'customer_id' => $customer_id,
                        'loan_id' => $loan_id,
                        'loan_amount' => $loan_amount,
                        'term_duration' => $term_duration,
                        'loan_applied_date' => $loan_applied_date,
                    )
                );

                $loan_request_id = DB::getPdo()->lastInsertId();

                $schedule_amount = $loan_amount / $term_duration;

                $next_payment_date = $loan_applied_date;
                for($i = 1; $i<=$term_duration; $i++){
                    DB::table('customer_loan_scheduled_payments')->insert(
                        array(
                            'customer_id' => $customer_id,
                            'loan_request_id' => $loan_request_id,
                            'payment_date' => $next_payment_date,
                            'payment_amount' => $schedule_amount,
                        )
                    );

                    $next_payment_date = date("Y-m-d", strtotime("+7 days", strtotime($next_payment_date)));
                }

                $data = [
                    "status" => "200",
                    "message" => "Loan request sent successfully",
                ];
            }
        }

        return response()->json($data);
    }


    public function get_customer_loan_requests(Request $request)
    {
        $header_api_key = !empty($request->header('loan-api-key')) ? $request->header('loan-api-key') : '';

        $data = array(
            "status" => "0",
            "message" => "Something went wrong",
            "details" => array(),
        );

        if(empty($header_api_key)){
            $data = [
                "status" => "0",
                'message' => "Please provide api key to access this API",
                "details" => array(),
            ];
        }else{
            $request_data = $request->all();
            $customer_id = !empty($request_data['customer_id']) ? $request_data['customer_id'] : 0;

            if(empty($customer_id)){
                $data = [
                    "status" => "0",
                    'message' => "Please provide customer id",
                    "details" => array(),
                ];
            }else{
                $loan_details = DB::table('customer_loan_requests')->where([['loan_paid_status', '=', 0], ['customer_id', '=', $customer_id]])->get();
                if(!empty($loan_details))
                {
                    $loan_details = json_decode(json_encode($loan_details), TRUE);
                    foreach($loan_details as $loan_details_key => $loan_details_val)
                    {
                        $loan_request_id = $loan_details_val['id'];

                        //Get loan scheduled payment details
                        $tmp_loan_schedule_payment_details = DB::table('customer_loan_scheduled_payments')->where([['customer_id', '=', $customer_id], ['loan_request_id', '=', $loan_request_id]])->get();

                        $loan_details[$loan_details_key]['schedule_payment_details'] = $tmp_loan_schedule_payment_details;

                        //Get Load received payment details
                        $tmp_loan_received_payment_details = DB::table('customer_loan_payments')->where([['customer_id', '=', $customer_id], ['loan_request_id', '=', $loan_request_id]])->get();
                        $loan_details[$loan_details_key]['received_payment_details'] = $tmp_loan_received_payment_details;
                    }
                }

                $data = [
                    "status" => "200",
                    'message' => "Loan details received successfully",
                    "details" => $loan_details,
                ];
            }
        }

        return response()->json($data);
    }

    public function customer_loan_schedule_payment_add(Request $request)
    {
        $header_api_key = !empty($request->header('loan-api-key')) ? $request->header('loan-api-key') : '';

        $data = array(
            "status" => "0",
            "message" => "Something went wrong",
        );

        if(empty($header_api_key)){
            $data = [
                "status" => "0",
                'message' => "Please provide api key to access this API",
            ];
        }else{
            $request_data = $request->all();

            $customer_id = !empty($request_data['customer_id']) ? $request_data['customer_id'] : 0;
            $loan_request_id = !empty($request_data['loan_request_id']) ? $request_data['loan_request_id'] : 0;
            $payment_date = !empty($request_data['payment_date']) ? $request_data['payment_date'] : 0;
            $payment_amount = !empty($request_data['payment_amount']) ? $request_data['payment_amount'] : 0;

            if(empty($customer_id) || empty($loan_request_id) || empty($payment_date) || empty($payment_amount))
            {
                $data = [
                    "status" => "0",
                    'message' => "Please provide customer id, loan request id, payment date and payment amount",
                ];
            }
            else
            {
                DB::table('customer_loan_payments')->insert(array(
                    'customer_id' => $customer_id,
                    'loan_request_id' => $loan_request_id,
                    'payment_date' => $payment_date,
                    'payment_amount' => $payment_amount,
                    'payment_status' => 0,
                ));

                //Get loan request id details
                $get_loan_request_details = DB::table('customer_loan_requests')->where('id', '=', $loan_request_id)->first();
                if(!empty($get_loan_request_details)){
                    $total_loan_amount = $get_loan_request_details->loan_amount;

                    //Get total of received payment
                    $get_total_received_payment_details = DB::table('customer_loan_payments')->where([['customer_id', '=', $customer_id], ['loan_request_id', '=', $loan_request_id]])->get();
                    $total_received_amount = 0;
                    if(!empty($get_total_received_payment_details)){
                        foreach($get_total_received_payment_details as $k1 => $v1){
                            $total_received_amount = $total_received_amount + $v1->payment_amount;
                        }
                    }

                    if($total_loan_amount <= $total_received_amount){
                        //Update loan status to paid
                        DB::table('customer_loan_requests')->where('id', '=', $loan_request_id)->update(array('loan_paid_status' => 1));
                    }
                }

                $data = [
                    "status" => "200",
                    'message' => "Payment received successfully",
                ];
            }
        }

        return response()->json($data);
    }

    public function customer_approve_loan(Request $request)
    {
        $header_api_key = !empty($request->header('loan-api-key')) ? $request->header('loan-api-key') : '';

        $data = array(
            "status" => "0",
            "message" => "Something went wrong",
        );

        if(empty($header_api_key)){
            $data = [
                "status" => "0",
                'message' => "Please provide api key to access this API",
            ];
        }else{
            $request_data = $request->all();
            $loan_request_id = !empty($request_data['loan_request_id']) ? $request_data['loan_request_id'] : 0;

            if(empty($loan_request_id)){
                $data = [
                    "status" => "0",
                    'message' => "Please pass loan request id",
                ];   
            }else{
                DB::table('customer_loan_requests')->where('id', '=', $loan_request_id)->update(array('loan_status' => 1));
                $data = [
                    "status" => "1",
                    'message' => "Loan approved successfully",
                ];
            }
        }
        
        return response()->json($data);        
    }
}
