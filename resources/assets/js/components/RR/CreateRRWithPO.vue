<template lang="html">
<div class="vue-rr-create-withpo">
  <div class="selected-session-rr-w-po">
    <div class="add-item-rr-w-pobtn">
      <button type="button"v-on:click="IsModalActive=!IsModalActive"><i class="material-icons">add</i> items</button>
    </div>
    <div class="table-rr-w-po">
      <table>
        <tr>
          <th>ItemCode</th>
          <th>AccountCode</th>
          <th>Description</th>
          <th>UnitCost</th>
          <th>QuantityDelivered</th>
          <th>QuantityAccepted</th>
          <th>Unit</th>
          <th>Amount</th>
          <th>Remove</th>
        </tr>
        <tr v-for="(sessionitem,count) in SessionItems">
          <td v-if="sessionitem.ItemCode!=null">{{sessionitem.ItemCode}}</td>
          <td v-else><i class="material-icons decliner">do_not_disturb</i></td>
          <td v-if="sessionitem.AccountCode!=null">{{sessionitem.AccountCode}}</td>
          <td v-else><i class="material-icons decliner">do_not_disturb</i></td>
          <td>{{sessionitem.Description}}</td>
          <td>₱{{formatPrice(sessionitem.UnitCost)}}</td>
          <td>{{sessionitem.QuantityDelivered}}</td>
          <td>{{sessionitem.QuantityAccepted}}</td>
          <td>{{sessionitem.Unit}}</td>
          <td>₱{{formatPrice(sessionitem.Amount)}}</td>
          <td><i class="material-icons" v-on:click="deleteSession(count)">close</i></td>
        </tr>
      </table>
    </div>
  </div>
  <div class="right-form-rr-with-po">
    <input type="text" autocomplete="off" name="InvoiceNo" v-model="InvoiceNo" placeholder="Invoice number">
    <input type="text" autocomplete="off" name="Carrier" v-model="Carrier" placeholder="Carrier">
    <input type="text" autocomplete="off" name="DeliveryReceiptNo" v-model="DeliveryReceiptNo" placeholder="Delivery Receipt Number">
    <input type="text" autocomplete="off" name="Note" v-model="Note" placeholder="Note">
    <select v-model="ReceivedBy" :class="[ReceivedBy!='' ?'black':'']">
      <option value="" class="gray">Received by</option>
      <option v-for="user in allusers" class="black" :value="user.id">{{user.FullName}}</option>
    </select>
    <select name="Verifiedby" v-model="Verifiedby" :class="[Verifiedby!='' ?'black':'']">
      <option value="" class="gray">Verified by</option>
      <option v-for="manager in managers" class="black" :value="manager.id">{{manager.FullName}}</option>
    </select>
    <select name="ReceivedOriginalby" v-model="ReceivedOriginalby" :class="[ReceivedOriginalby!='' ?'black':'']">
      <option value="" class="gray">Received Originaly by</option>
      <option v-for="auditor in auditors" class="black" :value="auditor.id">{{auditor.FullName}}</option>
    </select>
    <select name="PostedtoBINby" v-model="PostedtoBINby" :class="[PostedtoBINby!='' ?'black':'']" >
      <option value="" class="gray">Posted to BIN by</option>
      <option v-for="clerk in clerks" class="black" :value="clerk.id">{{clerk.FullName}}</option>
    </select>
    <longpress id="withposubmit" :class="{'hide':HideSubmitBtn}" duration="3" :on-confirm="SubmitRRwithPO" pressing-text="Submitting in {$rcounter}" action-text="Loading . . .">
      Submit
    </longpress>
  </div>
  <div class="rr-with-po-modal" :class="{'active':IsModalActive}" v-on:click="IsModalActive=!IsModalActive">
    <div class="middle-rr-withpo">
      <table>
        <tr>
          <th>ItemCode</th>
          <th>Price</th>
          <th>Unit</th>
          <th>Description</th>
          <th>Qty delivered</th>
          <th>Qty accepted</th>
          <th>Add</th>
        </tr>
        <tr v-for="(rrvalidator, count) in rrvalidatorwpo">
          <td v-if="rrvalidator.ItemCode!=null">{{rrvalidator.ItemCode}}</td>
          <td v-else><i class="material-icons decliner">do_not_disturb</i></td>
          <td>₱{{formatPrice(rrvalidator.Price)}}</td>
          <td>{{rrvalidator.Unit}}</td>
          <td>{{rrvalidator.Description}}</td>
          <td><input type="text" v-model="QuantityDelivered[count]"></td>
          <td><input type="text" v-model="QuantityAccepted[count]"></td>
          <td><button type="button" v-on:click="addtosession(rrvalidator,count)"> <i class="material-icons">add</i></button></td>
        </tr>
      </table>
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
  data () {
     return {
       IsModalActive:false,
       QuantityDelivered:[],
       QuantityAccepted:[],
       SessionItems:[],
       InvoiceNo:'',
       Carrier:'',
       DeliveryReceiptNo:'',
       Note:'',
       ReceivedBy:'',
       Verifiedby:'',
       ReceivedOriginalby:'',
       PostedtoBINby:'',
       HideSubmitBtn:false,
       }
     },
     props: ['pomasters','rrvalidatorwpo','auditors','managers','clerks','allusers'],
      methods: {
        addtosession(data,count)
        {
          this.$loading('Adding');
          var vm=this;
          axios.post(`/rr-storing-session-with-po`,{
            ItemCode:data.ItemCode,
            AccountCode:data.AccountCode,
            Description:data.Description,
            QuantityDelivered:this.QuantityDelivered[count],
            QuantityAccepted:this.QuantityAccepted[count],
            UnitCost:data.Price,
            Unit:data.Unit,
            PONo:this.rrvalidatorwpo[0].PONo,
          }).then(function(response)
          {
            if (response.data.error!=null)
            {
              vm.$toast.top(response.data.error);
              vm.$loading.close();
            }else
            {
              vm.showaddedSession();
              vm.$toast.top('Successfully Added');
              vm.$loading.close();
            }
          },function(error)
          {
            console.log(error)
            {
              if (error.response.data.QuantityDelivered!=null)
              {
                vm.$toast.top(error.response.data.QuantityDelivered[0]);
              }else if (error.response.data.QuantityAccepted!=null)
              {
                vm.$toast.top(error.response.data.QuantityAccepted[0]);
              }
              vm.$loading.close();
            }
          });
        },
        showaddedSession()
        {
          var vm=this;
          axios.get(`/show-rr-session-data`).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'SessionItems',response.data);
          });
        },
        formatPrice(value) {
              let val = (value/1).toFixed(2).replace('.', '.')
              return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
        deleteSession(count)
        {
          this.$loading('Removing');
          var vm=this;
          axios.delete(`/delete-session-with-po/`+count).then(function(response)
          {
            console.log(response);
            vm.showaddedSession();
            vm.$toast.top('Successfully removed');
            vm.$loading.close();
          });
        },
        SubmitRRwithPO()
        {
          this.$loading('Submitting');
          this.HideSubmitBtn=true;
          var vm=this;
          axios.post(`/save-rr-with-po-to-db`,{
            Receivedby:this.ReceivedBy,
            Verifiedby:this.Verifiedby,
            ReceivedOriginalby:this.ReceivedOriginalby,
            PostedtoBINby:this.PostedtoBINby,
            InvoiceNo:this.InvoiceNo,
            Carrier:this.Carrier,
            DeliveryReceiptNo:this.DeliveryReceiptNo,
            Note:this.Note,
            PONo:this.rrvalidatorwpo[0].PONo,
          }).then(function(response)
          {
            console.log(response);
            if (response.data.error!=null)
            {

              vm.$toast.top(response.data.error);
              Vue.set(vm.$data,'HideSubmitBtn',false);
            }else
            {
               window.location=response.data.redirect;
            }
            vm.$loading.close();
          },function(error)
          {
            console.log(error);
            Vue.set(vm.$data,'HideSubmitBtn',false);

            if (error.response.data.InvoiceNo!=null)
            {
              vm.$toast.top(error.response.data.InvoiceNo[0]);
            }else if (error.response.data.DeliveryReceiptNo!=null)
            {
              vm.$toast.top(error.response.data.DeliveryReceiptNo[0]);
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
            }
            vm.$loading.close();
          });
        }
      },
      mounted () {
        this.showaddedSession();
      },
      components: {
         Longpress
       },
  }
</script>
