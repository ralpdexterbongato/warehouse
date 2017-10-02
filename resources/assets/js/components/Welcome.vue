<template lang="html">
<div class="welcome.vue">
  <div class="new-search-container">
    <div class="search-box" :class="[latestFound.MTNo!=null?'SearchItemSuccess':'']">
      <div class="text-left">
        <p v-if="latestFound.MTNo==null">
          <span class="big"><i class="fa fa-search"></i> Search</span> & <span class="big">check</span> item's latest data & history.
        </p>
        <h1 v-else>
          <i class="fa fa-th-large"></i> Item # {{latestFound.MTNo}} detail
        </h1>
      </div>
      <div class="Search-item-box">
        <input id="search-code-input" autocomplete="off" type="text" v-on:keyup.enter="SearchItemHistory(1)" v-model="ItemCodeSearch" placeholder="Item code" required>
        <button id="search-go" type="submit" v-on:click="SearchItemHistory(1)"><i class="fa fa-search"></i></button>
      </div>
    </div>
  </div>
  <div class="data-results-container">
    <div v-if="latestFound.MTNo!=null" class="animated bounceInUp">
      <div class="search-welcome-title">
        <div class="Current-title">
          <h1><i class="fa fa-fire"></i> Latest info</h1>
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
            <th>Month</th>
          </tr>
            <tr>
              <td>{{latestFound.MTType}}</td>
              <td>{{latestFound.MTNo}}</td>
              <td>{{latestFound.AccountCode}}</td>
              <td>{{latestFound.ItemCode}}</td>
              <td>{{latestFound.master_items.Description}}</td>
              <td>{{latestFound.UnitCost}}</td>
              <td>{{latestFound.Quantity}}</td>
              <td>{{latestFound.master_items.Unit}}</td>
              <td>{{latestFound.Amount}}</td>
              <td>{{latestFound.CurrentCost}}</td>
              <td>{{latestFound.CurrentQuantity}}</td>
              <td>{{latestFound.CurrentAmount}}</td>
              <td>{{latestFound.MTDate}}</td>
            </tr>
        </table>

      </div>
      <div class="history-found">
        <h1><i class="fa fa-history"></i> History</h1>
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
            <!-- @foreach ($historiesfound as $history) -->
            <tr v-for="history in historiesfound" v-if="history.id!=latestFound.id">
              <td>{{history.MTType}}</td>
              <td>{{history.MTNo}}</td>
              <td>{{history.AccountCode}}</td>
              <td>{{history.ItemCode}}</td>
              <td>{{latestFound.master_items.Description}}</td>
              <td>{{history.UnitCost}}</td>
              <td>{{history.Quantity}}</td>
              <td>{{latestFound.master_items.Unit}}</td>
              <td>{{history.Amount}}</td>
              <td>{{history.CurrentCost}}</td>
              <td>{{history.CurrentQuantity}}</td>
              <td>{{history.CurrentAmount}}</td>
              <td>{{history.MTDate}}</td>
            </tr>
            <!-- @endforeach -->
        </table>
        <div class="paginate-container">
          <ul class="pagination">
            <li v-if="pagination.current_page > 1">
              <a href="#" @click.prevent="changepage(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
            </li>
            <li v-for="page in pagesNumber" v-bind:class="[ page == isActive ? 'active':'']">
              <a href="#" @click.prevent="changepage(page)">{{page}}</a>
            </li>
            <li v-if="pagination.current_page < pagination.last_page">
              <a href="#" @click.prevent="changepage(pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
            </li>
          </ul>
        </div>
      </div><!--  end of v-if result is not empty -->
    </div>
    <div class="background-pic" v-else>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios';
  export default {
    data () {
      return {
        ItemCodeSearch:'',
        pagination:[],
        offset:4,
        latestFound:[],
        historiesfound:[],
      }
    },
    props: [],
    methods: {
      SearchItemHistory(page)
      {
        var vm=this;
        axios.get(`/search-item-code?ItemCode=`+this.ItemCodeSearch+`&page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'latestFound',response.data.latestFound[0]);
          Vue.set(vm.$data,'historiesfound',response.data.historiesfound.data);
          Vue.set(vm.$data,'pagination',response.data.historiesfound);
        });
      },
      changepage(next){
        this.pagination.current_page = next;
        this.SearchItemHistory(next);
      },
    },
    computed:{
      isActive:function(){
        return this.pagination.current_page;
      },
      pagesNumber:function(){
        if (!this.pagination.to) {
                      return [];
                  }
                  var from = this.pagination.current_page - this.offset;
                  if (from < 1) {
                      from = 1;
                  }
                  var to = from + (this.offset * 2);
                  if (to >= this.pagination.last_page) {
                      to = this.pagination.last_page;
                  }
                  var pagesArray = [];
                  while (from <= to) {
                      pagesArray.push(from);
                      from++;
                  }
                  return pagesArray;
      },
    },
  }
</script>
