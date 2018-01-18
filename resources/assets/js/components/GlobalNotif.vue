<template lang="html">
  <li class="dropping-parent" v-on:click="dropIsActive=!dropIsActive,[MIRSNotifs[0]==null?fetchData(1):'']">
    <h1 class="waves-effect waves-light">
      <i class="material-icons">notifications</i>
    </h1>
    <h2 class="number-of-unread z-depth-1" v-if="AddedCount!='0'">{{AddedCount}}</h2>
    <div class="notification-drop z-depth-1" v-on:click="dropIsActive=!dropIsActive" :class="[dropIsActive == true?'active':'']">
      <div class="notification-header">
        <h2> Notifications</h2>
      </div>
      <div class="notification-navs">
        <h2 :class="[MIRSBtn==true?'active':'']" v-on:click="MIRSBtn=true,MRBtn=false,RVBtn=false,POBtn=false,RRBtn=false">MIRS</h2>
        <h2 :class="[RVBtn==true?'active':'']"  v-on:click="RVBtn=true,MRBtn=false,MIRSBtn=false,POBtn=false,RRBtn=false">RV</h2>
        <h2 v-if="user.Role == '3' || user.Role == '4'" :class="[POBtn==true?'active':'']"  v-on:click="POBtn=true,MRBtn=false,MIRSBtn=false,RVBtn=false,RRBtn=false">PO</h2>
        <h2 v-if="user.Role == '4' || user.Role == '3'" :class="[RRBtn==true?'active':'']"  v-on:click="RRBtn=true,MRBtn=false,MIRSBtn=false,RVBtn=false,POBtn=false">RR</h2>
        <h2 v-if="user.Role == '4' || user.Role == '3'" :class="[MRBtn==true?'active':'']"  v-on:click="MRBtn=true,RRBtn=false,MIRSBtn=false,RVBtn=false,POBtn=false">MR</h2>
      </div>
      <div class="notification-line-container">
        <a :href="'/previewFullMIRS/'+ mirs.MIRSNo" v-for="mirs in MIRSNotifs">
          <div class="notification-drop-line" :class="[mirs.UnreadNotification == '0'?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>MIRS : {{mirs.MIRSNo}}</h5>
              <p>
                has been
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
        <div class="notification-drop-line load-more" v-on:click="fetchData(MIRSCurrentPage+1)" v-if="MIRSCurrentPage!=MIRSLastPage && MIRSNotifs[0]!=null ">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-mirs-notif" v-if="MIRSNotifs[0]==null">
          <i class="material-icons">notifications_none</i> empty
        </div>
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
        AddedCount:0,
        MIRSCurrentPage:1,
        MIRSLastPage:1
      }
    },
    props: ['user'],
    mounted () {
      this.countNotif();
    },
    methods:
    {
      fetchData(page)
      {
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
      countNotif()
      {
        var vm=this;
        axios.get(`/notif-global-count`).then(function(response)
        {
          console.log(response);
          vm.AddedCount = response.data;
        }).then(function(error)
        {
          console.log(error);
        })
      }
    },
  }
</script>
