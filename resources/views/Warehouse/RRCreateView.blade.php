@extends('layouts.master')
@section('title')
  Make RR
@endsection
@section('body')
  <div class="MakeRR-Container">
    <h1 class="RR-title">Create Receiving Report</h1>
    <div class="search-orInputNew-buttons">
      <button class="searchRRitem" type="button" name="button"><i class="fa fa-search"></i> Find Item</button>
      <button class="addnon-existing"type="button" name="button"><i class="fa fa-plus"></i> Add non-existing item</button>
    </div>
    <div class="RR-Form-container">
      <div class="Items-RR-table">
        <table>
          <tr>
            <th>Code NO.</th>
            <th>Article</th>
            <th>Unit</th>
            <th>Quantity Delivered</th>
            <th>Quantity Accepted</th>
            <th>U-Cost</th>
            <th>Action</th>
          </tr>
          @if (Session::has('RR-Items-Added'))
          @foreach (Session::get('RR-Items-Added') as $RRselecteditems)
            <tr>
              <td>{{$RRselecteditems->ItemCode}}</td>
              <td>{{$RRselecteditems->Description}}</td>
              <td>{{$RRselecteditems->Unit}}</td>
              <td>{{$RRselecteditems->QuantityDelivered}}</td>
              <td>{{$RRselecteditems->QuantityAccepted}}</td>
              <td>{{number_format($RRselecteditems->UnitCost,'2','.',',')}}</td>
              <td><i class="fa fa-trash" onclick="$('.delete-session-rr-form{{$RRselecteditems->ItemCode}}').submit()"></i></td>
              <form class="delete-session-rr-form{{$RRselecteditems->ItemCode}}" action="{{route('RRDeleteSession',[$RRselecteditems->ItemCode])}}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE')}}
              </form>
            </tr>
          @endforeach
        @endif
        </table>
      </div>
      <div class="RRMaster-details-container">
        <form class="form-rr-master" action="{{route('SaveRRtoDB')}}" method="post">
          {{ csrf_field() }}
          <input type="text" name="Suplier" value="{{old('Suplier')}}" placeholder="Suplier" required>
          <input type="text" name="Address" value="{{old('Address')}}" placeholder="Address" required>
          <input type="text" name="InvoiceNo" value="{{old('InvoiceNo')}}" placeholder="Invoice No." required>
          <input type="text" name="RVNo" value="{{old('RVNo')}}" placeholder="R.V. No." required>
          <input type="text" name="BNo" value="{{old('BNo')}}" placeholder="B/L-AW/-W/B-No." required>
          <input type="text" name="Carrier" value="{{old('Carrier')}}" placeholder="Carrier" required>
          <input type="text" name="DeliveryReceiptNo" value="{{old('DeliveryReceiptNo')}}" placeholder="Delivery Receipt No." required>
          <input type="text" name="PONo" value="{{old('PONo')}}" placeholder="P.O. No" required>
          <textarea name="Note" placeholder="Note(50 characters max)" value="{{old('Note')}}" required></textarea>
          <select class="RRoptions" name="Verifiedby" required>
            <option value="">Select a Manager to Verify</option>
            @foreach ($Managers as $manager)
              <option value="{{$manager->id}}">{{$manager->Fname.' '.$manager->Lname}}</option>
            @endforeach
          </select>
          <select class="RRoptions" name="ReceivedOriginalby" required>
            <option value="">Received originaly by</option>
            @foreach ($Auditors as $auditor)
            <option value="{{$auditor->id}}">{{$auditor->Fname.' '.$auditor->Lname}}</option>
            @endforeach
          </select>
          <select class="RRoptions" name="PostedtoBINby" required>
            <option value="">Posted to BIN by</option>
            @foreach ($Clerks as $clerk)
              <option value="{{$clerk->id}}">{{$clerk->Fname.' '.$clerk->Lname}}</option>
            @endforeach
          </select>
          <div class="rr-form-btns">
            <button class="cancel-btn-rr" type="button">Cancel</button>
            <button class="submit-btn-rr"type="submit">Done</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="add-new-modal">
    <div class="new-modal-box">
        <div class="new-modal-title">
          <h1>Create new item</h1>
        </div>
          <div class="add-new-item-form">
              <form class="form-new-item" action="{{route('RRSession')}}" method="post">
                {{ csrf_field() }}
                <table>
                  <tr>
                    <th>Account code</th>
                    <td><input type="text" name="AccountCode" value="{{old('AccountCode')}}" required></td>
                  </tr>
                  <tr>
                    <th>Item code</th>
                    <td><input type="text" name="ItemCode" value="{{old('ItemCode_id')}}" required></td>
                  </tr>
                  <tr>
                    <th>Description</th>
                    <td><textarea name="Description" value="{{old('Description')}}" required></textarea></td>
                  </tr>
                  <tr>
                    <th>Unit cost</th>
                    <td><input type="text" name="UnitCost" value="{{old('UnitCost')}}" min="1" step=".01" required></td>
                  </tr>
                  <tr>
                    <th>Unit</th>
                    <td><select name="Unit" value="{{old('Unit')}}" required>
                      <option value="">Unit</option>
                      <option value="PC">PC</option>
                      <option value="BOX">BOX</option>
                      <option value="DOZ">DOZ</option>
                      <option value="REAM">REAM</option>
                    </select></td>
                  </tr>
                  <tr>
                    <th>Quantity delivered</th>
                    <td><input type="number" name="QuantityDelivered" value="{{old('QuantityDelivered')}}" min="1" required></td>
                  </tr>
                  <tr>
                    <th>Quantity accepted</th>
                    <td><input type="number" name="QuantityAccepted" value="{{old('QuantityAccepted')}}" min="1" required></td>
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
  <div class="search-itemRR-Container">
    <div class="search-RR-center">
      <h1><i class="fa fa-times"></i></h1>
      <div class="searchboxes-div">
        <form class="searchboxRR" action="{{route('SearchbyDescriptionRR')}}" method="GET">
          <input type="text" name="Description" placeholder="Search by Description"><button type="submit" name="button"><i class="fa fa-search"></i></button>
        </form>
        <form class="searchboxRR" action="{{route('RRSearchItemCode')}}" method="GET">
          <input type="text" name="ItemCode_id" placeholder="Search by Item Code"><button type="submit" name="button"><i class="fa fa-search"></i></button>
        </form>
      </div>
      <div class="table-RRresults-container">
        <table>
          <tr>
            <th>Code No.</th>
            <th>Article</th>
            <th>Unit</th>
            <th>Quantity Delivered</th>
            <th>Quantity Accepted</th>
            <th>U-Cost</th>
            <th>Action</th>
          </tr>
          @if (Session::has('itemMastersRR'))
            @foreach (Session::get('itemMastersRR') as $itemMaster)
              <form action="{{route('addExistingToSession')}}" method="post">
              {{ csrf_field() }}
                <tr>
                  <input type="text" name="AccountCode" value="{{$itemMaster->AccountCode}}" style="display:none">
                  <input type="text" name="ItemCode" value="{{$itemMaster->ItemCode_id}}" style="display:none">
                  <input type="text" name="Description" value="{{$itemMaster->Description}}"style="display:none">
                  <input type="text" name="Unit" value="{{$itemMaster->Unit}}" style="display:none">
                  <td>{{$itemMaster->ItemCode_id}}</td>
                  <td>{{$itemMaster->Description}}</td>
                  <td>{{$itemMaster->Unit}}</td>
                  <td><input type="number" name="QuantityDelivered" min="1" required></td>
                  <td><input type="number" name="QuantityAccepted" min="1" required></td>
                  <td><input class="inputUC" type="text" name="UnitCost" min="1" step="0.01" required></td>
                  <td><button class="add-toRRlist-btn" type="submit" name="button"><i class="fa fa-plus"></i> Add</button></td>
                </tr>
            </form>
          @endforeach
        @else
          <tr>
            <td>empty</td>
            <td>empty</td>
            <td>empty</td>
            <td>empty</td>
            <td>empty</td>
            <td>empty</td>
            <td>empty</td>
          </tr>
        @endif
        </table>
        @if (Session::has('itemMastersRR'))
          {{Session::get('itemMastersRR')->links()}}
        @endif
      </div>
    </div>
  </div>

@endsection
