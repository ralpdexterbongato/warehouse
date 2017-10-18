<template lang="html">
<div class="">
  <div class="RV-signature-print-container">
    <div class="print-and-unreceved" v-if="(((RVMaster.RequisitionerSignature!=null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.BudgetOfficerSignature!=null)&&(RVMaster.GeneralManagerSignature!=null))||((RVMaster.RequisitionerSignature!=null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.BudgetOfficerSignature!=null)&&(RVMaster.ApprovalReplacerSignature!=null)))">
      <a :href="'/RV.pdf/'+rvno.RVNo"><button type="submit" class="bttn-unite bttn-sm bttn-primary" name="RVNo" value="RVNohere"><i class="fa fa-print"></i> Print</button></a>
      <li class="pending-delivery-number" v-if="((RVMaster.IfPurchased==null)&&(checkPO==null)&&(checkRR!=null))"><h1>pending item: <span class="color-blue">{{undeliveredTotal}}</span></h1></li>
    </div>
    <div v-else-if="user.Fname+' '+user.Lname!=RVMaster.BudgetOfficer" class="empty-left">
    </div>
    <div v-if="((RVMaster.BudgetOfficerSignature==null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null)))" class="empty-left relative">
      <button v-on:click="RemarksIsActive=true" class="bttn-unite bttn-sm bttn-primary pending-remarks" type="button" v-if="((RVMaster.BudgetOfficer==user.Fname+' '+user.Lname)&&(pendingRemarksShow==null))"><i class="fa fa-clock-o"></i> remarks</button>
      <div v-if="(pendingRemarksShow!=null)&&(RVMaster.BudgetOfficerSignature==null)&&((user.Fname+' '+user.Lname==RVMaster.Requisitioner)||(user.Fname+' '+user.Lname==RVMaster.BudgetOfficer))" class="BudgetRemarkShow">
        <div class="remarks-box animated" :class="{'hinge':drop}">
          <h1> budget officer: <i class="fa fa-thumb-tack animated" v-on:click="drop=true"></i></h1>
          <p>{{pendingRemarksShow}}</p>
        </div>
      </div>
      <div class="pending-remarks-input" v-if="RemarksIsActive==true">
        <h1>Input pending remarks</h1>
        <textarea v-model="pendingremarks" name="name" rows="4" cols="30" maxlength="100" placeholder="max:(100characters)"></textarea>
        <span class="pending-remarks-btn">
          <button type="button" name="button" v-on:click="RemarksIsActive=false">cancel</button>
          <button type="button" name="button" v-on:click="PendingRemarksSubmit()">save</button>
        </span>
      </div>
    </div>
    <div class="manager-replacer-accept-cant Request-manager-replace" v-if="((user.Fname+' '+user.Lname==RVMaster.ManagerReplacer)&&(RVMaster.ManagerReplacerSignature==null))">
      <h6 class="approve-managerreplace-note"><i class="fa fa-info-circle color-blue"></i>
        <span class="color-blue">{{RVMaster.Requisitioner}}</span> is asking for your signature b/c the {{RVMaster.RecommendedbyPosition}} is not available
      </h6>
      <span :class="{'hide':SignatureManagerReplacerHide}">
        <longpress class="rvapprovebtn" duration="3" :on-confirm="signatureRequestManagerReplacer" :disabled="btndisabled" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="fa fa-pencil"></i> Signature
        </longpress>
        <longpress class="RVdeclineBtn" duration="3" :on-confirm="cancelRequestManagerReplacer" :disabled="btndisabled" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="fa fa-times"></i> I can't
        </longpress>
      </span>
    </div>
    <div class="Approve-replacer-accept-cant Request-manager-replace" v-if="((RVMaster.BudgetOfficerSignature!=null)&&(user.Fname+' '+user.Lname==RVMaster.ApprovalReplacer)&&(RVMaster.ApprovalReplacerSignature==null)&&((RVMaster.GeneralManagerSignature==null)&&((RVMaster.ManagerReplacerSignature!=null)||(RVMaster.RecommendedbySignature!=null))))">
      <h6 class="approve-managerreplace-note"><i class="fa fa-info-circle color-blue"></i>
        <span class="color-blue">{{RVMaster.Requisitioner}}</span> is asking for your signature b/c the General Manager is not available
      </h6>
      <span :class="{'hide':SignatureApprovalReplacerHide}">
        <longpress class="rvapprovebtn" duration="3" :on-confirm="acceptApproveRequest" :disabled="approveBtnReplacer" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="fa fa-pencil"></i> Signature
        </longpress>
        <longpress class="RVdeclineBtn" duration="3" :on-confirm="cancelApprovalRequest" :disabled="approveBtnReplacer" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="fa fa-times"></i> I can't
        </longpress>
      </span>
    </div>
    <div class="declineOrSignatureBtn">
          <span :class="{'hide':SignatureRVBtnHide}" v-if="(((RVMaster.BudgetOfficer==user.Fname+' '+user.Lname)&&(RVMaster.BudgetOfficerSignature==null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.IfDeclined==null))||((RVMaster.IfDeclined==null)&&(RVMaster.Recommendedby==user.Fname+' '+user.Lname)&&(RVMaster.RecommendedbySignature==null)&&(RVMaster.ManagerReplacerSignature==null))||((RVMaster.IfDeclined==null)&&(RVMaster.GeneralManager==user.Fname+' '+user.Lname)&&(RVMaster.GeneralManagerSignature==null)&&(RVMaster.ApprovalReplacerSignature==null)&&(RVMaster.BudgetOfficerSignature!=null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))))">
            <div class="RVapprove">
              <longpress class="rvapprovebtn" duration="3" :on-confirm="Signature" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
              <i class="fa fa-pencil"></i> Signature
              </longpress>
            </div>
            <div class="RVdecline">
              <longpress class="RVdeclineBtn" duration="3" :on-confirm="declineRV" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
              <i class="fa fa-times"></i> Decline
              </longpress>
            </div>
          </span>
      <span v-if="checkPO!=null">
        <div class="viewPObtn">
            <div class="status-po-wrapper" v-if="(RVMaster.IfPurchased!=null)">
              <h1>Status : <span class="underline">Already Purchased</span> <i class="fa fa-check"></i></h1>
            </div>
            <div class="status-po-wrapper" v-else>
              <h1>Status: <span class="underline">Waiting for RR</span></h1>
            </div>
           <a :href="'/po-list-view-of-rv/'+rvno.RVNo"><button type="button" class="bttn-unite bttn-sm bttn-primary">Show P.O.</button></a>
        </div>
      </span>
      <span v-if="((((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.BudgetOfficerSignature!=null)&&((RVMaster.GeneralManagerSignature!=null)||RVMaster.ApprovalReplacerSignature!=null)))">
        <div class="status-po-wrapper" v-if="(RVMaster.IfPurchased==null)&&(checkPO==null)">
          <h1 class="no-PO">Status : <span class="underline">Waiting for RR</span></h1>
        </div>
        <div class="status-po-wrapper" v-if="((RVMaster.IfPurchased)&&(checkPO==null))">
          <h1 class="done-but-no-po">Status : <span class="underline">Already purchased <i class="fa fa-check"></i> without P.O.</span></h1>
        </div>
          <div class="CreateRRwoPO" v-else-if="((RVMaster.IfPurchased==null)&&(checkPO==null)&&((user.Role==4)||(user.Role==3)))">
            <a :href="'/create-rr-wo-po/'+rvno.RVNo"><button type="button" class="bttn-unite bttn-sm bttn-primary"> <i class="fa fa-plus"></i> RR</button></a>
          </div>
          <span v-if="checkPO==null&&checkRR!=null">
          </span>
          <span v-else>
            <div class="CanvasBtn" v-if="(((RVMaster.RequisitionerSignature!=null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.BudgetOfficerSignature!=null)&&(RVMaster.GeneralManagerSignature!=null)&&(user.Role==4)&&(RVMaster.IfPurchased==null))||((RVMaster.RequisitionerSignature!=null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.BudgetOfficerSignature!=null)&&(RVMaster.ApprovalReplacerSignature!=null)&&(user.Role==4)&&(RVMaster.IfPurchased==null))||((RVMaster.RequisitionerSignature!=null)&&((RVMaster.RecommendedbySignature!=null)||RVMaster.ManagerReplacerSignature!=null)&&(RVMaster.BudgetOfficerSignature!=null)&&(RVMaster.GeneralManagerSignature!=null)&&(user.Role==3)&&(RVMaster.IfPurchased==null))||((RVMaster.RequisitionerSignature!=null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.BudgetOfficerSignature!=null)&&(RVMaster.ApprovalReplacerSignature!=null)&&(user.Role==3)&&(RVMaster.IfPurchased==null)))">
                <a :href="'/CanvassCreate/'+rvno.RVNo"><button type="submit" class="bttn-unite bttn-sm bttn-primary"><i class="fa fa-building"></i> Canvass</button></a>
            </div>
          </span>
          <div class="show-rr-of-rv" v-if="checkRR!=null">
            <a :href="'/rr-of-rv-list/'+rvno.RVNo"><button class="bttn-unite bttn-sm bttn-primary" type="button">R.R. history</button></a>
          </div>
      </span>

    </div>
  </div>
  <ul class="error-tab" v-if="laravelerrors!=''" @click="laravelerrors=[]">
    <span v-for="errors in laravelerrors">
      <li v-for="error in errors">{{error}}</li>
    </span>
  </ul>
  <div class="bondpaper-RV-container">
    <div class="bondpaper-RV">

          <div v-if="((RVMaster.IfDeclined==null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))&&(RVMaster.BudgetOfficerSignature!=null)&&((RVMaster.GeneralManagerSignature!=null)||(RVMaster.ApprovalReplacerSignature!=null)))" class="status-rv approved">
            <i class="fa fa-thumbs-up"></i>
            <h1>Approved</h1>
          </div>
          <div class="status-rv" v-else-if="RVMaster.IfDeclined==null">
            <i class="fa fa-clock-o"></i>
            <h1>Pending</h1>
          </div>
          <div class="status-rv declined" v-else>
            <i class="fa fa-times"></i>
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
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>.</td>
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
                  <h6 v-if="RVMaster.RequisitionerSignature!=null"><img :src="'/storage/signatures/'+RVMaster.RequisitionerSignature" alt="signature"></h6>
                  <p>
                    {{RVMaster.Requisitioner}}
                    <i v-if="(RVMaster.Requisitioner==RVMaster.IfDeclined)" class="fa fa-times decliner"></i>
                  </p>
                  <label>{{RVMaster.RequisitionerPosition}}</label>
                </div>
            </div>
            <div class="RV-top-RightSignature">
              <h5>Recommended by:</h5>
              <div class="requestRV-content">
                <h6 v-if="RVMaster.RecommendedbySignature!=null"><img :src="'/storage/signatures/'+RVMaster.RecommendedbySignature" alt="signature"></h6>
                <h6 v-else-if="RVMaster.ManagerReplacerSignature!=null"><h1>For :</h1><img :src="'/storage/signatures/'+RVMaster.ManagerReplacerSignature" alt="signature"></h6>
                <p>
                  {{RVMaster.Recommendedby}}
                <span class="opener-manager-replace opener-icon">
                  <div class="mini-menu-managers" v-if="(user.Fname+' '+user.Lname)==(RVMaster.Requisitioner)&&(this.ManagerBehalfActive==true)&&(RVMaster.ManagerReplacerSignature==null)&&(RVMaster.RecommendedbySignature==null)">
                    <h1 v-if="RVMaster.ManagerReplacer==null">Request signature to</h1>
                    <h1 v-else>Request pending <i class="fa fa-clock-o color-white"></i></h1>
                    <div class="manager-list-menu"v-if="RVMaster.ManagerReplacer==null">
                      <select v-model="ManagerID">
                        <option :value="null">Choose a manager</option>
                        <option v-for="manager in activemanager"  v-if="manager.Fname+' '+manager.Lname!=RVMaster.Recommendedby" :value="manager.id">{{manager.Fname}} {{manager.Lname}}</option>
                      </select>
                      <p v-if="error!=null" class="color-red">*{{error}}</p>
                      <span class="send-cancel-btns">
                        <button type="button" v-on:click="ManagerBehalfActive=false">Cancel</button>
                        <button type="button" v-on:click="sendRequestManagerReplacer()">Send</button>
                      </span>
                    </div>
                    <div class="manager-replacer-sent" v-else>
                      <p>Your request has been sent to<br> <span class="underline">{{RVMaster.ManagerReplacer}}</span></p>
                      <span class="cancel-manager-replace" v-on:click="cancelRequestManagerReplacer()"><i class="fa fa-times color-red"></i>cancel</span>
                    </div>
                  </div>
                  <i v-on:click="ManagerBehalfActive=!ManagerBehalfActive,[activemanager[0]!=null?'':fetchAllManager()]" class="fa fa-users color-blue" v-if="(user.Fname+' '+user.Lname==RVMaster.Requisitioner)&&(RVMaster.ManagerReplacerSignature==null)&&(RVMaster.RecommendedbySignature==null)"></i>
                </span>
                   <i v-if="RVMaster.Recommendedby==RVMaster.IfDeclined" class="fa fa-times decliner"></i>
                </p>
                <label>{{RVMaster.RecommendedbyPosition}}</label>
              </div>
            </div>
          </div>
          <div class="bottom-RV-signatures">
            <div class="RVbottom-left-signature">
              <h6 v-if="RVMaster.BudgetOfficerSignature!=null"><img :src="'/storage/signatures/'+RVMaster.BudgetOfficerSignature" alt="signature"></h6>
              <h3>BUDGET AVAILABLE ON THIS REQUEST</h3>
              <h4>
                <span class="rv-signature-form">
                  <input type="text"  v-model="BudgetAvail" v-if="(user.Role==7)&&(RVMaster.BudgetAvailable==null)&&(RVMaster.BudgetOfficer==user.Fname+' '+user.Lname)&&(RVMaster.BudgetOfficerSignature==null)&&((RVMaster.RecommendedbySignature!=null)||(RVMaster.ManagerReplacerSignature!=null))" class="forBudgetOfficerOnly">
                </span>
                  <span class="budget-from" v-if="(editbudgetActive==false)">{{RVMaster.BudgetAvailable}}</span>
                    <span class="form-edit-budget" v-if="((user.Role==7)&&(RVMaster.BudgetOfficer==user.Fname+' '+user.Lname)&&(RVMaster.BudgetOfficerSignature!=null)&&((RVMaster.RecommendedbySignature==null)||(RVMaster.GeneralManagerSignature==null)))">
                      <span v-if="editbudgetActive==true" class="flex">
                        <input type="text" class="editbudget-input" v-model="BudgetUpdate=RVMaster.BudgetAvailable">
                        <span class="update-budget-btn">
                          <button class="editbudget" type="submit" v-on:click="UpdateBudget(),editbudgetActive=false"><i class="fa fa-check"></i></button>
                          <button type="button" class="editbudget cancel-edit" v-on:click="editbudgetActive=false,fetchData();"><i class="fa fa-times"></i></i></button>
                        </span>
                      </span>
                      <button type="button" v-on:click="editbudgetActive=true" class="edit-budget-opener" v-if="editbudgetActive==false"><i class="fa fa-pencil-square-o"></i></button>
                    </span>
              </h4>
              <p>
                  {{RVMaster.BudgetOfficer}}
                  <i v-if="(RVMaster.BudgetOfficer==RVMaster.IfDeclined)" class="fa fa-times decliner"></i>
              </p>
              <label>Budget Officer</label>
            </div>
            <div class="RVbottom-right-signature">
              <h3>Approved:</h3>
              <div class="requestRV-content">
                <h6 v-if="RVMaster.GeneralManagerSignature!=null"><img :src="'/storage/signatures/'+RVMaster.GeneralManagerSignature" alt="signature"></h6>
                <h6 v-else-if="RVMaster.ApprovalReplacerSignature!=null"><h2>For :</h2><img :src="'/storage/signatures/'+RVMaster.ApprovalReplacerSignature" alt="signature"></h6>
                <p>
                  {{RVMaster.GeneralManager}}
                  <i v-if="RVMaster.GeneralManager==RVMaster.IfDeclined" class="fa fa-times decliner"></i>
                </p>
                <label>General Manager</label>
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
          laravelerrors:[],
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
        this.SignatureRVBtnHide=true;
        var vm=this;
        axios.put(`/RVsignature/`+this.rvno.RVNo,{
          BudgetAvailable:this.BudgetAvail,
        }).then(function(response)
        {
          console.log(response);
        },function(error)
        {
          console.log(error);
          Vue.set(vm.$data,'laravelerrors',error.response.data)
        });
        this.fetchData();
      },
      declineRV()
      {
        this.SignatureRVBtnHide=true;
        var vm=this;
        axios.put(`/declineRV/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchData();
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      },
      UpdateBudget()
      {
        var vm=this;
        axios.put(`/update-budget/`+this.rvno.RVNo,{
          BudgetUpdate:this.BudgetUpdate,
        }).then(function(response)
        {
          console.log(response);

        })
        this.fetchData();
      },
      ApproveInBehalf()
      {
        var vm=this;
        axios.put(`/rv-signature-in-behalf/`+this.rvno.RVNo);
        this.fetchData();
      },
      ApproveInBehalfCanceled()
      {
        var vm=this;
        axios.put(`/rv-signature-in-behalf-cancel/`+this.rvno.RVNo);
        this.fetchData();
      },
      fetchAllManager()
      {
        var vm=this;
        axios.get(`/fetchAllManagerRV`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'activemanager',response.data);
        });
        this.fetchData();
      },
      sendRequestManagerReplacer()
      {
        var vm=this;
        axios.put(`/send-request-to-manager-replacer/`+this.rvno.RVNo,{
          ManagerID:this.ManagerID,
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error!=null)
          {
            Vue.set(vm.$data,'error',response.data.error);
          }
        });
        this.fetchAllManager();
      },
      cancelRequestManagerReplacer()
      {
        this.SignatureManagerReplacerHide=true;
        var vm=this;
        axios.put(`/cancelrequestsentReplacer/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
        })
        this.fetchAllManager();
      },
      signatureRequestManagerReplacer()
      {
        this.SignatureManagerReplacerHide=true;
        var vm=this;
        axios.put(`/AcceptManagerReplacer/`+this.rvno.RVNo);
        this.fetchData();
      },
      PendingRemarksSubmit()
      {
        var vm=this;
        axios.put(`/save-budget-officer-pending-remarks/`+this.rvno.RVNo,{PendingRemarks:this.pendingremarks}).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'RemarksIsActive',false);
        })
        this.displayRemarks()
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
        this.SignatureApprovalReplacerHide=true;
        var vm=this;
        axios.put(`/rv-signature-in-behalf-cancel/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
        })
        this.fetchData();
      },
      acceptApproveRequest()
      {
        this.SignatureApprovalReplacerHide=true;
        var vm=this;
        axios.put(`/rv-approve-behalf-accept/`+this.rvno.RVNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchData();
      }
     },
     mounted () {
       this.fetchData();
       this.displayRemarks();
     },
     components: {
        Longpress
      },
  }
</script>
