<template lang="html">
  <div class="containerRR-noPO">
    <div class="top-title-rr-nopo">
      <i class="material-icons">add</i> Create receiving report w/o PO.
    </div>
    <div class="flexible-container">
      <div class="selected-items-rrnopo">
        <div class="btn-add-item-rrnopo">
          <button type="button" v-on:click="ModalIsActive=!ModalIsActive"><i class="material-icons">add</i> Items</button>
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
              <th>Remove</th>
            </tr>
            <tr v-for="(sessionItem,count) in ItemsInSession">
              <td v-if="sessionItem.ItemCode!=null">{{sessionItem.ItemCode}}</td>
              <td v-else><i class="material-icons color-red">do_not_disturb</i></td>
              <td v-if="sessionItem.AccountCode!=null">{{sessionItem.AccountCode}}</td>
              <td v-else><i class="material-icons color-red">do_not_disturb</i></td>
              <td>{{sessionItem.Description}}</td>
              <td>₱{{formatPrice(sessionItem.UnitCost)}}</td>
              <td>{{sessionItem.QuantityDelivered}}</td>
              <td>{{sessionItem.QuantityAccepted}}</td>
              <td>{{sessionItem.Unit}}</td>
              <td>₱{{formatPrice(sessionItem.Amount)}}</td>
              <td><i class="material-icons" v-on:click="deleteSession(count)">close</i></td>
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
        <select name="ReceivedBy" v-model="ReceivedBy" :class="[ReceivedBy!='' ?'black':'']">
          <option value="" class="gray">Received by</option>
          <option class="black" v-for="user in allusers" v-bind:value="user.id">{{user.FullName}}</option>
        </select>
        <select name="Verifiedby" v-model="Verifiedby" :class="[Verifiedby!='' ?'black':'']">
          <option value="" class="gray">Verified by</option>
          <option class="black" v-for="manager in managers" v-bind:value="manager.id">{{manager.FullName}}</option>
        </select>
        <select name="ReceivedOriginalby" v-model="ReceivedOriginalby" :class="[ReceivedOriginalby!='' ?'black':'']">
          <option value="" class="gray">Received originaly by</option>
          <option class="black" v-for="auditor in auditors" v-bind:value="auditor.id" >{{auditor.FullName}}</option>
        </select>
        <select name="PostedtoBINby" v-model="PostedtoBINby" :class="[PostedtoBINby!='' ?'black':'']">
          <option value="" class="gray">Posted to B.I.N. by</option>
          <option class="black" v-for="clerk in clerks" v-bind:value="clerk.id">{{clerk.FullName}}</option>
        </select>
        <longpress  duration="3" id="submitNoPO" :class="{'hide':HideBtn}" :on-confirm="submitToDB" pressing-text="submitting in {$rcounter}" action-text="Loading . . .">
         Submit
        </longpress>
      </div>
    </div>
    <div @click.prevent="ModalIsActive=!ModalIsActive" class="modal-rr-no-po" :class="{'active' : ModalIsActive}">
      <div class="center-white-modal">
        <div class="items-from-rv-nopo-table">
          <table>
            <tr>
              <th>Item Code</th>
              <th>Name/Description</th>
              <th>UnitCost</th>
              <th>Quantity delivered</th>
              <th>Quantity accepted</th>
              <th>Unit</th>
              <th class="right-part">Add</th>
            </tr>
            <tr v-for="(rvdetail, loop) in fromrvdetail">
              <input type="text" v-if="rvdetail.AccountCode!=null" v-model="AccountCode[loop]=rvdetail.AccountCode" class="hide">
              <td v-if="rvdetail.ItemCode!=null" v-model="ItemCode[loop]=rvdetail.ItemCode">{{rvdetail.ItemCode}}</td>
              <td v-else><i class="material-icons color-red">do_not_disturb</i></td>
              <td>{{rvdetail.Particulars}}</td>
              <td><input autocomplete="off" type="text" name="UnitCost" v-model="UnitCost[loop]"></td>
              <td><input autocomplete="off" min="1" type="number" name="delivered" v-model="QuantityDelivered[loop]" ></td>
              <td><input autocomplete="off" min="0" type="number" :max="QuantityDelivered[loop]" name="accepted" v-model="QuantityAccepted[loop]"></td>
              <td>{{rvdetail.Unit}}</td>
              <td><button type="submit" v-on:click="submitTosession(rvdetail.Particulars,rvdetail.Unit,loop)"><i class="material-icons">add</i></button></td>
            </tr>
          </table>
        </div>
      </div>
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

    data ()
    { return {
        ModalIsActive:false,
        ItemCode:[],
        AccountCode:[],
        UnitCost:[],
        QuantityDelivered:[],
        QuantityAccepted:[],
        ItemsInSession:[],
        ReceivedBy:'',
        Verifiedby:'',
        ReceivedOriginalby:'',
        PostedtoBINby:'',
        Supplier:'',
        Address:'',
        InvoiceNo:'',
        Carrier:'',
        DeliveryReceiptNo:'',
        Note:'',
        HideBtn:false,
      }
    },
     props: ['fromrvdetail','managers','auditors','clerks','allusers'],
     methods: {
       submitTosession(particular,unit,count)
       {
         this.$loading('Adding');
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

           if (response.data.error!=null)
           {
             vm.$toast.top(response.data.error);
             vm.$loading.close();
           }else
           {
             vm.fetchDataofSession();
             vm.$toast.top('Added successfully');
             vm.$loading.close();
           }
         },function(error)
         {
           if (error.response.data.UnitCost!=null)
           {
             vm.$toast.top(error.response.data.UnitCost[0]);
           }else if (error.response.data.QuantityDelivered!=null)
           {
             vm.$toast.top(error.response.data.QuantityDelivered[0]);
           }else if (error.response.data.QuantityAccepted!=null)
           {
             vm.$toast.top(error.response.data.QuantityAccepted[0]);
           }
           vm.$loading.close();
         });
       },
       fetchDataofSession()
       {
         var vm=this;
         axios.get(`/show-rr-session-data-no-po`).then(function(response)
         {
            vm.ItemsInSession=response.data;
         });
       },
       deleteSession(count)
       {
         this.$loading('Removing');
         var vm=this;
         axios.delete(`/delete-session-no-po/`+count).then(function(response)
         {
           vm.fetchDataofSession();
           vm.$toast.top('Removed');
           vm.$loading.close();
         });
       },
       formatPrice(value) {
             let val = (value/1).toFixed(2).replace('.', '.')
             return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
       },
       submitToDB()
       {
         this.$loading('Submitting');
         this.HideBtn=true;
         var vm=this;
         axios.post(`/save-rr-no-po-to-db`,{
           Receivedby:this.ReceivedBy,
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
           if (response.data.error==null)
           {
             window.location=response.data.redirect;
           }else
           {
             vm.$toast.top(response.data.error);
             Vue.set(vm.$data,'HideBtn',false);
           }
            vm.$loading.close();
         },function(error)
         {
          //  vm.$toast.top(error.response.data);
           Vue.set(vm.$data,'HideBtn',false);
           vm.$loading.close();
           if (error.response.data.Supplier!=null)
           {
             vm.$toast.top(error.response.data.Supplier[0]);
           }else if (error.response.data.Address!=null)
           {
             vm.$toast.top(error.response.data.Address[0]);
           }else if (error.response.data.Receivedby!=null)
           {
             vm.$toast.top(error.response.data.Receivedby[0]);
           }else if (error.response.data.Verifiedby!=null)
           {
             vm.$toast.top(error.response.data.Verifiedby[0]);
           }else if (error.response.data.ReceivedOriginalby!=null)
           {
             vm.$toast.top(error.response.data.ReceivedOriginalby[0]);
           }else if (error.response.data.PostedtoBINby!=null)
           {
             vm.$toast.top(error.response.data.PostedtoBINby[0]);
           }else if (error.response.data.DeliveryReceiptNo!=null)
           {
             vm.$toast.top(error.response.data.DeliveryReceiptNo[0]);
           }else if (error.response.data.InvoiceNo!=null)
           {
             vm.$toast.top(error.response.data.InvoiceNo[0]);
           }else if (error.response.data.Note!=null)
           {
             vm.$toast.top(error.response.data.Note[0]);
           }
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
