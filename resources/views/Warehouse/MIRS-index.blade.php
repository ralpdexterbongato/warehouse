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
            <td>{{$MIRSfound->Preparedby}}
              @if (!empty($MIRSfound->PreparedSignature))
               <i class="fa fa-check"></i>
             @elseif($MIRSfound->IfDenied==$MIRSfound->Preparedby)
               <i class="fa fa-times decliner"></i>
             @endif
            </td>
            <td>{{$MIRSfound->Recommendedby}}
              @if (!empty($MIRSfound->RecommendSignature))
               <i class="fa fa-check"></i>
             @elseif($MIRSfound->IfDenied==$MIRSfound->Recommendedby)
               <i class="fa fa-times decliner"></i>
             @endif
            </td>
            <td>{{$MIRSfound->Approvedby}}
              @if (!empty($MIRSfound->ApproveSignature))
               <i class="fa fa-check"></i>
             @elseif($MIRSfound->IfDenied==$MIRSfound->Approvedby)
               <i class="fa fa-times decliner"></i>
              @endif
            </td>
            <td>{{$MIRSfound->MIRSDate->format('M d, Y')}}</td>
            @if ($MIRSfound->IfDenied==null)
              @if(($MIRSfound->PreparedSignature==null)||($MIRSfound->ApproveSignature==null)||($MIRSfound->RecommendSignature==null))
              <td><i class="fa fa-clock-o"></i></td>
              @else
              <td><i class="fa fa-thumbs-up"></i></td>
              @endif
            @else
              <td><i class="fa fa-times decliner"></i></td>
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
            <td>{{$allmaster->Preparedby}}
              @if (!empty($allmaster->PreparedSignature))
               <i class="fa fa-check"></i>
             @elseif ($allmaster->Preparedby == $allmaster->IfDenied)
               <i class="fa fa-times decliner"></i>
             @endif
            </td>
            <td>
              {{$allmaster->Recommendedby}}
              @if (!empty($allmaster->RecommendSignature))
               <i class="fa fa-check"></i>
             @elseif ($allmaster->Recommendedby == $allmaster->IfDenied)
               <i class="fa fa-times decliner"></i>
             @endif
            </td>
            <td>{{$allmaster->Approvedby}}
              @if (!empty($allmaster->ApproveSignature))
                <i class="fa fa-check"></i>
              @elseif ($allmaster->Approvedby == $allmaster->IfDenied)
                <i class="fa fa-times decliner"></i>
              @endif
            </td>
            <td>{{$allmaster->MIRSDate->format('M d, Y')}}</td>
            @if ($allmaster->IfDenied==null)
              @if (($allmaster->PreparedSignature==null)||($allmaster->ApproveSignature==null)||($allmaster->RecommendSignature==null))
              <td><i class="fa fa-clock-o"></i></td>
              @else
              <td><i class="fa fa-thumbs-up"></i></td>
              @endif
            @else
              <td><i class="fa fa-times decliner"></i></td>
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
