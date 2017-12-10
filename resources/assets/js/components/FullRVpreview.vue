<template lang="html">
<div v-if="RVMaster.users!=null">
  <div class="RV-signature-print-container">
    <div class="print-and-unreceved" v-if="AlreadyApproved">
      <a :href="'/RV.pdf/'+rvno.RVNo"><button type="submit"  name="RVNo" value="RVNohere"><i class="material-icons">print</i></button></a>
      <li class="pending-delivery-number" v-if="((RVMaster.IfPurchased==null)&&(checkPO==null)&&(checkRR!=null))"><h1>pending item: <span class="color-blue">{{undeliveredTotal}}</span></h1></li>
    </div>
    <div v-else-if="RVMaster.SignatureTurn!='2'" class="empty-left">
    </div>
    <div v-if="((RVMaster.users[2].pivot.Signature==null)&&((RVMaster.users[1].pivot.Signature=='0')||(ManagerReplacerData!=null && ManagerReplacerData.pivot.Signature=='0')))" class="empty-left relative">
      <button v-on:click="RemarksIsActive=true" class="pending-remarks" type="button" v-if="((RVMaster.users[2].id==user.id)&&(pendingRemarksShow==null))"><i class="material-icons">access_time</i> remarks</button>
      <div v-if="(pendingRemarksShow!=null)&&(RVMaster.users[2].pivot.Signature==null)&&((user.id==RVMaster.users[0].id)||(user.id==RVMaster.users[2].id))" class="BudgetRemarkShow">
        <div class="remarks-box">
          <h1> budget officer: <i class="material-icons" v-on:click="RemovePendingRemarks()">close</i></h1>
          <p>{{pendingRemarksShow}}</p>
        </div>
      </div>
      <div class="pending-remarks-input" v-if="RemarksIsActive==true">
        <h1>Input pending remarks</h1>
        <textarea v-model="pendingremarks" name="name" maxlength="100" placeholder="max:(100characters)"></textarea>
        <span class="pending-remarks-btn">
          <button type="button" name="button" v-on:click="RemarksIsActive=false">cancel</button>
          <button type="button" name="button" v-on:click="PendingRemarksSubmit()">save</button>
        </span>
      </div>
    </div>
    <div class="manager-replacer-accept-cant Request-manager-replace" v-if="(ManagerReplacerData!=null && ManagerReplacerData.id==user.id && ManagerReplacerData.pivot.Signature==null)">
      <h6 class="approve-managerreplace-note"><i class="material-icons color-blue">info</i>
        <span class="color-blue">{{RVMaster.users[0].FullName}}</span> is asking for your signature b/c the {{RVMaster.users[1].Position}} is not available
      </h6>
      <span :class="{'hide':SignatureManagerReplacerHide}">
        <longpress class="rvapprovebtn waves-effect waves-light" duration="3" :on-confirm="signatureRequestManagerReplacer" :disabled="btndisabled" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">edit</i> Signature
        </longpress>
        <longpress class="RVdeclineBtn waves-effect waves-light" duration="3" :on-confirm="cancelRequestManagerReplacer" :disabled="btndisabled" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">close</i> I can't
        </longpress>
      </span>
    </div>
    <div class="Approve-replacer-accept-cant Request-manager-replace" v-if="((RVMaster.users[2].pivot.Signature=='0')&&(ApprovalReplacerData!=null)&&(user.id==ApprovalReplacerData.id)&&(ApprovalReplacerData.pivot.Signature==null))">
      <h6 class="approve-managerreplace-note"><i class="material-icons color-blue">info</i>
        <span class="color-blue">{{RVMaster.users[0].FullName}}</span> is asking for your signature b/c the General Manager is not available
      </h6>
      <span :class="{'hide':SignatureApprovalReplacerHide}">
        <longpress class="rvapprovebtn waves-effect waves-light" duration="3" :on-confirm="acceptApproveRequest" :disabled="approveBtnReplacer" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">edit</i> Signature
        </longpress>
        <longpress class="RVdeclineBtn waves-effect waves-light" duration="3" :on-confirm="cancelApprovalRequest" :disabled="approveBtnReplacer" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">close</i> I can't
        </longpress>
      </span>
    </div>
    <div class="declineOrSignatureBtn">
          <span class="RVsignatureBtns" :class="{'hide':SignatureRVBtnHide}" v-if="((RequisitionerCanSignature)||(RecommendedByCanSignature)||(BudgetOfficerCanSignature)||(GMCanSignature))">
            <div class="RVapprove">
              <longpress class="rvapprovebtn waves-effect waves-light" duration="3" :on-confirm="Signature" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
              <i class="material-icons">edit</i> Signature
              </longpress>
            </div>
            <div class="RVdecline">
              <longpress class="RVdeclineBtn waves-effect waves-light" duration="3" :on-confirm="declineRV" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
              <i class="material-icons">close</i> Decline
              </longpress>
            </div>
          </span>
      <span v-if="checkPO!=null">
        <div class="viewPObtn">
            <div class="status-po-wrapper" v-if="(RVMaster.IfPurchased!=null)">
              <h1>Status : <span class="underline">Already Purchased</span> <i class="material-icons">check</i></h1>
            </div>
            <div class="status-po-wrapper" v-else>
              <h1>Status: <span class="underline">Waiting for RR</span></h1>
            </div>
           <a :href="'/po-list-view-of-rv/'+rvno.RVNo"><button type="button" ><i class="material-icons">history</i> PO history</button></a>
        </div>
      </span>
      <span v-if="AlreadyApproved">
        <div class="status-po-wrapper" v-if="(RVMaster.IfPurchased==null)&&(checkPO==null)">
          <h1 class="no-PO">Status : <span class="underline">Waiting for RR</span></h1>
        </div>
        <div class="status-po-wrapper" v-if="((RVMaster.IfPurchased)&&(checkPO==null))">
          <h1 class="done-but-no-po">Status : <span class="underline">Already purchased <i class="material-icons">check</i> without P.O.</span></h1>
        </div>
          <div class="CreateRRwoPO" v-else-if="((RVMaster.IfPurchased==null)&&(checkPO==null)&&((user.Role==4)||(user.Role==3)))">
            <a :href="'/create-rr-wo-po/'+rvno.RVNo"><button type="button" > <i class="material-icons">add</i> RR</button></a>
          </div>
          <span v-if="checkPO==null&&checkRR!=null">
          </span>
          <span v-else>
            <div class="CanvasBtn" v-if="((user.Role==3)||(user.Role==4))&&(AlreadyApproved)">
                <a :href="'/CanvassCreate/'+rvno.RVNo"><button type="submit" ><i class="material-icons">store</i> Canvass</button></a>
            </div>
          </span>
          <div class="show-rr-of-rv" v-if="checkRR!=null">
            <a :href="'/rr-of-rv-list/'+rvno.RVNo"><button  type="button"><i class="material-icons">history</i> R.R. history</button></a>
          </div>
      </span>

    </div>
  </div>
  <div class="bondpaper-RV-container">
    <div class="bondpaper-RV">

          <div v-if="(RVMaster.Status=='0')" class="status-rv approved">
            <i class="material-icons">thumb_up</i>
            <h1>Approved</h1>
          </div>
          <div class="status-rv" v-else-if="(RVMaster.Status==null)">
            <i class="material-icons">access_time</i>
            <h1>Pending</h1>
          </div>
          <div class="status-rv declined" v-else-if="RVMaster.Status=='1'">
            <i class="material-icons">close</i>
            <h1>Declined</h1>
          </div>
      <!-- @endif -->
      <div class="top-rv-contents">
        <h5>BOHOL I ELECTRIC COOPERATIVE</h5>
        <h6>Cabulijan, Tubigon, Bohol</h6>
        <h4>REQUISITION VOUCHER</h4>
      </div>
      <div class="RVdate-RVNo-container">
        <ul>
          <li><label>RV No.</label><p>{{RVMaster.RVNo}}</p></li>
          <li><label>DATE:</label><p>{{RVMaster.RVDate}}</p></li>
        </ul>
      </div>
      <div class="to-gm-container">
        <p>TO: The General Manager</p>
        <div class="toGM-parag">
          <p>Please furnish the following Materials/Supplies for</p><h3>{{RVMaster.Purpose}}</h3>
        </div>
      </div>
      <div class="full-RVtable">
        <table>
          <tr>
            <th>Articles</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Remarks</th>
          </tr>
            <tr v-for="rvdata in RVDetails">
              <td>{{rvdata.Particulars}}</td>
              <td>{{rvdata.Unit}}</td>
              <td>{{rvdata.Quantity}}</td>
              <td>{{rvdata.Remarks}}</td>
            </tr>
        </table>
        <div class="certify-RV">
          <h3>I hereby certify that the above requested materials/supplies are necessary and will be used solely for the purpose stated above.</h3>
        </div>
        <div class="RVsignatures-container">
          <div class="top-signature-RV">
            <div class="RV-top-leftSignature">
              <h5>Requested by:</h5>
                <div class="requestRV-content">
                  <h6 v-if="RVMaster.users[0].pivot.Signature=='0'"><img :src="'/storage/signatures/'+RVMaster.users[0].Signature" alt="signature"></h6>
                  <p>
                    {{RVMaster.users[0].FullName}}
                    <i v-if="RVMaster.users[0].pivot.Signature=='1'" class="material-icons decliner">close</i>
                  </p>
                  <label>{{RVMaster.users[0].Position}}</label>
                </div>
            </div>
            <div class="RV-top-RightSignature">
              <h5>Recommended by:</h5>
              <div class="requestRV-content">
                <h6 v-if="RVMaster.users[1].pivot.Signature=='0'"><img :src="'/storage/signatures/'+RVMaster.users[1].Signature" alt="signature"></h6>
                <h6 v-else-if="ManagerReplacerData!=null && ManagerReplacerData.pivot.Signature=='0'"><h1>For :</h1><img :src="'/storage/signatures/'+ManagerReplacerData.Signature" alt="signature"></h6>
                <p>
                  {{RVMaster.users[1].FullName}}
                <span class="opener-manager-replace opener-icon">
                  <div class="mini-menu-managers" v-if="(user.id)==(RVMaster.users[0].id)&&(this.ManagerBehalfActive==true)&&((ManagerReplacerData==null)||(ManagerReplacerData.pivot.Signature==null))&&(RVMaster.users[1].pivot.Signature==null)">
                    <h1 v-if="ManagerReplacerData==null">Request signature to</h1>
                    <h1 v-else>Request pending <i class="material-icons color-white">access_time</i></h1>
                    <div class="manager-list-menu"v-if="ManagerReplacerData==null">
                      <select v-model="ManagerID">
                        <option :value="null">Choose a manager</option>
                        <option v-for="manager in activemanager"  v-if="manager.id!=RVMaster.users[1].id" :value="manager.id">{{manager.FullName}}</option>
                      </select>
                      <p v-if="error!=null" class="color-red">*{{error}}</p>
                      <span class="send-cancel-btns">
                        <button type="button" v-on:click="ManagerBehalfActive=false">Cancel</button>
                        <button type="button" v-on:click="sendRequestManagerReplacer()">Send</button>
                      </span>
                    </div>
                    <div class="manager-replacer-sent" v-else>
                      <p>Your request has been sent to<br> <span class="underline">{{ManagerReplacerData.FullName}}</span></p>
                      <span class="cancel-manager-replace" v-on:click="cancelRequestManagerReplacer()"><i class="material-icons color-red">close</i>cancel</span>
                    </div>
                  </div>
                  <i v-on:click="ManagerBehalfActive=!ManagerBehalfActive,[activemanager[0]!=null?'':fetchAllManager()]" class="material-icons color-blue" v-if="((user.id==RVMaster.users[0].id)&&((ManagerReplacerData==null)||(ManagerReplacerData.pivot.Signature==null))&&(RVMaster.users[1].pivot.Signature==null)&&(RVMaster.users[0].pivot.Signature=='0'))">people</i>
                </span>
                   <i v-if="RVMaster.users[1].pivot.Signature=='1'" class="material-icons decliner">close</i>
                </p>
                <label>{{RVMaster.users[1].Position}}</label>
              </div>
            </div>
          </div>
          <div class="bottom-RV-signatures">
            <div class="RVbottom-left-signature">
              <h6 v-if="RVMaster.users[2].pivot.Signature=='0'"><img :src="'/storage/signatures/'+RVMaster.users[2].Signature" alt="signature"></h6>
              <h3>BUDGET AVAILABLE ON THIS REQUEST</h3>
              <h4>
                <span class="rv-signature-form">
                  <input type="text"  v-model="BudgetAvail" v-if="(user.Role==7)&&(RVMaster.BudgetAvailable==null)&&(RVMaster.users[2].id==user.id)&&(RVMaster.users[2].pivot.Signature==null)&&((RVMaster.users[1].pivot.Signature=='0')||(ManagerReplacerData!=null && ManagerReplacerData.pivot.Signature=='0'))" class="forBudgetOfficerOnly">
                </span>
                  <span class="budget-from" v-if="(editbudgetActive==false)">{{RVMaster.BudgetAvailable}}</span>
                    <span class="form-edit-budget" v-if="((user.Role==7)&&(RVMaster.users[2].id==user.id)&&(RVMaster.users[2].pivot.Signature=='0')&&(RVMaster.users[3].pivot.Signature==null))">
                      <span v-if="editbudgetActive==true" class="flex">
                        <input type="text" class="editbudget-input" v-model="BudgetUpdate=RVMaster.BudgetAvailable">
                        <span class="update-budget-btn">
                          <button class="editbudget" type="submit" v-on:click="UpdateBudget(),editbudgetActive=false"><i class="material-icons">check</i></button>
                          <button type="button" class="editbudget cancel-edit" v-on:click="editbudgetActive=false,fetchData();"><i class="material-icons">close</i></button>
                        </span>
                      </span>
                      <button type="button" v-on:click="editbudgetActive=true" class="edit-budget-opener" v-if="editbudgetActive==false"><i class="fa fa-pencil-square-o"></i></button>
                    </span>
              </h4>
              <p>
                  {{RVMaster.users[2].FullName}}
                  <i v-if="RVMaster.users[2].pivot.Signature=='1'" class="material-icons decliner">close</i>
              </p>
              <label>{{RVMaster.users[2].Position}}</label>
            </div>
            <div class="RVbottom-right-signature">
              <h3>Approved:</h3>
              <div class="requestRV-content">
                <h6 v-if="RVMaster.users[3].pivot.Signature=='0'"><img :src="'/storage/signatures/'+RVMaster.users[3].Signature" alt="signature"></h6>
                <h6 v-else-if="((ApprovalReplacerData!=null) && (ApprovalReplacerData.pivot.Signature=='0'))"><h2>For :</h2><img :src="'/storage/signatures/'+ApprovalReplacerData.Signature" alt="signature"></h6>
                <p>
                  {{RVMaster.users[3].FullName}}
                  <i v-if="RVMaster.users[3].pivot.Signature=='1'" class="material-icons decliner">close</i>
                </p>
                <label>{{RVMaster.users[3].Position}}</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</template>
