<template lang="html">
  <div class="mrt-index-vue">
    <div class="title-mrt-index-and-search">
      <h1><i class="fa fa-th-large"></i> Materials Returned Ticket index</h1>
      <input type="text" placeholder="MRT #" v-model="MRTSearch" v-on:keyup="fetchandSearch()">
    </div>
    <div class="mrt-index-table-container">
      <table>
        <tr>
          <th>MRTNo</th>
          <th>MCTNo</th>
          <th>Return Date</th>
          <th>Particulars</th>
          <th>Addressed to</th>
          <th>Received by</th>
          <th>Returned by</th>
          <th>Status</th>
          <th>Show</th>
        </tr>
        <tr v-for="data in MRTindexData" v-if="data.users[0]!=null">
          <td>{{data.MRTNo}}</td>
          <td>{{data.MCTNo}}</td>
          <td>{{data.ReturnDate}}</td>
          <td>{{data.Particulars}}</td>
          <td>{{data.AddressTo}}</td>
          <td>
            {{data.users[0].FullName}}<br>
            <i class="fa fa-check" v-if="data.users[0].pivot.Signature=='0'"></i>
            <i class="fa fa-times decliner" v-if="data.users[0].pivot.Signature=='1'"></i>
          </td>
          <td>
            {{data.users[1].FullName}}<br>
            <i class="fa fa-check" v-if="data.users[1].pivot.Signature=='0'"></i>
            <i class="fa fa-times decliner" v-if="data.users[1].pivot.Signature=='1'"></i>
          </td>
          <td>
            <i class="fa fa-thumbs-up" v-if="data.Status=='0'"></i>
            <i class="fa fa-times decliner" v-else-if="data.Status=='1'"></i>
            <i class="fa fa-clock-o darker-blue" v-else></i>
          </td>
          <td><a :href="'/mrt-preview-page/'+data.MRTNo"><i class="fa fa-eye"></i></a></td>
        </tr>
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
    </div>
  </div>
</template>

<script>
import axios from 'axios';
  export default {
     data () {
       return {
       MRTSearch:'',
       MRTindexData:[],
       pagination:[],
       offset:4,
       }
     },
    // props: [],
     methods: {
       fetchandSearch(page)
       {
         var vm=this;
         axios.get(`/mrt-index-fetch-search?MRTNo=`+this.MRTSearch+`&page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'MRTindexData',response.data.data);
          Vue.set(vm.$data,'pagination',response.data);
        })
      },
      changepage(next){
      this.pagination.current_page = next;
      this.fetchandSearch(next);
      },
     },
     created()
     {
       this.fetchandSearch(1);
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
