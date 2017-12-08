@extends('layouts.master')
@section('title')
  Add new Warehouse Item
@endsection
@section('body')
  <div class="add-warehouse-item" id="items">
    <div class="label-warehouse-add-item">
      <h1>Add non-existing <i class="material-icons decliner">do_not_disturb</i> item</h1>
    </div>
    <additemtolist></additemtolist>
  </div>
  <script type="text/javascript" src="/js/item.js">
  </script>
@endsection