<script>
import axios from 'axios';
import VueNumeric from 'vue-numeric';
import Longpress from 'vue-longpress';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
Vue.use(VueNumeric);
  export default {
     data () {
        return {
          RVMaster:[],
          RVDetails:[],
          checkPO:null,
          checkRR:null,
          undeliveredTotal:null,
          BudgetAvail:'',
          btndisabled:false,
          editbudgetActive:false,
          ManagerBehalfActive:false,
          activemanager:[],
          ManagerID:null,
          error:null,
          RemarksIsActive:false,
          pendingremarks:'',
          pendingRemarksShow:'',
          drop:false,
          approveBtnReplacer:false,
          BudgetUpdate:'',
          SignatureRVBtnHide:false,
          SignatureManagerReplacerHide:false,
          SignatureApprovalReplacerHide:false,
        }
      },
     props: ['rvno','user'],
     methods: {
       fetchData()
       {
         var vm=this;
         axios.get(`/rv-full-preview-fetch/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'RVMaster',response.data.RVMaster[0])
          Vue.set(vm.$data,'RVDetails',response.data.RVDetails)
          Vue.set(vm.$data,'checkPO',response.data.checkPO)
          Vue.set(vm.$data,'checkRR',response.data.checkRR)
          Vue.set(vm.$data,'undeliveredTotal',response.data.undeliveredTotal)
        })
      },
      Signature()
      {
        this.$loading('Signaturing...');
        this.SignatureRVBtnHide=true;
        var vm=this;
        axios.put(`/RVsignature/`+this.rvno.RVNo,{
          BudgetAvailable:this.BudgetAvail,
        }).then(function(response)
        {
          console.log(response);
          vm.fetchData();
          vm.$loading.close();
        });
      },
      declineRV()
      {
        this.$loading('Declining');
        this.SignatureRVBtnHide=true;
        var vm=this;
        axios.put(`/declineRV/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
          vm.fetchData();
          vm.$loading.close();
        });
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      },
      UpdateBudget()
      {
        this.$loading('updating...');
        var vm=this;
        axios.put(`/update-budget/`+this.rvno.RVNo,{
          BudgetUpdate:this.BudgetUpdate,
        }).then(function(response)
        {
          console.log(response);
          vm.fetchData();
          vm.$loading.close();
          vm.$toast.top('Budget updated');
        })
      },
      fetchAllManager()
      {
        var vm=this;
        axios.get(`/fetchAllManagerRV`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'activemanager',response.data);
          vm.fetchData();
        });
      },
      sendRequestManagerReplacer()
      {
        this.$loading('Sending...');
        var vm=this;
        axios.put(`/send-request-to-manager-replacer/`+this.rvno.RVNo,{
          ManagerID:this.ManagerID,
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error!=null)
          {
            Vue.set(vm.$data,'error',response.data.error);
            vm.$loading.close();
          }else
          {
            vm.fetchAllManager();
            vm.$loading.close();
            vm.$toast.top('Request sent.');
          }
        });
      },
      cancelRequestManagerReplacer()
      {
        this.$loading('Canceling...');
        this.SignatureManagerReplacerHide=true;
        var vm=this;
        axios.put(`/cancelrequestsentReplacer/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
          vm.fetchAllManager();
          vm.$loading.close();
        })
      },
      signatureRequestManagerReplacer()
      {
        this.$loading('Signaturing...');
        this.SignatureManagerReplacerHide=true;
        var vm=this;
        axios.put(`/AcceptManagerReplacer/`+this.rvno.RVNo).then(function(response)
        {
          vm.fetchData();
          vm.$loading.close();
        });
      },
      PendingRemarksSubmit()
      {
        this.$loading('Saving remarks...');
        var vm=this;
        axios.put(`/save-budget-officer-pending-remarks/`+this.rvno.RVNo,{PendingRemarks:this.pendingremarks}).then(function(response)
        {
          console.log(response);
          vm.displayRemarks();
          Vue.set(vm.$data,'RemarksIsActive',false);
          vm.$loading.close();
        })
      },
      RemovePendingRemarks()
      {
        this.$loading('Removing remarks...');
        var vm=this;
        axios.put(`/budget-officer-pending-remarks/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
          vm.displayRemarks();
          vm.$loading.close();
        });
      },
      displayRemarks()
      {
        var vm=this;
        axios.get(`/show-budget-officer-pending-remarks/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'pendingRemarksShow',response.data[0].PendingRemarks)
        })
      },
      cancelApprovalRequest()
      {
        this.$loading('Please wait...');
        this.SignatureApprovalReplacerHide=true;
        var vm=this;
        axios.put(`/rv-signature-in-behalf-cancel/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
          vm.fetchData();
          vm.$loading.close();
        })
      },
      acceptApproveRequest()
      {
        this.$loading('Signaturing...');
        this.SignatureApprovalReplacerHide=true;
        var vm=this;
        axios.put(`/rv-approve-behalf-accept/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
          vm.fetchData();
          vm.$loading.close();
        });
      }
     },
     mounted () {
       this.fetchData();
       this.displayRemarks();
     },
     components: {
        Longpress
     },
     computed: {
       ManagerReplacerData: function()
       {
         if ((this.RVMaster.users[4]!=null)&&(this.RVMaster.users[4].pivot.SignatureType=='ManagerReplacer'))
         {
           return this.RVMaster.users[4];
         }else if ((this.RVMaster.users[5]!=null)&&(this.RVMaster.users[5].pivot.SignatureType=='ManagerReplacer'))
         {
           return this.RVMaster.users[5];
         }else
         {
           return null;
         }
       },
       ApprovalReplacerData: function()
       {
         if ((this.RVMaster.users[4]!=null)&&(this.RVMaster.users[4].pivot.SignatureType=='ApprovalReplacer'))
         {
           return this.RVMaster.users[4];
         }else if ((this.RVMaster.users[5]!=null)&&(this.RVMaster.users[5].pivot.SignatureType=='ApprovalReplacer'))
         {
           return this.RVMaster.users[5];
         }else
         {
           return null;
         }
       },
       AlreadyApproved: function()
       {
         if ((this.RVMaster.users[0].pivot.Signature=='0')&&((this.RVMaster.users[1].pivot.Signature=='0')||(this.ManagerReplacerData!=null && this.ManagerReplacerData.pivot.Signature=='0'))&&(this.RVMaster.users[2].pivot.Signature=='0')&&((this.RVMaster.users[3].pivot.Signature=='0')||(this.ApprovalReplacerData!=null && this.ApprovalReplacerData.pivot.Signature=='0')))
         {
           return true;
         }else
         {
           return false;
         }
       },
       RequisitionerCanSignature: function()
       {
         if ((this.RVMaster.users[0].pivot.Signature==null && this.RVMaster.users[0].id == this.user.id))
         {
           return true;
         }else
         {
           return false;
         }
       },
       RecommendedByCanSignature: function()
       {
         if ((this.RVMaster.users[0].pivot.Signature=='0')&&(this.RVMaster.users[1].pivot.Signature==null)&&(this.user.id==this.RVMaster.users[1].id)&&((this.ManagerReplacerData==null)||(this.ManagerReplacerData.pivot.Signature==null)))
         {
           return true;
         }else
         {
           return false;
         }
       },
       BudgetOfficerCanSignature: function()
       {
         if ((this.RVMaster.users[2].pivot.Signature==null)&&(this.user.id== this.RVMaster.users[2].id)&&((this.RVMaster.users[1].pivot.Signature=='0')||(this.ManagerReplacerData!=null)&&(this.ManagerReplacerData.pivot.Signature=='0')))
         {
           return true;
         }else
         {
           return false;
         }
       },
       GMCanSignature: function()
       {
         if ((this.RVMaster.users[2].pivot.Signature=='0') && (this.RVMaster.users[3].pivot.Signature==null)  && (this.user.id== this.RVMaster.users[3].id) && ((this.ApprovalReplacerData==null)||(this.ApprovalReplacerData.pivot.Signature==null)))
         {
           return true;
         }else
         {
           return false;
         }
       }
     },
  }
</script>
