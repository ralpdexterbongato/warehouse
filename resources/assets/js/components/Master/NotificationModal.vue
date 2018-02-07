<template lang="html">
  <span>
    <div class="top-nav-container">
      <div class="left-nav-content">
          <button type="button" class="burger-button" v-on:click="modalOpen=true,refreshall()">
            <div id="nav-icon3" :class="[modalOpen==true?'open':'']">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            </div>
          </button>
        <h1><a href="/"><img src="/DesignIMG/logo.png" alt="logo"></a></h1>
      </div>
      <div class="right-nav-content">
        <div class="title-top">
          <p> Warehouse Inventory</p>
        </div>
      </div>
    </div>
    <div class="Account-modal" :class="[modalOpen==true?'active':'']" v-on:click="modalOpen=!modalOpen">
    </div>
    <div class="middle-account-modal" :class="[modalOpen==true?'active':'']">
      <ul>
        <a>
          <li class="sidebar-title CurrentUser">
            <div class="flex">
              <i class="material-icons">person</i> {{user.FullName}}
            </div>
          </li>
        </a>
        <div class="sidebar-scrolling-container">
          <a>
            <li class="sidebar-title">
              <span>
                Notifications
              </span>
            </li>
          </a>
          <a href="/mirs-signature-list">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">notifications_none</i>MIRS
              </span>
              <span class="notif" :class="[MIRSNotif!=0?'active':'']">
                {{MIRSNotif}}
                <small class="new-notif" v-if="MIRSNew==true">new !</small>
              </span>
            </li>
          </a>
          <a href="/mct-signature-request">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">notifications_none</i>MCT
              </span>
              <span class="notif" :class="[NewlyCreatedMCT!=0?'active':'']">{{NewlyCreatedMCT}}
                <small class="new-notif" v-if="MCTNew==true">new !</small>
              </span>
            </li>
          </a>
          <a href="/my-mrt-signature-request">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">notifications_none</i> MRT
              </span>
              <span class="notif" :class="[NewlyCreatedMRT!=0?'active':'']">{{NewlyCreatedMRT}}
                <small class="new-notif" v-if="MRTNew==true">new !</small>
              </span>
            </li>
          </a>
          <a href="/checkout-rr-request">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">notifications_none</i> RR
              </span>
              <span class="notif" :class="[RRRequestCount!=0?'active':'']">{{RRRequestCount}}
                <small class="new-notif" v-if="RRNew==true">new !
                </small>
              </span>
            </li>
          </a>
          <a href="/myRVrequest">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">notifications_none</i> RV
              </span>
              <span class="notif" :class="[NewlyCreatedRV!=0?'active':'']">{{NewlyCreatedRV}}
                <small class="new-notif" v-if="RVNew==true">new !
                </small>
              </span>
            </li>
          </a>
          <a href="/my-mr-request">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">notifications_none</i> MR
              </span>
              <span class="notif" :class="[CountMRRequest!=0?'active':'']">{{CountMRRequest}}
                <small class="new-notif" v-if="MRNew==true">new !
                </small>
              </span>
            </li>
          </a>
          <a v-if="user.Role==0||user.Role==2" href="/my-PO-request">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">notifications_none</i>PO
              </span>
              <span class="notif" :class="[CountPOrequest!=0?'active':'']">{{CountPOrequest}}
                <small class="new-notif" v-if="PONew==true">new !
                </small>
              </span>
            </li>
          </a>
          <span v-if="user.Role==3||user.Role==4">
            <a href="/waiting-to-be-purchased-rv">
              <li class="clickable waves-effect waves">
                <span>
                  <i class="material-icons">notifications_none</i>Approved rv
                </span>
                <span class="notif" :class="[RVWaitingRRCount!=0?'active':'']">{{RVWaitingRRCount}}
                  <small class="new-notif" v-if="RVwaitingRRNew==true">new !
                  </small>
                </span>
              </li>
            </a>
          </span>
            <a>
              <li class="sidebar-title">
                <span>
                  Settings
                </span>
              </li>
            </a>
            <a v-if="user.Role==2" href="/manager-take-placer-setting">
              <li class="clickable waves-effect waves">
                <span>
                  <i class="material-icons">face</i>Assign a Manager
                </span>
              </li>
            </a>
          <span v-if="user.Role==1">
            <a href="/create-non-existing-item-in-warehouse">
              <li class="clickable waves-effect waves">
                <span>
                  <i class="material-icons">fiber_new</i> Items
                </span>
              </li>
            </a>
            <a href="/settings-accounts-list">
              <li class="clickable waves-effect waves">
                <span>
                  <i class="material-icons">people</i>Accounts
                </span>
              </li>
            </a>
          </span>
          <span v-else>
            <a href="/my-own-account-settings-page">
              <li class="clickable waves-effect waves">
                <span>
                  <i class="material-icons">account_circle</i>My account
                </span>
              </li>
            </a>
          </span>
          <a>
            <li class="sidebar-title">
              <span>
                History
              </span>
            </li>
          </a>
          <a href="/show-my-history">
            <li class="clickable waves-effect waves">
              <span>
                <i class="material-icons">history</i> My history
              </span>
            </li>
          </a>
          <a>
            <li v-on:click="logout()" class="logout-btn clickable waves-effect waves">
            <span>
            <i class="material-icons">exit_to_app</i> Logout
            </span>
            </li>
          </a>
        </div>
      </ul>
    </div>
    <audio ref="audioElm" src="/audio/NotificationSound.mp3">
    </audio>
  </span>
