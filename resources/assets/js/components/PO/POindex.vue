<template lang="html">
  <div class="po-index-vue">
    <div class="po-index-title-and-search">
      <h1><i class="material-icons">content_paste</i> Purchase Order index</h1>
      <input type="text" placeholder="Enter PO #" v-model="ponumsearch" v-on:keyup="FetchAndSearch(1)">
    </div>
    <div class="po-index-table">
      <table>
        <tr>
          <th>PO #</th>
          <th>PO Date</th>
          <th>RV #</th>
          <th>RV Date</th>
          <th>Supplier</th>
          <th>Purpose</th>
          <th>General Manager</th>
          <th>Status</th>
          <th>Show</th>
        </tr>
        <tr v-for="data in POindexData" v-if="data.users[0]!=null">
          <td>{{data.PONo}}</td>
          <td>{{data.PODate}}</td>
          <td>{{data.RVNo}}</td>
          <td>{{data.RVDate}}</td>
          <td>{{data.Supplier}}</td>
          <td>{{data.Purpose}}</td>
          <td>{{data.users[0].FullName}}</td>
          <td>
            <i class="material-icons" v-if="data.Status=='0'">thumb_up</i>
            <i class="material-icons decliner" v-else-if="data.Status=='1'">close</i>
            <i class="material-icons" v-else>access_time</i>
          </td>
          <td>
            <a :href="'/po-full-preview/'+data.PONo">
              <i class="material-icons darker-blue">remove_red_eye</i>
            </a>
          </td>
        </tr>
      </table>
      <div class="paginate-container">
        <ul class="pagination">
          <li v-if="pagination.current_page > 1">
            <a href="#" @click.prevent="changepage(pagination.current_page - 1)"><i class="material-icons">keyboard_arrow_left</i></a>
          </li>
          <li v-for="page in pagesNumber" v-bind:class="[ page == isActive ? 'active':'']">
            <a href="#" @click.prevent="changepage(page)">{{page}}</a>
          </li>
          <li v-if="pagination.current_page < pagination.last_page">
            <a href="#" @click.prevent="changepage(pagination.current_page+1)"><i class="material-icons">keyboard_arrow_right</i></a>
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
         ponumsearch:'',
         POindexData:[],
         pagination:[],
         offset:4,
       }
     },
    // props: [],
    methods: {
      FetchAndSearch(page)
      {
        var vm=this;
        axios.get(`/po-index-fetch-and-search?PONo=`+this.ponumsearch+`&page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'POindexData',response.data.data);
          Vue.set(vm.$data,'pagination',response.data);
        });
      },
      changepage(next){
      this.pagination.current_page = next;
      this.FetchAndSearch(next);
      },
    },
    created () {
      this.FetchAndSearch(1);
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
