@extends('layouts.master')
@section('title')
 MR|request
@endsection
@section('body')
  <div class="my-mr-request-container">
    <div class="mr-request-title">
      <h1>My M.R. signature <i class="fa fa-pencil"></i> request</h1>
    </div>
    @if (!empty($MRRequest[0]))
    <div class="mr-table-container">
      <table>
        <tr>
          <th>M.R. No</th>
          <th>Note</th>
          <th>Received by</th>
          <th>Recommended by</th>
          <th>General Manager</th>
          <th>M.R. Date</th>
          <th>Action</th>
        </tr>
        @foreach ($MRRequest as $mrRequest)
          <tr>
            <td>{{$mrRequest->MRNo}}</td>
            <td>{{$mrRequest->Note}}</td>
            <td>{{$mrRequest->Receivedby}}</td>
            <td>
              {{$mrRequest->Recommendedby}}
              @if ($mrRequest->RecommendedbySignature)
                <i class="fa fa-check"></i>
              @endif
            </td>
            <td>
              {{$mrRequest->GeneralManager}}
              @if ($mrRequest->GeneralManagerSignature)
                <i class="fa fa-check"></i>
              @endif
            </td>
            <td>{{$mrRequest->MRDate->format('M d, Y')}}</td>
            <td><a href="{{route('fullMR',[$mrRequest->MRNo])}}"><i class="fa fa-eye"></i></a></td>
          </tr>
        @endforeach
      </table>
    </div>
    <div class="pagination-container">
      {{$MRRequest->links()}}
    </div>
    @else
      <h1 class="no-mr">Empty M.R. request</h1>
    @endif
  </div>
@endsection
