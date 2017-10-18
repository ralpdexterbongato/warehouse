<template lang="html">
<div class="mct-index-vue-container">
  <div class="index-mct-title-and-search">
    <h1><i class="fa fa-th-large"></i> Materials Charge Ticket index</h1>
    <div class="search-mct-index">
      <input type="text" placeholder="MCT #" v-on:keyup="fetchdatatable()" v-model="searchMCTNo">
    </div>
  </div>
  <div class="mct-index-table">
    <table>
      <tr>
        <th>MCTNo</th>
        <th>Date created</th>
        <th>Particulars</th>
        <th>Issued by</th>
        <th>Received by</th>
        <th>Addressed to</th>
        <th>Status</th>
        <th>Show</th>
      </tr>
      <tr v-for="data in indexData">
        <td>{{data.MCTNo}}</td>
        <td>{{data.MCTDate}}</td>
        <td>{{data.Particulars}}</td>
        <td>
          {{data.Issuedby}}<br>
          <i class="fa fa-check"></i>
        </td>
        <td>
          {{data.Receivedby}}<br>
          <i class="fa fa-check" v-if="data.ReceivedbySignature!=null"></i>
          <i class="fa fa-times decliner" v-if="data.IfDeclined==data.Receivedby"></i>
        </td>
        <td>{{data.AddressTo}}</td>
        <td>
          <i class="fa fa-thumbs-up" v-if="data.ReceivedbySignature!=null"></i>
          <i class="fa fa-times decliner" v-else-if="data.IfDeclined!=null"></i>
          <i class="fa fa-clock-o darker-blue" v-else></i>
        </td>
        <td><a :href="'/preview-mct-page-only/'+data.MCTNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
  </div>
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
</div>

</template>
<script>
import axios from 'axios';
  export default {
    data () {
       return {
         indexData:[],
         pagination:[],
         searchMCTNo:'',
         offset:4,
       }
     },

     methods: {
       fetchdatatable(page)
       {
         var vm=this;
         axios.get(`/mct-index-fetchdata?MCTNo=`+this.searchMCTNo+`&page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'indexData',response.data.data);
          Vue.set(vm.$data,'pagination',response.data);
        })
      },
      changepage(next){
        this.pagination.current_page = next;
        this.fetchdatatable(next);
      },
     },
     created()
     {
       this.fetchdatatable(1);
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
       }
     },

  }
</script>
