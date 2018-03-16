<template lang="html">
  <span v-if="MRTMaster.users!=null">
    <div class="reversed-alert-mrt">
      <p v-if="MRTMaster.IsRollBack==0"><i class="material-icons">warning</i>Invalid transaction</p>
    </div>
    <div class="top-MRT-buttons">
      <div class="mrt-print" v-if="MRTMaster.Status==0">
        <a :href="'/print-mrt.pdf/'+MRTMaster.MRTNo">
          <button type="button" name="button">PDF</button>
        </a>
      </div>
      <span class="edit-mrt-container" v-if="StillEditable">
        <button v-on:click="Editbtn=true" :class="{'hide':Editbtn}"><i class="material-icons">edit</i> Edit</button>
        <span class="edit-mrt-btns" :class="{'active':Editbtn}">
          <p class="color-blue">Save?</p>
          <button type="button" name="button" v-on:click="Editbtn=false,fetchdata()">Cancel</button>
          <button type="button" name="button" v-on:click="Editbtn=false,updateQty()">Save</button>
        </span>
      </span>
      <span v-else>
        <span v-if="user.Role==1 && MRTMaster.Status=='0'">
          <button type="button" class="undo-btn" v-if="MRTMaster.IsRollBack==null || MRTMaster.IsRollBack==1" name="button" v-on:click="RollbackMRT()"><i class="material-icons">replay</i> reverse</button>
          <button type="button" class="undo-btn" v-if="MRTMaster.IsRollBack==0" name="button" v-on:click="UndoRollbackMRT()"><i class="material-icons">refresh</i> Undo reverse</button>
        </span>
      </span>
      <span class="signature-decline-mrt" :class="{'hide':SignatureBtnHide}" v-if="UserCanSignature">
        <longpress id="signature-mrt" class="waves-effect waves-light" duration="3" :on-confirm="signatureMRT" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">edit</i> Signature
        </longpress>
        <longpress id="decline-mrt" class="waves-effect waves-light" duration="3" :on-confirm="declineMRT" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">close</i> Decline
        </longpress>
      </span>
    </div>
    <div class="Bondpaper-mrt-preview">
      <div class="header-mrt-center">
        <h4>BOHOL I ELECTRIC COOPERATIVE, INC.</h4>
        <p class="address-mrt">Cabulijan, Tubigon, Bohol</p>
        <h3>MATERIALS RETURN TICKET</h3>
      </div>
      <div class="left-right-mrt">
        <div class="left-mrt">
          <span class="mrt-info"><label>Particulars:</label><h3>{{MRTMaster.Particulars}}</h3></span>
          <span class="mrt-info"><label>Address:</label><h3>{{MRTMaster.AddressTo}}</h3></span>
        </div>
        <div class="right-mrt">
          <span class="mrt-info"><label>MCT No:</label><h3>{{MRTMaster.MCTNo}}</h3></span>
          <span class="mrt-info"><label>MRT No:</label><h3>{{MRTMaster.MRTNo}}</h3></span>
          <span class="mrt-info"><label>Returned Date:</label><h3>{{MRTMaster.ReturnDate}}</h3></span>
          <span class="mrt-info"><label>Remarks:</label><h3>{{MRTMaster.Remarks}}</h3></span>
        </div>
      </div>
      <div class="mrt-table-viewer">
        <table>
          <tr>
            <th>Acct. Code</th>
            <th>Item Code</th>
            <th>Description</th>
            <th>Unit Cost</th>
            <th>Amount</th>
            <th>Unit</th>
            <th>Returned</th>
          </tr>
          <tr v-for="(mrtconfirm, count) in forMRTConfirmation">
            <td>{{mrtconfirm.AccountCode}}</td>
            <td>{{mrtconfirm.ItemCode}}</td>
            <td>{{mrtconfirm.Description}}</td>
            <td>{{formatPrice(mrtconfirm.UnitCost)}}</td>
            <td>{{formatPrice(mrtconfirm.Amount)}}</td>
            <td>{{mrtconfirm.Unit}}</td>
            <td class="align-right"><span :class="[Editbtn==true?'hide':'show']">{{mrtconfirm.Quantity}}</span><span :class="[Editbtn==true?'show':'hide']" v-if="user.id==MRTMaster.users[0].id"><input type="text" v-model="EditedQty[count]=mrtconfirm.Quantity"></span></td>
          </tr>
        </table>
      </div>
      <div class="groupbyAccount">
          <span class="mrt-info" v-for="mrtgrouped in MRTbyAcntCode"><label>{{mrtgrouped.AccountCode}}</label><h5>{{formatPrice(mrtgrouped.totalAMT)}}</h5></span>
      </div>
      <div class="total-mrt-info">
        <span class="total-mrt-flex">
          <h3>Total ammount Returned</h3>
          <h4>{{formatPrice(totalsum)}}</h4>
        </span>
      </div>
      <div class="mrt-returnby-receivedby-container">
        <div class="mrt-returnby-container">
            <p>Returned by:</p>
            <div class="mrt-bottom-data">
              <h3 v-if="MRTMaster.users[1].pivot.Signature=='0'"><img :src="'/storage/signatures/'+MRTMaster.users[1].Signature" alt="signature"></h3>
              <p>{{MRTMaster.users[1].FullName}} <i class="material-icons decliner" v-if="MRTMaster.users[1].pivot.Signature=='1'">close</i></p>
              <p>{{MRTMaster.users[1].Position}}</p>
            </div>
        </div>
        <div class="mrt-received-container">
          <p>Recieved by:</p>
          <div class="mrt-bottom-data">
            <h3 v-if="MRTMaster.users[0].pivot.Signature=='0'"><img :src="'/storage/signatures/'+MRTMaster.users[0].Signature" alt="signature"></h3>
            <p>{{MRTMaster.users[0].FullName}}<i class="material-icons decliner" v-if="MRTMaster.users[0].pivot.Signature=='1'">close</i></p>
            <p>{{MRTMaster.users[0].Position}}</p>
          </div>
        </div>
      </div>
    </div>
  </span>
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
          MRTMaster:[],
          MRTbyAcntCode:[],
          totalsum:'',
          forMRTConfirmation:[],
          Editbtn:false,
          EditedQty:[],
          DisableBtn:false,
          SignatureBtnHide:false,
        }
      },
     props: ['mrtno','user'],
     methods: {
       formatPrice(value) {
             let val = (value/1).toFixed(2).replace('.', '.')
             return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
       },
       fetchdata()
       {
         var vm=this;
         axios.get(`/mrt-viewer/`+this.mrtno.MRTNo).then(function(response){
          console.log(response);
          Vue.set(vm.$data,'MRTMaster',response.data.MRTMaster[0]);
          Vue.set(vm.$data,'MRTbyAcntCode',response.data.MRTbyAcntCode);
          Vue.set(vm.$data,'totalsum',response.data.totalsum);
          Vue.set(vm.$data,'forMRTConfirmation',response.data.MRTConfirmationItems);
        });
      },
      signatureMRT()
      {
        this.$loading('Signaturing...');
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/signatureMRT/`+this.mrtno.MRTNo).then(function(response)
        {
          console.log(response);
          vm.fetchdata();
          vm.SignatureBtnHide=false;
          vm.$loading.close();
        });
      },
      declineMRT()
      {
        this.$loading('Declining...');
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/declineMRT/`+this.mrtno.MRTNo).then(function(response)
        {
          console.log(response);
          vm.fetchdata();
          vm.$loading.close();
        });
      },
      updateQty()
      {
        this.$loading('Updating');
        var vm=this;
        axios.put(`/updateMRTQty/`+this.mrtno.MRTNo,{UpdatedQty:this.EditedQty}).then(function(response)
        {
          console.log(response);
          vm.fetchdata();
          if (response.data.error!=null)
          {
            vm.$toast.top(response.data.error);
          }else
          {
            vm.$toast.top('Updated');
          }
          vm.$loading.close();
        });
      },
      RollbackMRT()
      {
        if (confirm('Are you sure to roll back?'))
        {
          this.$loading('Rolling back');
          var vm=this;
          axios.put(`/rollback-mrt-history/`+this.mrtno.MRTNo).then(function(response)
          {
            console.log(response);
            vm.fetchdata();
            vm.$toast.top('Rolled back successfully');
            vm.$loading.close();
          }).catch(function(error)
          {
            console.log(error);
            vm.$loading.close();
          })
        }
      },
      UndoRollbackMRT()
      {
        if (confirm('Are you sure to undo this rollback?'))
        {
          this.$loading('Undoing rollback');
          var vm=this;
          axios.put(`/undo-rollback-mrt-history/`+this.mrtno.MRTNo).then(function(response)
          {
            console.log(response);
            vm.fetchdata();
            vm.$toast.top('rollback undid successfully');
            vm.$loading.close();
          }).catch(function(error)
          {
            console.log(error);
            vm.$loading.close();
          })
        }
      },
     },
     mounted () {
       this.fetchdata();
     },
     components: {
        Longpress
     },
     computed: {
       StillEditable: function()
       {
         if (this.MRTMaster.users[0]!=null && this.user.id==this.MRTMaster.users[0].id && this.MRTMaster.users[1]!=null && this.MRTMaster.users[1].pivot.Signature==null && this.MRTMaster.users[1].pivot.Signature!='1' && this.MRTMaster.users[0].pivot.Signature!='1')
         {
           return true;
         }else
         {
           return false;
         }
       },
       UserCanSignature: function()
       {
         if (((this.MRTMaster.users[0].id==this.user.id)&&(this.MRTMaster.users[0].pivot.Signature==null)||(this.user.id==this.MRTMaster.users[1].id)&&(this.MRTMaster.users[1].pivot.Signature==null)&&(this.MRTMaster.users[0].pivot.Signature=='0')))
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
