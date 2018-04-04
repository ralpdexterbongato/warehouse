<template lang="html">
<div class="rr-preview-vue" v-if="RRMaster.users!=null">
  <div class="reversed-alert">
    <p v-if="RRMaster.IsRollBack==0"><i class="material-icons">warning</i>Invalid transaction</p>
  </div>
  <div class="signature-btn">
    <div class="empty-div-left file-edit-container">
      <div v-if="(RRMaster.Status==null && ((user.Role==3) || (user.Role==4))) ">
        <span class="edit-file" :class="ShowEdit==true?'hide':'show'" v-on:click="ShowEdit=true"><i class="material-icons">edit</i>Edit</span>
        <span class="edit-file" :class="ShowEdit==false?'hide':'show'">
          <span class="color-blue">Save?</span>
          <button type="button" v-on:click="ShowEdit=false">cancel</button>
          <button v-on:click="ShowEdit=false,updateData()" type="button" name="button">Save</button>
        </span>
      </div>
    </div>
    <div class="signature-wrap" :class="{'hide':SignatureBtnHide}" v-if="UserCanSignature && RRMaster.Status==null">
      <longpress id="RRsignature" class="waves-effect waves-light" duration="3" :on-confirm="signature"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">edit</i> Signature
      </longpress>
      <longpress id="RRdecline" class="waves-effect waves-light" duration="3" :on-confirm="declinesignature"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
        <i class="material-icons">close</i> Decline
      </longpress>
    </div>
  </div>
  <div class="print-RR-btn" v-if="(RRMaster.Status=='0')">
    <span>
      <a :href="'/RR.pdf/'+RRMaster.RRNo" v-if="(RRMaster.IsRollBack!=0)&&(RRMaster.CreatorID==user.id)"><button type="submit"  name="RRNo" value="RRNohere"><i class="material-icons">print</i></button></a>
      <span v-if="user.Role==1">
        <button type="button" class="undo-btn" name="button" v-if="RRMaster.IsRollBack==null || RRMaster.IsRollBack==1" v-on:click="RollbackRR()"><i class="material-icons">replay</i> reverse</button>
        <button type="button" class="undo-btn" name="button" v-if="RRMaster.IsRollBack==0" v-on:click="UndoRollbackRR()"><i class="material-icons">refresh</i> undo reverse</button>
      </span>
    </span>
    <div>
      <a :href="'/view-list-MR-of-RR/'+RRMaster.RRNo" v-if="checkMR!=0"><button type="button" id="full-mr-preview-btn" ><i class="material-icons">history</i> MR</button></a>
      <a :href="'/create-mr/'+RRMaster.RRNo" v-if="(((user.Role==4)||(user.Role==3))&&(RRMaster.Status=='0'))"><button type="button" class="make-mr"><i class="material-icons">add</i>M.R.</button></a>
    </div>
  </div>
    <div class="bondpaper-RR">
      <div class="top-title-rr">
        <h5>BOHOL I ELECTRIC COOPERATIVE,INC.</h5>
        <h6>Cabulijan, Tubigon, Bohol</h6>
      </div>
      <div class="rr-titlebox">
        RECEIVING REPORT
      </div>
      <div class="right-date-rr">
        <div class="empty-left-rr">
        </div>
        <div class="content-right-rr">
          <li><label>RR No.:</label><h5>{{RRMaster.RRNo}}</h5></li>
          <li><label>Date:</label><h5>{{RRMaster.RRDate}}</h5></li>
        </div>
      </div>
      <div class="RRmasters-details">
        <div class="RRmaster-left">
          <ul>
            <li>
              <label>Supplier:</label>
              <h4 v-if="ShowEdit==false||RRMaster.PONo!=null">{{RRMaster.Supplier}}</h4>
              <h4 v-else-if="RRMaster.PONo ==null && ShowEdit==true"><input v-model="updateSupplier = RRMaster.Supplier" type="text"></h4>
            </li>
            <li>
              <label>Address:</label>
              <h4 v-if="ShowEdit==false || RRMaster.PONo!=null">{{RRMaster.Address}}</h4>
              <h4 v-else-if="RRMaster.PONo ==null && ShowEdit==true"><input type="text" v-model="updateAddress = RRMaster.Address"></h4>
            </li>
            <li><label>Invoice No.:</label>
              <h4 v-if="ShowEdit==false">{{RRMaster.InvoiceNo}}</h4>
              <h4 v-else><input type="text" v-model="updateInvoiceNum = RRMaster.InvoiceNo"></h4>
            </li>
            <li><label>R.V. No.:</label><h4>{{RRMaster.RVNo}}</h4></li>
          </ul>
        </div>
        <div class="RRmaster-right">
          <ul>
            <li><label>Carrier:</label>
              <h4 v-if="ShowEdit==false">{{RRMaster.Carrier}}</h4>
              <h4 v-else><input type="text" v-model="updateCarrier = RRMaster.Carrier"></h4>
            </li>
            <li>
              <label>Delivery Receipt No:</label>
              <h4 v-if="ShowEdit==false">{{RRMaster.DeliveryReceiptNo}}</h4>
              <h4 v-else><input type="text" v-model="updateDeliveryReceipt = RRMaster.DeliveryReceiptNo"></h4>
            </li>
            <li><label>P.O. No:</label>
              <h4 v-if="RRMaster.PONo!=null">{{RRMaster.PONo}}</h4>
            </li>
          </ul>
        </div>
      </div>
      <div class="RR-table-container">
        <table>
          <tr>
            <th class="left-side-th">Code No.</th>
            <th>Article</th>
            <th>Unit</th>
            <th>Quantity Delivered</th>
            <th>Quantity Accepted</th>
            <th>U-Cost</th>
            <th class="right-side-th">Amount</th>
          </tr>
          <tr v-for="(data,key) in RRconfirmationDetails">
            <td>{{data.ItemCode}}</td>
            <td>{{data.Description}}</td>
            <td>{{data.Unit}}</td>
            <td>
              <span v-if="ShowEdit==false">{{data.RRQuantityDelivered}}</span>
              <span v-else><input type="text" v-model="updateQtyDelivered[key] = data.RRQuantityDelivered" class="update-qty-input"></span>
            </td>
            <td>
              <span v-if="ShowEdit==false">{{data.QuantityAccepted}}</span>
              <span v-else><input type="text" v-model="updateQtyAccepted[key] = data.QuantityAccepted" class="update-qty-input"></span>
            </td>
            <td>{{formatPrice(data.UnitCost)}}</td>
            <td>{{formatPrice(data.Amount)}}</td>
          </tr>
        </table>
      </div>
      <div class="RRNetsales-Total">
        <div class="netsales-total-content">
          <li><label>Net Sales</label><h4>{{formatPrice(Netsales)}}</h4></li>
          <li class="RRadded-VAT"><h5>Add:Vat</h5> <h5>12%</h5><h5>{{formatPrice(VAT)}}</h5></li>
          <li><label>TOTAL AMOUNT</label><h4>{{formatPrice(TOTALamt)}}</h4></li>
        </div>
      </div>
      <h1 class="noteRR">
        <label>Note:</label>
        <p v-if="ShowEdit==false">{{RRMaster.Note}}</p>
        <p v-else><input type="text" v-model="updateNote = RRMaster.Note" name="" value=""></p>
      </h1>
      <div class="RRSignatures-container">
        <div class="bottom-signatures-rr">
          <div class="signature-rr-left">
            <label>RECEIVED BY:</label>
            <div class="signatureRR-content">
              <h2 v-if="RRMaster.users[0].pivot.Signature=='0'">
                <img :src="'/ForHerokuOnly/'+RRMaster.users[0].Signature" alt="signature">
              </h2>
              <h4>{{RRMaster.users[0].FullName}}
                <i class="material-icons" v-if="RRMaster.users[0].pivot.Signature=='1'">close</i>
              </h4>
              <p>{{RRMaster.users[0].Position}}</p>
            </div>
          </div>
          <div class="signature-rr-right">
            <h2 v-if="RRMaster.users[2].pivot.Signature=='0'"><img :src="'/ForHerokuOnly/'+RRMaster.users[2].Signature" alt="signature"></h2>
            <label>RECEIVED ORIGINAL BY:</label>
            <div class="signatureRR-content">
              <h4>{{RRMaster.users[2].FullName}}
                <i class="material-icons" v-if="RRMaster.users[2].pivot.Signature=='1'">close</i>
              </h4>
              <p>{{RRMaster.users[2].Position}}</p>
            </div>
          </div>
        </div>
        <div class="bottom-signatures-rr">
          <div class="signature-rr-left">
            <label>VERIFIED BY:</label>
            <div class="signatureRR-content">
              <h2 v-if="RRMaster.users[1].pivot.Signature=='0'"><img :src="'/ForHerokuOnly/'+RRMaster.users[1].Signature" alt="signature"></h2>
              <h4>
                {{RRMaster.users[1].FullName}}
                <i class="material-icons" v-if="RRMaster.users[1].pivot.Signature=='1'">close</i>
              </h4>
              <p>{{RRMaster.users[1].Position}}</p>
            </div>
          </div>
          <div class="signature-rr-right">
            <h2 v-if="RRMaster.users[3].pivot.Signature=='0'"><img :src="'/ForHerokuOnly/'+RRMaster.users[3].Signature" alt="signature"></h2>
            <label>POSTED TO BIN CARD BY:</label>
            <div class="signatureRR-content">
              <h4>
                {{RRMaster.users[3].FullName}}
                <i class="material-icons" v-if="RRMaster.users[3].pivot.Signature=='1'">close</i>
              </h4>
              <p>{{RRMaster.users[3].Position}}</p>
            </div>
          </div>
        </div>
      </div>
      <p class="creator-display"><i class="material-icons">content_paste</i>created by: <span class="bold">{{RRMaster.creator.FullName}}</span></p>
    </div>
