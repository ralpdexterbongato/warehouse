@extends('layouts.master')
@section('title')
  If G.M. is Absent confirmation
@endsection
@section('body')
  <div class="container-gm-signature-absent">
    <h1 class="note-gm-absent"><i class="fa fa-info-circle color-blue"></i> Please Click the confirm button <span class="color-blue">only</span> if the <span class="underline">General Manager</span> is not available.</h1>
    @if (isset($MIRSrequestGMisAbsent[0]))
    <div class="confirm-from-admin">
      <table>
        <tr>
          <th class="gm-absent-header"><h1>Materials Issuance Request Tickets</h1></th>
          <td></td>
        </tr>
          @foreach ($MIRSrequestGMisAbsent as $mirsdata)
            <tr>
              <th><span class="color-blue">{{$mirsdata->ApprovalReplacerFname}} {{$mirsdata->ApprovalReplacerLname}} </span> Wants to Approve the M.I.R.S. no.{{$mirsdata->MIRSNo}} in behalf of our General Manager</th>
              <td>
                <button type="button" onclick="$('.LetManagerApproveIt{{$mirsdata->MIRSNo}}').submit()" class="confirm-button-gm">Confirm <i class="fa fa-check"></i> </button>
                <button type="button" onclick="$('.denyform{{$mirsdata->MIRSNo}}').submit()">Decline <i class="fa fa-times deny"></i></button>
              </td>
              <form class="LetManagerApproveIt{{$mirsdata->MIRSNo}}" action="{{route('letManagerApprove',[$mirsdata->MIRSNo])}}" method="post">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
              </form>
              <form class="denyform{{$mirsdata->MIRSNo}}" action="{{route('denyManagerRequest.MIRS',[$mirsdata->MIRSNo])}}" method="post">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
              </form>
            </tr>
          @endforeach
          <tr>
            <th>
                <div class="paginate-container">
                  {{$MIRSrequestGMisAbsent->links()}}
                </div>
            </th>
            <td></td>
          </tr>
      </table>
    </div>
    @endif
    @if (isset($RVrequestGMisAbsent[0]))
    <div class="confirm-from-admin">
      <table>
        <tr>
          <th class="gm-absent-header"><h1>Requisition vouchers</h1></th>
          <td></td>
        </tr>
        @foreach ($RVrequestGMisAbsent as $RVdata)
        <tr>
          <th><span class="color-blue">{{$RVdata->ApprovalReplacerFname.' '.$RVdata->ApprovalReplacerLname}}</span> Wants to Approve the Requisition voucher no.{{$RVdata->RVNo}} in behalf of our general manager</th>
          <td>
            <button type="button" onclick="$('.confirmformRV{{$RVdata->RVNo}}').submit()" class="confirm-button-gm">Confirm <i class="fa fa-check"></i></button>
            <button type="button" onclick="$('.denyformRV{{$RVdata->RVNo}}').submit()" name="button">Decline <i class="fa fa-times deny"></i></button></td>
            <form class="confirmformRV{{$RVdata->RVNo}}" action="{{route('ConfirmSignatureinBehalfRV',[$RVdata->RVNo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
            </form>
            <form class="denyformRV{{$RVdata->RVNo}}" action="{{route('rv-behalf-denied.byadmin',[$RVdata->RVNo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
            </form>
        </tr>
        @endforeach
        <tr>
          <th>
              <div class="paginate-container">
                {{$RVrequestGMisAbsent->links()}}
              </div>
          </th>
          <td></td>
        </tr>
      </table>
    </div>
    @endif
    @if (isset($POrequestGMisAbsent[0]))
    <div class="confirm-from-admin">
      <table>
        <tr>
          <th class="gm-absent-header"><h1>Purchase Orders</h1></th>
          <td></td>
        </tr>
        @foreach ($POrequestGMisAbsent as $POrequest)
          <tr>
            <th><span class="color-blue">{{$POrequest->ApprovalReplacerFname}} {{$POrequest->ApprovalReplacerLname}}</span> Wants to Approve the P.O. no.{{$POrequest->PONo}} in behalf of our General Manager</th>
            <td><button type="button" class="confirm-button-gm" onclick="$('.PObehalfConfirmform{{$POrequest->PONo}}').submit()">Confirm <i class="fa fa-check"></i> </button><button type="button" onclick="$('.PObehalfdenyform{{$POrequest->PONo}}').submit()">Decline <i class="fa fa-times deny"></i></button></td>
            <form class="PObehalfConfirmform{{$POrequest->PONo}}" action="{{route('authorizeinbehalf-confirm',[$POrequest->PONo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
            </form>
            <form class="PObehalfdenyform{{$POrequest->PONo}}" action="{{route('autorizeInBehalf.denybyadmin',[$POrequest->PONo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
            </form>
          </tr>
        @endforeach
        <tr>
          <th>
              <div class="paginate-container">
                {{$POrequestGMisAbsent->links()}}
              </div>
          </th>
          <td></td>
        </tr>
      </table>
    </div>
    @endif
    @if (isset($MRrequestGMisAbsent[0]))
      <div class="confirm-from-admin">
        <table>
          <tr>
            <th class="gm-absent-header"><h1>Memorandum receipts</h1></th>
            <td></td>
          </tr>
          @foreach ($MRrequestGMisAbsent as $MRrequest)
          <tr>
            <th><span class="color-blue">{{$MRrequest->ApprovalReplacerFname}} {{$MRrequest->ApprovalReplacerLname}}</span> Wants to Approve the M.R. no.{{$MRrequest->MRNo}} in behalf of our general manager</th>
            <td><button type="button" onclick="$('.MRdenyform{{$MRrequest->MRNo}}').submit()" class="confirm-button-gm">Confirm <i class="fa fa-check"></i> </button><button type="button" onclick="$('.MRdenyform{{$MRrequest->MRNo}}').submit()">Decline <i class="fa fa-times deny"></i></button></td>
            <form class="MRConfirmform{{$MRrequest->MRNo}}" action="" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
            </form>
            <form class="MRdenyform{{$MRrequest->MRNo}}" action="{{route('confirmApproveInBehalf',[$MRrequest->MRNo])}}" method="post">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
            </form>
          </tr>
          @endforeach
          <tr>
            <th>
                <div class="paginate-container">
                  {{$MRrequestGMisAbsent->links()}}
                </div>
            </th>
            <td></td>
          </tr>
        </table>
      </div>
    @endif
    <div class="empty-bottom-msg">
      @if (empty($MIRSrequestGMisAbsent[0]))
        <h2 class="no-current-request">No current M.I.R.S. request</h2>
      @endif
      @if (empty($RVrequestGMisAbsent[0]))
      <h2 class="no-current-request">No current R.V. request</h2>
      @endif
      @if (empty($POrequestGMisAbsent[0]))
        <h2 class="no-current-request">No current P.O. request</h2>
      @endif
      @if (empty($MRrequestGMisAbsent[0]))
        <h2 class="no-current-request">No current M.R. request</h2>
      @endif
    </div>
  </div>
@endsection
