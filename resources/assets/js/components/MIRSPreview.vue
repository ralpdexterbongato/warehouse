<template lang="html">
<span v-if="MIRSMaster.users!=null">
  <div class="printable-paper">
    <div class="print-btn-container">
      <div class="download-form" v-if="approved">
        <a :href="'/MIRS.pdf/'+mirsno.MIRSNo"><button type="submit">PDF <i class="fa fa-file-pdf-o"></i></button></a>
        unclaimed:<span class="color-blue">{{unclaimed}}</span>
      </div>
      <div class="empty-left" v-else>
      </div>
      <div class="Request-manager-replace" v-if="managerReplaceistrue">
        <h6 class="mirs-managerreplace-info"><i class="fa fa-info-circle color-blue"></i>
          <span class="color-blue">{{MIRSMaster.users[0].FullName}}</span> is asking for your signature b/c the {{MIRSMaster.users[1].Position}} is not available
        </h6>
        <span class="manager-replacer-accept-cant" :class="{'hide':SignatureManagerRelacerBtnHide}">
          <longpress  duration="3" id="manager-replacer-accept" :on-confirm="AcceptrequestReplacer"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="fa fa-pencil"></i> Signature
          </longpress>
          <longpress  duration="3" id="manager-replacer-cant" :on-confirm="cancelrequestReplacer"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="fa fa-pencil"></i> I can't
          </longpress>
        </span>
      </div>
      <div class="Request-manager-replace"  v-if="UserIsApprovalReplacer">
        <h6 class="mirs-managerreplace-info"><i class="fa fa-info-circle color-blue"></i>
          <span class="color-blue">{{MIRSMaster.users[0].FullName}}</span> is asking for your signature b/c the General Manager is not available
        </h6>
        <span class="Approve-replacer-accept-cant" :class="{'hide':SignatureApproveBtnHide}">
          <longpress  duration="3" id="manager-replacer-accept" :on-confirm="AcceptApprovalReplacerequest"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="fa fa-pencil"></i> Signature
          </longpress>
          <longpress  duration="3" id="manager-replacer-cant" :on-confirm="cancelRequestApprovalReplacer"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="fa fa-pencil"></i> I can't
          </longpress>
        </span>
      </div>
      <span v-if="((ManagerCansignature)&&(NoManagerReplacerSignature)||((GMCanSignature)&&(NoApprovalReplacerSignature)))">
        <div class="middle-status" :class="{'hide':SignatureBtnHide}">
          <longpress id="accepted" duration="3" :on-confirm="SignatureMIRS"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
            <i class="fa fa-pencil"></i> Signature
          </longpress>
          <longpress id="not-accepted" duration="3" :on-confirm="DeclineMIRS"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
            <i class="fa fa-times"></i> Decline
          </longpress>
        </div>
      </span>
      <div class="mct-create-mct-list" v-if="approved">
        <a :href="'/create-mct/'+mirsno.MIRSNo" v-if="((user.Role==4)||(user.Role==3))"><button type="button" id="mct-modal-btn" name="button"><i class="fa fa-plus"></i> Record MCT</button></a>
        <h1 v-if="((user.Role!=3)&&(user.Role!=4)&&(MCTNumber==null))">Empty MCT</h1>
        <a :href="'/MCTofMIRS/'+mirsno.MIRSNo" v-else-if="MCTNumber!=null"><button type="button" name="button"><i class="fa fa-table"></i> M.C.T. list</button></a>
      </div>
    </div>
  </div>
    <div class="bondpaper-size">
      <div class="top-part-box1">
        <h1>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h1>
        <h4>Cabulijan, Tubigon, Bohol</h4>
        <h2>MATERIALS ISSUANCE REQUISITION SLIP</h2>
        <div class="status-mirs declined" v-if="declinedistrue">
          <h1 class="deny-sign"><i class="fa fa-times"></i><br>Declined</h1>
        </div>
        <div class="status-mirs approved" v-else-if="approved">
          <h1 class="approved-sign"><i class="fa fa-thumbs-up"></i> <br>Approved</h1>
        </div>
        <div class="status-mirs" v-else>
          <h1><i class="fa fa-clock-o"></i><br>Pending</h1>
        </div>
      </div>
      <div class="top-part-box2">
        <div class="top-box2-left">
        </div>
        <div class="top-box2-right">
          <div class="top-box2-right-data">
            <label>MIRS #:</label><h1>{{MIRSMaster.MIRSNo}}</h1>
          </div>
          <div class="top-box2-right-data">
            <label>Date:</label><h1>{{MIRSMaster.MIRSDate}}</h1>
          </div>
        </div>
      </div>
      <div class="top-part-box3">
        <p>
          TO: The General Manager <br>
          Please furnish the following materials for :
        </p>
        <div class="purpose-container">
          <h2>{{MIRSMaster.Purpose}}</h2>
        </div>
      </div>
      <div class="mirs-details-container">
        <div class="table-mirs-container">
          <table>
            <tr>
              <th class="noborder-left">CODE</th>
              <th>PARTICULARS</th>
              <th>UNIT</th>
              <th>QUANTITY</th>
              <th class="noborder-right">REMARKS</th>
            </tr>
            <tr v-for="mirsdata in MIRSDetail">
              <td class="noborder-left">{{mirsdata.ItemCode}}</td>
              <td class="particular">{{mirsdata.Particulars}}</td>
              <td>{{mirsdata.Unit}}</td>
              <td>{{mirsdata.Quantity}}</td>
              <td>{{mirsdata.Remarks}}</td>
            </tr>
          </table>
        </div>
        <div class="statement-container">
          <p>I hereby certify that the materials/supplies requested above are <br>necessary and with purpose stated above</p>
        </div>
        <div class="bottom-mirs-part">
          <div class="request-recommend-sig">
            <div class="request-sig">
              <h4>Prepared by:</h4>
                <h3>
                  <img v-if="(MIRSMaster.users[0].pivot.Signature=='0')" :src="'/storage/signatures/'+MIRSMaster.users[0].Signature">
                </h3>
              <h2>
                {{MIRSMaster.users[0].FullName}}
                  <i v-if="(MIRSMaster.users[0].pivot.Signature=='1')"class="fa fa-times decliner"></i>
                  <br>
                {{MIRSMaster.users[0].Position}}
              </h2>
            </div>
            <div class="recommend-sig">
            <h4>Recommended by:</h4>
            <h3 v-if="MIRSMaster.users[1].pivot.Signature=='0'">
              <img :src="'/storage/signatures/'+MIRSMaster.users[1].Signature">
            </h3>
            <h3 v-else-if="((MIRSMaster.users[3]!=null)&&(MIRSMaster.users[3].pivot.Signature=='0')&&(MIRSMaster.users[3].pivot.SignatureType=='ManagerReplacer'))">
              <h2>For :</h2>
              <img :src="'/storage/signatures/'+MIRSMaster.users[3].Signature">
            </h3>
            <h3 v-else-if="((MIRSMaster.users[4]!=null)&&(MIRSMaster.users[4].pivot.Signature=='0')&&(MIRSMaster.users[4].pivot.SignatureType='ManagerReplacer'))">
              <h2>For :</h2>
              <img :src="'/storage/signatures/'+MIRSMaster.users[4].Signature">
            </h3>
            <h2>
             <span class="bold">{{MIRSMaster.users[1].FullName}}
             <span class="opener-manager-replace">
               <div class="mini-menu-managers" v-if="user.id==MIRSMaster.users[0].id && this.ManagerBehalfActive==true">
                 <h1 v-if="ManagerReplacerData==null">Request signature to</h1>
                 <h1 v-else>Request pending <i class="fa fa-clock-o color-white"></i></h1>
                 <div class="manager-list-menu"v-if="ManagerReplacerData==null">
                   <select v-model="ManagerReplacerID">
                     <option :value="null">Choose a manager</option>
                     <option v-for="manager in allManager" :value="manager.id" v-if="manager.id!=MIRSMaster.users[1].id">{{manager.FullName}}</option>
                   </select>
                   <p v-if="error!=null" class="color-red">*{{error}}</p>
                   <span class="send-cancel-btns">
                     <button type="button" v-on:click="ManagerBehalfActive=false">Cancel</button>
                     <button type="button" v-on:click="sendrequestReplacer()">Send</button>
                   </span>
                 </div>
                 <div class="manager-replacer-sent" v-else>
                   <p>Your request has been sent to<br> <span class="underline">{{ManagerReplacerData.FullName}}</span></p>
                   <span class="cancel-manager-replace" v-on:click="cancelrequestReplacer()"><i class="fa fa-times color-red"></i>cancel</span>
                 </div>
               </div>
               <i v-if="RecommendedBySignatureNull" class="color-blue" :class="[MIRSMaster.ManagerReplacer==null?'fa fa-users':'fa fa-clock-o']" v-on:click="ManagerBehalfActive=!ManagerBehalfActive,[allManager[0]==null?fetchAllManager():'']" ></i>
             </span>
             <i class="fa fa-times decliner" v-if="(MIRSMaster.users[1].pivot.Signature=='1')"></i>
             </span><br>
              {{MIRSMaster.users[1].Position}}
            </h2>
            </div>
          </div>
          <div class="gm-sig">
            <h4>APPROVED:</h4>
            <h3 v-if="(MIRSMaster.users[2].pivot.Signature=='0')">
              <img :src="'/storage/signatures/'+MIRSMaster.users[2].Signature">
            </h3>
            <h3 v-else-if="((MIRSMaster.users[3]!=null)&&(MIRSMaster.users[3].pivot.Signature=='0')&&(MIRSMaster.users[3].pivot.SignatureType=='ApprovalReplacer'))">
              <p>For :</p><img :src="'/storage/signatures/'+MIRSMaster.users[3].Signature">
            </h3>
            <h2>
            <span class="gm-info-box bold">
              {{MIRSMaster.users[2].FullName}}
              <i class="fa fa-times decliner" v-if="(MIRSMaster.users[2].pivot.Signature=='1')"></i>
            </span><br>
              {{MIRSMaster.users[2].Position}}
            </h2>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</span>
