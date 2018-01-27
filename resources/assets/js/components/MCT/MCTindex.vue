<template lang="html">
<div class="mct-index-vue-container">
  <div class="index-mct-title-and-search">
    <h1><i class="material-icons">show_chart</i> Materials Charge Ticket index</h1>
    <div class="search-mct-index">
      <input type="text" placeholder="MCT #" v-on:keyup="fetchdatatable()" v-model="searchMCTNo">
    </div>
  </div>
  <div class="mct-index-table">
    <table>
      <tr>
        <th>MCTNo</th>
        <th>Date</th>
        <th>Particulars</th>
        <th>Issued by</th>
        <th>Received by</th>
        <th>Addressed to</th>
        <th>Status</th>
        <th>View</th>
      </tr>
      <tr v-for="data in indexData" v-if="data.users[0]!=null">
        <td class="rollback-sign"><h2 v-if="data.IsRollBack==0"></h2>{{data.MCTNo}}</td>
        <td>{{data.MCTDate}}</td>
        <td>{{data.Particulars}}</td>
        <td>
          {{data.users[0].FullName}}<br>
          <i class="material-icons" v-if="data.users[0].pivot.Signature=='0'">check</i>
          <i class="material-icons decliner" v-else-if="data.users[0].pivot.Signature=='1'">close</i>
        </td>
        <td>
          {{data.users[1].FullName}}<br>
          <i class="material-icons" v-if="data.users[1].pivot.Signature=='0'">check</i>
          <i class="material-icons decliner" v-else-if="data.users[1].pivot.Signature=='1'">close</i>
        </td>
        <td>{{data.AddressTo}}</td>
        <td>
          <i class="material-icons" v-if="data.Status=='0'">thumb_up</i>
          <i class="material-icons decliner" v-else-if="data.Status=='1'">close</i>
          <i class="material-icons" v-else>access_time</i>
        </td>
        <td><a :href="'/preview-mct-page-only/'+data.MCTNo"><i class="material-icons">visibility</i></a></td>
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
