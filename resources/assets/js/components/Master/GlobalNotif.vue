<template lang="html">
  <li class="dropping-parent" v-on:click="dropIsActive=!dropIsActive,[Loaded==false?fetchData(1):'']">
    <h1 class="waves-effect waves-light">
      <i class="material-icons">notifications</i>
    </h1>
    <h2 class="number-of-unread z-depth-1" v-if="AddedCounts!='0'">
      <span v-if="AddedCounts < 10">{{AddedCounts}}</span>
      <span v-else>9+</span>
    </h2>
    <div class="notification-drop z-depth-1" v-on:click="dropIsActive=!dropIsActive" :class="[dropIsActive == true?'active':'']">
      <div class="notification-header">
        <h2> Notifications</h2>
      </div>
      <div class="notification-navs">
        <h2 :class="[MIRSBtn==true?'active':'']" v-on:click="MRTBtn=false,MCTBtn=false,MIRSBtn=true,MRBtn=false,RVBtn=false,POBtn=false,RRBtn=false">MIRS <p class="notification-dot-mark" v-if="NotificationCounts.unreadMIRS!=0"></p></h2>
        <h2 :class="[RVBtn==true?'active':'']"  v-on:click="MRTBtn=false,MCTBtn=false,RVBtn=true,MRBtn=false,MIRSBtn=false,POBtn=false,RRBtn=false,[RVNotifs[0]==null?fetchRV(RVCurrentPage):'']">RV <p class="notification-dot-mark" v-if="NotificationCounts.unreadRV!=0"></p></h2>
        <h2 v-if="user.Role == '3' || user.Role == '4'" :class="[POBtn==true?'active':'']"  v-on:click="MRTBtn=false,MCTBtn=false,POBtn=true,MRBtn=false,MIRSBtn=false,RVBtn=false,RRBtn=false,[PONotifs[0]==null?fetchPO(POCurrentPage):'']">PO <p class="notification-dot-mark" v-if="NotificationCounts.unreadPO!=0"></p></h2>
        <h2 v-if="user.Role == '4' || user.Role == '3'" :class="[RRBtn==true?'active':'']"  v-on:click="MRTBtn=false,MCTBtn=false,RRBtn=true,MRBtn=false,MIRSBtn=false,RVBtn=false,POBtn=false,[RRNotifs[0]==null?fetchRR(RRCurrentPage):'']">RR <p class="notification-dot-mark" v-if="NotificationCounts.unreadRR!=0"></p></h2>
        <h2 v-if="user.Role == '4' || user.Role == '3'" :class="[MRBtn==true?'active':'']"  v-on:click="MRTBtn=false,MCTBtn=false,MRBtn=true,RRBtn=false,MIRSBtn=false,RVBtn=false,POBtn=false,[MRNotifs[0]==null?fetchMR(MRCurrentPage):'']">MR <p class="notification-dot-mark" v-if="NotificationCounts.unreadMR!=0"></p></h2>
        <h2 v-if="user.Role == '4' || user.Role == '3'" :class="[MCTBtn==true?'active':'']"  v-on:click="MRTBtn=false,MCTBtn=true,MRBtn=false,RRBtn=false,MIRSBtn=false,RVBtn=false,POBtn=false,[MCTNotifs[0]==null?fetchMCT(MCTCurrentPage):'']">MCT <p class="notification-dot-mark" v-if="NotificationCounts.unreadMCT!=0"></p></h2>
        <h2 v-if="user.Role == '4' || user.Role == '3'" :class="[MRTBtn==true?'active':'']"  v-on:click="MRTBtn=true,MCTBtn=false,MRBtn=false,RRBtn=false,MIRSBtn=false,RVBtn=false,POBtn=false,[MRTNotifs[0]==null?fetchMRT(MRTCurrentPage):'']">MRT <p class="notification-dot-mark" v-if="NotificationCounts.unreadMRT!=0"></p></h2>
      </div>
      <div class="notification-line-container">
        <!-- mirs start -->
        <a :href="'/previewFullMIRS/'+ mirs.MIRSNo" v-if="MIRSBtn==true" v-for="mirs in MIRSNotifs">
          <div class="notification-drop-line" :class="[mirs.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>MIRS : {{mirs.MIRSNo}}</h5>
              <p>
                materials issueance requisition slip has been
                <span v-if="mirs.Status==0">approved</span>
                <span v-if="mirs.Status==1">declined</span>
              </p><br>
              <div class="time-notified">
                <i v-if="mirs.Status==0" class="material-icons color-blue">check_circle</i>
                <i v-if="mirs.Status==1" class="material-icons color-red">close</i>{{mirs.notification_date_time}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchData(MIRSCurrentPage+1)" v-if="MIRSCurrentPage!=MIRSLastPage && MIRSNotifs[0]!=null && MIRSBtn==true ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="MIRSNotifs[0]==null && MIRSBtn==true">
          <i class="material-icons">notifications_none</i> empty
        </div>
        <!-- mirs end -->
        <!-- rv start -->
        <a :href="'/RVfullview/'+ rv.RVNo" v-if="RVBtn==true" v-for="rv in RVNotifs">
          <div class="notification-drop-line" :class="[rv.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>RV : {{rv.RVNo}}</h5>
              <p>
                requisition voucher has been
                <span v-if="rv.Status==0">approved</span>
                <span v-if="rv.Status==1">declined</span>
              </p><br>
              <div class="time-notified">
                <i v-if="rv.Status==0" class="material-icons color-blue">check_circle</i>
                <i v-if="rv.Status==1" class="material-icons color-red">close</i>{{rv.notification_date_time}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchRV(RVCurrentPage+1)" v-if="RVCurrentPage!=RVLastPage && RVNotifs[0]!=null && RVBtn==true ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="RVNotifs[0]==null && RVBtn==true">
          <i class="material-icons">notifications_none</i> empty
        </div>
        <!-- end rv -->
        <!-- start po -->
        <a :href="'/po-full-preview/'+ po.PONo" v-if="POBtn==true" v-for="po in PONotifs">
          <div class="notification-drop-line" :class="[po.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>PO : {{po.PONo}}</h5>
              <p>
                purchase order has been
                <span v-if="po.Status==0">approved</span>
                <span v-if="po.Status==1">declined</span>
              </p><br>
              <div class="time-notified">
                <i v-if="po.Status==0" class="material-icons color-blue">check_circle</i>
                <i v-if="po.Status==1" class="material-icons color-red">close</i>{{po.notification_date_time}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchPO(POCurrentPage+1)" v-if="POCurrentPage!=POLastPage && PONotifs[0]!=null && POBtn==true ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="PONotifs[0]==null && POBtn==true">
          <i class="material-icons">notifications_none</i> empty
        </div>
        <!-- end po -->
        <!-- start rr -->
        <a :href="'/RR-fullpreview/'+rr.RRNo" v-if="RRBtn==true" v-for="rr in RRNotifs">
          <div class="notification-drop-line" :class="[rr.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>RR : {{rr.RRNo}}</h5>
              <p>
                <span v-if="rr.IsRollBack==0">receiving report has been reversed/rollbacked by the administrator</span>
                <span v-if="rr.IsRollBack==1">the administrator undid the reversed/rollbacked data</span>
                <span v-if="rr.Status==0 && rr.IsRollBack==null">receiving report signatures are complete</span>
                <span v-if="rr.Status==1 && rr.IsRollBack==null">receiving report has been declined</span>
              </p><br>
              <div class="time-notified">
                <i v-if="rr.Status==0 && rr.IsRollBack==null" class="material-icons color-blue">check_circle</i>
                <i v-if="rr.Status==1 && rr.IsRollBack==null" class="material-icons color-red">close</i>
                <i v-if="rr.IsRollBack==0" class="material-icons color-red">replay</i>
                <i v-if="rr.IsRollBack==1" class="material-icons color-blue">refresh</i>
                {{rr.notification_date_time}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchRR(RRCurrentPage+1)" v-if="RRCurrentPage!=RRLastPage && RRNotifs[0]!=null && RRBtn==true ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="RRNotifs[0]==null && RRBtn==true">
          <i class="material-icons">notifications_none</i> empty
        </div>
        <!-- end rr -->
        <!-- start mr -->
        <a :href="'/full-preview-MR/'+mr.MRNo" v-if="MRBtn==true" v-for="mr in MRNotifs">
          <div class="notification-drop-line" :class="[mr.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>MR : {{mr.RRNo}}</h5>
              <p>
                memorandum receipt has been
                <span v-if="mr.Status==0">approved</span>
                <span v-if="mr.Status==1">declined</span>
              </p><br>
              <div class="time-notified">
                <i v-if="mr.Status==0" class="material-icons color-blue">check_circle</i>
                <i v-if="mr.Status==1" class="material-icons color-red">close</i>{{mr.notification_date_time}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchMR(MRCurrentPage+1)" v-if="MRCurrentPage!=MRLastPage && MRNotifs[0]!=null && MRBtn==true ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="MRNotifs[0]==null && MRBtn==true">
          <i class="material-icons">notifications_none</i> empty
        </div>
        <!-- end mr -->
        <!-- start mct -->
        <a :href="'/preview-mct-page-only/'+mct.MCTNo" v-if="MCTBtn==true" v-for="mct in MCTNotifs">
          <div class="notification-drop-line" :class="[mct.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>MCT : {{mct.MCTNo}}</h5>
              <p>
                <span v-if="mct.IsRollBack==0">material charge ticket has been reversed/rollbacked by the administrator</span>
                <span v-if="mct.IsRollBack==1">the administrator undid the reversed/rollbacked data</span>
                <span v-if="mct.Status==0 && mct.IsRollBack==null">Materials charge ticket signatures are complete</span>
                <span v-if="mct.Status==1 && mct.IsRollBack==null">Materials charge ticket has been declined</span>
              </p><br>
              <div class="time-notified">
                <i v-if="mct.Status==0 && mct.IsRollBack==null" class="material-icons color-blue">check_circle</i>
                <i v-if="mct.Status==1 && mct.IsRollBack==null" class="material-icons color-red">close</i>
                <i v-if="mct.IsRollBack==0" class="material-icons color-red">replay</i>
                <i v-if="mct.IsRollBack==1" class="material-icons color-blue">refresh</i>
                {{mct.notification_date_time}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchMCT(MCTCurrentPage+1)" v-if="MCTCurrentPage!=MCTLastPage && MCTNotifs[0]!=null && MCTBtn==true ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="MCTNotifs[0]==null && MCTBtn==true">
          <i class="material-icons">notifications_none</i> empty
        </div>
        <!-- end mct -->
        <!-- start mrt -->
        <a :href="'/mrt-preview-page/'+mrt.MRTNo" v-if="MRTBtn==true" v-for="mrt in MRTNotifs">
          <div class="notification-drop-line" :class="[mrt.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>MRT : {{mrt.MRTNo}}</h5>
              <p>
                <span v-if="mrt.IsRollBack==0">materials return ticket has been reversed/rollbacked by the administrator</span>
                <span v-if="mrt.IsRollBack==1">the administrator undid the reversed/rollbacked data</span>
                <span v-if="mrt.Status==0 && mrt.IsRollBack==null">materials return ticket signatures are complete</span>
                <span v-if="mrt.Status==1 && mrt.IsRollBack==null">materials return ticket has been declined</span>
              </p><br>
              <div class="time-notified">
                <i v-if="mrt.Status==0 && mrt.IsRollBack==null" class="material-icons color-blue">check_circle</i>
                <i v-if="mrt.Status==1 && mrt.IsRollBack==null" class="material-icons color-red">close</i>
                <i v-if="mrt.IsRollBack==0" class="material-icons color-red">replay</i>
                <i v-if="mrt.IsRollBack==1" class="material-icons color-blue">refresh</i>
                {{mrt.notification_date_time}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchMRT(MRTCurrentPage+1)" v-if="MRTCurrentPage!=MRTLastPage && MRTNotifs[0]!=null && MRTBtn==true ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="MRTNotifs[0]==null && MRTBtn==true">
          <i class="material-icons">notifications_none</i> empty
        </div>
        <!-- end mrt -->
      </div>
      <div class="notification-footer">
      </div>
    </div>
  </li>
</template>

<script>
  import axios from 'axios';
  export default {
    data () {
      return {
        dropIsActive:false,
        MIRSNotifs:[],
        MIRSBtn:true,
        POBtn:false,
        RVBtn:false,
        RRBtn:false,
        MRBtn:false,
        MCTBtn:false,
        MRTBtn:false,
        NotificationCounts:0,
        MIRSCurrentPage:1,
        MIRSLastPage:1,
        RVNotifs:[],
        RVCurrentPage:1,
        RVLastPage:1,
        POCurrentPage:1,
        POLastPage:1,
        PONotifs:[],
        RRCurrentPage:1,
        RRLastPage:1,
        RRNotifs:[],
        MRCurrentPage:1,
        MRLastPage:1,
        MRNotifs:[],
        MCTCurrentPage:1,
        MCTLastPage:1,
        MCTNotifs:[],
        MRTCurrentPage:1,
        MRTLastPage:1,
        MRTNotifs:[],
        AddedCounts:0,
        Loaded:false,

      }
    },
    props: ['user'],
    mounted () {
      this.countNotif();
      Echo.private('GlobalNotif.'+this.user.id)
      .listen('GlobalNotifEvent', (e) => {
          console.log(e);
          this.countNotif();
      });
      if (this.user.Role==3 || this.user.Role==4)
      {
        Echo.private('Global.Warehouse')
        .listen('GlobalNotifWarehouseEvent', (e) => {
            console.log(e);
            this.countNotif();
        });
      }
    },
    methods:
    {
      fetchData(page)
      {
        this.Loaded=true;
        var vm=this;
        axios.get(`/fetch-mirs-global-notifications?page=`+page).then(function(response)
        {
          console.log(response);
          if (response.data.current_page==1)
          {
            vm.MIRSNotifs = response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.MIRSNotifs.push(response.data.data[i]);
            }
          }
          vm.MIRSCurrentPage=response.data.current_page;
          vm.MIRSLastPage=response.data.last_page;
        })
      },
      fetchRV(page)
      {
        var vm=this;
        axios.get(`/fetch-rv-global-notifications?page=`+page).then(function(response)
        {
          console.log(response);
          vm.RVCurrentPage=response.data.current_page;
          vm.RVLastPage=response.data.last_page;
          if (response.data.current_page==1)
          {
            vm.RVNotifs=response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.RVNotifs.push(response.data.data[i]);
            }
          }
        }).then(function(error)
        {
          console.log(error);
        });
      },
      fetchPO(page)
      {
        var vm=this;
        axios.get(`/fetch-po-global-notifications?page=`+page).then(function(response)
        {
          console.log(response);
          if (response.data.current_page==1)
          {
            vm.PONotifs=response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.PONotifs.push(response.data.data[i]);
            }
          }
          vm.POCurrentPage=response.data.current_page;
          vm.POLastPage=response.data.last_page;
        }).then(function(error)
        {
          console.log(error);
        })
      },
      fetchRR(page)
      {
        var vm=this;
        axios.get(`/fetch-rr-global-notifications?page=`+page).then(function(response)
        {
          console.log(response);
          if (response.data.current_page==1)
          {
            vm.RRNotifs=response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.RRNotifs.push(response.data.data[i]);
            }
          }
          vm.RRCurrentPage=response.data.current_page;
          vm.RRLastPage=response.data.last_page;
        }).then(function(error)
        {
          console.log(error);
        })
      },
      fetchMR(page)
      {
        var vm=this;
        axios.get(`/fetch-mr-global-notifications?page=`+page).then(function(response)
        {
          console.log(response);
          if (response.data.current_page==1)
          {
            vm.MRNotifs=response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.MRNotifs.push(response.data.data[i]);
            }
          }
          vm.MRCurrentPage=response.data.current_page;
          vm.MRLastPage=response.data.last_page;
        }).then(function(error)
        {
          console.log(error);
        })
      },
      fetchMCT(page)
      {
        var vm=this;
        axios.get(`/fetch-mct-global-notifications?page=`+page).then(function(response)
        {
          console.log(response);
          if (response.data.current_page==1)
          {
            vm.MCTNotifs=response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.MCTNotifs.push(response.data.data[i]);
            }
          }
          vm.MCTCurrentPage=response.data.current_page;
          vm.MCTLastPage=response.data.last_page;
        }).then(function(error)
        {
          console.log(error);
        })
      },
      fetchMRT(page)
      {
        var vm=this;
        axios.get(`/fetch-mrt-global-notifications?page=`+page).then(function(response)
        {
          console.log(response);
          if (response.data.current_page==1)
          {
            vm.MRTNotifs=response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.MRTNotifs.push(response.data.data[i]);
            }
          }
          vm.MRTCurrentPage=response.data.current_page;
          vm.MRTLastPage=response.data.last_page;
        }).then(function(error)
        {
          console.log(error);
        })
      },
      countNotif()
      {
        var vm=this;
        axios.get(`/notif-global-count`).then(function(response)
        {
          console.log(response);
          vm.NotificationCounts = response.data;
          if (vm.user.Role==3||vm.user.Role==4)
          {
            vm.AddedCounts = response.data.unreadMIRS + response.data.unreadMR + response.data.unreadPO + response.data.unreadRR +response.data.unreadRV + response.data.unreadMCT + response.data.unreadMRT;
          }else
          {
            vm.AddedCounts = response.data.unreadMIRS + response.data.unreadRV;
          }
        }).then(function(error)
        {
          console.log(error);
        })
      }
    },
  }
</script>
