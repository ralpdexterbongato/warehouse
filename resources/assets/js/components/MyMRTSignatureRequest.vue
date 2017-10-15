<template lang="html">
  <div class="table-container-mrt-request">
    <table v-if="MRTrequests[0]!=null">
      <tr>
        <th>MRTNo</th>
        <th>Return date</th>
        <th>Particulars</th>
        <th>Address To</th>
        <th>Returned by</th>
        <th>Receivedby</th>
        <th>Remarks</th>
        <th>Open</th>
      </tr>
      <tr v-for="mrt in MRTrequests">
        <td>{{mrt.MRTNo}}</td>
        <td>{{mrt.ReturnDate}}</td>
        <td>{{mrt.Particulars}}</td>
        <td>{{mrt.AddressTo}}</td>
        <td>{{mrt.Returnedby}}</td>
        <td>{{mrt.Receivedby}}</td>
        <td>{{mrt.Remarks}}</td>
        <td><a :href="'/mrt-preview-page/'+mrt.MRTNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
    <h1 v-else class="No-MRT"> No MRT signature request</h1>
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
import axios from 'axios'
  export default {
     data () {
        return {
          MRTrequests:[],
          offset:'4',
          pagination:[],
        }
      },
     methods: {
       fetchData(page)
       {
         var vm=this;
         axios.get(`/my-mrt-signature-request-fetchdata?page=`+page).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'MRTrequests',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
         });
       },
       changepage(next){
       this.pagination.current_page = next;
       this.fetchData(next);
      },
     },
     created()
     {
       this.fetchData();
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
