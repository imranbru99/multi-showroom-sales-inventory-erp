@extends('admin.layouts.masterView')

@section('custom_css')
<style type="text/css">
    .info_head {
        font-weight: bold;
    }
</style>
@endsection

@section('card_body')
<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-sm productTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" colspan="2"><b>Customer Information</b></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="info_head" width="100px">Code</td>
                            <td>{{ @$retailSales->customer->code }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="100px">Name</td>
                            <td>{{ @$retailSales->customer->name }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="100px">Contact</td>
                            <td>{{ @$retailSales->customer->phone_no }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="100px">Address</td>
                            <td>{{ @$retailSales->customer->current_residence }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="{{ @$retailSales->purchase_type == "Cash" ? 'col-md-12' : 'col-md-6' }}">
            <div class="table-responsive">
                <table class="table table-bordered table-sm productTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" colspan="2"><b>Sales Information</b></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="info_head" width="200px">Purchase Date</td>
                            <td>{{ @$retailSales->sale_date }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Product Model</td>
                            <td>
                                <?php
                                $modelNo = [];
                                foreach (@$retailSales->products as $product) {

                                    $modelNo[] = $product->product->model_no;
                                }
                                echo implode(', ', $modelNo);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Product Name</td>
                            <td>
                                <?php
                                $productName = [];
                                foreach (@$retailSales->products as $product) {

                                    $productName[] = $product->product->name;
                                }
                                echo implode(', ', $productName);
                                ?>
                            </td>

                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Quantity</td>
                            <td>
                                <?php
                                $qty = 0;
                                foreach (@$retailSales->products as $product) {

                                    $qty += $product->qty;
                                }
                                echo $qty;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Purchase Type</td>
                            <td>{{ @$retailSales->sale_type }}</td>
                        </tr>
                        @if (@$retailSales->sale_type == "Cash")
                        <tr>
                            <td class="info_head" width="200px">Cash Price</td>
                            <td>{{ @$retailSales->products->sum('cash_price') }}</td>
                        </tr>
                        @else
                        @if (@$retailSales->sale_type == "Short Installment")
                        <tr>
                            <td class="info_head" width="200px">MRP Price</td>
                            <td>{{ @$retailSales->products->sum('mrp_price') }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="info_head" width="200px">Hire Price</td>
                            <td>{{ @$retailSales->installment_price }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info_head" width="200px">Deposite</td>
                            <td>{{ @$retailSales->deposite }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Total Installment</td>
                            <td>{{ @$retailSales->total_installment }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Monthly Installment Price</td>
                            <td>{{ @$retailSales->monthly_installment_amount }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Discount</td>
                            <td>{{ @$retailSales->discount }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Gift Voucher</td>
                            <td>{{ @$retailSales->gift_voucher }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Exchange CRT</td>
                            <td>{{ @$retailSales->exchange_crt }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Mobile Gift</td>
                            <td>{{ @$retailSales->mobile_gift }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if (@$retailSales->purchase_type != "Cash")
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-bordered table-sm productTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" colspan="2"><b>Guarantor Information</b></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="info_head" width="200px">Name</td>
                            <td>{{ @$retailSales->guarantor[0]->gurantor_name }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Father Name</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_father_name }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Contact</td>
                            <td>{{ @$retailSales->guarantor[0]->gurantor_phone_no }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Age</td>
                            <td>{{ @$retailSales->guarantor[0]->gurantor_age }}</td>
                        </tr>

                        @if (@$retailSales->guarantor_marital_status == "Married")
                        <tr>
                            <td class="info_head" width="200px">Spouse Name</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_spouse_name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info_head" width="200px">Present Address</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_present_address }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Permanent Address</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_permanent_address }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Profession Name</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_profession_name }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Designation</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_designation }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Professional Contact</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_workplace_phone_no }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Monthly Income</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_monthly_income }}</td>
                        </tr>
                        <tr>
                            <td class="info_head" width="200px">Work Place Address</td>
                            <td>{{ @$retailSales->guarantor[0]->guarantor_work_place_address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection