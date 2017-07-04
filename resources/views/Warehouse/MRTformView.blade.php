@extends('layouts.master')
@section('title')
 MRT |form
@endsection
@section('body')
<div class="MRT-form-bigcontainer">
  <div class="MRT-container">
    <div class="List-items-mrt">
        <div class="pick-from-items">
          <button type="button"><i class="fa fa-plus"></i> Item</button>
        </div>
      <div class="items-form">
          <div class="items-from-mct">
            <table>
              <tr>
                <th>Item Code</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Summary</th>
                <th>Action</th>
              </tr>
              @if (Session::has('MCTSelected'))
              @foreach (Session::get('MCTSelected') as $MCTselected)
                <tr>
                  <td>{{$MCTselected->ItemCode}}</td>
                  <td>{{$MCTselected->Description}}</td>
                  <td>{{$MCTselected->Unit}}</td>
                  <td>{{$MCTselected->Summary}}</td>
                    <td><i class="fa fa-trash-o" onclick="$('.deletemctSession{{$MCTselected->ItemCode}}').submit()"></i></td>
                  <form class="deletemctSession{{$MCTselected->ItemCode}}" action="{{route('mrtSession.deleting',[$MCTselected->ItemCode])}}" method="post">
                      {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                  </form>
                </tr>
              @endforeach
              @endif
            </table>
          </div>
      </div>
    </div>
    <div class="MRT-middle-form">
        <form class="mrt-form" action="{{route('storing.mrt')}}" method="post">
          {{ csrf_field() }}
          <input type="text" name="MCTNo" value="{{$MTDetails[0]->MTNo}}" style="display:none">
          <input type="text" name="Particulars" value="{{$MCTdata[0]->Particulars}}" style="display:none">
          <input type="text" name="AddressTo" value="{{$MCTdata[0]->AddressTo}}" style="display:none">
          <input type="text" name="Returnedby" placeholder="Returned by" required>
          <input type="text" name="Receivedby" placeholder="Received by" required>
          <input type="text" name="Remarks" placeholder="Remarks" required>
          <div class="mrt-btns">
            <button type="button" class="mrt-cancel">Cancel</button>
            <button type="submit" class="mrt-gobtn">Create</button>
          </div>
      </form>
    </div>
  </div>
</div>
<div class="mrt-items-modal">
  <div class="mrt-items">
    <h1><i class="fa fa-times"></i></h1>
    <table>
      <tr>
        <th>Item Code</th>
        <th>Description</th>
        <th>Unit</th>
        <th>Summary</th>
        <th>Action</th>
      </tr>
      @foreach ($MTDetails as $details)
      <tr>
        <form action="{{route('Session.addings')}}" method="post">
          {{ csrf_field() }}
          <td>{{$details->ItemCode}}</td>
          <input type="text" name="ItemCode" value="{{$details->ItemCode}}" style="display:none">
          <input type="text" name="Description" value="{{$details->MasterItems->Description}}" style="display:none">
          <input type="text" name="Unit" value="{{$details->Unit}}" style="display:none">
          <td>{{$details->MasterItems->Description}}</td>
          <td>{{$details->Unit}}</td>
          <td><input type="number" name="Summary" min="1" max="{{$details->Quantity}}" required></td>
          <td><button type="submit">Select</button></td>
        </form>
      </tr>
      @endforeach
    </table>
  </div>
</div>
@endsection
