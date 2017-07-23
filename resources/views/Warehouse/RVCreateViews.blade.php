@extends('layouts.master')
@section('title')
  Requisition Voucher
@endsection
@section('body')
  <div class="RV-container">
    <div class="title-RV">
      <h3>Create Requisition Voucher</h3>
      <h3 class="empty-right"></h3>
    </div>
    <div class="RV-wrapper">
      <div class="added-items-table">
        <div class="add-item-RV">
          <button type="button" name="button"><i class="fa fa-plus"></i> Add item</button>
        </div>
        <table>
          <tr>
            <th>Articles</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Remarks</th>
            <th>Action</th>
          </tr>
          @if (Session::has('ItemSessionList'))
          @foreach (Session::get('ItemSessionList') as $count=> $ItemSession)
            <tr>
              <td>{{$ItemSession->Description}}</td>
              <td>{{$ItemSession->Unit}}</td>
              <td>{{$ItemSession->Quantity}}</td>
              <td>{{$ItemSession->Remarks}}</td>
              <td><i class="fa fa-trash" onclick="$('.session-trash{{$count}}').submit()"></i></td>
              <form class="session-trash{{$count}}" action="{{route('DeletingSessionRV',[$count])}}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
              </form>
            </tr>
          @endforeach
          @endif
        </table>
      </div>
      <div class="RVMaster-form">
        <form class="RV-form" action="{{route('SavingRRtoDB')}}" method="post">
          {{ csrf_field() }}
          <input type="text" name="Purpose" placeholder="Purpose" required>
          <select name="Recommendedby" required>
            <option value="">Recommended by</option>
          @foreach ($managers as $manager)
            <option value="{{$manager->id}}">{{$manager->Fname.' '.$manager->Lname}}</option>
          @endforeach
          </select>
          <div class="autoselectedRV">
            @if (!empty($currentBudgetOfficer[0]))
              <h4>{{$currentBudgetOfficer[0]->Fname.' '.$currentBudgetOfficer[0]->Lname}}</h4>
            @else
              <h4>No Account yet</h4>
            @endif
            <p>Budget Officer</p>
          </div>
          <div class="autoselectedRV">
            @if (!empty($GM[0]))
              <h4>{{$GM[0]->Fname.' '.$GM[0]->Lname}}</h4>
            @else
              <h4>No Account yet</h4>
            @endif
            <p>General Manager</p>
          </div>
          <div class="submit-button-RV">
            <button type="submit" name="button">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="add-RV-item-modal">
    <div class="rv-modal-centered">
      <h1>Request Item</h1>
      <form class="itemRV-form" action="{{route('SavingSessionRV')}}" method="post">
        {{ csrf_field() }}
        <textarea name="Description" placeholder="Articles (max:50 characters)"></textarea>
        <select name="Unit">
          <option value="">Select Unit</option>
          <option value="PC">PC</option>
          <option value="BOX">BOX</option>
          <option value="DOZ">DOZ</option>
          <option value="REAM">REAM</option>
        </select>
        <input type="number" name="Quantity" min="1">
        <input type="text" name="Remarks" placeholder="Remarks">
        <div class="buttons-RV-Item">
          <button type="button" id="closemodalRV">Cancel</button>
          <button type="submit" id="addtolistRV">Add to list</button>
        </div>
      </form>
    </div>
  </div>
@endsection
