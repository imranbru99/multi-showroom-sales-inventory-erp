@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <div class="card-body">
        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Dealer Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="dealer" value="{{ $commission->dealer }}"
                        required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Retail Discount Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_retail_discount" value="{{ $commission->d_retail_discount }}"
                        required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Retail Cash Without Discount Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_retail_cash_without_discount" value="{{ $commission->d_retail_cash_without_discount }}"
                        required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Retail Cash With Discount Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_retail_cash_with_discount" value="{{ $commission->d_retail_cash_with_discount }}"
                        required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Retail Hire Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_retail_hire" value="{{ $commission->d_retail_hire }}"
                        required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Recovery Cash Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_recovery_cash"
                        value="{{ $commission->d_recovery_cash }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Recovery Hire Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_recovery_hire"
                        value="{{ $commission->d_recovery_hire }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP Cash Discount Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_msp_cash_discount"
                        value="{{ $commission->d_msp_cash_discount }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP Cash Without Discount Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_msp_cash_wihout_discount"
                        value="{{ $commission->d_msp_cash_wihout_discount }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP Cash With Discount Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_msp_cash_with_discount"
                        value="{{ $commission->d_msp_cash_with_discount }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP Hire Commission (Dealer & Retail)</label>
                    <input type="text" class="form-control text-right" name="d_msp_hire"
                        value="{{ $commission->d_msp_hire }}" required>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Cash Commission (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="r_cash"
                        value="{{ $commission->r_cash }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Hire Commission (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="r_hire"
                        value="{{ $commission->r_hire }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP-Cash Commission (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="r_msp"
                        value="{{ $commission->r_msp }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP-Hire Commission (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="r_msp_hire"
                        value="{{ $commission->r_msp_hire }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP Cash Commission Without Discount (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="r_msp_cash_without_discount"
                        value="{{ $commission->r_msp_cash_without_discount }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP Cash Commission With Discount (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="r_msp_cash_with_discount_commission"
                        value="{{ $commission->r_msp_cash_with_discount_commission }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>MSP Hire Commission (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="r_msp_hire"
                        value="{{ $commission->r_msp_hire }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Agreement Commission (RETAIL COMMISSION)</label>
                    <input type="text" class="form-control text-right" name="agreement"
                        value="{{ $commission->agreement }}" required>
                </div>
            </div>
        </div>
    </div>
@endsection
