<template lang="html">
<div class="history-vue-container">
  <div class="top-history-setting">
    <div class="title-history">
      History
    </div>
  </div>
  <div class="searchbar-month-history">
    <div class="searchbox-for-admin-warehouse" v-if="user.Role==4||user.Role==3||user.Role==1">
      <select class="history-choices" v-model="Selected" @change="ChangeTheActive()">
        <option value="0"> MIRS</option>
        <option value="1"> MCT</option>
        <option value="2"> MRT</option>
        <option value="3"> MR</option>
        <option value="4"> RV</option>
        <option value="5"> RR</option>
      </select>
      <select v-model="searchID" v-on:change="NewNameSelected()">
        <option :value="null">{{user.FullName}}</option>
        <option :value="data.id" v-for="data in activeuser">{{data.FullName}}</option>
      </select>
    </div>
    <div v-else>
      <select class="history-choices" v-model="Selected" @change="ChangeTheActive()">
        <option value="0"> MIRS</option>
        <option value="1"> MCT</option>
        <option value="2"> MRT</option>
        <option value="3"> MR</option>
        <option value="4"> RV</option>
        <option value="5"> RR</option>
      </select>
    </div>
    <span class="monthsearch-history">
      <date-picker :date="MyMonth" :option="option" :limit="limit"></date-picker>
      <button type="button" v-on:click="[mirsbtn==true?searchMIRS():'',mctbtn==true?searchMCT():'',rrbtn==true?searchRR():'',mrtbtn==true?searchMRT():'',mrbtn==true?searchMR():'',rvbtn==true?searchRV():'']" name="button">
        <i class="material-icons">search</i>
      </button>
    </span>
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
          <i v-if="mirs.Status==0" class="material-icons">thumb_up</i>
          <i v-else-if="mirs.Status==1" class="material-icons decliner">close</i>
          <i v-else class="material-icons">access_time</i>
        </td>
        <td><a v-bind:href="'/previewFullMIRS/'+mirs.MIRSNo"><i class="material-icons">remove_red_eye</i></a></td>
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
        <td> <h3 class="reversed-marking" v-if="mct.IsRollBack==0"></h3>{{mct.MCTNo}}</td>
        <td>{{mct.MCTDate}}</td>
        <td>{{mct.AddressTo}}</td>
        <td>{{mct.Particulars}}</td>
        <td>
          <i v-if="mct.Status=='0'" class="material-icons">thumb_up</i>
          <i v-else-if="mct.Status=='1'" class="material-icons decliner">close</i>
          <i v-else class="material-icons">access_time</i>
        </td>
        <td><a :href="'/preview-mct-page-only/'+mct.MCTNo"><i class="material-icons">remove_red_eye</i></a></td>
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
        <td><h3 class="reversed-marking" v-if="mrt.IsRollBack==0"></h3>{{mrt.MRTNo}}</td>
        <td>{{mrt.ReturnDate}}</td>
        <td>{{mrt.Particulars}}</td>
        <td>{{mrt.AddressTo}}</td>
        <td>
          <i class="material-icons decliner" v-if="mrt.Status=='1'">close</i>
          <i class="material-icons" v-else-if="mrt.Status=='0'">thumb_up</i>
          <i class="material-icons color-blue" v-else>access_time</i>
        </td>
        <td><a :href="'/mrt-preview-page/'+mrt.MRTNo"><i class="material-icons">remove_red_eye</i></a></td>
      </tr>
    </table>
    <table v-if="mrbtn==true">
      <tr>
        <th>MRNo</th>
        <th>Supplier</th>
        <th>Status</th>
        <th>Show</th>
      </tr>
      <tr v-for="mr in mrResults">
        <td>{{mr.MRNo}}</td>
        <td>{{mr.Supplier}}</td>
        <td>
          <i v-if="mr.Status=='1'" class="material-icons decliner">close</i>
          <i v-else-if="mr.Status=='0'" class="material-icons">thumb_up</i>
          <i v-else class="material-icons">access_time</i>
        </td>
        <td><a :href="'full-preview-MR/'+mr.MRNo"><i class="material-icons">remove_red_eye</i></a></td>
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
          <i class="material-icons" v-if="rv.Status=='0'">thumb_up</i>
          <i class="material-icons decliner" v-else-if="rv.Status=='1'">close</i>
          <i class="material-icons" v-else>access_time</i>
        </td>
        <td><a :href="'/RVfullview/'+rv.RVNo"><i class="material-icons">remove_red_eye</i></a></td>
      </tr>
    </table>
    <table v-if="rrbtn==true">
      <tr>
        <th>RR No</th>
        <th>RR Date</th>
        <th>RV No</th>
        <th>Status</th>
        <th>Show</th>
      </tr>
      <tr v-for="rr in rrResults">
        <td><h3 class="reversed-marking" v-if="rr.IsRollBack==0"></h3>{{rr.RRNo}}</td>
        <td>{{rr.RRDate}}</td>
        <td>{{rr.RVNo}}</td>
        <td>
          <i class="material-icons" v-if="rr.Status=='0'">thumb_up</i>
          <i class="material-icons decliner" v-else-if="rr.Status=='1'">close</i>
          <i class="material-icons" v-else>access_time</i>
        </td>
        <td><a :href="'/RR-fullpreview/'+rr.RRNo"><i class="material-icons">remove_red_eye</i></a></td>
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
  import 'vue2-toast/lib/toast.css';
  import Toast from 'vue2-toast';
  import myDatepicker from 'vue-datepicker';
  Vue.use(Toast);
  export default {
    props:['user','activeuser'],
     data () {
        return{

          searchID:null,
          mirsResults:[],
          mctResults:[],
          mrtResults:[],
          mrResults:[],
          rvResults:[],
          rrResults:[],
          mirsbtn:true,
          mctbtn:false,
          mrbtn:false,
          mrtbtn:false,
          rvbtn:false,
          rrbtn:false,
          pagination:[],
          offset:4,
          Selected:0,
          MyMonth: {
            time: ''
          },
          option: {
            type: 'day',
            week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
            month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            format: 'YYYY-MM',
            placeholder: 'Month',
            inputStyle: {
              'display': 'inline-block',
              'padding': '6px',
              'line-height': '22px',
              'font-size': '16px',
              'border': '2px solid #fff',
              'box-shadow': '0 1px 3px 0 rgba(0, 0, 0, 0.2)',
              'border-radius': '2px',
              'color': '#5F5F5F'
          },
          color: {
            header: '#ccc',
            headerText: '#f00'
          },
          buttons: {
            ok: 'Ok',
            cancel: 'Cancel'
          },
          overlayOpacity: 0.5, // 0.5 as default
          dismissible: true // as true as default
        },
        timeoption: {
          type: 'min',
          week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
          month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          format: 'YYYY-MM-DD HH:mm'
        },
        multiOption: {
          type: 'multi-day',
          week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
          month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
          format:"YYYY-MM-DD HH:mm"
        },
        limit: [{
          type: 'weekday',
          available: [1, 2, 3, 4, 5]
        },
        {
          type: 'fromto',
          from: '2016-02-01',
          to: '2016-02-20'
        }]
        }
      },

      components: {
        'date-picker': myDatepicker
      },
     methods: {
       searchMIRS(page)
       {
         this.$loading('Loading');
         if (this.searchID==null)
         {
           var IDofuser=this.user.id;
         }else
         {
           var IDofuser=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mirs-history?PreparedById=`+IDofuser+`&YearMonth=`+this.MyMonth.time+`&page=`+page,{
           YearMonth:this.MyMonth.time,
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mirsResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
           vm.$loading.close();
         });
       },
       searchMCT(page)
       {
         this.$loading('Loading');
         if (this.searchID==null)
         {
           var receiverId=this.user.id;
         }else
         {
           var receiverId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mct-history?ReceivedById=`+receiverId+`&YearMonth=`+this.MyMonth.time+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mctResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
           vm.$loading.close();
         });
       },
       searchMRT(page)
       {
         this.$loading('Loading');
         if (this.searchID==null)
         {
           var returnerId=this.user.id;
         }else
         {
           var returnerId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mrt-history?ReturnedById=`+returnerId+`&YearMonth=`+this.MyMonth.time+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mrtResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
           vm.$loading.close();
         });
       },
       searchMR(page)
       {
         this.$loading('Loading');
         if (this.searchID==null)
         {
           var UserId=this.user.id;
         }else
         {
           var UserId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-mr-history?ReceivedById=`+UserId+`&YearMonth=`+this.MyMonth.time+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'mrResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
           vm.$loading.close();
         });
       },
       searchRV(page)
       {
         this.$loading('Loading');
         if (this.searchID==null)
         {
           var RequisitionerId=this.user.id;
         }else
         {
           var RequisitionerId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-rv-history?Requisitioner=`+RequisitionerId+`&YearMonth=`+this.MyMonth.time+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'rvResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
           vm.$loading.close();
         });
       },
       searchRR(page)
       {
         this.$loading('Loading');
         if (this.searchID==null)
         {
           var ReceiverId=this.user.id;
         }else
         {
           var ReceiverId=this.searchID;
         }
         var vm=this;
         axios.get(`/search-my-rr-history?ReceivedById=`+ReceiverId+`&YearMonth=`+this.MyMonth.time+`&page=`+page,{
         }).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'rrResults',response.data.data);
           Vue.set(vm.$data,'pagination',response.data);
           vm.$loading.close();
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
         }else if (this.rrbtn==true)
         {
           this.searchRR(next);
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
           this.searchMRT(1);
         }else if (this.mrbtn==true)
         {
           this.searchMR(1);
         }else if (this.rvbtn==true)
         {
           this.searchRV(1);
         }else if (this.rrbtn==true)
         {
           this.searchRR(1);
         }
       },
       ChangeTheActive()
       {
         if (this.Selected==0)
         {
           this.mirsbtn=true;
           this.mctbtn=false;
           this.mrtbtn=false;
           this.mrbtn=false;
           this.rvbtn=false;
           this.rrbtn=false;
           this.searchMIRS(1);
         }else if (this.Selected=='1')
         {
           this.mirsbtn=false;
           this.mctbtn=true;
           this.mrtbtn=false;
           this.mrbtn=false;
           this.rvbtn=false;
           this.rrbtn=false;
           this.searchMCT(1);
         }else if (this.Selected=='2')
         {
           this.mirsbtn=false;
           this.mctbtn=false;
           this.mrtbtn=true;
           this.mrbtn=false;
           this.rvbtn=false;
           this.rrbtn=false;
           this.searchMRT(1);
         }else if (this.Selected=='3')
         {
           this.mirsbtn=false;
           this.mctbtn=false;
           this.mrtbtn=false;
           this.mrbtn=true;
           this.rvbtn=false;
           this.rrbtn=false;
           this.searchMR(1);
         }else if (this.Selected=='4')
         {
           this.mirsbtn=false;
           this.mctbtn=false;
           this.mrtbtn=false;
           this.mrbtn=false;
           this.rvbtn=true;
           this.rrbtn=false;
           this.searchRV(1);
         }else if (this.Selected=='5')
         {
           this.mirsbtn=false;
           this.mctbtn=false;
           this.mrtbtn=false;
           this.mrbtn=false;
           this.rvbtn=false;
           this.rrbtn=true;
           this.searchRR(1);
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
