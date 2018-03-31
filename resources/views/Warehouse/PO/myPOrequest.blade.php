@extends('layouts.master')
@section('title')
 PO | Sign request
@endsection
@section('body')
<div class="myPOrequest-container">
  <div class="title-po-list-request">
    <h1><i class="material-icons">mode_edit</i>PO Sign request</h1>
  </div>
  <div class="po-request-table">
    @if (!empty($myPOlist[0]))
      <table>
        <tr>
          <th>PO No.</th>
          <th>PO Date</th>
          <th>RV No.</th>
          <th>Supplier</th>
          <th>Address</th>
          <th>Telephone</th>
          <th>Purpose</th>
          <th>Action</th>
        </tr>
        @foreach ($myPOlist as $polist)
          <tr>
            <td>{{$polist->PONo}}</td>
            <td>{{$polist->PODate->format('M d,Y')}}</td>
            <td>{{$polist->RVNo}}</td>
            <td>{{$polist->Supplier}}</td>
            <td>{{$polist->Address}}</td>
            <td>{{$polist->Telephone}}</td>
            <td>{{$polist->Purpose}}</td>
            <td><a href="{{route('POFullView',[$polist->PONo])}}"><i class="material-icons">remove_red_eye</i></a></td>
          </tr>
        @endforeach
      </table>
      <div class="paginate-container">
        {{$myPOlist->links()}}
      </div>
    @else
      <h1 class="empty-po">No sign request received</h1>
    @endif
  </div>
</div>
@endsection