</template>

<script>
import axios from 'axios';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
  export default {
     data () {
       return {
         MIRSNotif:0,
         modalOpen:false,
         MIRSNew:false,
         ApproveMIRSNew:false,
         sample:false,
         MCTNew:false,
         PONew:false,
         MRTNew:false,
         RVNew:false,
         RRNew:false,
         MRNew:false,
         NewlyCreatedMCT:0,
         NewlyCreatedMRT:0,
         NewlyCreatedRV:0,
         RRRequestCount:0,
         RVWaitingRRCount:0,
         CountMRRequest:0,
         CountPOrequest:0,
         RVwaitingRRNew:false,
       }
     },
     created()
     {
       Echo.private('MIRSChannel.'+this.user.id)
       .listen('NewMIRSEvent', (e) => {
           console.log(e);
           this.refreshNotifationMIRS();
           this.modalOpen=true;
           this.MIRSNew=true;
           this.playsound();
       });
       if (this.user.Role==3||this.user.Role==4)
       {
         Echo.private('NewRVApprovedchannel')
         .listen('NewRVApprovedEvent', (e) => {
             console.log(e);
             this.refreshCountRVWaitingForRR();
             this.modalOpen=true;
             this.RVwaitingRRNew=true;
             this.playsound();
         });
       }
       Echo.private('MCTchannel.'+this.user.id)
       .listen('NewMCTEvent', (e) => {
           console.log(e);
           this.refreshnewlyCreatedMCT();
           this.modalOpen=true;
           this.MCTNew=true;
           this.playsound();
       });
       Echo.private('MRTchannel.'+this.user.id)
       .listen('NewMRTEvent', (e) => {
           console.log(e);
           this.refreshNewlyCreatedMRT();
           this.modalOpen=true;
           this.MRTNew=true;
           this.playsound();
       });
       Echo.private('RVchannel.'+this.user.id)
       .listen('NewRVEvent', (e) => {
           console.log(e);
           this.refreshNewlyCreatedRV();
           this.modalOpen=true;
           this.RVNew=true;
           this.playsound();
       });
       if (this.user.Role==2||this.user.Role==0)
       {
         Echo.private('POchannel.'+this.user.id)
         .listen('NewPOEvent', (e) => {
             console.log(e);
             this.refreshCountNewlyCreatedPO();
             this.modalOpen=true;
             this.PONew=true;
             this.playsound();
         });
       }
       Echo.private('RRchannel.'+this.user.id)
       .listen('NewRREvent', (e) => {
           console.log(e);
           this.refresCountRRnewCreated();
           this.modalOpen=true;
           this.RRNew=true;
           this.playsound();
       });
       Echo.private('MRchannel.'+this.user.id)
       .listen('NewMREvent', (e) => {
           console.log(e);
           this.refreshCountMRNewlyCreated();
           this.modalOpen=true;
           this.MRNew=true;
           this.playsound();
       });
     },
     props: ['user'],
     methods: {
       refreshNotifationMIRS()
       {
         var vm=this;
         axios.get(`/mirs-notify`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'MIRSNotif',response.data.MIRSrequest)
        })
      },
      playsound()
      {
        this.$refs.audioElm.play();
      },
      logout()
      {
        this.$loading('Logging out');
        axios.post(`/logout`).then(function(response)
        {
          window.location=response.data.redirect;
        });
      },
      refreshnewlyCreatedMCT()
      {
        var vm=this;
        axios.get(`/mct-new-created-notify`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'NewlyCreatedMCT',response.data.MCTRequestCount);
        })
      },
      refreshNewlyCreatedMRT()
      {
        var vm=this;
        axios.get(`/mrt-new-created-notify`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'NewlyCreatedMRT',response.data.MRTRequestCount);
        })
      },
      refreshNewlyCreatedRV()
      {
        var vm=this;
        axios.get(`/rv-new-created-notify`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'NewlyCreatedRV',response.data.RVRequestCount);
        })
      },
      refreshCountRVWaitingForRR()
      {
        var vm=this;
        axios.get(`/rv-waiting-for-all-items-receiving-report`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'RVWaitingRRCount',response.data.RVwaitingRR);
        })
      },
      refresCountRRnewCreated()
      {
        var vm=this;
        axios.get(`/rv-new-created-rr-notify`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'RRRequestCount',response.data.RRrequestCount);
        })
      },
      refreshCountMRNewlyCreated()
      {
        var vm=this;
        axios.get(`/rv-new-created-mr-notify`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'CountMRRequest',response.data.CountMRRequest);
        });
      },
      refreshCountNewlyCreatedPO()
      {
        var vm=this;
        axios.get(`/po-count-notification`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'CountPOrequest',response.data.PONotifCount);
        });
      },
      refreshall()
      {
        this.refreshNotifationMIRS();
        this.refreshnewlyCreatedMCT();
        this.refreshNewlyCreatedMRT();
        this.refresCountRRnewCreated();
        this.refreshNewlyCreatedRV();
        this.refreshCountMRNewlyCreated();
        if (this.user.Role==3||this.user.Role==4)
        {
           this.refreshCountRVWaitingForRR();
        }
        if (this.user.Role==2||this.user.Role==0)
        {
          this.refreshCountNewlyCreatedPO();
        }
      }
     }

  }
</script>
