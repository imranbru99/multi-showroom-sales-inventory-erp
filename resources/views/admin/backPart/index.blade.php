@extends('admin.layouts.masterReportBack')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-6">
            <label for="product-category">Product Model</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="productModel" name="productModel[]">
                    @foreach ($products as $productInfo)
                        @php
                            $select = "";
                            if ($product)
                            {
                                if (in_array($productInfo->id, $product))
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $productInfo->id }}" {{ $select }}>{{ $productInfo->model_no }}</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-6">
            <label for="product">Products QTY</label>
            <div class="form-group">
                <input  type="number" class="form-control" id="qty" name="qty" value="1" min="1">
            </div>  
        </div>

    </div>	
@endsection

@section('print_card_header')
    @if ($productCategory)
        @foreach ($productCategory as $productCategoryInfo)
            <input type="hidden" name="productCategory[]" value="{{ $productCategoryInfo }}">
        @endforeach
    @endif

    @if ($product)
        @foreach ($product as $productInfo)
            <input type="hidden" name="product[]" value="{{ $productInfo }}">
        @endforeach
    @endif
    
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')

<table class="table table-striped table-hover"  id="dataTables-teachers">
              
                <tr>
                  <td>

                    <table style=" border: 0px solid;">
                      <tbody style="border: 1px solid; border-radius: 10px; padding: 10px; display: inline-block; width: 226.77px; height:359.05px; box-sizing: border-box; margin-right:8px; 
                            
                             ">
                        
                        <tr>
                          <td colspan="2" style="width: 226.77px; height:226px;">
                            <table style="width:100%;font-size: 8pt; ">
                              <tr>
                                <td  colspan="6" style="text-align: center; font-size: 15pt;padding-bottom: 16px;"><b></b></td>
                              </tr>
                              <tr>
                                <td colspan="6">
                                  <table style="width:100%">
                                    <tr>
                                      <td  style="width:28%;">Class</td>
                                      <td  style="width:2%;">:</td>
                                      <td  style="width:70%;"><b></b></td>
                                    </tr>
                                  </table>
                                </td>

                              </tr>
                              <tr>
                                <td colspan="6">
                                  <table style="width:100%">
                                    <tr>
                                      <td  style="width:28%;">Roll</td>
                                      <td  style="width:2%;">:</td>
                                      <td  style="width:70%;"><b>{{$student->class_roll}}</b></td>
                                    </tr>
                                  </table>
                                </td>

                              </tr>
                              <tr>
                                <td colspan="6">
                                  <table style="width:100%">
                                    <tr>
                                      <td  style="width:28%;">Section</td>
                                      <td  style="width:2%;">:</td>
                                      <td  style="width:70%;"><b>{{$section_name}}</b></td>
                                    </tr>
                                  </table>
                                </td>

                              </tr>
                              <tr>
                                <td colspan="6">
                                  <table style="width:100%">
                                    <tr>
                                      <td  style="width:28%;">Shift</td>
                                      <td  style="width:2%;">:</td>
                                      <td  style="width:70%;"><b>{{$student->shift}}</b></td>
                                    </tr>
                                  </table>
                                </td>

                              </tr>
                              <tr>
                                <td colspan="6">
                                  <table style="width:100%">
                                    <tr>
                                      <td  style="width:28%;">Mobile</td>
                                      <td  style="width:2%;">:</td>
                                      <td  style="width:70%;"><b>{{Session::get("phone")}}</b></td>
                                    </tr>
                                  </table>
                                </td>

                              </tr>
                              <tr>
                                <td colspan="6">
                                  <table style="width:100%">
                                    <tr>
                                      <td  style="width:28%;">Session</td>
                                      <td  style="width:2%;">:</td>
                                      <td  style="width:70%;"><b>{{$student->session}}</b></td>
                                    </tr>
                                  </table>
                                </td>

                              </tr>
                              

                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" style="width:226.77px; height:133.05px;">
                            <table style="width:100%; font-size: 8pt;">
                              <tr>
                                <td style="text-align: center;">
                                  <span style="border-top: 1px solid;">Principle Signature</span>
                                </td>
                              </tr>
                             
                            </table>
                          </td>
                        </tr>

                      </tbody>
                    </table>

                  </td>
                  
                </tr>
                                    
          </table>

	
@endsection
