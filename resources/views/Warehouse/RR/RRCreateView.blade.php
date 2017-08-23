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
      <div id="rr">
        <rrcreatesearch>
        </rrcreatesearch>
      </div>

      <div class="RRMaster-details-container">
        <form class="form-rr-master" action="{{route('SaveRRtoDB')}}" method="post">
          {{ csrf_field() }}
          <input type="text" name="Supplier" value="{!! old('Supplier') !!}" placeholder="Supplier" autocomplete="off" required>
          <input type="text" name="Address" value="{{old('Address')}}" placeholder="Address" autocomplete="off" required>
          <input type="text" name="InvoiceNo" value="{{old('InvoiceNo')}}" placeholder="Invoice No." autocomplete="off" required>
          <input type="text" name="RVNo" value="{{old('RVNo')}}" placeholder="R.V. No." autocomplete="off" required>
          <input type="text" name="Carrier" value="{{old('Carrier')}}" placeholder="Carrier" autocomplete="off" required>
          <input type="text" name="DeliveryReceiptNo" value="{{old('DeliveryReceiptNo')}}" placeholder="Delivery Receipt No." autocomplete="off" required>
          <input type="text" name="PONo" value="{{old('PONo')}}" placeholder="P.O. No" autocomplete="off">
          <textarea name="Note" placeholder="Note(50 characters max)" value="{{old('Note')}}" autocomplete="off" ></textarea>
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
                    <td><input type="text" autocomplete="off" name="AccountCode" value="{{old('AccountCode')}}" required></td>
                  </tr>
                  <tr>
                    <th>Item code</th>
                    <td><input type="text" autocomplete="off" name="ItemCode" value="{{old('ItemCode_id')}}" required></td>
                  </tr>
                  <tr>
                    <th>Description</th>
                    <td><textarea name="Description" value="{{old('Description')}}" required></textarea></td>
                  </tr>
                  <tr>
                    <th>Unit cost</th>
                    <td><input type="text" autocomplete="off" name="UnitCost" value="{{old('UnitCost')}}" min="1" step=".01" required></td>
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
                    <td><input type="number" autocomplete="off" name="QuantityDelivered" value="{{old('QuantityDelivered')}}" min="1" required></td>
                  </tr>
                  <tr>
                    <th>Quantity accepted</th>
                    <td><input type="number" autocomplete="off" name="QuantityAccepted" value="{{old('QuantityAccepted')}}" min="1" required></td>
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
  <script src="/js/rr.js" charset="utf-8">
  </script>

@endsection
