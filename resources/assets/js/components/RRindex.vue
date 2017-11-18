<template lang="html">
<div class="index-rr-vue">
  <div class="index-RRtitle-container">
    <h1><i class="fa fa-th-large"></i> Receiving Report index</h1>
    <div class="box-search-rr">
      <div class="searchbox-RR">
        <input type="text" autocomplete="off" v-on:keyup="SearchAndFetch()" v-model="SearchRRNo" name="RRNo" placeholder="Enter RR #">
      </div>
    </div>
  </div>
  <div class="index-RR-table-container">
    <table>
      <tr>
        <th>RR No.</th>
        <th>Supplier</th>
        <th>RV No.</th>
        <th>Received by</th>
        <th>Received original by</th>
        <th>Verified by</th>
        <th>Posted to BIN by</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <tr v-for="result in RRDataResults" v-if="result.users[0]!=null">
          <td>{{result.RRNo}}</td>
          <td>{{result.Supplier}}</td>
          <td>{{result.RVNo}}</td>
          <td>
            {{result.users[0].FullName}}<br>
            <i class="fa fa-check" v-if="result.users[0].pivot.Signature=='0'"></i>
            <i class="fa fa-times index-decline" v-else-if="result.users[0].pivot.Signature=='1'"></i>
          </td>
          <td>
            {{result.users[2].FullName}}<br>
            <i class="fa fa-check" v-if="result.users[2].pivot.Signature=='0'"></i>
            <i class="fa fa-times index-decline" v-else-if="result.users[2].pivot.Signature=='1'"></i>
          </td>
          <td>
            {{result.users[1].FullName}}<br>
            <i class="fa fa-check" v-if="result.users[1].pivot.Signature=='0'"></i>
            <i class="fa fa-times index-decline" v-else-if="result.users[1].pivot.Signature=='1'"></i>
          </td>
          <td>
            {{result.users[3].FullName}}<br>
            <i class="fa fa-check" v-if="result.users[3].pivot.Signature=='0'"></i>
            <i class="fa fa-times index-decline" v-if="result.users[3].pivot.Signature=='1'"></i>
          </td>
          <td>
            <i class="fa fa-thumbs-up" v-if="result.Status=='0'"></i>
            <i class="fa fa-times decliner" v-else-if="result.Status=='1'"></i>
            <i class="fa fa-clock-o" v-else></i>
          </td>
          <td><a :href="'/RR-fullpreview/'+result.RRNo"><i class="fa fa-eye"></i></a></td>
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
</div>
</template>

<script>
import axios from 'axios';
  export default {
    data () {
      return {
        SearchRRNo:'',
        RRDataResults:[],
        pagination:[],
        offset:4,
      }
    },
     methods: {
       SearchAndFetch(page)
       {
         var vm=this;
         axios.get(`/RR-index-fetch-and-search?RRNo=`+this.SearchRRNo+`&page=`+page).then(function(response)
         {
          console.log(response);
          Vue.set(vm.$data,'RRDataResults',response.data.data);
          Vue.set(vm.$data,'pagination',response.data);
         });
       },
       changepage(next){
         this.pagination.current_page = next;
         this.SearchAndFetch(next);
       },
    },
    created () {
      this.SearchAndFetch(1);
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
