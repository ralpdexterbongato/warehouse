<template lang="html">
<div class="">
  <div class="btns-mr-full">
    <div>
      <a :href="'/mr-print/'+this.mrno.MRNo" v-if="(((MRMaster.RecommendedbySignature!=null)&&(MRMaster.GeneralManagerSignature!=null)&&(MRMaster.ReceivedbySignature!=null))||((MRMaster.RecommendedbySignature!=null)&&(MRMaster.ApprovalReplacerSignature!=null)&&(MRMaster.ReceivedbySignature!=null)))"><button type="submit" name="MRNo" value="mrnohere"><i class="fa fa-print"></i> Print</button></a>
      <h6 class="approve-managerreplace-note" v-if="user.Fname+' '+user.Lname==MRMaster.ApprovalReplacer"><i class="fa fa-info-circle color-blue"></i>
        The <span class="color-blue">{{MRMaster.WarehouseMan}}</span> is asking for your signature b/c the General Manager is not available
      </h6>
    </div>
    <div class="signature-MR-btns">
      <span class="Approve-MR-inBehalf-btn" v-if="MRMaster.ApprovalReplacer==user.Fname+' '+user.Lname&&MRMaster.GeneralManagerSignature==null&&MRMaster.ApprovalReplacerSignature==null&&MRMaster.RecommendedbySignature!=null">
        <button type="button" v-on:click="SignatureApproveInBehalf()"><i class="fa fa-pencil"></i> Signature</button>
        <button type="button" v-on:click="refuseApproveInBehalf()"><i class="fa fa-times"></i> Refuse</button>
      </span>
      <span v-if="(((MRMaster.RecommendedbySignature==null)&&(MRMaster.Recommendedby==user.Fname+' '+user.Lname)&&(MRMaster.IfDeclined==null))||((MRMaster.GeneralManagerSignature==null)&&(MRMaster.GeneralManager==user.Fname+' '+user.Lname)&&(MRMaster.RecommendedbySignature!=null)&&(MRMaster.IfDeclined==null))||((MRMaster.ReceivedbySignature==null)&&(MRMaster.ReceivedbySignature==null)&&(MRMaster.Receivedby==user.Fname+' '+user.Lname)&&(MRMaster.IfDeclined==null)&&((MRMaster.GeneralManagerSignature!=null)||(MRMaster.ApprovalReplacerSignature!=null))))">
        <div class="signature-mr">
          <button type="submit" v-on:click="signatureMR()" name="MRNo"><i class="fa fa-pencil"></i> Signature</button>
        </div>
        <div class="decline-mr">
          <button v-on:click="declineMR()" type="submit" name="MRNo" value="MRNohere">Decline</button>
        </div>
      </span>
    </div>
  </div>
  <div class="mr-full-bondpaper">
    <div class="mr-top-titles">
      <h1>BOHOL I ELECTRIC COOPERATIVE, INC.</h1>
      <h3>Cabulijan, Tubigon, Bohol</h3>
      <h2>MEMORANDUM RECEIPT FOR EQUIPMENT . SEMI-EXPENDABLE AND NON EXPENDABLE PROPERTY</h2>
    </div>
    <div class="list-number-dates">
      <div class="DateNumBox">
        <h1>RR No. {{MRMaster.RRNo}}</h1>
        <p>{{MRMaster.RRDate}}</p>
      </div>
      <div class="DateNumBox">
        <h1>RV No. {{MRMaster.RVNo}}</h1>
        <p>{{MRMaster.RVDate}}</p>
      </div>
      <div class="DateNumBox">
        <h1>MR No.{{MRMaster.MRNo}}</h1>
        <p>{{MRMaster.MRDate}}</p>
      </div>
    </div>
    <div class="acknowledgeParagraph">
      <p class="align-center">I HEREBY ACKNOWLEGE to have received from
        <span class="bold">{{MRMaster.WarehouseMan}}</span> Warehouseman,
        the following</p><p> property
        for which I am responsible, subject to the provision of the usual accounting and auditing rules and regulations
        and which will be used for General Services.
      </p>
    </div>
    <div class="table-mr-list-container">
      <table>
        <tr>
          <th>QUANTITY</th>
          <th>UNIT</th>
          <th>NAME AND DESCRIPTION</th>
          <th>PROPERTY NUMBER</th>
          <th>UNIT VALUE</th>
          <th>TOTAL VALUE</th>
          <th>REMARKS</th>
        </tr>
        <tr v-for="mrdata in MRDetail">
          <td>{{mrdata.Quantity}}</td>
          <td>{{mrdata.Unit}}</td>
          <td>{{mrdata.NameDescription}}</td>
          <td></td>
          <td>{{formatPrice(mrdata.UnitValue)}}</td>
          <td>{{formatPrice(mrdata.TotalValue)}}</td>
          <td>{{mrdata.Remarks}}</td>
        </tr>
        <!-- @endforeach -->
        <tr>
          <td>.</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>
    </div>
    <div class="note-mr-container">
      <p>Note:{{MRMaster.Note}}</p>
    </div>
    <div class="bottom-mr-bondpaper">
      <div class="left-reference-box">
        <div class="reference-box">
          <h3>REFERENCE:</h3>
          <p>Purchase from: {{MRMaster.Supplier}}</p>
          <p>Invoice number: {{MRMaster.InvoiceNo}}</p>
        </div>
        <div class="instruction-for-mr">
          <h3>INSTRUCTION</h3>
          <p>
            This form shall be prepared in FOUR (4)<br>
            LEGIBLE COPIES,DISTRIBUTION:(1) ORIGINAL<br>
            should be KEPT by the Accountable Officer<br>
            (2) DUPLICATE must be FILED in the Personal<br>
             file of the Employee Concerned. (3) TRIPLICATE <br>
             should be FILED in the OFFICE OF THE <br>
             Accounting Section.(4) QUADRUPLICATE <br>
             must be KEPT by the Responsible Employee.
           </p>
        </div>
      </div>
      <div class="MR-Signatures-container">
        <h4>P.O. Number: {{MRMaster.PONo}}</h4>
        <div class="signature-mr-box">
          <label>RECOMMENDING APPROVAL:</label>
            <h5 v-if="MRMaster.RecommendedbySignature!=null"><img :src="'/storage/signatures/'+MRMaster.RecommendedbySignature" alt="signature"></h5>
          <h3>
            {{MRMaster.Recommendedby}}
              <i v-if="(MRMaster.Recommendedby==MRMaster.IfDeclined)" class="fa fa-times decliner"></i>
          </h3>
          <p>{{MRMaster.RecommendedbyPosition}}</p>
        </div>
        <div class="signature-mr-box">
          <label>APPROVED:</label>
            <h5 v-if="MRMaster.GeneralManagerSignature!=null"><img :src="'/storage/signatures/'+MRMaster.GeneralManagerSignature" alt="signature"></h5>
            <h5 v-else-if="MRMaster.ApprovalReplacerSignature!=null"><p>For :</p><img :src="'/storage/signatures/'+MRMaster.ApprovalReplacerSignature" alt="signature"></h5>
          <h3>
            {{MRMaster.GeneralManager}}
              <i v-if="MRMaster.GeneralManager==MRMaster.IfDeclined" class="fa fa-times decliner"></i>
          </h3>
          <p>General Manager</p>
        </div>
        <div class="signature-mr-box">
          <label>RECEIVED:</label>
            <h5 v-if="MRMaster.ReceivedbySignature!=null"><img :src="'/storage/signatures/'+MRMaster.ReceivedbySignature" alt="signature"></h5>
          <h3>{{MRMaster.Receivedby}}<i class="fa fa-times decliner" v-if="MRMaster.Receivedby==MRMaster.IfDeclined"></i></h3>
          <p>{{MRMaster.ReceivedbyPosition}}</p>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios';
  export default {
    props: ['mrno','user'],
     data () {
        return {
          MRMaster:[],
          MRDetail:[],
        }
      },
     methods: {
       fetchData()
       {
         var vm=this;
         axios.get(`/full-preview-MR-Fetch/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'MRMaster',response.data.MRMaster[0]);
          Vue.set(vm.$data,'MRDetail',response.data.MRDetail);
        });
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
      signatureMR()
      {
        var vm=this;
        axios.put(`/signature-MR/`+this.mrno.MRNo).then(function(response)
      {
        console.log(response);
      });
      this.fetchData();
      },
      declineMR()
      {
        var vm=this;
        axios.put(`/Decline-MR/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
        })
      },
      refuseApproveInBehalf()
      {
        var vm=this;
        axios.put(`/mr-approve-inbehalf-refused/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchData();
      },
      SignatureApproveInBehalf()
      {
        var vm=this;
        axios.put(`/confirmApproveinBehalf/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
        });
        this.fetchData();
      }
     },
     created()
     {
       this.fetchData();
     }
  }
</script>
