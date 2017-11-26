@extends('layouts.Master')
@section('title')
  RV | Waiting to be purchased List
@endsection
@section('body')
<div class="Unpurchase-RV-list">
  <div class="rv-unpurchased-list">
    <h1><i class="fa fa-cart-plus"></i> RV purchase pending</h1>
  </div>
  <div class="pending-purchase-table">
    @if (!empty($unpurchaselist[0]))
    <table>
      <tr>
        <th>RV No</th>
        <th>RV Date</th>
        <th>Purpose</th>
        <th>Budget available</th>
        <th>View</th>
      </tr>
      @foreach ($unpurchaselist as $listpending)
      <tr>
        <td>{{$listpending->RVNo}}</td>
        <td>{{$listpending->RVDate->format('M, d Y')}}</td>
        <td>{{$listpending->Purpose}}</td>
        @if ($listpending->BudgetAvailable)
          <td>{{$listpending->BudgetAvailable}}</td>
        @else
          <td>N/A</td>
        @endif
        <td><a href="{{route('RVfullpreviewing',[$listpending->RVNo])}}"><i class="fa fa-eye"></i></a></td>
      </tr>
      @endforeach
    </table>
    <div class="pagination-container">
      {{$unpurchaselist->links()}}
    </div>
    @else
      <h1 class="all-is-purchased">No pending RV purchase <i class="fa fa-thumbs-up"></i></h1>
    @endif
  </div>
</div>
@endsection
