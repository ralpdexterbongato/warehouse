<template lang="html">
  <div class="po-full-buttons">
    <div class="print-po-btn">
      <div class="Approve-replacer-accept-cant Request-manager-replace" v-if="(user.Fname+' '+user.Lname==OrderMaster.ApprovalReplacer)&&(OrderMaster.GeneralManagerSignature==null)&&(OrderMaster.ApprovalReplacerSignature==null)">
        <h6 class="approve-managerreplace-note"><i class="fa fa-info-circle color-blue"></i>
          The <span class="color-blue">Warehouse section</span> is asking for your signature b/c the General Manager is not available
        </h6>
        <span class="approval-po-replacer-btn">
          <longpress duration="3" class="signaturePObtn" :on-confirm="ApproveAuthorizeInBehalf"  pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
          <i class="fa fa-pencil"></i> Signature
          </longpress>
          <longpress  duration="3" class="declinePObtn" :on-confirm="RefuseToAuthorizeInBehalf"  pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
          <i class="fa fa-times"></i> I Can't
          </longpress>
        </span>
      </div>
      <span class="make-rr-and-print" v-if="((OrderMaster.GeneralManagerSignature!=null)||(OrderMaster.ApprovalReplacerSignature!=null))">
        <div class="left-detail-po">
          <a :href="'/po-download-print/'+pono.PONo"><button type="submit" class="bttn-unite bttn-xs bttn-primary" name="PONo" value="ponohere"><i class="fa fa-print"></i> Print</button></a>
          <li class="pending-delivery-number"><h1>Unreceived items: <span class="color-blue">{{remaining}}</span></h1></li>
        </div>
         <div class="rr-with-po-btn" v-if="user.Role==4||user.Role==3">
            <a :href="'/create-rr-w-po/'+pono.PONo"><button type="button" class="bttn-unite bttn-xs bttn-primary"><i class="fa fa-plus"></i> RR</button></a>
         </div>
      </span>
      <div v-else class="empty-left">
      </div>
      <div class="signature-btns-wrap-po" v-if="((user.Role==2)&&(OrderMaster.GeneralManager==user.Fname+' '+user.Lname)&&(OrderMaster.GeneralManagerSignature==null)&&(OrderMaster.IfDeclined==null)&&(OrderMaster.ApprovalReplacerSignature==null))">
        <longpress duration="3" class="signaturePObtn" :on-confirm="GMsignaturePO"  pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
        <i class="fa fa-pencil"></i> Signature
        </longpress>
        <longpress  duration="3" class="declinePObtn" :on-confirm="GMDeclinedPO" pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
        <i class="fa fa-times"></i> Decline
        </longpress>
      </div>
  </div>
  <div class="PO-bondpaper">
    <div class="PO-top-titles">
      <h3>BOHOL 1 ELECTRIC COOPERATIVE INC.</h3>
      <h3>CABULIJAN, TUBIGON, BOHOL</h3>
      <p>Tel# 508-9741 / 508-9731</p>
      <h1>PURCHASE ORDER</h1>
    </div>
    <div class="po-master-data">
      <div class="left-data-po">
        <ul>
          <li><label>TO: </label> <h1>{{OrderMaster.Supplier}}</h1></li>
          <li><label></label><h1>{{OrderMaster.Address}}</h1></li>
          <li><label></label><h1>Tel# {{OrderMaster.Telephone}}</h1></li>
        </ul>
      </div>
      <div class="right-data-po">
        <ul>
          <li><label>P.O. No.</label><h1>{{OrderMaster.PONo}}</h1></li>
          <li><label>DATE:</label><h1>{{OrderMaster.PODate}}</h1></li>
          <li><label>TERMS:</label><h1></h1></li>
          <li>( {{OrderMaster.Purpose}} )</li>
        </ul>
      </div>
    </div>
    <div class="po-statement">
      <p>Please furnish the following articles subject to all terms and conditions stated here and in accordance with the quotation.</p>
    </div>
    <div class="PO-Details-table">
      <table>
        <tr>
          <th>ITEM</th>
          <th>QTY</th>
          <th>UNIT</th>
          <th>DESCRIPTION</th>
          <th>UNIT PRICE</th>
          <th>AMOUNT</th>
        </tr>
          <tr v-for="(podetail,index) in OrderMaster.p_o_details">
            <td>{{index+1}}</td>
            <td>{{podetail.Qty}}</td>
            <td>{{podetail.Unit}}</td>
            <td>{{podetail.Description}}</td>
            <td>{{formatPrice(podetail.Price)}}</td>
            <td>{{formatPrice(podetail.Amount)}}</td>
          </tr>
        <tr class="total-amt-po">
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>TOTAL AMOUNT:</td>
          <td>{{formatPrice(totalAmt)}}</td>
        </tr>
      </table>
    </div>
    <div class="RV-data-in-PO">
      <h1>APPROVED RV No.{{OrderMaster.RVNo}}</h1><p>Dated: {{OrderMaster.RVDate}}</p>
    </div>
    <div class="signatures-po">
      <div class="label-signatures-po">
       <h4>ACCEPTED ORDER AND RECEIVED<br> ORIGINAL COPY OF THIS PURCHASE<br> ORDER:</h4>
       <h1></h1>
       <p>(Seller)</p>
       <h1></h1>
      </div>
      <div class="label-signatures-po">
        <h4>ORDER ISSUED AND AUTHORIZED<br> BY:</h4>
        <li>
          <h6 v-if="OrderMaster.GeneralManagerSignature!=null"><img :src="'/storage/signatures/'+OrderMaster.GeneralManagerSignature" alt="signature"></h6>
          <h6 v-else-if="OrderMaster.ApprovalReplacerSignature!=null"><p>For :</p><img :src="'/storage/signatures/'+OrderMaster.ApprovalReplacerSignature" alt="signature"></h6>
          <h3>
            {{OrderMaster.GeneralManager}}
            <i class="fa fa-times decliner" v-if="OrderMaster.IfDeclined!=null"></i>
          </h3>
          <label>General Manager</label>
        </li>
      </div>
    </div>
  </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import Longpress from 'vue-longpress';
  export default {
     data () {
       return {
         OrderMaster:[],
         totalAmt:0,
         remaining:0,
     }
   },
   components: {
      Longpress
    },
     props: ['pono','user'],
     methods: {
       fetchPOPreview()
       {
         var vm=this;
         axios.get(`/po-full-preview-fetch/`+this.pono.PONo).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'OrderMaster',response.data.OrderMaster[0])
           Vue.set(vm.$data,'totalAmt',response.data.totalAmt)
           Vue.set(vm.$data,'remaining',response.data.remainingUnreceived)
         })
       },
       GMsignaturePO()
       {
         var vm=this;
         axios.put(`/gm-signature-po/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
        });
        this.fetchPOPreview();
      },
      GMDeclinedPO()
      {
        var vm=this;
        axios.put(`/gm-decline-po/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
        });
        this.fetchPOPreview();
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
      RefuseToAuthorizeInBehalf()
      {
        var vm=this;
        axios.put(`/declined-Authorize-inbehalf/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
        });
        this.fetchPOPreview();
      },
      ApproveAuthorizeInBehalf()
      {
        var vm=this;
        axios.put(`/authorize-in-behalf-confirmed/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
        });
        this.fetchPOPreview();
      }
     },
     created () {
       this.fetchPOPreview();
     },
  }
</script>
