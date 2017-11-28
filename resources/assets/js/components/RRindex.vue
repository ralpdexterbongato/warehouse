<template lang="html">
<div class="index-rr-vue">
  <div class="index-RRtitle-container">
    <h1><i class="material-icons">show_chart</i> Receiving Report index</h1>
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
        <th>RR Date</th>
        <th>Supplier</th>
        <th>Received by</th>
        <th>Verified by</th>
        <th>ReceivedOriginal</th>
        <th>PostedToBIN by</th>
        <th>Status</th>
        <th>View</th>
      </tr>
      <tr v-for="result in RRDataResults" v-if="result.users[0]!=null && result.users[1]!=null && result.users[2]!=null && result.users[3]!=null">
          <td>{{result.RRNo}}</td>
          <td>{{result.RRDate}}</td>
          <td>{{result.Supplier}}</td>
          <td>
            {{result.users[0].FullName}}<br>
            <i class="material-icons" v-if="result.users[0].pivot.Signature=='0'">check</i>
            <i class="material-icons index-decline" v-else-if="result.users[0].pivot.Signature=='1'">close</i>
          </td>
          <td>
            {{result.users[1].FullName}}<br>
            <i class="material-icons" v-if="result.users[1].pivot.Signature=='0'">check</i>
            <i class="material-icons index-decline" v-else-if="result.users[1].pivot.Signature=='1'">close</i>
          </td>
          <td>
            {{result.users[2].FullName}}<br>
            <i class="material-icons" v-if="result.users[2].pivot.Signature=='0'">check</i>
            <i class="material-icons index-decline" v-else-if="result.users[2].pivot.Signature=='1'">close</i>
          </td>
          <td>
            {{result.users[3].FullName}}<br>
            <i class="material-icons" v-if="result.users[3].pivot.Signature=='0'">check</i>
            <i class="material-icons index-decline" v-if="result.users[3].pivot.Signature=='1'">close</i>
          </td>
          <td>
            <i class="material-icons" v-if="result.Status=='0'">thumb_up</i>
            <i class="material-icons decliner" v-else-if="result.Status=='1'">close</i>
            <i class="material-icons" v-else>access_time</i>
          </td>
          <td><a :href="'/RR-fullpreview/'+result.RRNo"><i class="material-icons">remove_red_eye</i></a></td>
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
