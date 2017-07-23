@extends('layouts.master')
@section('title')
MIRS signature request list
@endsection
@section('body')
  <div class="MIRS-request-container">
    <h1>My MIRS signature <i class="fa fa-pencil"></i>request</h1>
      @if (!empty($myrequestMIRS[0]))
    <div class="mirs-request-table-list">
      <table>
        <tr>
          <th>MIRS No.</th>
          <th>Purpose</th>
          <th>Prepared by</th>
          <th>recommended by</th>
          <th>Approved by</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
          @foreach ($myrequestMIRS as $mirsRequest)
          <tr>
            <td>{{$mirsRequest->MIRSNo}}</td>
            <td>{{$mirsRequest->Purpose}}</td>
            <td>{{$mirsRequest->Preparedby}}
              @if($mirsRequest->PreparedSignature)
                <i class="fa fa-check"></i>
              @endif
            </td>
            <td>{{$mirsRequest->Recommendedby}}
              @if($mirsRequest->RecommendSignature)
                <i class="fa fa-check"></i>
              @endif
            </td>
            <td>{{$mirsRequest->Approvedby}}
              @if($mirsRequest->ApproveSignature)
                <i class="fa fa-check"></i>
              @endif
            </td>
            <td>{{$mirsRequest->MIRSDate->format('M d, Y')}}</td>
            <td><i class="fa fa-eye" onclick="$('.FullMIRS{{$mirsRequest->MIRSNo}}').submit()"></i></td>
            <form class="FullMIRS{{$mirsRequest->MIRSNo}}" action="{{route('full-mirs')}}" method="get">
            <input type="text" name="MIRSNo" value="{{$mirsRequest->MIRSNo}}" style="display:none">
            </form>
          </tr>
          @endforeach
      </table>
      {{$myrequestMIRS->links()}}
    </div>
  @else
  <h3 class="MIRS-request-empty">MIRS request is empty</h3>
  @endif
  </div>
@endsection
