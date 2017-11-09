<template lang="html">
<div class="mirs-index-vue">
  <div class="top-wrap-index-mirs">
    <div class="Title-MIRS-Index">
    <i class="fa fa-th-large"></i>  Materials Issuance Requisition Slip index
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
        <th>Action</th>
      </tr>
      <tr v-for="result in SearchResult" v-if="result.users[0]!=null">
          <td>{{result.MIRSNo}}</td>
          <td>{{result.Purpose}}</td>
          <td>{{result.users[0].FullName}}
             <i class="fa fa-check"></i>
          </td>
          <td>{{result.users[1].FullName}}
             <i class="fa fa-check" v-if="((result.users[1].pivot.Signature=='0')||((result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ManagerReplacer'))||((result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ManagerReplacer')))"></i> <!-- zero means true -->
             <i class="fa fa-times decliner" v-else-if="result.users[1].pivot.Signature=='1'"></i>  <!--one is false -->
          </td>
          <td>{{result.users[2].FullName}}
            <i class="fa fa-check" v-if="((result.users[2].pivot.Signature=='0')||((result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ApprovalReplacer'))||((result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ApprovalReplacer')))"></i> <!-- zero means true -->
            <i class="fa fa-times decliner" v-else-if="result.users[2].pivot.Signature=='1'"></i>  <!--one is false -->

             <!-- <i class="fa fa-check" v-if="result.ApproveSignature!=null||result.ApprovalReplacerSignature!=null"></i>
             <i class="fa fa-times decliner" v-if="result.IfDeclined==result.Approvedby"></i> -->
          </td>
          <td>{{result.MIRSDate}}</td>
            <td>
              <i class="fa fa-thumbs-up" v-if="((result.users[0].pivot.Signature=='0')&&((result.users[1].pivot.Signature=='0')||(result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ManagerReplacer')||(result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ManagerReplacer'))&&((result.users[2].pivot.Signature=='0')||(result.users[3]!=null)&&(result.users[3].pivot.Signature=='0')&&(result.users[3].pivot.SignatureType=='ApprovalReplacer')||(result.users[4]!=null)&&(result.users[4].pivot.Signature=='0')&&(result.users[4].pivot.SignatureType=='ApprovalReplacer')))"></i>
              <i class="fa fa-times decliner" v-else-if="((result.users[0].pivot.Signature=='1')||(result.users[1].pivot.Signature=='1')||(result.users[2].pivot.Signature=='1'))"></i>
              <i class="fa fa-clock-o" v-else></i>
            </td>
          <td class="fullmirsClick"><a :href="'/previewFullMIRS/'+result.MIRSNo"><i class="fa fa-eye"></i></a></td>
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
          console.log(response);
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
