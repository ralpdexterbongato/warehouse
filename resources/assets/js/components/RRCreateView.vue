<template lang="html">
  <div class="big-container-session">
    <div class="Items-RR-table">
      <ul class="rr-error-tab" v-if="laravelerrors!=''">
        <span v-for="errors in laravelerrors">
          <li v-for="error in errors">{{error}}</li>
        </span>
      </ul>
      <div class="successAlertRRsession" v-if="successAlerts!=''">
        <p>{{successAlerts}}</p>
      </div>
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
          <tr v-for="session in sessions">
            <td>{{session.ItemCode}}</td>
            <td>{{session.Description}}</td>
            <td>{{session.Unit}}</td>
            <td>{{session.QuantityDelivered}}</td>
            <td>{{session.QuantityAccepted}}</td>
            <td>{{formatPrice(session.UnitCost)}}</td>
            <td><i @click="RemoveFromSession(session.ItemCode)" class="fa fa-trash"></i></td>
          </tr>
      </table>
    </div>
    <div class="search-itemRR-Container">
      <div class="search-RR-center">
        <h1><i class="fa fa-times"></i></h1>
        <div class="searchboxes-div">
            <div class="search-rr-create">
              <input @keyup="fetchdataItems()" type="text" name="search" v-model="search" placeholder="Search by Description">
            </div>
            <div class="search-rr-create">
              <input @keyup="fetchdataItemsByCode()" type="text" name="searchcode" v-model="searchcode" placeholder="Search by Item Code">
            </div>
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
            <tr v-for="masteritem in model">
                <td>{{masteritem.ItemCode_id}}</td>
                <td>{{masteritem.Description}}</td>
                <td>{{masteritem.Unit}}</td>
                <td><input  type="number" name="QuantityDelivered" v-model="QuantityDelivered[masteritem.ItemCode_id]" min="1" autocomplete="off"></td>
                <td><input type="number" name="QuantityAccepted" v-model="QuantityAccepted[masteritem.ItemCode_id]" min="1" autocomplete="off"></td>
                <td><input  class="inputUC" type="text" name="UnitCost" v-model="UnitCost[masteritem.ItemCode_id]" min="1" step="0.01" autocomplete="off">
                </td>
                <td><button @click.prevent="submitToSession(masteritem)" class="add-toRRlist-btn" type="submit"><i class="fa fa-plus"></i> Add</button></td>
            </tr>
          </table>
            <div class="paginate-container">
              <ul class="pagination" v-if="this.searchcode ==''">
                <li v-if="pagination.current_page > 1">
                  <a href="#" @click.prevent="changePage(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
                </li>
                <li v-for="page in pagesNumber" v-bind:class="[ page == isActive ? 'active':'']">
                  <a href="#" @click.prevent="changePage(page)">{{page}}</a>
                </li>
                <li v-if="pagination.current_page < pagination.last_page">
                  <a href="#" @click.prevent="changePage(pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
                </li>
              </ul>
              <ul class="pagination" v-else="this.searchcode!=''">
                <li v-if="pagination.current_page > 1">
                  <a href="#" @click.prevent="changePageCode(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
                </li>
                <li v-for="page in pagesNumber" v-bind:class="[ page == isActive ? 'active':'']">
                  <a href="#" @click.prevent="changePageCode(page)">{{page}}</a>
                </li>
                <li v-if="pagination.current_page < pagination.last_page">
                  <a href="#" @click.prevent="changePageCode(pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
                </li>
              </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios'
  export default {
  data () {
     return {
       model:[],
       search:'',
       searchcode:'',
       urlsearchcode:'RRsearchitembyCode',
       url:'Items-ByDescription',
       pagination:[],
       offset:4,
       QuantityDelivered:[],
       QuantityAccepted:[],
       UnitCost:[],
       sessions:[],
       laravelerrors:[],
       successAlerts:'',
     }
   },
   computed: {
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
      }
   },


   created: function(){
     this.fetchdataItems(this.pagination.current_page);
     this.DisplaySessiontable();
   },
     methods: {
       fetchdataItems(page){
         this.searchcode='';
         var vm= this
         axios.get(`${this.url}?search=${this.search}&page=`+page)
         .then(function (response){
           console.log(response)
           Vue.set(vm.$data,'model',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
         })
       },
       fetchdataItemsByCode(page){
         this.search='';
          var vm= this
          axios.get(`${this.urlsearchcode}?searchcode=${this.searchcode}&page=`+page)
          .then(function(response){
            Vue.set(vm.$data,'model',response.data.model);
            Vue.set(vm.$data,'pagination',response.data);
          })
      },
     changePage(page){
      this.pagination.current_page = page;
     this.fetchdataItems(page);
    },
    changePageCode(page){
      this.pagination.current_page = page;
      this.fetchdataItemsByCode(page);
    },
    submitToSession(datas)
    {
      if ((this.QuantityDelivered==null)||(this.QuantityAccepted==null)||(this.UnitCost==null)) {
        return [];
      }
      var vm=this
      axios.post(`RRaddingSessionFromExistingItem`,{
        AccountCode:datas.AccountCode,
        ItemCode:datas.ItemCode_id,
        Description:datas.Description,
        Unit:datas.Unit,
        QuantityDelivered:this.QuantityDelivered[datas.ItemCode_id],
        QuantityAccepted:this.QuantityAccepted[datas.ItemCode_id],
        UnitCost:this.UnitCost[datas.ItemCode_id],
      }).then(function(response){
        console.log(response);
        Vue.set(vm.$data,'laravelerrors','');
        Vue.set(vm.$data,'successAlerts','success !');
      },function (error) {
        console.log(error.response.data);
        Vue.set(vm.$data,'successAlerts','');
        Vue.set(vm.$data,'laravelerrors',error.response.data)
        });
      this.DisplaySessiontable();

        this.QuantityDelivered=[];
        this.QuantityAccepted=[];
        this.UnitCost=[];
    },

    DisplaySessiontable()
    {
      var vm=this
      axios.get(`/displayRRcreateSession`).then(function(response)
      {
        Vue.set(vm.$data,'sessions',response.data.sessions);
      })
    },
    RemoveFromSession(item)
    {
      var vm=$(this)
      axios.delete(`/DeleteSession-RR/`+item)
    this.DisplaySessiontable();
  },

  formatPrice(value) {
        let val = (value/1).toFixed(2).replace('.', '.')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }
  }
}
</script>
