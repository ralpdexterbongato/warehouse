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
        <th>RVNo</th>
        <th>Purpose</th>
        <th>Requisitioner</th>
        <th>RVDate</th>
        <th>Action</th>
      </tr>
      @foreach ($unpurchaselist as $listpending)
      <tr>
        <td>{{$listpending->RVNo}}</td>
        <td>{{$listpending->Purpose}}</td>
        <td>{{$listpending->Requisitioner}}</td>
        <td>{{$listpending->RVDate->format('M d, Y')}}</td>
        <td><a href="{{route('RVfullpreviewing',[$listpending->RVNo])}}"><i class="fa fa-eye"></i></a></td>
      </tr>
      @endforeach
    </table>
    {{$unpurchaselist->links()}}
    @endif
  </div>
</div>
@endsection
