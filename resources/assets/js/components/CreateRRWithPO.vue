<template lang="html">
<div class="vue-rr-create-withpo">
  <div class="selected-session-rr-w-po">
    <div class="add-item-rr-w-pobtn">
      <button type="button"v-on:click="IsModalActive=!IsModalActive" class="bttn-unite bttn-xs bttn-primary"><i class="fa fa-plus"></i> items</button>
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
          <th>Cancel</th>
        </tr>
        <tr v-for="(sessionitem,count) in SessionItems">
          <td v-if="sessionitem.ItemCode!=null">{{sessionitem.ItemCode}}</td>
          <td v-else><i class="fa fa-ban decliner"></i></td>
          <td v-if="sessionitem.AccountCode!=null">{{sessionitem.AccountCode}}</td>
          <td v-else><i class="fa fa-ban decliner"></i></td>
          <td>{{sessionitem.Description}}</td>
          <td>₱{{formatPrice(sessionitem.UnitCost)}}</td>
          <td>{{sessionitem.QuantityDelivered}}</td>
          <td>{{sessionitem.QuantityAccepted}}</td>
          <td>{{sessionitem.Unit}}</td>
          <td>₱{{formatPrice(sessionitem.Amount)}}</td>
          <td><i class="fa fa-trash" v-on:click="deleteSession(count)"></i></td>
        </tr>
      </table>
    </div>
  </div>
  <div class="right-form-rr-with-po">
    <input type="text" autocomplete="off" name="InvoiceNo" v-model="InvoiceNo" placeholder="Invoice number">
    <input type="text" autocomplete="off" name="Carrier" v-model="Carrier" placeholder="Carrier">
    <input type="text" autocomplete="off" name="DeliveryReceiptNo" v-model="DeliveryReceiptNo" placeholder="Delivery Receipt Number">
    <input type="text" autocomplete="off" name="Note" v-model="Note" placeholder="Note">
    <select name="Verifiedby" v-model="Verifiedby" :class="[Verifiedby!=null ?'black':'']">
      <option :value="null" class="gray">Verified by</option>
      <option v-for="manager in managers" class="black" :value="manager.id">{{manager.Fname}} {{manager.Lname}}</option>
    </select>
    <select name="ReceivedOriginalby" v-model="ReceivedOriginalby" :class="[ReceivedOriginalby!=null ?'black':'']">
      <option :value="null" class="gray">Received Originaly by</option>
      <option v-for="auditor in auditors" class="black" :value="auditor.id">{{auditor.Fname}} {{auditor.Lname}}</option>
    </select>
    <select name="PostedtoBINby" v-model="PostedtoBINby" :class="[PostedtoBINby!=null ?'black':'']" >
      <option :value="null" class="gray">Posted to BIN by</option>
      <option v-for="clerk in clerks" class="black" :value="clerk.id">{{clerk.Fname}} {{clerk.Lname}}</option>
    </select>
    <button type="button" name="button" v-on:click="SubmitRRwithPO()">Submit</button>
  </div>
  <div class="rr-with-po-modal animated" :class="{'fadeIn active':IsModalActive}" v-on:click="IsModalActive=!IsModalActive">
    <div class="middle-rr-withpo" v-on:click="IsModalActive=!IsModalActive">
      <table>
        <tr>
          <th>ItemCode</th>
          <th>AccountCode</th>
          <th>Price</th>
          <th>Unit</th>
          <th>Description</th>
          <th>Qty delivered</th>
          <th>Qty accepted</th>
          <th>Add</th>
        </tr>
        <tr v-for="(rrvalidator, count) in rrvalidatorwpo">
          <td v-if="rrvalidator.ItemCode!=null">{{rrvalidator.ItemCode}}</td>
          <td v-else><i class="fa fa-ban decliner"></i></td>
          <td v-if="rrvalidator.AccountCode!=null">{{rrvalidator.AccountCode}}</td>
          <td v-else><i class="fa fa-ban decliner"></i></td>
          <td>₱{{formatPrice(rrvalidator.Price)}}</td>
          <td>{{rrvalidator.Unit}}</td>
          <td>{{rrvalidator.Description}}</td>
          <td><input type="text" v-model="QuantityDelivered[count]"></td>
          <td><input type="text" v-model="QuantityAccepted[count]"></td>
          <td><button type="button" v-on:click="IsModalActive=!IsModalActive,addtosession(rrvalidator,count)" class="bttn-unite bttn-xs bttn-primary"> <i class="fa fa-plus"></i></button></td>
        </tr>
      </table>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios';
  export default {
  data () {
     return {
       IsModalActive:false,
       QuantityDelivered:[],
       QuantityAccepted:[],
       successAlerts:'',
       laravelerrors:[],
       ownerrors:'',
       SessionItems:[],
       InvoiceNo:'',
       Carrier:'',
       DeliveryReceiptNo:'',
       Note:'',
       Verifiedby:null,
       ReceivedOriginalby:null,
       PostedtoBINby:null,
       }
     },
     props: ['pomasters','rrvalidatorwpo','auditors','managers','clerks'],
      methods: {
        addtosession(data,count)
        {
          var vm=this;
          axios.post(`/rr-storing-session-with-po`,{
            ItemCode:data.ItemCode,
            AccountCode:data.AccountCode,
            Description:data.Description,
            QuantityDelivered:this.QuantityDelivered[count],
            QuantityAccepted:this.QuantityAccepted[count],
            UnitCost:data.Price,
            Unit:data.Unit,
            MaxQty:data.Qty,
          }).then(function(response)
          {
            if (response.data.error!=null)
            {
              Vue.set(vm.$data,'ownerrors',response.data.error);
              Vue.set(vm.$data,'successAlerts','');
              Vue.set(vm.$data,'laravelerrors','');
            }else
            {
              Vue.set(vm.$data,'successAlerts','Successfully added !');
              Vue.set(vm.$data,'ownerrors','');
              Vue.set(vm.$data,'laravelerrors','');
            }
            console.log(response);
          },function(error)
          {
            console.log(error)
            {
              Vue.set(vm.$data,'laravelerrors',error.response.data);
              Vue.set(vm.$data,'successAlerts','');
              Vue.set(vm.$data,'ownerrors','');
            }
          });
          this.showaddedSession();
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
          var vm=this;
          axios.delete(`/DeleteSession-RR/`+count).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'successAlerts','Successfully removed');
            Vue.set(vm.$data,'laravelerrors','');
            Vue.set(vm.$data,'ownerrors','');
          });
          this.showaddedSession();
        },
        SubmitRRwithPO()
        {
          var vm=this;
          axios.post(`/save-rr-with-po-to-db`,{
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
              Vue.set(vm.$data,'ownerrors',response.data.error);
            }else
            {
               window.location=response.data.redirect;
            }
          },function(error)
          {
            console.log(error);
            Vue.set(vm.$data,'laravelerrors',error.response.data)
          });
        }
      },
      mounted () {
        this.showaddedSession();
      },
  }
</script>
