<template lang="html">
<div class="print-MCT-wrap" v-if="(this.MCTMaster.users!=null)">
  <div class="reversed-alert">
    <p v-if="MCTMaster.IsRollBack==0"><i class="material-icons">warning</i>Invalid transaction</p>
  </div>
  <div class="MCT-title">
    <span v-if="AlreadySignatured">
      <form action="/MCT.pdf" method="get" class="mct-print-form">
        <button v-if="MCTMaster.CreatorID==user.id && MCTMaster.IsRollBack!=0" type="submit" :value="this.mctno.MCTNo" name="MCTNo"><i class="material-icons">print</i></button>
        <button v-if="(MCTMaster.IsRollBack==null || MCTMaster.IsRollBack==1)&&(user.Role==1 && MCTMaster.Status=='0')" v-on:click="rollbackMCT()" type="button" class="undo-btn" name="button"><i class="material-icons">replay</i> reverse</button>
        <button v-if="(MCTMaster.IsRollBack==0 && user.Role==1 && MCTMaster.Status=='0')" v-on:click="undoRollbackMCT()"  type="button" class="undo-btn" name="button"><i class="material-icons">refresh</i> Undo reverse</button>
      </form>
    </span>
    <div class="empty-div-left mct-edit-container" v-if="(user.id==MCTMaster.users[0].id)&&(MCTMaster.users[1].pivot.Signature==null)&&(MCTMaster.users[0].pivot.Signature!='1')">
      <span class="edit-mct" :class="ShowEdit==true?'hide':'show'" v-on:click="ShowEdit=true"><i class="material-icons">edit</i>Edit</span>
      <span class="edit-mct" :class="ShowEdit==false?'hide':'show'">
        <span class="color-blue">Save?</span>
        <button type="button" v-on:click="ShowEdit=false,fetchData();">cancel</button>
        <button v-on:click="ShowEdit=false,editMCTSave()" type="button" name="button">Save</button>
        </span>
    </div>
    <span v-else>
    </span>
    <span v-if="AlreadySignatured">
      <span v-if="(((MRTCheck==null)&&(user.Role==4)&&(MCTMaster.IsRollBack != 0))||((MRTCheck==null)&&(user.Role==3)&&(MCTMaster.IsRollBack != 0)))">
        <div class="Create-MRT-btn">
          <a :href="'/MRT-create/'+this.mctno.MCTNo"><button type="submit"><i class="material-icons">add</i> MRT</button></a>
        </div>
      </span>
      <span v-else-if="MRTCheck!=null">
        <div class="View-MRT-btn">
          <div class="mrt-done">
           <a :href="'/mrt-preview-page/'+MRTCheck"><button type="submit">MRT</button></a>
          </div>
        </div>
      </span>
      <span v-else>
        No MRT generated yet
      </span>
    </span>
    <span v-else-if="((MCTMaster.users[0].pivot.Signature=='0')&&(MCTMaster.users[1].id==user.id)&&(MCTMaster.users[1].pivot.Signature==null)||(MCTMaster.users[0].id==user.id)&&(MCTMaster.users[0].pivot.Signature==null))">
      <div class="signature-mct-btn" :class="{'hide':SignatureMCTBtnHide}">
        <longpress id="signatureMCT" class="waves-effect waves-light" duration="3" :on-confirm="signatureMCT" :disabled="IsDisabled" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">edit</i> Signature
        </longpress>
        <longpress id="declineMCT" class="waves-effect waves-light" duration="3" :on-confirm="declineMCT" :disabled="IsDisabled" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">close</i> Decline
        </longpress>
      </div>
    </span>
  </div>
    <div class="bondpaper-preview">
    <div class="bond-center-titles">
      <h1>BOHOL I ELECTRIC COOPERATIVE, INC.</h1>
      <h5>Cabulijan, Tubigon, Bohol</h5>
      <h2>MATERIALS CHARGE TICKET</h2>
      <img src="/DesignIMG/logo.png" alt="logo">
    </div>
    <div class="MCTMaster-details">
      <div class="MCTmaster-left">
        <ul>
          <li><label>Particulars :</label><h2>{{MCTMaster.Particulars}}</h2></li>
          <li>
            <label>Address :</label>
            <h2 :class="ShowEdit==true?'hide':'true'">{{MCTMaster.AddressTo}}</h2>
            <h2 :class="ShowEdit==true?'show':'hide'">
              <input type="text" v-model="AddressToEdit=MCTMaster.AddressTo">
            </h2>
          </li>
        </ul>
      </div>
      <div class="MCTmaster-right">
        <ul>
          <li><label>MIRS No :</label><h2>{{MCTMaster.MIRSNo}}</h2></li>
          <li><label>MCT Date : </label><h2>{{MCTMaster.MCTDate}}</h2></li>
          <li><label>MCT No.:</label><h2>{{MCTMaster.MCTNo}}</h2></li>
        </ul>
      </div>
    </div>
    <div class="MCTpreview">
      <table>
        <tr>
          <th>AcctCode</th>
          <th>Item Code</th>
          <th>Description</th>
          <th>Unit Cost</th>
          <th>Quantity</th>
          <th>Amount</th>
          <th>Unit</th>
        </tr>
        <tr v-for="(mctconfirm, count) in MCTConfirmDetails">
          <td>{{mctconfirm.AccountCode}}</td>
          <td>{{mctconfirm.ItemCode}}</td>
          <td class="align-left">{{mctconfirm.Description}}</td>
          <td>{{formatPrice(mctconfirm.UnitCost)}}</td>
          <td><span :class="ShowEdit==true?'hide':'show'">{{mctconfirm.Quantity}}</span><input type="text" v-model="QuantityArray[count]=mctconfirm.Quantity" :class="ShowEdit==true?'show':'hide'"></td>
          <td>{{formatPrice(mctconfirm.Amount)}}</td>
          <td>{{mctconfirm.Unit}}</td>
        </tr>
      </table>
    </div>
    <div class="totalcost-mct">
      <ul>
        <li v-for="accountcode in AccountCodeGroup"><label>{{accountcode.AccountCode}}</label><h1>{{formatPrice(accountcode.totals)}}</h1></li>
      </ul>
      <div class="total-result">
        <h1>TOTAL</h1><h2>{{formatPrice(TotalSum)}}</h2>
      </div>
    </div>
    <div class="signatures-mct-preview">
      <div class="mct-signature-left">
        <div class="issuedby-label">
          Issued by:
        </div>
        <div class="issuedby-data">
          <div class="signature-issuedmct">
              <img :src="'/storage/signatures/'+MCTMaster.users[0].Signature" v-if="MCTMaster.users[0].pivot.Signature=='0'" alt="signature">
          </div>
          <h1>{{MCTMaster.users[0].FullName}}<i v-if="MCTMaster.users[0].pivot.Signature=='1'" class="material-icons decliner">close</i></h1>
          <h5>{{MCTMaster.users[0].Position}}</h5>
        </div>
      </div>
      <div class="mct-signature-right">
        <div class="receivedby-label">
          Recieved by:
        </div>
        <div class="receivedby-data">
          <div class="signature-recievedmct">
            <img :src="'/storage/signatures/'+MCTMaster.users[1].Signature" v-if="MCTMaster.users[1].pivot.Signature=='0'" alt="signature">
          </div>
          <h1>{{MCTMaster.users[1].FullName}} <i v-if="MCTMaster.users[1].pivot.Signature=='1'" class="material-icons decliner">close</i></h1>
          <h5>{{MCTMaster.users[1].Position}}</h5>
        </div>
      </div>
    </div>
    <p class="creator-display"><i class="material-icons">content_paste</i>created by: <span class="bold">{{MCTMaster.users[0].FullName}}</span></p>
  </div>
