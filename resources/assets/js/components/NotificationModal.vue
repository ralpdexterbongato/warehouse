<template lang="html">
  <span>
    <div class="Account-modal" :class="[modalOpen==true?'active':'']" v-on:click="modalOpen=!modalOpen">
      <div class="middle-account-modal" v-on:click="modalOpen=!modalOpen">
        <li class="userinfo">
          <h3 class="account-image"><img src="/DesignIMG/logo.png" alt="pic"></h3>
          <p class="name-of-user">{{user.Fname}} {{user.Lname}}</p>
          <p class="position">{{user.Position}}</p>
          <h1><a href="/show-my-history"><i class="fa fa-history"></i> Histories</a></h1>
        </li>
        <ul>
          <a href="/mirs-signature-list">
            <li>
              <span>
                <i class="fa fa-bell-o" :class="[MIRSNew==true?'animated swing':'']"></i> MIRS signature
              </span>
              <span class="notif" :class="[MIRSNotif!=0?'active':'']">
                {{MIRSNotif}}
                <small class="new-notif" v-if="MIRSNew==true">new !</small>
              </span>
            </li>
          </a>
          <a href="/mct-signature-request">
            <li>
              <span>
                <i class="fa fa-bell-o" :class="[MCTNew==true?'animated swing':'']"></i> MCT signature
              </span>
              <span class="notif" :class="[NewlyCreatedMCT!=0?'active':'']">{{NewlyCreatedMCT}}
                <small class="new-notif" v-if="MCTNew==true">new !</small>
              </span>
            </li>
          </a>
          <a href="/my-mrt-signature-request">
            <li>
              <span>
                <i class="fa fa-bell-o" :class="[MRTNew==true?'animated swing':'']"></i> MRT signature
              </span>
              <span class="notif" :class="[NewlyCreatedMRT!=0?'active':'']">{{NewlyCreatedMRT}}
                <small class="new-notif" v-if="MRTNew==true">new !</small>
              </span>
            </li>
          </a>
          <a href="/checkout-rr-request">
            <li>
              <span>
                <i class="fa fa-bell-o" :class="[RRNew==true?'animated swing':'']"></i> RR signature
              </span>
              <span class="notif" :class="[RRRequestCount!=0?'active':'']">{{RRRequestCount}}
                <small class="new-notif" v-if="RRNew==true">new !
                </small>
              </span>
            </li>
          </a>
          <a href="/myRVrequest">
            <li>
              <span>
                <i class="fa fa-bell-o" :class="[RVNew==true?'animated swing':'']"></i> RV signature
              </span>
              <span class="notif" :class="[NewlyCreatedRV!=0?'active':'']">{{NewlyCreatedRV}}
                <small class="new-notif" v-if="RVNew==true">new !
                </small>
              </span>
            </li>
          </a>
          <a href="/my-mr-request">
            <li>
              <span>
                <i class="fa fa-bell-o" :class="[MRNew==true?'animated swing':'']"></i> M.R. signature
              </span>
              <span class="notif" :class="[CountMRRequest!=0?'active':'']">{{CountMRRequest}}
                <small class="new-notif" v-if="MRNew==true">new !
                </small>
              </span>
            </li>
          </a>
          <a v-if="user.Role==0||user.Role==2" href="/my-PO-request">
            <li>
              <span>
          <i class="fa fa-bell-o" :class="[PONew==true?'animated swing':'']"></i>PO signature
              </span>
              <span class="notif" :class="[CountPOrequest!=0?'active':'']">{{CountPOrequest}}
                <small class="new-notif" v-if="PONew==true">new !
                </small>
              </span>
            </li>
          </a>
          <span v-if="user.Role==3||user.Role==4">
            <a href="/ready-mirs">
              <li>
                <span>
                  <i class="fa fa-bell-o" :class="[ApproveMIRSNew==true?'animated swing':'']"></i>Newly approved MIRS
                </span>
                <span class="notif" :class="[NewlyApprovedMIRS!=0?'active':'']">{{NewlyApprovedMIRS}}
                  <small class="new-notif" v-if="ApproveMIRSNew==true">new !
                  </small>
                </span>
              </li>
            </a>
            <a href="/waiting-to-be-purchased-rv">
              <li>
                <span>
                  <i class="fa fa-bell-o" :class="[RVwaitingRRNew==true?'animated swing':'']"></i> RV waiting for RR
                </span>
                <span class="notif" :class="[RVWaitingRRCount!=0?'active':'']">{{RVWaitingRRCount}}
                  <small class="new-notif" v-if="RVwaitingRRNew==true">new !
                  </small>
                </span>
              </li>
            </a>
          </span>
            <a v-if="user.Role==2" href="/manager-take-placer-setting">
              <li>
                <span>
                  <i class="fa fa-user"></i>Assign a Manager
                </span>
              </li>
            </a>
          <span v-if="user.Role==1">
            <a href="/create-non-existing-item-in-warehouse">
              <li>
                <span>
                  <i class="fa fa-plus"></i> Add new item
                </span>
              </li>
            </a>
            <a href="/settings-accounts-list">
              <li>
                <span>
                  <i class="fa fa-cog"></i> Manage accounts
                </span>
              </li>
            </a>
          </span>
          <a>
            <li v-on:click="logout()">
            <span>
            <i class="fa fa-sign-out"></i> Logout
            </span>
            </li>
          </a>
        </ul>
      </div>
    </div>
    <audio ref="audioElm" src="/audio/NotificationSound.mp3">
    </audio>
  </span>