</template>

<script>
import axios from 'axios';
import Longpress from 'vue-longpress';
  export default {
     data () {
        return {
          MIRSMaster:[],
          MCTNumber:null,
          MIRSDetail:[],
          unclaimed:0,
          ManagerBehalfActive:false,
          allManager:[],
          ManagerReplacerID:null,
          error:null,
          ApproveReplacerName:'',
          SignatureBtnHide:false,
          SignatureApproveBtnHide:false,
          SignatureManagerRelacerBtnHide:false
        }
      },
     props: ['mirsno','user'],
     methods: {
       fetchMIRSData()
       {
         var vm=this;
         axios.get(`/fetchpreview-full-mirs-/`+this.mirsno.MIRSNo).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'MIRSMaster',response.data.MIRSMaster[0])
           Vue.set(vm.$data,'MIRSDetail',response.data.MIRSDetail)
           Vue.set(vm.$data,'MCTNumber',response.data.MCTNumber)
           Vue.set(vm.$data,'unclaimed',response.data.unclaimed)
         })
       },
       SignatureMIRS()
       {
         this.SignatureBtnHide=true;
         var vm=this;
         axios.put(`/MIRS-Signature/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchMIRSData();
      },
      DeclineMIRS()
      {
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/deniedmirs/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        })
        this.fetchMIRSData();
      },
      ApproveinBehalf()
      {
        var vm=this;
        axios.put(`/mirs-signature-if-gm-isabsent/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchMIRSData();
      },
      CancelApproveinBehalf()
      {
        var vm=this;
        axios.put(`/cancel-request-toadmin/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchMIRSData();
      },
      fetchAllManager()
      {
        var vm=this;
        axios.get(`/fetch-all-managers`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'allManager',response.data);
        })
      },
      sendrequestReplacer()
      {
        var vm=this;
        axios.post(`/send-request-manager-replacer/`+this.mirsno.MIRSNo,{
          ManagerReplacerID:this.ManagerReplacerID,
        }).then(function(response)
        {
          if (response.data.error!=null)
          {
            Vue.set(vm.$data,'error',response.data.error);
          }
        });
        this.fetchMIRSData();
      },
      cancelrequestReplacer()
      {
        this.SignatureManagerRelacerBtnHide=true;
        var vm=this;
        axios.delete(`/cancel-request-manager-replacer/` + this.mirsno.MIRSNo).then(function(response)
        {
          Vue.set(vm.$data,'ManagerBehalfActive',false);
          console.log(response);
        });
        this.fetchMIRSData();
      },
      AcceptrequestReplacer()
      {
        this.SignatureManagerRelacerBtnHide=true;
        var vm=this;
        axios.put(`/signature-replacer-accepted/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchMIRSData();
      },
      cancelRequestApprovalReplacer()
      {
        this.SignatureApproveBtnHide=true;
        var vm=this;
        axios.put(`/cancel-request-approval/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        })
        this.fetchMIRSData();
      },
      AcceptApprovalReplacerequest()
      {
        this.SignatureApproveBtnHide=true;
        var vm=this;
        axios.put(`/confirm-manager-toreplace-gm-signature/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchMIRSData();
      }
     },
     mounted () {
       this.fetchMIRSData();
     },
     components: {
        Longpress
     },
     computed: {
       approved: function()
       {
         if(((this.MIRSMaster.users[0].pivot.Signature=='0')&&((this.MIRSMaster.users[1].pivot.Signature=='0')||((this.MIRSMaster.users[3]!=null)&&(this.MIRSMaster.users[3].pivot.Signature=='0')&&(this.MIRSMaster.users[3].pivot.SignatureType=='ManagerReplacer'))||((this.MIRSMaster.users[4]!=null)&&(this.MIRSMaster.users[4].pivot.Signature=='0')&&(this.MIRSMaster.users[4].pivot.SignatureType=='ManagerReplacer')))&&(this.MIRSMaster.users[2].pivot.Signature=='0')||((this.MIRSMaster.users[3]!=null)&&(this.MIRSMaster.users[3].pivot.Signature=='0')&&(this.MIRSMaster.users[3].pivot.SignatureType=='ApprovalReplacer'))||((this.MIRSMaster.users[4]!=null)&&(this.MIRSMaster.users[4].pivot.Signature=='0')&&(this.MIRSMaster.users[4].pivot.SignatureType=='ApprovalReplacer')))) {
           return true;
         }else {
           return false;
         }
       },
       declinedistrue: function()
       {
         if ((this.MIRSMaster.users[0].pivot.Signature=='1')||(this.MIRSMaster.users[1].pivot.Signature=='1')||(this.MIRSMaster.users[2].pivot.Signature=='1'))
         {
           return true;
         }else
         {
           return false;
         }
       },
       ManagerReplacerData: function()
       {
         if (this.MIRSMaster.users[3]!=null && this.MIRSMaster.users[3].pivot.SignatureType=='ManagerReplacer')
         {
           return this.MIRSMaster.users[3];
         }else if(this.MIRSMaster.users[4]!=null && this.MIRSMaster.users[4].pivot.SignatureType=='ManagerReplacer')
         {
           return this.MIRSMaster.users[4];
         }else
         {
           return null;
         }
       },
       managerReplaceistrue: function()
       {
         if (((this.MIRSMaster.users[3]!=null)&&(this.MIRSMaster.users[3].id==this.user.id)&&(this.MIRSMaster.users[3].pivot.SignatureType=='ManagerReplacer')&&(this.MIRSMaster.users[3].pivot.Signature==null))||(this.MIRSMaster.users[4]!=null)&&(this.MIRSMaster.users[4].id==this.user.id)&&(this.MIRSMaster.users[4].pivot.SignatureType=='ManagerReplacer')&&(this.MIRSMaster.users[3].pivot.Signature==null))
         {
           return true;
         }else
         {
           return false;
         }
       },
       ManagerCansignature: function()
       {
         if((this.MIRSMaster.users[1].id==this.user.id)&&(this.MIRSMaster.users[1].pivot.Signature==null))
         {
           return true;
         }else
         {
           return false;
         }
       },
       GMCanSignature: function()
       {
         if ((this.MIRSMaster.users[2].id==this.user.id)&&(this.MIRSMaster.users[2].pivot.Signature==null)&&((this.MIRSMaster.users[1].pivot.Signature=='0')||((this.ManagerReplacerData!=null)&&(this.MIRSMaster.users[1].pivot.Signature=='0'))))
         {
           return true;
         }else
         {
           return false;
         }
       },
       NoManagerReplacerSignature: function()
       {
         if (((this.MIRSMaster.users[3]!=null)&&(this.MIRSMaster.users[3].pivot.Signature=='0')&&(this.MIRSMaster.users[3].pivot.SignatureType=='ManagerReplacer'))||((this.MIRSMaster.users[4]!=null)&&(this.MIRSMaster.users[4].pivot.Signature=='0')&&(this.MIRSMaster.users[4].pivot.SignatureType=='ManagerReplacer')))
         {
           return false;
         }else
         {
           return true;
         }
       },
       ApprovalReplacerData: function()
       {
         if ((this.MIRSMaster.users[3]!=null)&&(this.MIRSMaster.users[3].pivot.SignatureType=='ApprovalReplacer'))
         {
           return this.MIRSMaster.users[3];
         }else if((this.MIRSMaster.users[4]!=null)&&(this.MIRSMaster.users[4].pivot.SignatureType=='ApprovalReplacer'))
         {
           return this.MIRSMaster.users[4];
         }else
         {
           return null;
         }
       },

       NoApprovalReplacerSignature: function()
       {
         if (this.ApprovalReplacerData!=null && this.ApprovalReplacerData.pivot.Signature!=null)
         {
           return false;
         }else
         {
           return true;
         }
       },
       UserIsApprovalReplacer: function()
       {
         if(((this.MIRSMaster.users[1].pivot.Signature=='0')&&(this.MIRSMaster.users[3]!=null)&&(this.user.id==this.MIRSMaster.users[3].id)&&(this.MIRSMaster.users[3].pivot.Signature==null)&&(this.MIRSMaster.users[3].pivot.SignatureType=='ApprovalReplacer')&&(this.MIRSMaster.users[2].pivot.Signature==null))||((this.MIRSMaster.users[1].pivot.Signature=='0')&&(this.MIRSMaster.users[4]!=null)&&(this.user.id==this.MIRSMaster.users[4].id)&&(this.MIRSMaster.users[4].pivot.Signature==null)&&(this.MIRSMaster.users[4].pivot.SignatureType=='ApprovalReplacer')&&(this.MIRSMaster.users[2].pivot.Signature==null)))
         {
           return true;
         }else
         {
           return false;
         }
       },
       RecommendedBySignatureNull: function()
       {
         if (((this.user.id==this.MIRSMaster.users[0].id)&&(this.MIRSMaster.users[1].pivot.Signature==null)&&(this.MIRSMaster.users[3]!=null)&&(this.MIRSMaster.users[3].pivot.Signature==null)&&(this.MIRSMaster.users[3].pivot.SignatureType=='ManagerReplacer'))||((this.user.id==this.MIRSMaster.users[0].id)&&(this.MIRSMaster.users[1].pivot.Signature==null)&&(this.MIRSMaster.users[4]!=null)&&(this.MIRSMaster.users[4].pivot.Signature==null)&&(this.MIRSMaster.users[4].pivot.SignatureType=='ManagerReplacer'))||((this.user.id==this.MIRSMaster.users[0].id)&&(this.MIRSMaster.users[1].pivot.Signature==null)&&(this.MIRSMaster.users[3]==null))||((this.user.id==this.MIRSMaster.users[0].id)&&(this.MIRSMaster.users[1].pivot.Signature==null)&&(this.MIRSMaster.users[4]==null)))
         {
           return true;
         }else
         {
           return false;
         }
       },
     }
  }
</script>
