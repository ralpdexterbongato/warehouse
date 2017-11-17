<template lang="html">
<div class="history-vue-container">
  <div class="top-history-setting">
    <div class="title-history">
      History
    </div>
    <ul>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mirsbtn==true?'active':'']" v-on:click="mirsbtn=true,mctbtn=false,mrtbtn=false,mrbtn=false,rvbtn=false,searchMIRS(1)">MIRS</button></li>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mctbtn==true?'active':'']" v-on:click="mirsbtn=false,mctbtn=true,mrtbtn=false,mrbtn=false,rvbtn=false,searchMCT(1)">MCT</button></li>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mrtbtn==true?'active':'']"v-on:click="mirsbtn=false,mctbtn=false,mrtbtn=true,mrbtn=false,rvbtn=false,searchMRT(1)" >MRT</button></li>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mrbtn==true?'active':'']" v-on:click="mirsbtn=false,mctbtn=false,mrtbtn=false,mrbtn=true,rvbtn=false,searchMR(1)">MR</button></li>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[rvbtn==true?'active':'']" v-on:click="mirsbtn=false,mctbtn=false,mrtbtn=false,mrbtn=false,rvbtn=true,searchRV(1)">RV</button></li>
    </ul>
  </div>
  <div class="searchbar-month-history">
    <div class="searchbox-for-admin-warehouse" v-if="user.Role==4||user.Role==3||user.Role==1">
      Histories of
      <select v-model="searchID" v-on:change="NewNameSelected()">
        <option :value="null">{{user.FullName}}</option>
        <option :value="data.id" v-for="data in activeuser">{{data.FullName}}</option>
      </select>
    </div>
    <div v-else>
      <!-- empty -->
    </div>
    <span class="monthsearch-history"><h1>Sort by date</h1><input type="text" placeholder="y y y y - m m" v-model="searchmonth" v-on:keypress.enter.prevent="[mirsbtn==true?searchMIRS():'',mctbtn==true?searchMCT():'',mrtbtn==true?searchMRT():'',mrbtn==true?searchMR():'',rvbtn==true?searchRV():'']"><button type="button" v-on:click="[mirsbtn==true?searchMIRS():'',mctbtn==true?searchMCT():'',mrtbtn==true?searchMRT():'',mrbtn==true?searchMR():'',rvbtn==true?searchRV():'']" name="button"><i class="fa fa-search"></i></button></span>
  </div>
  <div class="table-history-container">
    <table v-if="mirsbtn==true">
      <tr>
        <th class="left-part">MIRS No</th>
        <th>Date</th>
        <th>Purpose</th>
        <th>Status</th>
        <th class="right-part">Show</th>
      </tr>
      <tr v-if="mirsResults!=null" v-for="mirs in mirsResults">
        <td>{{mirs.MIRSNo}}</td>
        <td>{{mirs.MIRSDate}}</td>
        <td>{{mirs.Purpose}}</td>
        <td>
          <i v-if="mirs.Status==0" class="fa fa-thumbs-up"></i>
          <i v-else-if="mirs.Status==1" class="fa fa-times decliner"></i>
          <i v-else class="fa fa-clock-o"></i>
        </td>
        <td><a v-bind:href="'/previewFullMIRS/'+mirs.MIRSNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
    <table v-if="mctbtn==true">
      <tr>
        <th class="left-part">MCT No.</th>
        <th>MCTDate</th>
        <th>Addressed to</th>
        <th>Particular</th>
        <th>Status</th>
        <th class="right-part">Show</th>
      </tr>
      <tr v-if="mctResults!=null" v-for="mct in mctResults">
        <td>{{mct.MCTNo}}</td>
        <td>{{mct.MCTDate}}</td>
        <td>{{mct.AddressTo}}</td>
        <td>{{mct.Particulars}}</td>
        <td>
          <i v-if="mct.Status=='0'" class="fa fa-thumbs-up"></i>
          <i v-else-if="mct.Status=='1'" class="fa fa-times decliner"></i>
          <i v-else class="fa fa-clock-o"></i>
        </td>
        <td><a :href="'/preview-mct-page-only/'+mct.MCTNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
    <table v-if="mrtbtn==true">
      <tr>
        <th>MRT No.</th>
        <th>Return Date</th>
        <th>Particulars</th>
        <th>Address To</th>
        <th>Status</th>
        <th>Show</th>
      </tr>
      <tr v-for="mrt in mrtResults">
        <td>{{mrt.MRTNo}}</td>
        <td>{{mrt.ReturnDate}}</td>
        <td>{{mrt.Particulars}}</td>
        <td>{{mrt.AddressTo}}</td>
        <td>
          <i class="fa fa-times decliner" v-if="mrt.Status=='1'"></i>
          <i class="fa fa-thumbs-up" v-else-if="mrt.Status=='0'"></i>
          <i class="fa fa-clock-o color-blue" v-else></i>
        </td>
        <td><a :href="'/mrt-preview-page/'+mrt.MRTNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
    <table v-if="mrbtn==true">
      <tr>
        <th>MRNo</th>
        <th>Supplier</th>
        <th>Received by</th>
        <th>Recommended by</th>
        <th>Approved by</th>
        <th>Status</th>
        <th>Show</th>
      </tr>
      <tr v-for="mr in mrResults">
        <td>{{mr.MRNo}}</td>
        <td>{{mr.Supplier}}</td>
        <td>{{mr.Receivedby}}
          <i v-if="mr.ReceivedbySignature!=null" class="fa fa-check"></i>
          <i v-else-if="mr.IfDeclined==mr.Receivedby" class="fa fa-times decliner"></i>
        </td>
        <td>
          {{mr.Recommendedby}}
          <i v-if="mr.RecommendedbySignature!=null" class="fa fa-check"></i>
          <i v-else-if="mr.IfDeclined==mr.Recommendedby" class="fa fa-times decliner"></i>
        </td>
        <td v-if="mr.ApprovalReplacerSignature!=null">
          {{mr.ApprovalReplacerFullName}}
          <i class="fa fa-check"></i>
        </td>
        <td v-else>
          {{mr.GeneralManager}}
          <i v-if="mr.GeneralManagerSignature!=null" class="fa fa-check"></i>
          <i v-else-if="mr.IfDeclined==mr.GeneralManager" class="fa fa-times decliner"></i>
        </td>
        <td>
          <i v-if="mr.IfDeclined!=null" class="fa fa-times decliner"></i>
          <i v-else-if="mr.ReceivedbySignature!=null&&mr.RecommendedbySignature!=null&&mr.GeneralManagerSignature!=null" class="fa fa-thumbs-up"></i>
          <i v-else-if="mr.ReceivedbySignature!=null&&mr.RecommendedbySignature!=null&&mr.ApprovalReplacerSignature!=null" class="fa fa-thumbs-up"></i>
          <i v-else class="fa fa-clock-o"></i>
        </td>
        <td><a :href="'full-preview-MR/'+mr.MRNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
    <table v-if="rvbtn==true">
      <tr>
        <th>RVNo</th>
        <th>RVDate</th>
        <th>Purpose</th>
        <th>Status</th>
        <th>Show</th>
      </tr>
      <tr v-for="rv in rvResults">
        <td>{{rv.RVNo}}</td>
        <td>{{rv.RVDate}}</td>
        <td>{{rv.Purpose}}</td>
        <td>
          <i class="fa fa-thumbs-up" v-if="rv.Status=='0'"></i>
          <i class="fa fa-times decliner" v-else-if="rv.Status=='1'"></i>
          <i class="fa fa-clock-o" v-else></i>
        </td>
        <td><a :href="'/RVfullview/'+rv.RVNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
    <div class="paginate-container">
      <ul class="pagination">
        <li v-if="pagination.current_page > 1">
          <a href="#" @click.prevent="changepage(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
        </li>
        <li v-for="page in pagesNumber" v-bind:class="[ page == Activepage ? 'active':'']">
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
    props:['user','activeuser'],
     data () {
        return{
          searchmonth:'',
          searchID:null,
          mirsResults:[],
          mctResults:[],
          mrtResults:[],
          mrResults:[],
          rvResults:[],
          mirsbtn:true,
          mctbtn:false,
          mrbtn:false,
          mrtbtn:false,
          rvbtn:false,
          pagination:[],
          offset:4,
        }
      },
     methods: {
       searchMIRS(page)
       {
         if (this.searchID==null)
         {
           var IDofuser=this.user.id;
         }else
         {
           var IDofuser=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mirs-history?PreparedById=`+IDofuser+`&YearMonth=`+this.searchmonth+`&page=`+page,{
           YearMonth:this.searchmonth,
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mirsResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
         });
       },
       searchMCT(page)
       {
         if (this.searchID==null)
         {
           var receiverId=this.user.id;
         }else
         {
           var receiverId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mct-history?ReceivedById=`+receiverId+`&YearMonth=`+this.searchmonth+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mctResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
         });
       },
       searchMRT(page)
       {
         if (this.searchID==null)
         {
           var returnerId=this.user.id;
         }else
         {
           var returnerId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mrt-history?ReturnedById=`+returnerId+`&YearMonth=`+this.searchmonth+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mrtResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
         });
       },
       searchMR(page)
       {
         if (this.searchID==null)
         {
           var fullname=this.user.FullName;
         }else
         {
           var fullname=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mr-history?Receivedby=`+fullname+`&YearMonth=`+this.searchmonth+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mrResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
         });
       },
       searchRV(page)
       {
         if (this.searchID==null)
         {
           var RequisitionerId=this.user.id;
         }else
         {
           var RequisitionerId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-rv-history?Requisitioner=`+RequisitionerId+`&YearMonth=`+this.searchmonth+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'rvResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
         });
       },
       changepage(next)
       {
         this.pagination.current_page = next;
         if (this.mirsbtn==true)
         {
           this.searchMIRS(next);
         }else if (this.mctbtn==true)
         {
           this.searchMCT(next);
         }else if (this.mrtbtn==true)
         {
           this.searchMRT(next)
         }else if (this.mrbtn==true)
         {
           this.searchMR(next);
         }else if (this.rvbtn==true)
         {
           this.searchRV(next);
         }
       },
       NewNameSelected()
       {
         if (this.mirsbtn==true)
         {
           this.searchMIRS(1);
         }else if (this.mctbtn==true)
         {
           this.searchMCT(1);
         }else if (this.mrtbtn==true)
         {
           this.searchMRT(1)
         }else if (this.mrbtn==true)
         {
           this.searchMR(1);
         }else if (this.rvbtn==true)
         {
           this.searchRV(1);
         }
       }

     },
     created ()
     {
       var page='1';
        this.searchMIRS(page);
     },
     computed:{
       Activepage:function(){
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
