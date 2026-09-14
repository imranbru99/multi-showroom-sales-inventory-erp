@extends('admin.layouts.masterPrint')

@section('custome-css')
    <style>
        #report-table td, #report-table th {
            border: 0px solid #ddd;
           /* padding: 5px;*/
        }

        #up-header{
            background-color: white;
        }
    </style>

@endsection

@php
    use App\Product;
    use App\ShowroomSetup;
@endphp

@section('content')
    <table id="report-header">
        <tr>
            <td>Customer Details Information</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table width="100%">
        <tr>
            <td width="50%">
                <table id="report-table" width="100%">
                    <thead>
                        <tr>
                            <th colspan="3">Personal Information</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td width="40%">Applicant's Code</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->code }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Applicant's Name</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->name }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Nick Name</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->nick_name }}</td>
                        </tr>

                         <tr>
                            <td width="40%">Age</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->age }}</td>
                        </tr>
                        <tr>
                            <td width="40%">Phone No</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->phone_no }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Marital Status</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->marital_status }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Spouse Name</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->spouse_name }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Father's Name</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->fathers_name }}</td>
                        </tr>

                        <tr>
                            <td width="5%">Mother's Name</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->mothers_name }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Gender</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->gender }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Total Family Member</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->total_family_member }}</td>
                        </tr>

                    </tbody>
                </table>
            </td>

            <td width="50%" style="padding-top: -5px;">
                <table id="report-table" width="100%">
                    <thead>
                        <tr>
                            <th colspan="3">Professional Information</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td width="40%">Profession Name</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->profession_name }}</td>
                        </tr>
                        <tr>
                            <td width="40%">Profession Duration</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->profession_duration }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Total Earnign Member</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->total_earning_member }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Designtion</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->designation }}</td>
                        </tr>
                        <tr>
                            <td width="40%">Monthly Income</td>
                            <td width="5%">:</td>
                            <td>{{ $customer->monthly_income }}</td>
                        </tr>

                         <tr>
                            <td width="40%">Work Place Address</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->work_place_address }}</td>
                        </tr>
                    </tbody>
                </table>

                <table id="report-table">
                    <thead>
                        <tr>
                            <th colspan="3">Other's Information</th>
                        </tr>
                    </thead>

                    <tbody>
                         <tr>
                            <td width="40%">Current Residence</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->current_residence }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Residence Duration</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->residence_duration }}</td>
                        </tr>

                         <tr>
                            <td width="40%">Present Address</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->present_address }}</td>
                        </tr>

                        <tr>
                            <td width="40%">Permananet Address</td>
                            <td width="5%">:</td>
                            <td width="55%">{{ $customer->permanent_address }}</td>
                        </tr>

                    </tbody>
                </table>
            </td>
        </tr>
        <tr></tr>
    </table>

    <div id="pad-bottom"></div>    

    <table id="report-table" width="100%">
        <caption style="text-decoration: none;">Previous Product List</caption>
        <thead>
            <tr>
                <th rowspan="2" width="130px">Showroom</th>
                <th colspan="2" style="">Purcahse</th>
                <th rowspan="2" width="125px">Name</th>
                <th rowspan="2">Model</th>
                <th rowspan="2">Price</th>
                <th rowspan="2">Deposite</th>
                <th colspan="3" style="">Installment</th>
            </tr>
            <tr>
                <th width="">Date</th>
                <th width="">Type</th>
                <th width="">Price</th>
                <th width="">Total</th>
                <th width="">Mothly</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customerProducts as $customerProduct)
                 @php
                    $purchaseDate =  date('d-m-Y',strtotime($customerProduct->purchase_date));
                    $productDetails= Product::where('id',$customerProduct->product_id)->first();
                    $showroomDetails= ShowroomSetup::where('id',$customerProduct->showroom_id)->first();
                 @endphp
            <tr>
                <td>{{ $showroomDetails->name }}</td>
                <td>{{ $purchaseDate }}</td>
                <td>{{ $customerProduct->purchase_type }}</td>
                <td>{{ $productDetails->name }}</td>
                <td>{{ $customerProduct->product_model }}</td>
                <td>{{ $customerProduct->cash_price }}</td>
                <td align="center">{{ $customerProduct->deposite }}</td>
                <td>{{ $customerProduct->installment_price }}</td>
                <td align="center">{{ $customerProduct->total_installment }}</td>
                <td>{{ $customerProduct->monthly_installment_amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

     <div id="pad-bottom"></div>   

    <table id="report-table">
        <caption style="text-decoration: none;">Previous Guarantor List</caption>
        <thead>
            <tr>
                <th>Product</th>
                <th width="80px">Model</th>
                <th>Name</th>
                <th>Phone No</th>
                <th width="150px">Present Address</th>
                <th width="150px">Permanent Address</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customerGuarantor as $guarantor)
                 @php
                    $productDetails= Product::where('id',$guarantor->product_id)->first();
                 @endphp
            <tr>
                <td>{{@$productDetails->name}}</td>
                <td>{{@$productDetails->model_no}}</td>
                <td>{{@$guarantor->gurantor_name}}</td>
                <td>{{@$guarantor->gurantor_phone_no}}</td>
                <td>{{@$guarantor->guarantor_present_address}}</td>
                <td>{{@$guarantor->guarantor_permanent_address}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
@endsection