@extends('layouts.master')
@section('title')
warehouse | BOHECO 1
@endsection

@section('body')
  <div class="body-container">
    <div class="new-search-container">
      <div class="empty-left">
        <div class="empty-left-btn">
          <ul>
            <li class="dropping-parent">
              <h1>MIRS <i class="fa fa-angle-down"></i></h1>
              <ul class="dropping">
                <a href="{{route('mirs.add')}}"><li><i class="fa fa-print"></i> Create MIRS</li></a>
                <a href="{{route('PreviewMIRS')}}"><li><i class="fa fa-search"></i> Search MIRS</li></a>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      <div class="search-box">
        <form action="{{route('search.code')}}" method="get">
          {{ csrf_field() }}
          <input id="search-code-input" type="text" name="ItemCode" placeholder="Item code here..." required>
          <button id="search-go" type="submit" name="button"><i class="fa fa-search"></i></button>
        </form>
      </div>
      <div class="new-item">
        <div class="add-new-item">
          <button type="button">Add new record</button>
        </div>
      </div>
    </div>
    <div class="data-results-container">
    @if(isset($historiesfound[0]))
      <div class="small-choices">
        <div class="small-choices-btns">
          <a href="#"><button type="button">RR</button></a>
        </div>
        <div class="Current-title">
          <h1>Latest Data</h1>
        </div>
        <div class="empty-space">
        </div>
      </div>
      <div class="latest-data">
        <table>
          <tr>
            <th>MT type</th>
            <th>MT No.</th>
            <th>Account code</th>
            <th>Item code</th>
            <th>Description</th>
            <th>Unit cost</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Amount</th>
            <th>Current cost</th>
            <th>Current quantity</th>
            <th>Current amount</th>
            <th>Date</th>
          </tr>
            <tr>
              <td>{{$historiesfound[0]->MTType}}</td>
              <td>{{$historiesfound[0]->MTNo}}</td>
              <td>{{$historiesfound[0]->AccountCode}}</td>
              <td>{{$historiesfound[0]->ItemCode}}</td>
              <td>{{$masterfound[0]->Description}}</td>
              <td>{{number_format($historiesfound[0]->UnitCost,'2','.',',')}}</td>
              <td>{{$historiesfound[0]->Quantity}}</td>
              <td>{{$historiesfound[0]->Unit}}</td>
              <td>{{number_format($historiesfound[0]->Amount,'2','.',',')}}</td>
              <td>{{number_format($historiesfound[0]->CurrentCost,'2','.',',')}}</td>
              <td>{{$historiesfound[0]->CurrentQuantity}}</td>
              <td>{{number_format($historiesfound[0]->CurrentAmount,'2','.',',')}}</td>
              <td>{{$historiesfound[0]->created_at->format('M')}}</td>
            </tr>
        </table>

      </div>
      <div class="history-found">
        <h1>Recent History</h1>
        <table>
          <tr>
            <th>MT type</th>
            <th>MT No.</th>
            <th>Account code</th>
            <th>Item code</th>
            <th>Description</th>
            <th>Unit cost</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Amount</th>
            <th>Current cost</th>
            <th>Current quantity</th>
            <th>Current amount</th>
            <th>Month</th>
          </tr>
            @foreach ($historiesfound as $history)
            <tr>
              <td>{{$history->MTType}}</td>
              <td>{{$history->MTNo}}</td>
              <td>{{$history->AccountCode}}</td>
              <td>{{$history->ItemCode}}</td>
              <td>{{$masterfound[0]->Description}}</td>
              <td>{{number_format($history->UnitCost,'2','.',',')}}</td>
              <td>{{$history->Quantity}}</td>
              <td>{{$history->Unit}}</td>
              <td>{{number_format($history->Amount,'2','.',',')}}</td>
              <td>{{number_format($history->CurrentCost,'2','.',',')}}</td>
              <td>{{$history->CurrentQuantity}}</td>
              <td>{{number_format($history->CurrentAmount,'2','.',',')}}</td>
              <td>{{$history->created_at->format('M')}}</td>
            </tr>
            @endforeach
          @else
            <div class="background-pic animated slideInRight">

            </div>
          @endif
        </table>
      </div>
    </div>
  </div>
  <div class="add-new-modal">
    <div class="new-modal-box">
        <div class="new-modal-title">
          <h1>Create new item</h1>
        </div>
        <div class="add-new-item-form">
          <form class="form-new-item" action="{{route('store')}}" method="post">
            {{ csrf_field() }}
            <table>
              <tr>
                <th>Account code</th>
                <td><input type="text" name="AccountCode" value="{{old('AccountCode')}}"></td>
              </tr>
              <tr>
                <th>Item code</th>
                <td><input type="text" name="ItemCode" value="{{old('ItemCode')}}"></td>
              </tr>
              <tr>
                <th>Description</th>
                <td><textarea name="Description" value="{{old('Description')}}"></textarea></td>
              </tr>
              <tr>
                <th>Unit cost</th>
                <td><input type="text" name="UnitCost" value="{{old('UnitCost')}}"></td>
              </tr>
              <tr>
                <th>Unit</th>
                <td><select name="Unit" value="{{old('Unit')}}">
                  <option value="PC">PC</option>
                  <option value="BOX">BOX</option>
                  <option value="DOZ">DOZ</option>
                  <option value="REAM">REAM</option>
                </select></td>
              </tr>
              <tr>
                <th>Quantity</th>
                <td><input type="text" name="Quantity" value="{{old('Quantity')}}"></td>
              </tr>
            </table>
            <div class="submit-bottons-newitem-container">
              <div class="empty-submit">

              </div>
              <div class="submit-bottons-new">
                <button id="cancel-btn" type="button">Cancel</button>
                <button id="go-create" type="submit">Go</button>
              </div>
            </div>
          </form>
        </div>
    </div>
  </div>
@endsection
