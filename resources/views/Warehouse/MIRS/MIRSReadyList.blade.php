@extends('layouts.master')
@section('title')
MIRS Ready for MCT
@endsection
@section('body')
  <div class="MIRS-Ready-Container">
    <div class="ready-mirs-container">
      @if (!empty($readyformct[0]))
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
        @foreach ($readyformct as $mctReady)
          <tr>
            <td>{{$mctReady->MIRSNo}}</td>
            <td>{{$mctReady->Purpose}}</td>
            <td>{{$mctReady->Preparedby}}</td>
            <td>{{$mctReady->Recommendedby}}</td>
            <td>{{$mctReady->Approvedby}}</td>
            <td>{{$mctReady->MIRSDate->format('M d,Y')}}</td>
            <td><a href="{{route('full-mirs',[$mctReady->MIRSNo])}}"><i class="fa fa-eye"></i></a></td>
          </tr>
        @endforeach
      </table>
      <div class="paginate-container">
        {{$readyformct->links()}}
      </div>
      @else
        <h1 class="no-waitingMIRS">MIRS waiting for MCT is empty</h1>
      @endif
    </div>
  </div>
@endsection
