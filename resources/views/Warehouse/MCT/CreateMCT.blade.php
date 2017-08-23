@extends('layouts.master')
@section('title')
  MCT | recording
@endsection
@section('body')
  <div class="RecordingMCT-Container">
    <h1 class="title-create-mct">Materials charge ticket recording</h1>
    <h2 class="mirs-num">Materials from MIRS No. <span class="color-blue">{{$FromValidator[0]->MIRSNo}}</span></h2>
    <div class="button-find-item-container">
      <button type="button" name="button">Select items</button>
    </div>
    <div class="session-and-formContainer">
      <div class="selected-items-session-mct">
        <table>
          <tr>
            <th>Item Code</th>
            <th>Particulars</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Remarks</th>
            <th>Delete</th>
          </tr>
        @if (Session::has('MCTSessionItems'))
          @foreach (Session::get('MCTSessionItems') as $sessionItem)
          <tr>
            <td>{{$sessionItem->ItemCode}}</td>
            <td>{{$sessionItem->Particulars}}</td>
            <td>{{$sessionItem->Unit}}</td>
            <td>{{$sessionItem->Quantity}}</td>
            <td>{{$sessionItem->Remarks}}</td>
            <td>
              <form class="deleteMCT-session-button" action="{{route('delete.a.session.mct',[$sessionItem->ItemCode])}}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit"><i class="fa fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @endforeach
        @endif
        </table>
      </div>
      <div class="smallform-mct-container">
        <form class="address-where-form" action="{{route('Storing.MCT')}}" method="post">
          {{ csrf_field() }}
          <input type="text" name="MIRSNo" value="{{$FromValidator[0]->MIRSNo}}" style="display:none">
          <input type="text" name="Particulars" value="{{$MIRSMasterPurpose}}" style="display:none">
          <input type="text" placeholder="example. Sub office Area" name="AddressTo" autocomplete="off" required>
          <label>ADDRESS TO</label>
          <button type="submit">submit</button>
        </form>
      </div>
    </div>
  </div>
  <div class="mct-modal-ofItems">
    <div class="mct-modal-center">
      <h1>Pick Items from MIRS No. {{$FromValidator[0]->MIRSNo}} <i class="fa fa-times"></i></h1>
      <div class="table-mct-itemchoices">
        @if (isset($FromValidator[0]))
        <table>
          <tr>
            <th>Item Code</th>
            <th>Particulars</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Unclaimed</th>
            <th>Remarks</th>
            <th>Select</th>
          </tr>
          @foreach ($FromValidator as $fromvalidator)
          <tr>
            <form action="{{route('sessionSaveMCT')}}" method="post">
              {{ csrf_field() }}
              <input type="text" name="MIRSNo" value="{{$fromvalidator->MIRSNo}}" style="display:none">
              <input type="text" name="ItemCode" value="{{$fromvalidator->ItemCode}}" style="display:none">
              <input type="text" name="Particulars" value="{{$fromvalidator->Particulars}}" style="display:none">
              <input type="text" name="Unit" value="{{$fromvalidator->Unit}}" style="display:none">
              <input type="text" name="Remarks" value="{{$fromvalidator->Remarks}}" style="display:none">
              <td>{{$fromvalidator->ItemCode}}</td>
              <td>{{$fromvalidator->Particulars}}</td>
              <td>{{$fromvalidator->Unit}}</td>
              <td><input type="number" name="Quantity" min="1" max="{{$fromvalidator->Quantity}}" autocomplete="off"></td>
              <td>{{$fromvalidator->Quantity}}</td>
              <td>{{$fromvalidator->Remarks}}</td>
              <td><button type="submit"><i class="fa fa-plus-circle"></i></button></td>
            </form>
          </tr>
          @endforeach
        </table>
        <div class="pagination-container">
          {{$FromValidator->links()}}
        </div>
        @endif
      </div>
    </div>
  </div>
@endsection
