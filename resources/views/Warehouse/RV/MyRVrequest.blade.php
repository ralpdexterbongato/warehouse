@extends('layouts.master')
@section('title')
 RV|request
@endsection
@section('body')
  <div class="my-rv-container">
    <div class="RV-request-title">
      <h1><i class="material-icons">mode_edit</i> Signature request RV</h1>
    </div>
    <div class="rv-request-table">
      @if (isset($myRVPendingrequest[0]))
      <table>
        <tr>
          <th>RV No.</th>
          <th>RV Date</th>
          <th>Purpose</th>
          <th>Action</th>
        </tr>
          @foreach ($myRVPendingrequest as $myRVrequest)
            <tr>
              <td>{{$myRVrequest->RVNo}}</td>
              <td>{{$myRVrequest->RVDate->format('m/d/Y')}}</td>
              <td>{{$myRVrequest->Purpose}}</td>
              <td><a href="{{route('RVfullpreviewing',[$myRVrequest->RVNo])}}"><i class="material-icons">remove_red_eye</i></a></td>
            </tr>
          @endforeach
      </table>
      @else
        <h1 class="no-rv-request">RV request is empty</h1>
      @endif

      @if (!empty($myRVPendingrequest[0]))
        {{$myRVPendingrequest->links()}}
      @endif
    </div>
  </div>
@endsection