</div>
</template>

<script>
import axios from 'axios';
import Longpress from 'vue-longpress';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
export default {
     data () {
        return {
          MRTCheck:'',
          MCTMaster:[],
          MCTConfirmDetails:[],
          AccountCodeGroup:[],
          TotalSum:'',
          ShowEdit:false,
          AddressToEdit:'',
          QuantityArray:[],
          IsDisabled:false,
          SignatureMCTBtnHide:false,
          modalIsActive:false,
          FromMIRSData:[]
        }
      },
     props: ['mctno','user'],
     methods: {
       fetchData()
       {
         var vm=this;
         axios.get(`/MCTpreview/`+this.mctno.MCTNo).then(function(response)
        {

          Vue.set(vm.$data,'MRTCheck',response.data.MRTcheck);
          Vue.set(vm.$data,'MCTMaster',response.data.MCTMaster[0]);
          Vue.set(vm.$data,'MCTConfirmDetails',response.data.MCTConfirmDetails);
          Vue.set(vm.$data,'AccountCodeGroup',response.data.AccountCodeGroup);
          Vue.set(vm.$data,'TotalSum',response.data.totalsum);
        });
      },
      signatureMCT()
      {
        this.$loading('Signing...');
        this.SignatureMCTBtnHide=true;
        var vm=this;
        axios.put(`/Signature-for-mct/`+this.mctno.MCTNo).then(function(response)
        {
          vm.fetchData();
          vm.SignatureMCTBtnHide=false;
          vm.$loading.close();
          if(response.data.error!=null)
          {
            vm.$toast.top(response.data.error);
          }
        });
      },
      declineMCT()
      {
        this.$loading('Declining...');
        this.SignatureMCTBtnHide=true;
        var vm=this;
        axios.put(`/decline-mct/`+this.mctno.MCTNo).then(function(response)
        {
          vm.fetchData();
          vm.$loading.close();
          if(response.data.error!=null)
          {
            vm.$toast.top(response.data.error);
          }
        });
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      },
      editMCTSave()
      {
        if (confirm("Signatures will restart, continue?"))
        {
          this.$loading('Updating');
          var vm=this;
          axios.put(`/update-mct/`+this.mctno.MCTNo,{
            NewQuantity:this.QuantityArray,
            NewAddressTo:this.AddressToEdit,
          }).then(function(response)
          {

            if (response.data.error!=null)
            {
              vm.$toast.top(response.data.error);
              vm.fetchData();
              vm.$loading.close();
            }else
            {
              vm.fetchData();
              vm.$toast.top('Updated Successfully');
              vm.$loading.close();
            }
          }).catch(function(error)
          {

            vm.fetchData();
            vm.$toast.top('Invalid input');
            vm.$loading.close();
          });
        }
      },
      rollbackMCT()
      {
        var vm=this;
        if (confirm('Are you sure to rollback this MCT?'))
        {
          vm.$loading('Rolling back data');
          axios.put(`/rollback-mct-history/`+this.mctno.MCTNo+`/`+this.MCTMaster.MIRSNo).then(function(response)
          {
            vm.fetchData();
            vm.$loading.close();
            if(response.data.error!=null)
            {
              vm.$toast.top(response.data.error);
            }else
            {
              vm.$toast.top('Reversed successfully');
            }
          }).catch(function(error)
          {

            vm.$loading.close();
          });
        }
      },
      undoRollbackMCT()
      {
        var vm=this;
        if (confirm('Are you sure to undo the rollback of this MCT?'))
        {
          vm.$loading('Undoing rollbacked data');
          axios.put(`/undo-rollback-mct-history/`+this.mctno.MCTNo+`/`+this.MCTMaster.MIRSNo).then(function(response)
          {
            vm.fetchData();
            vm.$loading.close();
            if(response.data.error!=null)
            {
              vm.$toast.top(response.data.error);
            }else
            {
              vm.$toast.top('undo rollback successful');
            }
          }).catch(function(error)
          {

            vm.$loading.close();
          });
        }
      },
     },
     created () {
       this.fetchData();
     },
     components: {
        Longpress
     },
     computed: {
       AlreadySignatured: function()
       {
           if ((this.MCTMaster.users[0]!=null)&&(this.MCTMaster.users[0].pivot.Signature=='0')&&(this.MCTMaster.users[1]!=null)&&(this.MCTMaster.users[1].pivot.Signature=='0'))
           {
             return true;
           }else
           {
             return false;
           }
        }
     }
  }
</script>
