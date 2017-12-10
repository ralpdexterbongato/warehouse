<template lang="html">
  <div class="po-full-buttons" v-if="OrderMaster.users!=null">
    <div class="print-po-btn">
      <div class="Approve-replacer-accept-cant Request-manager-replace" v-if="ApprovalReplacerCanSignature">
        <h6 class="approve-managerreplace-note"><i class="material-icons color-blue">info</i>
          The <span class="color-blue">Warehouse section</span> is asking for your signature b/c the General Manager is not available
        </h6>
        <span class="approval-po-replacer-btn" :class="{'hide':SignatureApproveReplacerHide}">
          <longpress duration="3" class="signaturePObtn waves-effect waves-light" :on-confirm="ApproveAuthorizeInBehalf"  pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
          <i class="material-icons">edit</i> Signature
          </longpress>
          <longpress  duration="3" class="declinePObtn waves-effect waves-light" :on-confirm="RefuseToAuthorizeInBehalf"  pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
          <i class="material-icons">close</i> I Can't
          </longpress>
        </span>
      </div>
      <span class="make-rr-and-print" v-if="AlreadyApproved">
        <div class="left-detail-po">
          <a :href="'/PO.pdf/'+pono.PONo"><button type="submit" name="PONo" value="ponohere"><i class="material-icons">print</i></button></a>
          <li class="pending-delivery-number"><h1>waiting for: <span class="color-blue">{{remaining}}</span> items</h1></li>
        </div>
         <div class="rr-with-po-btn" v-if="user.Role==4||user.Role==3">
            <a :href="'/create-rr-w-po/'+pono.PONo"><button type="button"><i class="material-icons">add</i> RR</button></a>
         </div>
      </span>
      <div v-else class="empty-left">
      </div>
      <div class="signature-btns-wrap-po" :class="{'hide':SignatureBtnHide}" v-if="GMCanSignature">
        <longpress duration="3" class="signaturePObtn" :on-confirm="GMsignaturePO"  pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
        <i class="material-icons">edit</i> Signature
        </longpress>
        <longpress  duration="3" class="declinePObtn" :on-confirm="GMDeclinedPO" pressing-text="confirmed in {$rcounter}" action-text="please wait . .">
        <i class="material-icons">close</i> Decline
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
          <h6 v-if="OrderMaster.users[0].pivot.Signature=='0'"><img :src="'/storage/signatures/'+OrderMaster.users[0].Signature" alt="signature"></h6>
          <h6 v-else-if="((OrderMaster.users[1]!=null)&&(OrderMaster.users[1].pivot.Signature=='0'))"><p>For :</p><img :src="'/storage/signatures/'+OrderMaster.users[1].Signature" alt="signature"></h6>
          <h3>
            {{OrderMaster.users[0].FullName}}
            <i class="material-icons decliner" v-if="OrderMaster.users[0].pivot.Signature=='1'">close</i>
          </h3>
          <label>{{OrderMaster.users[0].Position}}</label>
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
         SignatureBtnHide:false,
         SignatureApproveReplacerHide:false,
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
         this.SignatureBtnHide=true;
         var vm=this;
         axios.put(`/gm-signature-po/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
          vm.fetchPOPreview();
        });
      },
      GMDeclinedPO()
      {
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/gm-decline-po/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
          vm.fetchPOPreview();
        });
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      },
      RefuseToAuthorizeInBehalf()
      {
        this.SignatureApproveReplacerHide=true;
        var vm=this;
        axios.put(`/declined-Authorize-inbehalf/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
          vm.fetchPOPreview();
        });
      },
      ApproveAuthorizeInBehalf()
      {
        this.SignatureApproveReplacerHide=true;
        var vm=this;
        axios.put(`/authorize-in-behalf-confirmed/`+this.pono.PONo).then(function(response)
        {
          console.log(response);
          vm.fetchPOPreview();
        });
      }
     },
     created () {
       this.fetchPOPreview();
     },
     computed: {
       GMCanSignature:function()
       {
         if((this.OrderMaster.users[0].id==this.user.id)&&(this.OrderMaster.users[0].pivot.Signature==null)&&((this.OrderMaster.users[1]==null)||(this.OrderMaster.users[1]!=null && this.OrderMaster.users[1].pivot.Signature==null)))
         {
           return true;
         }else
         {
           return false;
         }
       },
       ApprovalReplacerCanSignature: function()
       {
         if((this.OrderMaster.users[1]!=null)&&(this.user.id==this.OrderMaster.users[1].id)&&(this.OrderMaster.users[0].pivot.Signature==null)&&(this.OrderMaster.users[1].pivot.Signature==null))
         {
           return true;
         }else
         {
           return false;
         }
       },
       AlreadyApproved: function()
       {
         if ((this.OrderMaster.users[0].pivot.Signature=='0')||((this.OrderMaster.users[1]!=null)&&(this.OrderMaster.users[1].pivot.Signature=='0')))
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
