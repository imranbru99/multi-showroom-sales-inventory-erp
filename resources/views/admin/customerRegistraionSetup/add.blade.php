@extends('admin.layouts.masterAddEdit')

@section('card_body')
    @php
        $maritalStatus = array('Unmarried' => 'Unmarried', 'Married' => 'Married');
    @endphp
    <style type="text/css">
        .blockTitle{
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }
    </style>

    @include('admin/customerRegistraionSetup/element/add/personal_information')
    @include('admin/customerRegistraionSetup/element/add/professional_information')
    @include('admin/customerRegistraionSetup/element/add/product_details')
    @include('admin/customerRegistraionSetup/element/add/first_guarantor_information')
    @include('admin/customerRegistraionSetup/element/add/second_guarantor_information')
 
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function() {
        /*code for spouce info*/
            $(".spouceName").prop('disabled', true);
            $('.maritalStatus').click(function(event) {
                var maritalStatus = $('.maritalStatus').val();
                if(maritalStatus == "Married"){
                    $(".spouceName").prop('disabled', false);
                }else{
                     $(".spouceName").prop('disabled', true);
                }
            })
        /*end code for spouce info*/
            
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            /*code for guarantor info*/
            $(".firstGuarantorSpouce").prop('disabled', true);
            $(".secondGuarantorSpouce").prop('disabled', true);
                $('.firstGuarantorMaritalStatus').click(function(event) {
                    var firstGuarantorMaritalStatus = $('.firstGuarantorMaritalStatus').val();
                    if(firstGuarantorMaritalStatus == "Married"){
                         $(".firstGuarantorSpouce").prop('disabled', false);
                    }else{
                         $(".firstGuarantorSpouce").prop('disabled', true);
                        $(".firstGuarantorSpouce").val('');
                    }
                })

                $('.secondGuarantorMaritalStatus').click(function(event) {
                    var secondGuarantorMaritalStatus = $('.secondGuarantorMaritalStatus').val();
                    if(secondGuarantorMaritalStatus == "Married"){
                         $(".secondGuarantorSpouce").prop('disabled', false);
                    }else{
                         $(".secondGuarantorSpouce").prop('disabled', true);
                        $(".secondGuarantorSpouce").val('');
                    }
                })

            /*end code for guarantor info*/

            // start code installment

            $('.installmentType').hide();
            $('.longInstallmentRow').hide();
            $('.shortInstallmentRow').hide();

             $('.purchaseType').click(function(event) {
                var purchaseType =  $("input[name='purchaseType']:checked").val();

                if(purchaseType == "Cash")
                {
                    $('.installmentType').hide();
                    $("select[name='installmentType']").prop('required',false);
                }

                if(purchaseType == "Short Installment")
                {
                    $('.shortInstallmentRow').show();
                    $('.installmentType').show();
                    $("input[name='shortInstallmentDeposite']").prop('required',true);
                    $("input[name='shortInstallmentPrice']").prop('required',true);
                    $("input[name='shortTotalInstallment']").prop('required',true);
                    $("input[name='shortInstallmentAmount']").prop('required',true);
                    $("select[name='installmentType']").prop('required',true);
                }
                else
                {
                    $('.shortInstallmentRow').hide();
                    $("input[name='shortInstallmentDeposite']").prop('required',false);
                    $("input[name='shortInstallmentPrice']").prop('required',false);
                    $("input[name='shortTotalInstallment']").prop('required',false);
                    $("input[name='shortInstallmentAmount']").prop('required',false);
                }

                if(purchaseType == "Long Installment")
                {
                    $('.longInstallmentRow').show();
                    $('.installmentType').show();
                    $("input[name='longInstallmentDeposite']").prop('required',true);
                    $("input[name='longInstallmentPrice']").prop('required',true);
                    $("input[name='longTotalInstallment']").prop('required',true);
                    $("input[name='longInstallmentAmount']").prop('required',true);
                    $("select[name='installmentType']").prop('required',true);
                }
                else
                {
                    $('.longInstallmentRow').hide();
                    $("input[name='longInstallmentDeposite']").prop('required',false);
                    $("input[name='longInstallmentPrice']").prop('required',false);
                    $("input[name='longTotalInstallment']").prop('required',false);
                    $("input[name='longInstallmentAmount']").prop('required',false);
                }
            })

            /*end code for installment*/            
        });
    </script>


    <script type="text/javascript">
    /*code for product info*/

        $(document).on('change', '.product', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var productId = $('.product option:selected').val();
            if(productId != '')
            {
                $.ajax({
                    type:'post',
                    url:'{{ route('customerRegistration.getProductInfo') }}',
                    data:{productId:productId},
                    success:function(data){
                        var product = data.product;
                        var purchaseType =  $("input[name='purchaseType']:checked").val();
                        $('.productModel').val(product.model_no);
                        $('.cashPrice').val(product.price);
                        $('.warranty').val(product.warranty);

                        if (purchaseType == 'Short Installment')
                        {
                            $('.shortInstallmentPrice').val(product.mrp_price);
                        }
                        else
                        {
                            $('.shortInstallmentPrice').val('');                             
                        }

                        if (purchaseType == 'Long Installment')
                        {
                            $('.longInstallmentPrice').val(product.haire_price);
                        }
                        else
                        {
                            $('.longInstallmentPrice').val('');                             
                        }
                        calculateShortInstallmentAmount();
                        calculateLongInstallmentAmount();
                    }
                });
            }
            else
            {
               $('.productModel').val('');
               $('.cashPrice').val('');  
               $('.warranty').val(''); 
               $('.longInstallmentPrice').val(''); 
               $('.shortInstallmentPrice').val(''); 
            }
        });

        function calculateShortInstallmentAmount()
        {
            var shortInstallmentDeposite = parseFloat($('.shortInstallmentDeposite').val());
            var shortInstallmentPrice = parseFloat($('.shortInstallmentPrice').val());
            var shortTotalInstallment = parseFloat($('.shortTotalInstallment').val());

            if (shortTotalInstallment == 0 || $('.shortTotalInstallment').val() == "")
            {
                var shortInstallmentAmount = (shortInstallmentPrice - shortInstallmentDeposite);
            }
            else
            {
                var shortInstallmentAmount = (shortInstallmentPrice - shortInstallmentDeposite)/shortTotalInstallment;                
            }

            $('.shortInstallmentAmount').val(Math.round(shortInstallmentAmount));
        }

        function calculateLongInstallmentAmount()
        {
            var longInstallmentDeposite = parseFloat($('.longInstallmentDeposite').val());
            var longInstallmentPrice = parseFloat($('.longInstallmentPrice').val());
            var longTotalInstallment = parseFloat($('.longTotalInstallment').val());

            if (longTotalInstallment == 0 || $('.longTotalInstallment').val() == "")
            {
                var longInstallmentAmount = (longInstallmentPrice - longInstallmentDeposite);
            }
            else
            {
                var longInstallmentAmount = (longInstallmentPrice - longInstallmentDeposite)/longTotalInstallment;                
            }

            $('.longInstallmentAmount').val(Math.round(longInstallmentAmount));
        }

    /*end code for product info*/

    </script>

    <script type="text/javascript">
    /*code for applicants code*/
        function ApplicantCode() {
            var applicantsId = document.getElementById("applicants_id").value;
            var applicantsPhoneNo = document.getElementById("applicants_phone_no").value;
             lastThreeDigitofPhoneNo = applicantsPhoneNo % 1000;
            document.getElementById("apllicantsCode").value = "mnh-" +applicantsId+"-"+lastThreeDigitofPhoneNo;
        }
    /*end code for applicants code*/
    </script>
@endsection