@extends('layouts.master')
@section('title')
  MIRS | Create
@endsection

@section('body')
  <div class="MCT-CONTAINER">
    <div class="Search-item-container">
      <div class="Added-Items">
        <div class="modal-find-button" >
            <button type="button" name="button">Add item</button>
        </div>
        <div class="added-table-wrapper">
          <table>
            <tr>
              <th>Item Code</th>
              <th>Particular</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Remarks</th>
              <th>Action</th>
            </tr>
              @if(!empty(Session::get('ItemSelected')))
              @foreach (Session::get('ItemSelected') as $selected)
                <tr>
                  <td>{{$selected->ItemCode_id}}</td>
                  <td>{{$selected->Particulars}}</td>
                  <td>{{$selected->Quantity}}</td>
                  <td>{{$selected->Unit}}</td>
                  <td>{{$selected->Remarks}}</td>
                  <td class="delete-trash"><i class="fa fa-trash" onclick="$('.delete-submit{{$selected->ItemCode_id}}').submit()"></i></td>
                  <form class="delete-submit{{$selected->ItemCode_id}}"  action="{{route('delete.session',[$selected->ItemCode_id])}}" method="post" style="display:none">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                  </form>
                </tr>
              @endforeach
              @else
                <td></td>
                <td></td>
                <td><h1>Add item here</h1></td>
                <td></td>
                <td></td>
                <td></td>
              @endif
          </table>
        </div>
      </div>
    </div>
    <div class="MCTform-container">
      <form action="{{route('mirs.store')}}" method="post">
        {{ csrf_field() }}
        <ul>
          <li><input type="text" name="Purpose" placeholder="Purpose" required></li>
          <li><select class="" name="Recommendedby" required>
              <option>Recommended by</option>
            @if (!empty($allManager[0]))
              @foreach ($allManager as $manager)
                <option value="{{$manager->id}}">{{ $manager->Fname.' '.$manager->Lname}}</option>
              @endforeach
            @endif
          </select></li>
          @if (!empty($GenMan[0]))
            <li>
              <input type="text" name="Approvedby" value="{{$GenMan[0]->id}}" style="display:none">
              <p class="gm-label">To be Approved by the General Manager</p><br><h3 class="gm-name">{{$GenMan[0]->Fname .' '.$GenMan[0]->Lname}}</h3>
            </li>
          @endif
          <div class="submitMCT-btn">
            <button type="button" name="button">Cancel</button>
            <button id="go-btn" type="submit">Done</button>
          </div>
        </ul>
      </form>
    </div>
    <div class="modal-search-item">
      <div class="middle-modal-search">
          <h5><i class="fa fa-times"></i></h5>
        <div class="search-item-bar">
          <form action="{{route('ItemsearchDescription')}}" method="post">
            {{ csrf_field() }}
            <input type="text" name="Description" placeholder="Search by description" required><button type="submit"><i class="fa fa-search"></i></button>
          </form>
          <form action="{{route('searchItemMaster')}}" method="GET">
            {{ csrf_field() }}
            <input type="text" name="ItemCode" placeholder="Search by item code">
            <button type="submit"><i class="fa fa-search"></i></button>
          </form>
        </div>
        <div class="modal-search-results">
          <table>
            <tr>
              <th>Item code</th>
              <th>Particular</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Remarks</th>
              <th>Action</th>
            </tr>
            @if(Session::has('itemMasters'))
            @foreach (Session::get('itemMasters') as $itemMaster)
              <form action="{{route('selecting.item')}}" method="post">
                {{ csrf_field() }}
                <tr>
                  <td>{{$itemMaster->ItemCode_id}}</td>
                  <td>{{$itemMaster->Description}}</td>
                  <input type="text" name="id" value="{{$itemMaster->id}}" style="display:none">
                  <input type="text" name="ItemCode_id" value="{{$itemMaster->ItemCode_id}}" style="display:none">
                  <input type="text" name="Particulars" value="{{$itemMaster->Description}}" style="display:none">
                  <input type="text" name="Unit" value="{{$itemMaster->Unit}}" style="display:none">
                  <td><input type="number" name="Quantity" min="1" max="{{$itemMaster->MaterialsTicketDetails->last()->CurrentQuantity}}" required></td>
                  <td>{{$itemMaster->Unit}}</td>
                  <td><input type="text" name="Remarks" required></td>
                  <td><button type="submit"><i class="fa fa-plus"></i>Add</button></td>
                </tr>
              </form>
            @endforeach
          @else
            <tr>
              <td></td>
              <td></td>
              <td><h1>No results found</h1></td>
              <td></td>
              <td></td>
              <td> </td>

            </tr>
          @endif

          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
