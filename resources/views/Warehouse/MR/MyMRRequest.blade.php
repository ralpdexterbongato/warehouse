@extends('layouts.master')
@section('title')
 MR|request
@endsection
@section('body')
  <div class="my-mr-request-container">
    <div class="mr-request-title">
      <h1><i class="material-icons">mode_edit</i>MR Sign request</h1>
    </div>
    @if (!empty($MRRequest[0]))
    <div class="mr-table-container">
      <table>
        <tr>
          <th>M.R. No</th>
          <th>M.R. Date</th>
          <th>Supplier</th>
          <th>Invoice #</th>
          <th>Warehouse Man</th>
          <th>View</th>
        </tr>
        @foreach ($MRRequest as $mrRequest)
          <tr>
            <td>{{$mrRequest->MRNo}}</td>
            <td>{{$mrRequest->MRDate->format('M d, Y')}}</td>
            <td>{{$mrRequest->Supplier}}</td>
            @if ($mrRequest->Invoice)
              <td>{{$mrRequest->Invoice}}</td>
            @else
              <td>N/A</td>
            @endif
            <td>{{$mrRequest->warehouseman->FullName}}</td>
            <td><a href="{{route('fullMR',[$mrRequest->MRNo])}}"><i class="material-icons">remove_red_eye</i></a></td>
          </tr>
        @endforeach
      </table>
    </div>
    <div class="pagination-container">
      {{$MRRequest->links()}}
    </div>
    @else
      <h1 class="no-mr">No sign request received</h1>
    @endif
  </div>
@endsection
