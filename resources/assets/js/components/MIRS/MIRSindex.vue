<template lang="html">
<div class="mirs-index-vue">
  <div class="top-wrap-index-mirs">
    <div class="Title-MIRS-Index">
    <i class="material-icons">content_paste</i> Materials Issuance Requisition Slip index
    </div>
    <div class="search-mirs-container">
      <input type="text" autocomplete="off" v-on:keyup="SearchAndFetch()" v-model="SearchInput"  placeholder="Enter MIRS #">
    </div>
  </div>
  <div class="table-mirs-list">
    <table>
      <tr>
        <th>MIRSNo</th>
        <th>Purpose</th>
        <th>Prepared by</th>
        <th>Recommended by</th>
        <th>Approved by</th>
        <th>Date</th>
        <th>Status</th>
        <th>View</th>
      </tr>
      <tr v-for="result in SearchResult" v-if="result.users[0]!=null">
          <td>{{result.MIRSNo}}</td>
          <td>{{result.Purpose}}</td>
          <td>{{result.users[0].FullName}}
            <i class="material-icons" v-if="(result.users[0].pivot.Signature=='0')">check</i> <!-- zero means true -->
            <i class="material-icons decliner" v-else-if="result.users[0].pivot.Signature=='1'">close</i>  <!--one is false -->
          </td>
          <td>{{result.users[1].FullName}}
             <i class="material-icons" v-if="((result.users[1].pivot.Signature=='0')||((result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ManagerReplacer'))||((result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ManagerReplacer')))">check</i> <!-- zero means true -->
             <i class="material-icons decliner" v-else-if="result.users[1].pivot.Signature=='1'">close</i>  <!--one is false -->
          </td>
          <td>{{result.users[2].FullName}}
            <i class="material-icons" v-if="((result.users[2].pivot.Signature=='0')||((result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ApprovalReplacer'))||((result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ApprovalReplacer')))">check</i> <!-- zero means true -->
            <i class="material-icons decliner" v-else-if="result.users[2].pivot.Signature=='1'">close</i>  <!--one is false -->
          </td>
          <td>{{result.MIRSDate}}</td>
            <td>
              <i class="material-icons" v-if="((result.users[0].pivot.Signature=='0')&&((result.users[1].pivot.Signature=='0')||(result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ManagerReplacer')||(result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ManagerReplacer'))&&((result.users[2].pivot.Signature=='0')||(result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ApprovalReplacer')||(result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ApprovalReplacer')))">thumb_up</i>
              <i class="material-icons decliner" v-else-if="((result.users[0].pivot.Signature=='1')||(result.users[1].pivot.Signature=='1')||(result.users[2].pivot.Signature=='1'))">close</i>
              <i class="material-icons" v-else>access_time</i>
            </td>
          <td class="fullmirsClick"><a :href="'/previewFullMIRS/'+result.MIRSNo"><i class="material-icons">remove_red_eye</i></a></td>
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
         SearchInput:'',
         SearchResult:[],
         pagination:[],
         offset:4,
       }
     },
    // props: [],
    methods: {
      SearchAndFetch(page)
      {
        var vm=this;
        axios.get(`/findmirs-and-fetch?MIRSNo=`+this.SearchInput+`&page=`+page).then(function(response)
        {
          Vue.set(vm.$data,'SearchResult',response.data.data);
          Vue.set(vm.$data,'pagination',response.data);
        });
      },
      changepage(next){
        this.pagination.current_page = next;
        this.SearchAndFetch(next);
      },
    },
    mounted () {
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
