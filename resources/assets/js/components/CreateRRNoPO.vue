<template lang="html">
  <div class="containerRR-noPO">
    <div class="top-title-rr-nopo">
      <i class="fa fa-plus"></i> Create receiving report w/o PO.
    </div>
    <div class="flexible-container">
      <div class="selected-items-rrnopo">
        <div class="btn-add-item-rrnopo">
          <button type="button" v-on:click="ModalIsActive=!ModalIsActive"><i class="fa fa-plus-circle"></i> Items</button>
        </div>
        <ul class="error-tab" v-if="laravelerrors!=''" v-on:click="laravelerrors=''">
          <span v-for="errors in laravelerrors">
            <li v-for="error in errors">{{error}}</li>
          </span>
        </ul>
        <ul class="error-tab" v-if="ownerrors!=''" v-on:click="ownerrors=''">
          <li>{{ownerrors}}</li>
        </ul>
        <div class="successAlertRRsession" v-if="successAlerts!=''" v-on:click="successAlerts=''">
          <p>{{successAlerts}}</p>
        </div>
        <div class="selected-received-items-table">
          <table>
            <tr>
              <th>ItemCode</th>
              <th>AccountCode</th>
              <th>Description</th>
              <th>UnitCost</th>
              <th>Quantity delivered</th>
              <th>Quantity accepted</th>
              <th>Unit</th>
              <th>Amount</th>
              <th>Delete</th>
            </tr>
            <tr v-if="ItemsInSession[0]!=null" v-for="(sessionItem,count) in ItemsInSession">
              <td v-if="sessionItem.ItemCode!=null">{{sessionItem.ItemCode}}</td>
              <td v-else><i class="fa fa-ban decliner"></i></td>
              <td v-if="sessionItem.AccountCode!=null">{{sessionItem.AccountCode}}</td>
              <td v-else><i class="fa fa-ban decliner"></i></td>
              <td>{{sessionItem.Description}}</td>
              <td>₱{{formatPrice(sessionItem.UnitCost)}}</td>
              <td>{{sessionItem.QuantityDelivered}}</td>
              <td>{{sessionItem.QuantityAccepted}}</td>
              <td>{{sessionItem.Unit}}</td>
              <td>₱{{formatPrice(sessionItem.Amount)}}</td>
              <td><i class="fa fa-trash" v-on:click="deleteSession(count)"></i></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="form-input-rrnopo">
        <input autocomplete="off" type="text" name="Supplier" v-model="Supplier" placeholder="Supplier">
        <input autocomplete="off" type="text" name="Address" v-model="Address" placeholder="Supplier Address">
        <input autocomplete="off" type="text" name="InvoiceNo" v-model="InvoiceNo" placeholder="Invoice Number (optional)">
        <input autocomplete="off" type="text" name="Carrier" v-model="Carrier" placeholder="Carrier (optional)">
        <input autocomplete="off" type="text" name="DeliveryReceiptNo" v-model="DeliveryReceiptNo" placeholder="Delivery Receipt No. (optional)">
        <input autocomplete="off" type="text" name="Note" v-model="Note" placeholder="Note (optional)">
        <select name="Verifiedby" v-model="Verifiedby" :class="[Verifiedby!=null ?'black':'']">
          <option value="null" class="gray">Verified by</option>
          <option class="black" v-for="manager in managers" v-bind:value="manager.id">{{manager.FullName}}</option>
        </select>
        <select name="ReceivedOriginalby" v-model="ReceivedOriginalby" :class="[ReceivedOriginalby!=null ?'black':'']">
          <option value="null" class="gray">Received originaly by</option>
          <option class="black" v-for="auditor in auditors" v-bind:value="auditor.id" >{{auditor.FullName}}</option>
        </select>
        <select name="PostedtoBINby" v-model="PostedtoBINby" :class="[PostedtoBINby!=null ?'black':'']">
          <option value="null" class="gray">Posted to B.I.N. by</option>
          <option class="black" v-for="clerk in clerks" v-bind:value="clerk.id">{{clerk.FullName}}</option>
        </select>
        <longpress  duration="3" id="submitNoPO" :class="{'hide':HideBtn}" :on-confirm="submitToDB" pressing-text="submitting in {$rcounter}" action-text="Loading . . .">
         Submit
        </longpress>
        <div id="loading-submit" :class="[HideBtn==true?'show':'hide']">
          <i class="fa fa-spinner fa-spin fa-pulse"></i>
        </div>
      </div>
    </div>
    <div @click.prevent="ModalIsActive=!ModalIsActive" class="modal-rr-no-po" :class="{'active' : ModalIsActive}">
      <div class="center-white-modal" @click.prevent="ModalIsActive=!ModalIsActive">
        <div class="items-from-rv-nopo-table">
          <table>
            <tr>
              <th class="left-part">AccountCode</th>
              <th>Item Code</th>
              <th>Name/Description</th>
              <th>UnitCost</th>
              <th>Quantity delivered</th>
              <th>Quantity accepted</th>
              <th>Unit</th>
              <th class="right-part"></th>
            </tr>
            <tr v-for="(rvdetail, count) in fromrvdetail">
              <td v-if="rvdetail.AccountCode!=null" v-model="AccountCode[count]=rvdetail.AccountCode">{{rvdetail.AccountCode}}</td>
              <td v-else><i class="fa fa-ban decliner"></i> For Warehouse</td>
              <td v-if="rvdetail.ItemCode!=null" v-model="ItemCode[count]=rvdetail.ItemCode">{{rvdetail.ItemCode}}</td>
              <td v-else><i class="fa fa-ban decliner"></i> For Warehouse</td>
              <td>{{rvdetail.Particulars}}</td>
              <td><input autocomplete="off" type="text" name="UnitCost" v-model="UnitCost[count]"></td>
              <td><input autocomplete="off" min="1" type="number" name="delivered" v-model="QuantityDelivered[count]" v-on:change="isDisabled=false" ></td>
              <td><input autocomplete="off" min="0" type="number" :max="QuantityDelivered[count]" name="accepted" v-model="QuantityAccepted[count]" :disabled="isDisabled"></td>
              <td>{{rvdetail.Unit}}</td>
              <td><button class="bttn-unite bttn-xs bttn-primary" type="submit" v-on:click="submitTosession(rvdetail.Particulars,rvdetail.Unit,count),ModalIsActive=!ModalIsActive"><i class="fa fa-plus"></i></button></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Longpress from 'vue-longpress'
  export default {

    data ()
    { return {
        ModalIsActive:false,
        ItemCode:[],
        AccountCode:[],
        UnitCost:[],
        QuantityDelivered:[],
        QuantityAccepted:[],
        ItemsInSession:[],
        isDisabled:true,
        laravelerrors:[],
        ownerrors:'',
        successAlerts:'',
        Verifiedby:null,
        ReceivedOriginalby:null,
        PostedtoBINby:null,
        Supplier:'',
        Address:'',
        InvoiceNo:'',
        Carrier:'',
        DeliveryReceiptNo:'',
        Note:'',
        HideBtn:false,
      }
    },
     props: ['fromrvdetail','managers','auditors','clerks'],
     methods: {
       submitTosession(particular,unit,count)
       {
         var vm=this;
         axios.post(`/rr-storing-session-no-po`,{
           AccountCode:this.AccountCode[count],
           ItemCode:this.ItemCode[count],
           UnitCost:this.UnitCost[count],
           Description:particular,
           Unit:unit,
           QuantityDelivered:this.QuantityDelivered[count],
           QuantityAccepted:this.QuantityAccepted[count],
           RVNo:this.fromrvdetail[0].RVNo,
         }).then(function(response)
         {
           console.log(response);
           if (response.data.error!=null)
           {
             Vue.set(vm.$data,'ownerrors',response.data.error);
             Vue.set(vm.$data,'laravelerrors','');
             Vue.set(vm.$data,'successAlerts','');
           }else
           {
             vm.fetchDataofSession();
             Vue.set(vm.$data,'ownerrors','');
             Vue.set(vm.$data,'laravelerrors','');
             Vue.set(vm.$data,'successAlerts','Successfully added !');
           }
         },function(error)
         {
           console.log(error)
           Vue.set(vm.$data,'laravelerrors',error.response.data);
           Vue.set(vm.$data,'ownerrors','');
           Vue.set(vm.$data,'successAlerts','');
         });
       },
       fetchDataofSession()
       {
         var vm=this;
         axios.get(`/show-rr-session-data`).then(function(response)
         {
           console.log(response);
            Vue.set(vm.$data,'ItemsInSession',response.data);
         });
       },
       deleteSession(count)
       {
         var vm=this;
         axios.delete(`/DeleteSession-RR/`+count).then(function(response)
         {
           console.log(response);
           vm.fetchDataofSession();
           Vue.set(vm.$data,'successAlerts','Deleted successfully !');
           Vue.set(vm.$data,'ownerrors','');
           Vue.set(vm.$data,'laravelerrors','');
         });
       },
       formatPrice(value) {
             let val = (value/1).toFixed(2).replace('.', '.')
             return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
       },
       submitToDB()
       {
         this.HideBtn=true;
         var vm=this;
         axios.post(`/save-rr-no-po-to-db`,{
           Verifiedby:this.Verifiedby,
           ReceivedOriginalby:this.ReceivedOriginalby,
           PostedtoBINby:this.PostedtoBINby,
           Supplier:this.Supplier,
           Address:this.Address,
           InvoiceNo:this.InvoiceNo,
           RVNo:this.fromrvdetail[0].RVNo,
           Carrier:this.Carrier,
           DeliveryReceiptNo:this.DeliveryReceiptNo,
           Note:this.Note,
         }).then(function(response)
         {
           console.log(response);
           if (response.data.error==null)
           {
             window.location=response.data.redirect;
           }else
           {
             Vue.set(vm.$data,'ownerrors',response.data.error);
             Vue.set(vm.$data,'HideBtn',false);
           }
         },function(error)
         {
           console.log(error)
           Vue.set(vm.$data,'laravelerrors',error.response.data);
           Vue.set(vm.$data,'HideBtn',false);
         });
       }
     },
     mounted()
     {
       this.fetchDataofSession();
     },
     components: {
        Longpress
      },
  }
</script>
