@extends('layouts.master')
@section('title')
 PO | Signature request
@endsection
@section('body')
<div class="myPOrequest-container">
  <div class="title-po-list-request">
    <h1>My P.O. signature <i class="fa fa-pencil"></i> request</h1>
  </div>
  <div class="po-request-table">
    @if (!empty($myPOlist[0]))
      <table>
        <tr>
          <th>PO No.</th>
          <th>RV No.</th>
          <th>Supplier</th>
          <th>Address</th>
          <th>Telephone</th>
          <th>Purpose</th>
          <th>PO Date</th>
          <th>Action</th>
        </tr>
        @foreach ($myPOlist as $polist)
          <tr>
            <td>{{$polist->PONo}}</td>
            <td>{{$polist->RVNo}}</td>
            <td>{{$polist->Supplier}}</td>
            <td>{{$polist->Address}}</td>
            <td>{{$polist->Telephone}}</td>
            <td>{{$polist->Purpose}}</td>
            <td>{{$polist->PODate->format('M d,Y')}}</td>
            <td><a href="{{route('POFullView',[$polist->PONo])}}"><i class="fa fa-eye"></i></a></td>
          </tr>
        @endforeach
      </table>
      {{$myPOlist->links()}}
    @else
      <h1 class="empty-po">P.O. Request is empty</h1>
    @endif
  </div>
</div>
@endsection