<template lang="html">
  <div class="mr-index-vue">
    <div class="mr-index-search-and-fetch">
      <h1><i class="material-icons">show_chart</i> Memorandum Receipt index</h1>
      <input type="text" placeholder="Enter MR #" v-model="MRNoSearch" v-on:keyup="fetchAndSearch(1)">
    </div>
    <div class="mr-index-table">
      <table>
        <tr>
          <th>MR #</th>
          <th>MR Date</th>
          <th>RV #</th>
          <th>RR #</th>
          <th>PO #</th>
          <th>Supplier</th>
          <th>Recommended by</th>
          <th>Approved by</th>
          <th>Received by</th>
          <th>Status</th>
          <th>View</th>
        </tr>
        <tr v-for="data in MRindexData" v-if="data.users[0].pivot!=null">
          <td>{{data.MRNo}}</td>
          <td>{{data.MRDate}}</td>
          <td>{{data.RVNo}}</td>
          <td>{{data.RRNo}}</td>
          <td v-if="data.PONo!=null">{{data.PONo}}</td>
          <td v-else>N/A</td>
          <td>{{data.Supplier}}</td>
          <td>
            {{data.users[0].FullName}}<br>
            <i class="material-icons" v-if="data.users[0].pivot.Signature=='0'">check</i>
            <i class="material-icons decliner" v-else-if="data.users[0].pivot.Signature=='1'">close</i>
          </td>
          <td>
            {{data.users[1].FullName}}<br>
            <i class="material-icons" v-if="((data.users[1].pivot.Signature=='0')||(data.users[3]!=null && data.users[3].pivot.Signature=='0'))">check</i>
            <i class="material-icons decliner" v-else-if="data.users[1].pivot.Signature=='1'">close</i>
          </td>
          <td>
            {{data.users[2].FullName}}<br>
            <i class="material-icons" v-if="data.users[2].pivot.Signature=='0'">check</i>
            <i class="material-icons decliner" v-else-if="data.users[2].pivot.Signature=='1'">close</i>
          </td>
          <td>
            <i class="material-icons" v-if="data.Status=='0'">thumb_up</i>
            <i class="material-icons decliner" v-else-if="data.Status=='1'">close</i>
            <i class="material-icons" v-else>access_time</i>
          </td>
          <td><a :href="'/full-preview-MR/'+data.MRNo"><i class="material-icons">remove_red_eye</i></a></td>
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
         MRNoSearch:'',
         pagination:[],
         MRindexData:[],
         offset:4,
       }
     },
    methods: {
      fetchAndSearch(page)
      {
        var vm=this;
        axios.get(`/mr-index-fetch-and-search?MRNo=`+this.MRNoSearch+`&page=`+page).then(function(response)
        {
          console.log(response);
          vm.MRindexData=response.data.data;
          vm.pagination=response.data;
        })
      },
      changepage(next){
        this.pagination.current_page = next;
        this.fetchAndSearch(next);
      },
    },
    created () {
      this.fetchAndSearch(1);
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