</template>

<script>
import axios from 'axios'
  export default {
     data () {
       return {
         MIRSNotif:0,
         NewlyApprovedMIRS:0,
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
       Echo.private('MIRSChannel.'+this.myFname+this.myLname)
       .listen('NewMIRSEvent', (e) => {
           console.log(e);
           this.refreshNotifationMIRS();
           this.modalOpen=true;
           this.MIRSNew=true;
           this.playsound();
       });
       this.refreshNotifationMIRS();
       if (this.user.Role==3||this.user.Role==4)
       {
         Echo.private('WarehouseRole')
         .listen('NewApprovedMIRSEvent', (e) => {
             console.log(e);
             this.RefreshNewlyApprovedMIRSNotification();
             this.modalOpen=true;
             this.ApproveMIRSNew=true;
             this.playsound();
         });
         this.RefreshNewlyApprovedMIRSNotification();
         this.refreshCountRVWaitingForRR();
         Echo.private('NewRVApprovedchannel')
         .listen('NewRVApprovedEvent', (e) => {
             console.log(e);
             this.refreshCountRVWaitingForRR();
             this.modalOpen=true;
             this.RVwaitingRRNew=true;
             this.playsound();
         });
       }
       Echo.private('MCTchannel.'+this.myFname+this.myLname)
       .listen('NewMCTEvent', (e) => {
           console.log(e);
           this.refreshnewlyCreatedMCT();
           this.modalOpen=true;
           this.MCTNew=true;
           this.playsound();
       });
       this.refreshnewlyCreatedMCT();
       this.refreshNewlyCreatedMRT();
       this.refreshNewlyCreatedRV();
       Echo.private('MRTchannel.'+this.myFname+this.myLname)
       .listen('NewMRTEvent', (e) => {
           console.log(e);
           this.refreshNewlyCreatedMRT();
           this.modalOpen=true;
           this.MRTNew=true;
           this.playsound();
       });
       Echo.private('RVchannel.'+this.myFname+this.myLname)
       .listen('NewRVEvent', (e) => {
           console.log(e);
           this.refreshNewlyCreatedRV();
           this.modalOpen=true;
           this.RVNew=true;
           this.playsound();
       });
       this.refresCountRRnewCreated();
       this.refreshCountMRNewlyCreated();
       if (this.user.Role==2||this.user.Role==0)
       {
         Echo.private('POchannel.'+this.myFname+this.myLname)
         .listen('NewPOEvent', (e) => {
             console.log(e);
             this.refreshCountNewlyCreatedPO();
             this.modalOpen=true;
             this.PONew=true;
             this.playsound();
         });
         this.refreshCountNewlyCreatedPO();
       }
       Echo.private('RRchannel.'+this.myFname+this.myLname)
       .listen('NewRREvent', (e) => {
           console.log(e);
           this.refresCountRRnewCreated();
           this.modalOpen=true;
           this.RRNew=true;
           this.playsound();
       });
       Echo.private('MRchannel.'+this.myFname+this.myLname)
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
        var vm=this;
        axios.post(`/logout`).then(function(response)
        {
          window.location=response.data.redirect;
        });
      },
      RefreshNewlyApprovedMIRSNotification()
      {
        var vm=this;
        axios.get(`/mirs-approved-new-notify`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'NewlyApprovedMIRS',response.data.NewlyApprovedMIRS)
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
        axios.get(`/rv-new-created-mrt-notify`).then(function(response)
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
      }
     },
      computed: {
        myFname: function () {
          return this.user.Fname.split(' ').join('');
        },
        myLname: function () {
          return this.user.Lname.split(' ').join('');
        }
      },

  }
</script>
