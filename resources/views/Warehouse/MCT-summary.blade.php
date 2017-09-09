@extends('layouts.master')
@section('title')
  MCT Summary
@endsection
@section('body')
  <div class="summary-mct-container">
    <div class="MCTSummaryForm">
      <form class="print-summary-mct-btn" action="index.html" method="post">
        <button type="button"><i class="fa fa-file-pdf-o"></i> Download & Print</button>
      </form>
      <form class="mct-sum-search" action="index.html" method="post">
        <input type="text" name="DateChargeSummary" placeholder="Year-Month(yyyy-mm)"><button type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
    <div class="bondpapercontainer-mct">
      <div class="landscape-bondpaper-mct">
        <div class="top-titles-mctSum">
          <img src="/DesignIMG/logo.png" alt="logo">
          <div class="titles-mct-content">
            <h4>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h4>
            <h4 class="address-mct-summary">Cabulijan, Tubigon, Bohol</h4>
            <h3>Summary of Charges(as of APRIL 2017)</h3>
          </div>
        </div>
        <div class="mct-summary-table">
          <table>
            <tr>
              <th>Account</th>
              <th>ItemCode</th>
              <th>Description</th>
              <th>UnitCost</th>
              <th>Unit</th>
              <th>Stock</th>
              <th class="monthof">Month of </th>
              <th class="month"> April</th>
            </tr>
            <tr>
              <th>Code</th>
              <th></th>
              <th></th>
              <th>(April)</th>
              <th></th>
              <th>Balance</th>
              <th>Issued</th>
              <th>Returned</th>
            </tr>
            <tr>
              <td>159-499-449</td>
              <td>L-003</td>
              <td>Description of the ahahaha</td>
              <td>3325</td>
              <td>PC</td>
              <td>3509</td>
              <td>43</td>
              <td>30</td>
            </tr>
            <tr>
              <td>159-499-449</td>
              <td>L-003</td>
              <td>Description of the ahahaha</td>
              <td>3325</td>
              <td>PC</td>
              <td>3509</td>
              <td>43</td>
              <td>30</td>
            </tr>
            <tr>
              <td>159-499-449</td>
              <td>L-003</td>
              <td>Description of the ahahaha</td>
              <td>3325</td>
              <td>PC</td>
              <td>3509</td>
              <td>43</td>
              <td>30</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
