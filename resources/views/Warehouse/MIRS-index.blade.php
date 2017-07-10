@extends('layouts.master')
@section('title')
  MIRS|Index
@endsection
@section('body')
  <div class="grid-mirs-container">
    <div class="top-wrap-index-mirs">
      <div class="Title-MIRS-Index">
        MIRS INDEX
      </div>
      <div class="search-mirs-container">
        <form action="{{route('finding.mirs')}}" method="get">
          <input type="text" name="MIRSNo" placeholder="Enter MIRS number"><button type="submit" name="button"><i class="fa fa-search"></i></button>
        </form>
      </div>
    </div>
    <div class="table-mirs-list">
      <table>
        <tr>
          <th>MIRSNo</th>
          <th>Purpose</th>
          <th>Prepared by</th>
          <th>Recommended by</th>
          <th>Approved by</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        @if (!empty($mirsResult[0]))
          @foreach ($mirsResult as $MIRSfound)
          <tr>
            <td>{{$MIRSfound->MIRSNo}}</td>
            <td>{{$MIRSfound->Purpose}}</td>
            <td>{{$MIRSfound->Preparedby}}</td>
            <td>{{$MIRSfound->Recommendedby}}</td>
            <td>{{$MIRSfound->Approvedby}}</td>
            <td>{{$MIRSfound->MIRSDate->format('M d, Y')}}</td>
            @if(($MIRSfound->PreparedSignature==null)||($MIRSfound->ApproveSignature==null)||($MIRSfound->RecommendSignature==null))
            <td><i class="fa fa-clock-o"></i></td>
            @else
            <td><i class="fa fa-thumbs-up"></i></td>
            @endif
            <td class="fullmirsClick"><i class="fa fa-eye" onclick="$('.ViewingFullMIRS{{$MIRSfound->MIRSNo}}').submit()"></i></td>
          </tr>
          <form class="ViewingFullMIRS{{$MIRSfound->MIRSNo}}" action="{{route('full-mirs')}}" method="get">
            <input type="text" name="MIRSNo" value="{{$MIRSfound->MIRSNo}}" style="display:none">
          </form>
          @endforeach
        @elseif (!empty($AllmasterMIRS[0]))
          @foreach ($AllmasterMIRS as $allmaster)
          <tr>
            <td>{{$allmaster->MIRSNo}}</td>
            <td>{{$allmaster->Purpose}}</td>
            <td>{{$allmaster->Preparedby}}</td>
            <td>{{$allmaster->Recommendedby}}</td>
            <td>{{$allmaster->Approvedby}}</td>
            <td>{{$allmaster->MIRSDate->format('M d, Y')}}</td>
            @if (($allmaster->PreparedSignature==null)||($allmaster->ApproveSignature==null)||($allmaster->RecommendSignature==null))
            <td><i class="fa fa-clock-o"></i></td>
            @else
            <td><i class="fa fa-thumbs-up"></i></td>
            @endif
            <td><i class="fa fa-eye" onclick="$('.ViewingFullMIRS{{$allmaster->MIRSNo}}').submit()"></i></td>
          </tr>
          <form class="ViewingFullMIRS{{$allmaster->MIRSNo}}" action="{{route('full-mirs')}}" method="get">
            <input type="text" name="MIRSNo" value="{{$allmaster->MIRSNo}}" style="display:none">
          </form>
          @endforeach
        @endif
      </table>
      @if (!empty($AllmasterMIRS[0]))
        {{$AllmasterMIRS->links()}}
      @endif
    </div>
  </div>
@endsection
