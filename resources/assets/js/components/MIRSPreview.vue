<template lang="html">
<span>
  <div class="printable-paper">
    <div class="print-btn-container">
      <div class="download-form" v-if="(MIRSMaster.PreparedSignature!=null)&&((MIRSMaster.RecommendSignature!=null)||(MIRSMaster.ManagerReplacerSignature!=null))&&((MIRSMaster.ApproveSignature!=null)||(MIRSMaster.ApprovalReplacerSignature!=null))">
        <a :href="'/download-pdf/'+mirsno.MIRSNo"><button type="submit">PDF <i class="fa fa-file-pdf-o"></i></button></a>
        unclaimed:<span class="color-blue">{{unclaimed}}</span>
      </div>
      <div class="empty-left-mirs" v-else-if="(MIRSMaster.Recommendedby!=user.Fname+' '+user.Lname)">
      </div>
      <div class="empty-left" v-else>
      </div>
        <div class="Request-manager-replace" v-if="((MIRSMaster.ManagerReplacer==user.Fname+' '+user.Lname)&&(MIRSMaster.ManagerReplacerSignature==null))">
          <h6 class="mirs-managerreplace-info"><i class="fa fa-info-circle color-blue"></i>
            <span class="color-blue">{{MIRSMaster.Preparedby}}</span> is asking for your signature b/c the {{MIRSMaster.RecommendPosition}} is not available
          </h6>
          <span class="manager-replacer-accept-cant">
            <longpress  duration="3" id="manager-replacer-accept" :on-confirm="AcceptrequestReplacer" :disabled="btndisable" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
            <i class="fa fa-pencil"></i> Signature
            </longpress>
            <longpress  duration="3" id="manager-replacer-cant" :on-confirm="cancelrequestReplacer" :disabled="btndisable" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
            <i class="fa fa-pencil"></i> I can't
            </longpress>
          </span>
        </div>
          <div class="Request-manager-replace"  v-if="((MIRSMaster.ApprovalReplacer==user.Fname+' '+user.Lname)&&(MIRSMaster.ApprovalReplacerSignature==null)&&(MIRSMaster.ApproveSignature==null)&&((MIRSMaster.ManagerReplacerSignature!=null)||(MIRSMaster.RecommendSignature!=null)))">
            <h6 class="mirs-managerreplace-info"><i class="fa fa-info-circle color-blue"></i>
              <span class="color-blue">{{MIRSMaster.Preparedby}}</span> is asking for your signature b/c the General Manager is not available
            </h6>
            <span class="Approve-replacer-accept-cant">
              <longpress  duration="3" id="manager-replacer-accept" :on-confirm="AcceptApprovalReplacerequest" :disabled="btndisable" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
              <i class="fa fa-pencil"></i> Signature
              </longpress>
              <longpress  duration="3" id="manager-replacer-cant" :on-confirm="cancelRequestApprovalReplacer" :disabled="btndisable" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
              <i class="fa fa-pencil"></i> I can't
              </longpress>
              <!-- <button type="button" v-on:click="AcceptApprovalReplacerequest()"> <i class="fa fa-pencil"></i> Signature</button>
              <button type="button" v-on:click="cancelRequestApprovalReplacer()"> <i class="fa fa-times"></i> I can't</button> -->
            </span>
          </div>
        <span v-if="((MIRSMaster.Recommendedby==user.Fname+' '+user.Lname)&&(MIRSMaster.RecommendSignature==null)&&(MIRSMaster.IfDeclined==null)&&(MIRSMaster.ManagerReplacerSignature==null))||((MIRSMaster.Approvedby==user.Fname+' '+user.Lname)&&(MIRSMaster.ApproveSignature==null)&&(MIRSMaster.IfDeclined==null))">
          <span v-if="((user.Role==2)&&((MIRSMaster.PreparedSignature==null)||((MIRSMaster.RecommendSignature==null)&&(MIRSMaster.ManagerReplacerSignature==null))))">
          </span>
          <div class="middle-status" v-else>
            <longpress id="accepted" duration="3" :on-confirm="SignatureMIRS" :disabled="btndisable" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
            <i class="fa fa-pencil"></i> Signature
            </longpress>
            <longpress id="not-accepted" duration="3" :on-confirm="DeclineMIRS" :disabled="btndisable" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
            <i class="fa fa-times"></i> Decline
            </longpress>

          </div>
        </span>
      <div class="mct-create-mct-list" v-if="(((MIRSMaster.RecommendSignature!=null)||(MIRSMaster.ManagerReplacerSignature!=null))&&((MIRSMaster.ApproveSignature!=null)||(MIRSMaster.ApprovalReplacerSignature!=null)))">
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
              <div class="status-mirs declined" v-if="MIRSMaster.IfDeclined!=null">
                <h1 class="deny-sign"><i class="fa fa-times"></i><br>Declined</h1>
              </div>
              <div class="status-mirs approved" v-else-if="(((MIRSMaster.ApprovalReplacerSignature!=null)||(MIRSMaster.ApproveSignature!=null))&&((MIRSMaster.RecommendSignature!=null)||(MIRSMaster.ManagerReplacerSignature!=null))&&MIRSMaster.PreparedSignature!=null)">
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
                  <img v-if="(MIRSMaster.PreparedSignature!=null)" :src="'/storage/signatures/'+MIRSMaster.PreparedSignature">
                </h3>
              <h2>
                {{MIRSMaster.Preparedby}}
                  <i v-if="(MIRSMaster.IfDeclined==MIRSMaster.Preparedby)"class="fa fa-times decliner"></i>
                  <br>
                {{MIRSMaster.PreparedPosition}}
              </h2>
            </div>
            <div class="recommend-sig">
            <h4>Recommended by:</h4>
            <h3 v-if="MIRSMaster.RecommendSignature!=null">
              <img :src="'/storage/signatures/'+MIRSMaster.RecommendSignature">
            </h3>
            <h3 v-else-if="MIRSMaster.ManagerReplacerSignature!=null">
              <h2>For :</h2>
              <img :src="'/storage/signatures/'+MIRSMaster.ManagerReplacerSignature">
            </h3>
            <h2>
             <span class="bold">{{MIRSMaster.Recommendedby}}
             <span class="opener-manager-replace">
               <div class="mini-menu-managers" v-if="user.Fname+' '+user.Lname==MIRSMaster.Preparedby&&this.ManagerBehalfActive==true">
                 <h1 v-if="MIRSMaster.ManagerReplacer==null">Request signature to</h1>
                 <h1 v-else>Request pending <i class="fa fa-clock-o color-white"></i></h1>
                 <div class="manager-list-menu"v-if="MIRSMaster.ManagerReplacer==null">
                   <select v-model="ManagerReplacerID">
                     <option :value="null">Choose a manager</option>
                     <option v-for="manager in allManager" :value="manager.id" v-if="manager.Fname+' '+manager.Lname!=MIRSMaster.Recommendedby">{{manager.Fname}} {{manager.Lname}}</option>
                   </select>
                   <p v-if="error!=null" class="color-red">*{{error}}</p>
                   <span class="send-cancel-btns">
                     <button type="button" v-on:click="ManagerBehalfActive=false">Cancel</button>
                     <button type="button" v-on:click="sendrequestReplacer()">Send</button>
                   </span>
                 </div>
                 <div class="manager-replacer-sent" v-else>
                   <p>Your request has been sent to<br> <span class="underline">{{MIRSMaster.ManagerReplacer}}</span></p>
                   <span class="cancel-manager-replace" v-on:click="cancelrequestReplacer()"><i class="fa fa-times color-red"></i>cancel</span>
                 </div>
               </div>
               <i class="color-blue" :class="[MIRSMaster.ManagerReplacer==null?'fa fa-users':'fa fa-clock-o']" v-on:click="ManagerBehalfActive=!ManagerBehalfActive,[allManager[0]==null?fetchAllManager():'']" v-if="(MIRSMaster.RecommendSignature==null)&&(MIRSMaster.Preparedby==user.Fname+' '+user.Lname)&&(MIRSMaster.ManagerReplacerSignature==null)"></i>
             </span>
             <i class="fa fa-times decliner" v-if="(MIRSMaster.IfDeclined==MIRSMaster.Recommendedby)"></i>
             </span><br>
               {{MIRSMaster.RecommendPosition}}
            </h2>
            </div>
          </div>
          <div class="gm-sig">
            <h4>APPROVED:</h4>
            <h3 v-if="(MIRSMaster.ApproveSignature!=null)">
              <img :src="'/storage/signatures/'+MIRSMaster.ApproveSignature">
            </h3>
            <h3 v-else-if="(MIRSMaster.ApprovalReplacerSignature!=null)">
              <p>For :</p><img :src="'/storage/signatures/'+MIRSMaster.ApprovalReplacerSignature">
            </h3>
            <h2>
            <span class="gm-info-box bold">
              {{MIRSMaster.Approvedby}}
              <i class="fa fa-times decliner" v-if="(MIRSMaster.IfDeclined==MIRSMaster.Approvedby)"></i>
            </span><br>
              {{MIRSMaster.ApprovePosition}}
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
          btndisable:false,
          ManagerBehalfActive:false,
          allManager:[],
          ManagerReplacerID:null,
          error:null,
          ApproveReplacerName:'',
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
         var vm=this;
         axios.put(`/MIRS-Signature/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchMIRSData();
      },
      DeclineMIRS()
      {
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
        axios.put(`/send-request-manager-replacer/`+this.mirsno.MIRSNo,{
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
        var vm=this;
        axios.put(`/cancel-request-manager-replacer/`+this.mirsno.MIRSNo).then(function(response)
        {
          Vue.set(vm.$data,'ManagerBehalfActive',false);
          console.log(response);
        });
        this.fetchMIRSData();
      },
      AcceptrequestReplacer()
      {
        var vm=this;
        axios.put(`/signature-replacer-accepted/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchMIRSData();
      },
      cancelRequestApprovalReplacer()
      {
        var vm=this;
        axios.put(`/cancel-request-approval/`+this.mirsno.MIRSNo).then(function(response)
        {
          console.log(response);
        })
        this.fetchMIRSData();
      },
      AcceptApprovalReplacerequest()
      {
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
  }
</script>
