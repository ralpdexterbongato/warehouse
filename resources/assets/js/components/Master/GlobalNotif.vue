<template lang="html">
  <li class="dropping-parent" v-on:click="dropIsActive=!dropIsActive,[Loaded==false?fetchData(1):'']">
    <h1 class="waves-effect waves-light" :class="[NotificationCounts!='0'?'pulse':'']">
      <i class="material-icons">notifications</i>
    </h1>
    <h2 class="number-of-unread z-depth-1" v-if="NotificationCounts!='0'">
      <span v-if="NotificationCounts < 10">{{NotificationCounts}}</span>
      <span v-else>9+</span>
    </h2>
    <div class="notification-drop z-depth-1" v-on:click="dropIsActive=!dropIsActive" :class="[dropIsActive == true?'active':'']">
      <div class="notification-header">
        <h2> Notifications</h2>
      </div>
      <div class="notification-line-container">
        <a v-on:click="markSeen(notification.id)" :href="[notification.FileType=='MIRS'?'/previewFullMIRS/'+ notification.FileNo:notification.FileType=='MCT'?'/preview-mct-page-only/'+notification.FileNo:notification.FileType=='MRT'?'/mrt-preview-page/'+notification.FileNo:notification.FileType=='RV'?'/RVfullview/'+notification.FileNo:notification.FileType=='RR'?'/RR-fullpreview/'+notification.FileNo:notification.FileType=='PO'?'/po-full-preview/'+notification.FileNo:notification.FileType=='MR'?'/full-preview-MR/'+notification.FileNo:'']" v-for="notification in NotifList">
          <div class="notification-drop-line" :class="[notification.Seen==null ?'active':'']">
            <div>
              <h2><i class="material-icons">insert_drive_file</i></h2>
            </div>
            <div class="drop-line-detail">
              <h5>{{notification.FileType}} : {{notification.FileNo}}</h5>
              <p v-if="notification.NotificationType=='Request'">
                 New file has been added to your {{notification.FileType}} sign request pocket.
              </p>
              <p v-if="notification.NotificationType=='Invalid'">
                 The administrator marked this {{notification.FileType}} as invalid.
              </p>
              <p v-if="notification.NotificationType=='UndoInvalid'">
                 The administrator undid the marking of this {{notification.FileType}} as invalid, this file is now valid.
              </p>
              <p v-if="notification.NotificationType=='Refused'">
                 The signature-substitution request sent has been refused.
              </p>
              <p v-if="notification.NotificationType=='Canceled'">
                 The signature substitution request that you received has been canceled by the sender.
              </p>
              <p v-if="notification.NotificationType=='Pending'">
                 The budget officer posted a pending remarks.
              </p>
              <p v-if="notification.NotificationType=='Approved'">
                  Signatures are now complete.
              </p>
              <p v-if="notification.NotificationType=='Declined'">
                 File has been declined.
              </p>
              <p v-if="notification.NotificationType=='Updated'">
                 The file has been updated & the signatures restarted.
              </p>
              <p v-if="notification.NotificationType=='Replaced'">
                 A manager signed a file in behalf of you.
              </p>
              <br>
              <div class="time-notified">
                <i v-if="notification.NotificationType=='Approved'" class="material-icons color-blue">check_circle</i>
                <i v-if="notification.NotificationType=='Invalid'" class="material-icons color-orange">layers_clear</i>
                <i v-if="notification.NotificationType=='UndoInvalid'" class="material-icons color-blue">done_all</i>
                <i v-if="notification.NotificationType=='Pending'" class="material-icons color-blue">access_time</i>
                <i v-if="notification.NotificationType=='Replaced'" class="material-icons color-blue">loop</i>
                <i v-if="notification.NotificationType=='Updated'" class="material-icons color-blue">border_color</i>
                <i v-if="notification.NotificationType=='Request'" class="material-icons color-blue">note_add</i>
                <i v-if="notification.NotificationType=='Declined'||notification.NotificationType=='Canceled'||notification.NotificationType=='Refused'" class="material-icons color-red">close</i>
                {{notification.time_notified}}
              </div>
            </div>
          </div>
          <div class="divider">
          </div>
        </a>
        <div class="notification-drop-line load-more" v-on:click="fetchData(NotifCurrentPage+1)" v-if="NotifCurrentPage!=NotifLastPage && NotifList[0]!=null">
          <div class="load-more-btn" >
            <i class="material-icons">arrow_downward</i>
            Load
          </div>
        </div>
        <div class="empty-global-notif" v-if="NotifList[0]==null">
          <i class="material-icons">notifications_none</i> empty
        </div>
      </div>
      <div class="notification-footer">
        <span v-if="NotificationCounts>0" class="bold pointer" v-on:click="markAllSeen()">Mark all as read</span>
      </div>
    </div>
  </li>
</template>

<script>
  import 'vue2-toast/lib/toast.css';
  import Toast from 'vue2-toast';
  Vue.use(Toast);
  import axios from 'axios';
  export default {
    data () {
      return {
        dropIsActive:false,
        NotifList:[],
        NotificationCounts:0,
        NotifCurrentPage:1,
        NotifLastPage:1,
        Loaded:false,
      }
    },
    props: ['user'],
    mounted () {
      this.countNotif();
      Echo.private('GlobalNotif.'+this.user.id)
      .listen('GlobalNotifEvent', (e) => {
          this.countNotif();
          if (this.dropIsActive==true)
          {
            this.fetchData(1);
          }
      });
    },
    methods:
    {
      fetchData(page)
      {
        this.Loaded=true;
        var vm=this;
        axios.get(`/fetch-global-notifications?page=`+page).then(function(response)
        {

          if (response.data.current_page==1)
          {
            vm.NotifList = response.data.data;
          }else
          {
            for (var i = 0; i < response.data.data.length; i++)
            {
              vm.NotifList.push(response.data.data[i]);
            }
          }
          vm.NotifCurrentPage=response.data.current_page;
          vm.NotifLastPage=response.data.last_page;
        })
      },
      fetchRV(page)
      {
        var vm=this;
        axios.get(`/fetch-rv-global-notifications?page=`+page).then(function(response)
        {

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

        });
      },
      fetchPO(page)
      {
        var vm=this;
        axios.get(`/fetch-po-global-notifications?page=`+page).then(function(response)
        {

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

        })
      },
      fetchRR(page)
      {
        var vm=this;
        axios.get(`/fetch-rr-global-notifications?page=`+page).then(function(response)
        {
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
        })
      },
      fetchMR(page)
      {
        var vm=this;
        axios.get(`/fetch-mr-global-notifications?page=`+page).then(function(response)
        {
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
        })
      },
      fetchMCT(page)
      {
        var vm=this;
        axios.get(`/fetch-mct-global-notifications?page=`+page).then(function(response)
        {
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
        })
      },
      fetchMRT(page)
      {
        var vm=this;
        axios.get(`/fetch-mrt-global-notifications?page=`+page).then(function(response)
        {
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
        })
      },
      countNotif()
      {
        var vm=this;
        axios.get(`/notif-global-count`).then(function(response)
        {
          vm.NotificationCounts = response.data;
        }).then(function(error)
        {

        })
      },
      markSeen(id)
      {
        var vm=this;
        axios.get(`/mark-seen/`+id).then(function(response)
        {

        }).catch(function(error)
        {

        });
      },
      markAllSeen()
      {
        var vm=this;
        axios.get(`/mark-all-seen`).then(function(response)
        {
          vm.NotificationCounts=0;
          vm.fetchData(1);
          vm.$toast.top(response.data.success);
        }).catch(function(error)
        {

        })
      }
    },
  }
</script>
