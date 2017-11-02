<template lang="html">
<div class="rr-preview-vue">
  <div class="signature-btn" :class="{'hide':SignatureBtnHide}" v-if="(((RRMaster.Verifiedby==user.FullName)&&(RRMaster.VerifiedbySignature==null)&&(RRMaster.IfDeclined==null))||((RRMaster.ReceivedOriginalby==user.FullName)&&(RRMaster.ReceivedOriginalbySignature==null)&&(RRMaster.IfDeclined==null))||((RRMaster.PostedtoBINby==user.FullName)&&(RRMaster.PostedtoBINbySignature==null)&&(RRMaster.IfDeclined==null)))">
    <longpress id="RRsignature" duration="3" :on-confirm="signature"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
      <i class="fa fa-pencil"></i> Signature
    </longpress>
    <longpress id="RRdecline" duration="3" :on-confirm="declinesignature"  pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
      <i class="fa fa-times"></i> Decline
    </longpress>
  </div>
  <div class="print-RR-btn" v-else-if="((RRMaster.ReceivedOriginalbySignature!=null)&&(RRMaster.VerifiedbySignature!=null)&&(RRMaster.PostedtoBINbySignature!=null))">
      <a :href="'/RR.pdf/'+RRMaster.RRNo"><button type="submit" class="bttn-unite bttn-xs bttn-primary" name="RRNo" value="RRNohere"><i class="fa fa-file-pdf-o"></i> print</button></a>
    <div>
      <a :href="'/view-list-MR-of-RR/'+RRMaster.RRNo" v-if="checkMR!=0"><button type="button" id="full-mr-preview-btn" class="bttn-unite bttn-xs bttn-primary"><i class="fa fa-folder"></i> M.R. list</button></a>
      <a :href="'/create-mr/'+RRMaster.RRNo" v-if="(((user.Role==4)||(user.Role==3))&&(RRMaster.IfDeclined==null))"><button type="button" class="make-mr bttn-unite bttn-xs bttn-primary"><i class="fa fa-plus"></i> Make M.R.</button></a>
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
            <li><label>Supplier:</label><h4>{{RRMaster.Supplier}}</h4></li>
            <li><label>Address:</label><h4>{{RRMaster.Address}}</h4></li>
            <li><label>Invoice No.:</label><h4>{{RRMaster.InvoiceNo}}</h4></li>
            <li><label>R.V. No.:</label><h4>{{RRMaster.RVNo}}</h4></li>
          </ul>
        </div>
        <div class="RRmaster-right">
          <ul>
            <li><label>Carrier:</label>
              <h4 v-if="(RRMaster.Carrier!=null)">{{RRMaster.Carrier}}</h4>
            </li>
            <li><label>Delivery Receipt No:</label>
              <h4 v-if="RRMaster.DeliveryReceiptNo!=null">{{RRMaster.DeliveryReceiptNo}}</h4>
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
          <tr v-for="data in RRconfirmationDetails">
            <td>{{data.ItemCode}}</td>
            <td>{{data.Description}}</td>
            <td>{{data.Unit}}</td>
            <td>{{data.RRQuantityDelivered}}</td>
            <td>{{data.QuantityAccepted}}</td>
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
      <h1 class="noteRR"><label>Note:</label><p>{{RRMaster.Note}}</p></h1>
      <div class="RRSignatures-container">
        <div class="bottom-signatures-rr">
          <div class="signature-rr-left">
            <label>RECEIVED BY:</label>
            <div class="signatureRR-content">
              <h2 v-if="RRMaster.ReceivedbySignature!=null">
                <img :src="'/storage/signatures/'+RRMaster.ReceivedbySignature" alt="signature">
              </h2>
              <h4>{{RRMaster.Receivedby}}</h4>
              <p>{{RRMaster.ReceivedbyPosition}}</p>
            </div>
          </div>
          <div class="signature-rr-right">
            <h2 v-if="RRMaster.ReceivedOriginalbySignature!=null"><img :src="'/storage/signatures/'+RRMaster.ReceivedOriginalbySignature" alt="signature"></h2>
            <label>RECEIVED ORIGINAL BY:</label>
            <div class="signatureRR-content">

              <h4>{{RRMaster.ReceivedOriginalby}}
                <i class="fa fa-times" v-if="RRMaster.IfDeclined==RRMaster.ReceivedOriginalby"></i>
              </h4>
              <p>{{RRMaster.ReceivedOriginalbyPosition}}</p>
            </div>
          </div>
        </div>
        <div class="bottom-signatures-rr">
          <div class="signature-rr-left">
            <label>VERIFIED BY:</label>
            <div class="signatureRR-content">
              <h2 v-if="RRMaster.VerifiedbySignature!=null"><img :src="'/storage/signatures/'+RRMaster.VerifiedbySignature" alt="signature"></h2>
              <h4>
                {{RRMaster.Verifiedby}}
                <i class="fa fa-times" v-if="RRMaster.IfDeclined==RRMaster.Verifiedby"></i>
              </h4>
              <p>{{RRMaster.VerifiedbyPosition}}</p>
            </div>
          </div>
          <div class="signature-rr-right">
            <h2 v-if="RRMaster.PostedtoBINbySignature!=null"><img :src="'/storage/signatures/'+RRMaster.PostedtoBINbySignature" alt="signature"></h2>
            <label>POSTED TO BIN CARD BY:</label>
            <div class="signatureRR-content">
              <h4>
                {{RRMaster.PostedtoBINby}}
                <i class="fa fa-times" v-if="RRMaster.IfDeclined==RRMaster.PostedtoBINby"></i>
              </h4>
              <p>{{RRMaster.PostedtoBINbyPosition}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</template>

<script>
import axios from 'axios'
import Longpress from 'vue-longpress'
  export default {
    data () { return {
      RRMaster:[],
      checkMR:0,
      RRconfirmationDetails:[],
      Netsales:0,
      VAT:0,
      TOTALamt:0,
      SignatureBtnHide:false,
     }
    },
    props: ['user','rrno'],
    methods: {
      FetchData()
      {
        var vm=this;
        axios.get(`/RR-fullpreview-fetch-data/`+this.rrno.RRNo).then(function(response)
        {
          console.log(response);
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
          console.log(response);
        });
        this.FetchData();
      },
      declinesignature()
      {
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/decline-this-RR/`+this.rrno.RRNo).then(function(response)
        {
          console.log(response);
        });
        this.FetchData();
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
  }
</script>
