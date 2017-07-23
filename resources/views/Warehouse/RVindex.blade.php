@extends('layouts.master')
@section('title')
  Requisition Voucher|index
@endsection
@section('body')
  <div class="RV-index-container">
    <div class="top-RV-index">
      <div class="rv-index-title">
        <h1>Requisition Voucher index</h1>
      </div>
      <div class="searchbox-RV">
        <form class="search-form-rv" action="{{route('SearchingRV')}}" method="get">
          <input type="text" id="searchRVNum" name="RVNo" placeholder="Search RV No." required><button type="submit"><i class="fa fa-search"></i></button>
        </form>
      </div>
    </div>
    <div class="RV-index-table">
      <table>
        <thead>
          <tr>
            <th>RV No.</th>
            <th>Purpose</th>
            <th>Requisitioner</th>
            <th>Recommended by</th>
            <th>Budget Officer</th>
            <th>General Manager</th>
            <th>RV Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @if (!empty($allRVMaster[0]))
            @foreach ($allRVMaster as $RV)
              <tr>
                <td>{{$RV->RVNo}}</td>
                <td>{{$RV->Purpose}}</td>
                <td>
                  {{$RV->Requisitioner}}
                  @if ($RV->RequisitionerSignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                  @if ($RV->IfDeclined==$RV->Requisitioner)
                    <br><i class="fa fa-times decliner"></i>
                  @endif
                </td>
                <td>
                  {{$RV->Recommendedby}}
                  @if ($RV->RecommendedbySignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                  @if ($RV->IfDeclined==$RV->Recommendedby)
                    <br><i class="fa fa-times decliner"></i>
                  @endif
                </td>
                <td>
                  {{$RV->BudgetOfficer}}
                  @if ($RV->BudgetOfficerSignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                  @if ($RV->IfDeclined==$RV->BudgetOfficer)
                    <br><i class="fa fa-times decliner"></i>
                  @endif
                </td>
                <td>
                  {{$RV->GeneralManager}}
                  @if ($RV->GeneralManagerSignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                  @if ($RV->IfDeclined==$RV->GeneralManager)
                    <br><i class="fa fa-times decliner"></i>
                  @endif
                </td>
                <td>{{$RV->RVDate->format('m/d/Y')}}</td>
                <td>
                  @if ($RV->IfDeclined==null)
                    @if (($RV->RequisitionerSignature!=null)&&($RV->RecommendedbySignature!=null)&&($RV->BudgetOfficerSignature!=null)&&($RV->GeneralManagerSignature!=null))
                      <i class="fa fa-thumbs-up"></i>
                    @else
                      <i class="fa fa-clock-o"></i>
                    @endif
                  @else
                    <i class="fa fa-times decliner"></i>
                  @endif
                </td>
                <td>
                  <a href="{{route('RVfullpreviewing',[$RV->RVNo])}}"><i class="fa fa-eye"></i></a>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
      @if (!empty($allRVMaster[0]))
        {{$allRVMaster->links()}}
      @endif
    </div>
  </div>
@endsection
