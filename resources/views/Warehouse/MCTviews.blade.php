@extends('layouts.master')
@section('title')
  MCT ADDING
@endsection

@section('body')
  <div class="MCT-CONTAINER">
    <div class="Search-item-container">

      <div class="Added-Items">
        <div class="modal-find-button" >
            <button type="button" name="button">Add item</button>
        </div>
        <table>
          <tr>
            <th>Item Code</th>
            <th>Particular</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Remarks</th>
            <th>Action</th>
          </tr>
          @if(Session::has('ItemSelected'))
            @foreach (Session::get('ItemSelected') as $selected)
              <tr>
                <td>{{$selected->ItemCode}}</td>
                <td>{{$selected->Particulars}}</td>
                <td>{{$selected->Quantity}}</td>
                <td>{{$selected->Unit}}</td>
                <td>{{$selected->Remarks}}</td>
                <td><i class="fa fa-trash"></i></td>
              </tr>
            @endforeach
          @endif
        </table>
      </div>
    </div>
    <div class="MCTform-container">
      <div class="instructions">

      </div>
      <form class="" action="index.html" method="post">
        <ul>
          <li><input type="text" name="" placeholder="Purpose"></li>
          <li><input type="text" name="" placeholder="Remarks"></li>
          <li><input type="text" name="" placeholder="Prepared by"></li>
          <li><input type="text" name="" placeholder="Recomended by"></li>
          <li><input type="text" name="" placeholder="Approved by"></li>
          <div class="submitMCT-btn">
            <button type="button" name="button">Cancel</button>
            <button type="button" name="button">Done</button>
          </div>
        </ul>
      </form>
    </div>
    <div class="modal-search-item">
      <div class="middle-modal-search">
          <h5><i class="fa fa-times"></i></h5>
        <div class="search-item-bar">
          <form action="{{route('searchItemMaster')}}" method="GET">
            {{ csrf_field() }}
            <input type="text" name="ItemCode" placeholder="Enter item code">
            <button type="submit"><i class="fa fa-search"></i></button>
          </form>
        </div>
        <div class="modal-search-results">
          <table>
            <tr>
              <th>Item code</th>
              <th>Description</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Remarks</th>
              <th>Action</th>
            </tr>
            @if(isset($itemMasters[0]->id))
            @foreach ($itemMasters as $itemMaster)
              <form action="{{route('selecting.item')}}" method="post">
                {{ csrf_field() }}
                <tr>
                  <td>{{$itemMaster->ItemCode}}</td>
                  <td>{{$itemMaster->Description}}</td>
                  <input type="text" name="ItemCode" value="{{$itemMaster->ItemCode}}" style="display:none">
                  <input type="text" name="Particulars" value="{{$itemMaster->Description}}" style="display:none">
                  <td><input type="number" name="Quantity" min="1"></td>
                  <td>
                    <select name="Unit">
                      <option value="">Select</option>
                      <option value="PC">PC</option>
                      <option value="DOZ">DOZ</option>
                      <option value="BOX">BOX</option>
                    </select>
                  </td>
                  <td><input type="text" name="Remarks"></td>
                  <td><button type="submit"><i class="fa fa-plus"></i>Add</button></td>
                </tr>
              </form>
            @endforeach
          @else
            <tr>
              <td></td>
              <td> </td>
              <td><h1>No results found</h1> </td>
              <td> </td>
              <td> </td>
              <td> </td>

            </tr>
          @endif

          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
