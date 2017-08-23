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
          @if ((Auth::user()->Role==4)||(Auth::user()->Role==3))
          <button type="button" name="button" id="forstock-ItemRV"><i class="fa fa-dropbox"></i> request item for stocks</button>
          @endif
          <button type="button" name="button" id="none-existing-itemRV"><i class="fa fa-plus-circle"></i> request none existing item</button>
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
        <form class="RV-form" action="{{route('SavingRVtoDB')}}" method="post">
          {{ csrf_field() }}
          <input type="text" autocomplete="off" name="Purpose" placeholder="Purpose" value="{{old('Purpose')}}" required>
          <select name="Recommendedby" required>
            <option value="">Recommended by</option>
          @foreach ($managers as $manager)
            <option value="{{$manager->id}}">{{$manager->Fname.' '.$manager->Lname}}</option>
          @endforeach
          </select>
          @if (Auth::user()->Role=='7')
            <input type="text" autocomplete="off" name="BudgetAvailable" placeholder="Budget available" required>
          @endif
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
        <textarea name="Description" placeholder="Articles (max:50 characters)" required></textarea>
        <select name="Unit" required>
          <option value="">Select Unit</option>
          <option value="PC">PC</option>
          <option value="BOX">BOX</option>
          <option value="DOZ">DOZ</option>
          <option value="REAM">REAM</option>
        </select>
        <input type="number" autocomplete="off" name="Quantity" min="1" required>
        <input type="text" autocomplete="off" name="Remarks" placeholder="Remarks">
        <div class="buttons-RV-Item">
          <button type="button" id="closemodalRV">Cancel</button>
          <button type="submit" id="addtolistRV">Add to list</button>
        </div>
      </form>
    </div>
  </div>
  @if ((Auth::user()->Role==4)||(Auth::user()->Role==3))
  <div class="for-stock-Modal">
    <div class="middle-forStock-div">
      <h1>Request for warehouse stock items <a href="#"><i class="fa fa-times-circle"></i></a></h1>
      <div class="searchboxes-forstock">
        <div class="empty-left">
        </div>
        <form class="search-description-RV" action="{{route('SearchDescriptionRVstock')}}" method="get">
          <input type="text" autocomplete="off" name="Description" placeholder="Article/Description"><button type="submit" name="button"><i class="fa fa-search"></i></button>
        </form>
      </div>
      <div class="searchResults-forstock">
        <table>
          <tr>
            <th>Item Code</th>
            <th>Article</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Remaining</th>
            <th>Remarks</th>
            <th>Action</th>
          </tr>
          @if (Session::has('SessionForStock'))
            @foreach (Session::get('SessionForStock') as $item)
              <tr>
              <form class="" action="{{route('AddRVforStockSession')}}" method="post">
                {{ csrf_field() }}
                <input type="text"  name="Description" value="{{$item->Description}}" style="display:none">
                <input type="text" name="Unit" value="{{$item->Unit}}" style="display:none">
                <td>{{$item->ItemCode_id}}</td>
                <td>{{$item->Description}}</td>
                <td>{{$item->Unit}}</td>
                <td><input type="number" autocomplete="off" name="Quantity" min="1" required></td>
                <td>{{$item->MaterialsTicketDetails->last()->CurrentQuantity}}</td>
                <td><input type="text" autocomplete="off" name="Remarks"></td>
                <td><button type="submit"><i class="fa fa-plus-circle"></i></button></td>
              </form>
              </tr>
            @endforeach
          @else
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>No current results</td>
              <td></i></td>
              <td></i></td>
              <td></i></td>
            </tr>
          @endif
        </table>
        @if (Session::has('SessionForStock'))
          <div class="paginate-container">
            {{Session::get('SessionForStock')->links()}}
          </div>
        @endif
      </div>
    </div>
  </div>
  @endif
@endsection
