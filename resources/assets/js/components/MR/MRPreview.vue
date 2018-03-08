<template lang="html">
<div v-if="MRMaster.users!=null">
  <div class="btns-mr-full">
    <div>
      <div class="empty-div-left file-edit-container" v-if="(((user.Role == 3)||(user.Role == 4)) && (MRMaster.Status==null))">
        <span class="edit-file" :class="ShowEdit==true?'hide':'show'" v-on:click="ShowEdit=true"><i class="material-icons">edit</i>Edit</span>
        <span class="edit-file" :class="ShowEdit==false?'hide':'show'">
          <span class="color-blue">Save?</span>
          <button type="button" v-on:click="ShowEdit=false,fetchData()">cancel</button>
          <button v-on:click="ShowEdit=false,updateSave()" type="button" name="button">Save</button>
          </span>
      </div>
      <a :href="'/MR.pdf/'+this.mrno.MRNo" v-if="AlreadyApproved"><button type="submit" name="MRNo" value="mrnohere"><i class="material-icons">print</i></button></a>
      <h6 class="approve-managerreplace-note" v-if="replacerCanSignature"><i class="material-icons color-blue">info</i>
        The <span class="color-blue">{{MRMaster.warehouseman.FullName}}</span> is asking for your signature b/c the General Manager is not available
      </h6>
    </div>
    <div class="signature-MR-btns">
      <span class="Approve-MR-inBehalf-btn" :class="{'hide':SignatureApproveReplacer}" v-if="replacerCanSignature">
        <longpress class="rvapprovebtn waves-effect waves-light" duration="3" :on-confirm="SignatureApproveInBehalf" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="material-icons">edit</i> Signature
        </longpress>
        <longpress class="RVdeclineBtn waves-effect waves-light" duration="3" :on-confirm="refuseApproveInBehalf" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="material-icons">close</i> I can't
        </longpress>
      </span>
      <span :class="{'hide':SignatureBtnHide}" v-if="UserCanSignature">
        <longpress  id="signatureMRbtn" class="waves-effect waves-light" duration="3" :on-confirm="signatureMR" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="material-icons">edit</i> Signature
        </longpress>
        <longpress  id="declineMRbtn" class="waves-effect waves-light" duration="3" :on-confirm="declineMR" pressing-text="confirm in {$rcounter}" action-text="Loading . . .">
          <i class="material-icons">close</i> Decline
        </longpress>
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
      <p>I HEREBY ACKNOWLEGE to have received from
        <span class="bold">{{MRMaster.warehouseman.FullName}}</span> Warehouseman,
        the following<br> property
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
        <tr v-for="(mrdata,key) in MRDetail">
          <td>
            <span v-if="ShowEdit==false">{{mrdata.Quantity}}</span>
            <span v-else><input type="text" class="update-qty-input"  v-model="updateQty[key] = mrdata.Quantity"></span>
          </td>
          <td>{{mrdata.Unit}}</td>
          <td>{{mrdata.NameDescription}}</td>
          <td></td>
          <td>{{formatPrice(mrdata.UnitValue)}}</td>
          <td>{{formatPrice(mrdata.TotalValue)}}</td>
          <td>{{mrdata.Remarks}}</td>
        </tr>
      </table>
    </div>
    <div class="note-mr-container">
      <p>Note:
        <span v-if="ShowEdit==false">{{MRMaster.Note}}</span>
        <span v-else><input  class="note-update" type="text" v-model="updateNote"></span>
      </p>
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
            <h5 v-if="MRMaster.users[0].pivot.Signature=='0'"><img :src="'/storage/signatures/'+MRMaster.users[0].Signature" alt="signature"></h5>
          <h3>
            {{MRMaster.users[0].FullName}}
              <i v-if="(MRMaster.users[0].pivot.Signature=='1')" class="material-icons decliner">close</i>
          </h3>
          <p>{{MRMaster.users[0].Position}}</p>
        </div>
        <div class="signature-mr-box">
          <label>APPROVED:</label>
          <h5 v-if="MRMaster.users[1].pivot.Signature=='0'"><img :src="'/storage/signatures/'+MRMaster.users[1].Signature" alt="signature"></h5>
          <h5 v-else-if="((MRMaster.users[3]!=null)&&(MRMaster.users[3].pivot.Signature=='0'))"><p>For :</p><img :src="'/storage/signatures/'+MRMaster.users[3].Signature" alt="signature"></h5>
          <h3>
            {{MRMaster.users[1].FullName}}
              <i v-if="MRMaster.users[1].pivot.Signature=='1'" class="material-icons decliner">close</i>
          </h3>
          <p>{{MRMaster.users[1].Position}}</p>
        </div>
        <div class="signature-mr-box">
          <label>RECEIVED:</label>
            <h5 v-if="MRMaster.users[2].pivot.Signature=='0'"><img :src="'/storage/signatures/'+MRMaster.users[2].Signature" alt="signature"></h5>
          <h3>{{MRMaster.users[2].FullName}}<i class="material-icons decliner" v-if="MRMaster.users[2].pivot.Signature=='1'">close</i></h3>
          <p>{{MRMaster.users[2].Position}}</p>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios'
