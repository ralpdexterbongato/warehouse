@extends('layouts.master')
@section('title')
  RR index
@endsection
@section('body')
  <div class="RR-index-container">
    <div class="index-RRtitle-container">
      <h1><i class="fa fa-th-large"></i> Receiving Report index</h1>
      <div class="box-search-rr">
        <form class="searchbox-formRR" action="{{route('RRSearchNo')}}" method="GET">
          <input type="text" autocomplete="off" name="RRNo" placeholder="Search by RR No . . ."><button type="submit" name="button"><i class="fa fa-search"></i></button>
        </form>
      </div>
    </div>
    <div class="index-RR-table-container">
        <table>
          @if ((!empty($RRMasterResults[0]))||(!empty($RRmasters[0])))
            <tr>
              <th>RR No.</th>
              <th>Supplier</th>
              <th>Address</th>
              <th>RV No.</th>
              <th>Received by</th>
              <th>Received original by</th>
              <th>Verified by</th>
              <th>Posted to BIN by</th>
              <th>Action</th>
            </tr>
          @endif
          @if (!empty($RRMasterResults[0]))
            @foreach ($RRMasterResults as $RRresult)
              <tr>
                <td>{{$RRresult->RRNo}}</td>
                <td>{{$RRresult->Supplier}}</td>
                <td>{{$RRresult->Address}}</td>
                <td>{{$RRresult->RVNo}}</td>
                <td>
                  {{$RRresult->Receivedby}}
                  @if($RRresult->ReceivedbySignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                </td>
                <td>
                  {{$RRresult->ReceivedOriginalby}}
                  @if ($RRresult->ReceivedOriginalbySignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                  @if ($RRresult->IfDeclined==$RRresult->ReceivedOriginalby)
                  <br>  <i class="fa fa-times index-decline"></i>
                  @endif
                </td>
                <td>
                  {{$RRresult->Verifiedby}}
                  @if ($RRresult->VerifiedbySignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                  @if ($RRresult->IfDeclined==$RRresult->Verifiedby)
                  <br>  <i class="fa fa-times index-decline"></i>
                  @endif
                </td>
                <td>
                  {{$RRresult->PostedtoBINby}}
                  @if ($RRresult->PostedtoBINbySignature)
                    <br><i class="fa fa-check"></i>
                  @endif
                  @if ($RRresult->IfDeclined==$RRresult->PostedtoBINby)
                  <br>  <i class="fa fa-times index-decline"></i>
                  @endif
                </td>
                <td><a href="{{route('RRfullpreview',[$RRresult->RRNo])}}"><i class="fa fa-eye"></i></a></td>
              </tr>
            @endforeach
          @elseif(!empty($RRmasters[0]))
          @foreach ($RRmasters as $rrmaster)
            <tr>
              <td>{{$rrmaster->RRNo}}</td>
              <td>{{$rrmaster->Supplier}}</td>
              <td>{{$rrmaster->Address}}</td>
              <td>{{$rrmaster->RVNo}}</td>
              <td>
                {{$rrmaster->Receivedby}}
                @if ($rrmaster->ReceivedbySignature)
                  <br><i class="fa fa-check"></i>
                @endif
              </td>
              <td>
                {{$rrmaster->ReceivedOriginalby}}
                @if ($rrmaster->ReceivedOriginalbySignature)
                  <br><i class="fa fa-check"></i>
                @endif
                @if ($rrmaster->IfDeclined==$rrmaster->ReceivedOriginalby)
                  <br><i class="fa fa-times index-decline"></i>
                @endif
              </td>
              <td>
                {{$rrmaster->Verifiedby}}
                @if ($rrmaster->VerifiedbySignature)
                  <br><i class="fa fa-check"></i>
                @endif
                @if ($rrmaster->IfDeclined==$rrmaster->Verifiedby)
                  <br><i class="fa fa-times index-decline"></i>
                @endif
              </td>
              <td>
                {{$rrmaster->PostedtoBINby}}
              @if ($rrmaster->PostedtoBINbySignature)
                  <br><i class="fa fa-check"></i>
              @endif
              @if ($rrmaster->IfDeclined==$rrmaster->PostedtoBINby)
                <br><i class="fa fa-times index-decline"></i>
              @endif
              </td>
              <td><a href="{{route('RRfullpreview',[$rrmaster->RRNo])}}"><i class="fa fa-eye"></i></a></td>
            </tr>
          @endforeach
          @else
            <h1 class="RRempty"><i class="fa fa-search"></i> Receiving Reports index is empty</h1>
          @endif
        </table>
        @if (!empty($RRmasters[0]))
          <div class="paginate-container">
            {{$RRmasters->links()}}
          </div>
        @elseif (!empty($RRMasterResults[0]))
          <div class="paginate-container">
            {{$RRMasterResults->links()}}
          </div>
        @endif
    </div>
  </div>
@endsection
