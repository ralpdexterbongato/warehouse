<template lang="html">
<div class="history-vue-container">
  <div class="top-history-setting">
    <div class="title-history">
      My History
    </div>
    <ul>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mirsbtn==true?'active':'']" v-on:click="mirsbtn=true,mctbtn=false,mrtbtn=false,mrbtn=false,searchMIRS()">MIRS</button></li>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mctbtn==true?'active':'']" v-on:click="mirsbtn=false,mctbtn=true,mrtbtn=false,mrbtn=false,searchMCT()">MCT</button></li>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mrtbtn==true?'active':'']"v-on:click="mirsbtn=false,mctbtn=false,mrtbtn=true,mrbtn=false" >MRT</button></li>
      <li><button type="button" class="bttn-fill bttn-sm bttn-primary":class="[mrbtn==true?'active':'']" v-on:click="mirsbtn=false,mctbtn=false,mrtbtn=false,mrbtn=true">MR</button></li>
    </ul>
  </div>
  <div class="searchbar-month-history">
    <span>Month: <input type="text" placeholder="yyyy-mm" v-model="searchmonth" v-on:keypress.enter.prevent="[mirsbtn==true?searchMIRS():'',mctbtn==true?searchMCT():'']"><button type="button" v-on:click="[mirsbtn==true?searchMIRS():'',mctbtn==true?searchMCT():'']" name="button"><i class="fa fa-search"></i></button></span>
  </div>
  <div class="table-history-container">
    <table v-if="mirsbtn==true">
      <tr>
        <th class="left-part">MIRS No</th>
        <th>Date</th>
        <th>Requisitioner</th>
        <th>Recommended by</th>
        <th>Approved by</th>
        <th>Purpose</th>
        <th>Status</th>
        <th class="right-part">Show</th>
      </tr>
      <tr v-if="mirsResults!=null" v-for="mirs in mirsResults">
        <td>{{mirs.MIRSNo}}</td>
        <td>{{mirs.MIRSDate}}</td>
        <td>
          {{mirs.Preparedby}}
          <i class="fa fa-check"></i>
          <i class="fa fa-times decliner" v-if="mirs.Preparedby==mirs.IfDeclined"></i>
        </td>
        <td>
          {{mirs.Recommendedby}}
          <i class="fa fa-check" v-if="mirs.RecommendSignature!=null"></i>
          <i class="fa fa-times decliner" v-if="mirs.Recommendedby==mirs.IfDeclined"></i>
        </td>
        <td>
          {{mirs.Approvedby}}
          <i class="fa fa-check" v-if="mirs.ApproveSignature!=null"></i>
          <i class="fa fa-times decliner" v-if="mirs.Approvedby==mirs.IfDeclined"></i>
        </td>
        <td>{{mirs.Purpose}}</td>
        <td>
          <i v-if="mirs.RecommendSignature!=null&&mirs.ApproveSignature!=null" class="fa fa-thumbs-up"></i>
          <i v-else-if="mirs.IfDeclined!=null" class="fa fa-times decliner"></i>
          <i v-else class="fa fa-clock-o"></i>
        </td>
        <td><a v-bind:href="'/previewFullMIRS/'+mirs.MIRSNo"><i class="fa fa-eye"></i></a></td>
      </tr>
    </table>
    <table v-if="mctbtn==true">
      <tr>
        <th class="left-part">MCT No.</th>
        <th>MCTDate</th>
        <th>Receiver</th>
        <th>Addressed to</th>
        <th>Particular</th>
        <th class="right-part">Show</th>
      </tr>
      <tr v-if="mctResults!=null" v-for="mct in mctResults">
        <td>{{mct.MCTNo}}</td>
        <td>{{mct.MCTDate}}</td>
        <td>{{mct.Receivedby}}</td>
        <td>{{mct.AddressTo}}</td>
        <td>{{mct.Particulars}}</td>
        <td><i class="fa fa-eye"></i></td>
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
    props:['user'],
     data () {
        return{
          searchmonth:'',
          searchname:'',
          mirsResults:[],
          mctResults:[],
          mirsbtn:true,
          mctbtn:false,
          mrbtn:false,
          mrtbtn:false,
          pagination:[],
          offset:4,
        }
      },
     methods: {
       searchMIRS(page)
       {
         if (this.searchname=='')
         {
           var fullname=this.user.Fname+'%20'+this.user.Lname;
         }else
         {
           var fullname=this.searchname;
         }
         var vm=this;
         axios.get(`/search-my-mirs-history?Preparedby=`+fullname+`&YearMonth=`+this.searchmonth+`&page=`+page,{
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
         if (this.searchname=='')
         {
           var fullname=this.user.Fname+'%20'+this.user.Lname;
         }else
         {
           var fullname=this.searchname;
         }
         var vm=this;
         axios.get(`/search-my-mct-history?Receivedby=`+fullname+`&YearMonth=`+this.searchmonth+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mctResults',response.data.data);
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
         }
       },

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