</div>
</template>

<script>
import axios from 'axios'
import Longpress from 'vue-longpress'
import 'vue2-toast/lib/toast.css'
import Toast from 'vue2-toast'
  export default {
    data () { return {
      RRMaster:[],
      checkMR:0,
      RRconfirmationDetails:[],
      Netsales:0,
      VAT:0,
      TOTALamt:0,
      SignatureBtnHide:false,
      ShowEdit:false,
      updateQtyDelivered:[],
      updateQtyAccepted:[],
      updateSupplier:'',
      updateAddress:'',
      updateInvoiceNum:'',
      updateCarrier:'',
      updateDeliveryReceipt:'',
      updateNote:''
     }
    },
    props: ['user','rrno'],
    methods: {
    updateData()
    {
      this.SignatureBtnHide=false;
      if (confirm('Warning! - Signatures will be restarted, continue?'))
      {
        this.$loading('Please wait');
        for (var i = 0; i < this.RRconfirmationDetails.length; i++)
        {
          if ((isNaN(this.updateQtyDelivered[i]) || isNaN(this.updateQtyAccepted[i])))
          {
            this.$toast.top('Qty must be a number');
            this.FetchData();
            this.$loading.close();
            return false;
          }
          if (this.updateQtyDelivered[i] == '' || this.updateQtyAccepted[i] == '')
          {
            this.$toast.top('You cannot leave a blank field');
            this.FetchData();
            this.$loading.close();
            return false;
          }
          if(Number(this.updateQtyDelivered[i]) < Number(this.updateQtyAccepted[i]))
          {
            this.$toast.top('Qty accepted cannot be higher than it`s Qty delivered');
            this.FetchData();
            this.$loading.close();
            return false;
          }
          if (this.updateQtyDelivered[i] < 1 || this.updateQtyAccepted[i] < 1)
          {
            this.$toast.top('Qty must be atleast 1');
            this.FetchData();
            this.$loading.close();
            return false;
          }
        }
        var vm = this;
        axios.put(`/update-rr-file/`+this.rrno.RRNo,{
          newQtyDelivered:this.updateQtyDelivered,
          newQty:this.updateQtyAccepted,
          newSupplier:this.updateSupplier,
          newAddress:this.updateAddress,
          newInvoice:this.updateInvoiceNum,
          newCarrier:this.updateCarrier,
          newDeliveryReceipt:this.updateDeliveryReceipt,
          newNote:this.updateNote,
        }).then(function(response)
        {
          if (response.data.error!=null)
          {
            vm.$toast.top(response.data.error);
          }else
          {
            vm.$toast.top('updated sucessfully');
          }
          vm.FetchData();
          vm.$loading.close();
        }).catch(function(error)
        {
          vm.$loading.close();
        });
      }
      },
      FetchData()
      {
        var vm=this;
        axios.get(`/RR-fullpreview-fetch-data/`+this.rrno.RRNo).then(function(response)
        {

          Vue.set(vm.$data,'RRMaster',response.data.RRMaster[0]);
          Vue.set(vm.$data,'RRconfirmationDetails',response.data.RRconfirmationDetails);
          Vue.set(vm.$data,'checkMR',response.data.checkMR);
          Vue.set(vm.$data,'Netsales',response.data.Netsales);
          Vue.set(vm.$data,'VAT',response.data.VAT);
          Vue.set(vm.$data,'TOTALamt',response.data.TOTALamt);
        });
      },
      signature()
      {
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/RR-signature/`+this.rrno.RRNo).then(function(response)
        {

          vm.FetchData();
        });
      },
      declinesignature()
      {
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/decline-this-RR/`+this.rrno.RRNo).then(function(response)
        {

          vm.FetchData();
        });
      },
      RollbackRR()
      {
        if (confirm('Are you sure to roll back?'))
        {
          this.$loading('Rolling back...');
          var vm=this;
          axios.put(`/rollback-this-rr/`+this.rrno.RRNo).then(function(response)
          {

            vm.FetchData();
            vm.$toast.top('rolled back sucessfully');
            vm.$loading.close();
          }).catch(function(error)
          {

            vm.$loading.close();
          });
        }
      },
      UndoRollbackRR()
      {
        if (confirm('Are you sure to undo rollback?'))
        {
          this.$loading('undoing rollback...');
          var vm=this;
          axios.put(`/undo-rollback-this-rr/`+this.rrno.RRNo).then(function(response)
          {

            vm.FetchData();
            vm.$toast.top('rollback undid sucessfully');
            vm.$loading.close();
          }).catch(function(error)
          {

            vm.$loading.close();
          });
        }
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      },

    },
    created () {
      this.FetchData();
    },
    components: {
       Longpress
     },
     computed: {
       UserCanSignature: function()
       {
         if(((this.RRMaster.users[0]!=null)&&(this.RRMaster.users[0].pivot.Signature==null)&&(this.RRMaster.users[0].id==this.user.id))||((this.RRMaster.users[1]!=null)&&(this.RRMaster.users[1].pivot.Signature==null)&&(this.RRMaster.users[1].id==this.user.id))||((this.RRMaster.users[2]!=null)&&(this.RRMaster.users[2].pivot.Signature==null)&&(this.RRMaster.users[2].id==this.user.id))||((this.RRMaster.users[3]!=null)&&(this.RRMaster.users[3].pivot.Signature==null)&&(this.RRMaster.users[3].id==this.user.id)))
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