import Longpress from 'vue-longpress'
import 'vue2-toast/lib/toast.css'
import Toast from 'vue2-toast'
  export default {
    props: ['mrno','user'],
     data () {
        return {
          MRMaster:[],
          MRDetail:[],
          SignatureBtnHide:false,
          SignatureApproveReplacer:false,
          ShowEdit:false,
          updateNote:'',
          updateQty:[]
        }
      },
     methods: {
       updateSave()
       {
         if (confirm('Signatures will reset , continue?'))
         {
           this.$loading('Updating');

           var vm=this;
           axios.put(`/mr-update/`+this.mrno.MRNo,{
             NewQty:this.updateQty,
             NewNote:this.updateNote
           }).then(function(response)
          {
            console.log(response);
            if (response.data.error!=null)
            {
              vm.$toast.top(response.data.error);
            }else
            {
              vm.$toast.top('Updated successfully');
            }
            vm.fetchData();
            vm.$loading.close();
          }).catch(function(error)
          {
            console.log(error);
            vm.fetchData();
            vm.$loading.close();
          });
         }
       },
       fetchData()
       {
         var vm=this;
         axios.get(`/full-preview-MR-Fetch/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'MRMaster',response.data.MRMaster[0]);
          Vue.set(vm.$data,'MRDetail',response.data.MRDetail);
          vm.updateNote = response.data.MRMaster[0].Note;
        });
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
      signatureMR()
      {
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/signature-MR/`+this.mrno.MRNo).then(function(response)
      {
        console.log(response);
        vm.fetchData();
        vm.SignatureBtnHide=false;
      });
      },
      declineMR()
      {
        this.SignatureBtnHide=true;
        var vm=this;
        axios.put(`/Decline-MR/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
          vm.fetchData();
        })
      },
      refuseApproveInBehalf()
      {
        var vm=this;
        axios.put(`/mr-approve-inbehalf-refused/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
          vm.fetchData();
        });
      },
      SignatureApproveInBehalf()
      {
        this.SignatureApproveReplacer=true;
        var vm=this;
        axios.put(`/confirmApproveinBehalf/`+this.mrno.MRNo).then(function(response)
        {
          console.log(response);
          vm.fetchData();
        });
      }
     },
     created()
     {
       this.fetchData();
     },
     components: {
        Longpress
     },
     computed: {
       AlreadyApproved:function()
       {
         if ((this.MRMaster.users[0].pivot.Signature=='0')&&((this.MRMaster.users[1].pivot.Signature=='0')||(this.MRMaster.users[3]!=null && this.MRMaster.users[3].pivot.Signature=='0'))&&(this.MRMaster.users[2].pivot.Signature=='0'))
         {
           return true;
         }else
         {
           return false;
         }
       },
       replacerCanSignature: function()
       {
         if((this.MRMaster.users[3]!=null)&&(this.user.id==this.MRMaster.users[3].id)&&(this.MRMaster.users[3].pivot.Signature==null)&&(this.MRMaster.users[0].pivot.Signature=='0')&&(this.MRMaster.users[1].pivot.Signature==null))
         {
           return true;
         }else
         {
           return false;
         }
       },
       UserCanSignature: function()
       {
         if (((this.MRMaster.users[0].pivot.Signature==null)&&(this.MRMaster.users[0].id==this.user.id))||((this.MRMaster.users[1].pivot.Signature==null)&&(this.MRMaster.SignatureTurn=='1')&&(this.MRMaster.users[1].id==this.user.id))||((this.MRMaster.users[2].id==this.user.id)&&(this.MRMaster.users[2].pivot.Signature==null)&&(this.MRMaster.SignatureTurn=='2')))
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
