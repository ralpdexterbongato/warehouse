<template lang="html">
  <div class="wrapper-canvass">
    <div class="canvass-container">
      <div class="top-title-canvass">
        <h1>Record your canvass</h1>
      </div>
      <ul class="error-tab" v-if="laravelerrors!=''">
        <span v-for="errors in laravelerrors">
          <li v-for="error in errors">{{error}}</li>
        </span>
      </ul>
      <ul class="error-tab" v-if="ownerrors!=''">
        <li>{{ownerrors}}</li>
      </ul>
      <div class="successAlertRRsession" v-if="successAlerts!=''">
        <p>{{successAlerts}}</p>
      </div>
      <div class="add-supplier-canvass">
        <button type="button" name="button" @click.prevent="IsActive = !IsActive" v-on:click="Update=false"><i class="fa fa-plus-circle"></i>New supplier</button>
      </div>
      <div class="items-from-rv-table">
        <table>
          <tr>
            <th>Article</th>
            <th>Unit</th>
            <th>Qty</th>
            <th v-for="supplier in Suppliers">{{supplier.Supplier}}<br><br>
                <button type="button" @click.prevent="IsActive=!IsActive" v-on:click="Update=true,fetchSupplierUpdate(supplier.id)"><i class="fa fa-refresh"></i></button>
                <button type="button" name="button" v-on:click="deleteCanvass(supplier.id)"><i class="fa fa-trash"></i></button>
            </th>
          </tr>
            <tr v-for="(rvdata, index) in RVdata">
              <td>{{rvdata.Particulars}}</td>
              <td>{{rvdata.Unit}}</td>
              <td v-if="rvdata.QuantityValidator!=0">{{rvdata.QuantityValidator}}</td>
              <td v-else><i class="fa fa-check color-blue"></i></td>
              <td v-for="supplier in Suppliers">
                <input type="radio" @click.prevents="changeValue([index],supplier.Supplier)" v-bind:name="'SupplierChoice['+[index]+']'" v-if="supplier.canvass_detail[index].Price>0&&rvdata.QuantityValidator!=0">
                {{formatPrice(supplier.canvass_detail[index].Price)}}
              </td>
            </tr>
        </table>
      </div>
      <div class="GeneratePO-btn">
        <longpress duration="3" class="generate-po-btn" :class="{'hide':HideSubmitBtn}" :on-confirm="generatePO" pressing-text="Submitting in {$rcounter}" action-text="Loading . . .">
          Submit <i class="fa fa-check-circle"></i>
        </longpress>
        <i class="fa fa-spinner fa-spin fa-pulse" :class="[HideSubmitBtn==true?'show':'hide']" id="generating-po-loading"></i>
      </div>
    </div>
    <div class="modal-canvass" :class="{'active':IsActive}">
      <div class="canvass-center-form">
        <h1>Record Supplier</h1>
        <div class="canvass-form">
          <div class="suppliersInfoForm" v-if="Update==true">
            <input type="text" name="UpdateformSupplier" v-model="UpdateformSupplier"  placeholder="Supplier">
            <input type="text" name="UpdateformAddress" v-model="UpdateformAddress" placeholder="Address">
            <input type="text" name="UpdateformTelephone" v-model="UpdateformTelephone" placeholder="Telephone #">
          </div>
          <div class="suppliersInfoForm" v-if="Update==false">
            <input type="text" name="formSupplier" v-model="formSupplier"  placeholder="Supplier">
            <input type="text" name="formAddress" v-model="formAddress" placeholder="Address">
            <input type="text" name="formTelephone" v-model="formTelephone" placeholder="Telephone #">
          </div>
          <div class="table-wrap-canvass">
            <table>
              <tr>
                <th>Article</th>
                <th>Unit</th>
                <th>Prices</th>
              </tr>
                <tr v-for="(rvdata, count) in RVdata" v-if="Update==false">
                  <td>{{rvdata.Particulars}}</td>
                  <td>{{rvdata.Unit}}</td>
                  <input type="text" name="Particulars[]" v-model="Particulars[count]=rvdata.Particulars" style="display:none">
                  <input type="text" name="Unit[]" v-model="Unit[count]=rvdata.Unit" style="display:none">
                  <input type="text" name="Qty[]" v-model="Qty[count]=rvdata.QuantityValidator" style="display:none">
                  <input type="text" name="AccountCode[]" v-model="AccountCode[count]=rvdata.AccountCode" style="display:none">
                  <input type="text" name="ItemCode[]" v-model="ItemCode[count]=rvdata.ItemCode" style="display:none">
                  <td>

                    <vue-numeric v-bind:minus="false" v-bind:precision="2" min="0" currency="₱"  v-model="PriceNew[count]=Integ[count].price" placeholder="price"></vue-numeric>
                  </td>
                </tr>
                <tr v-for="(canvassData, loop) in fetchUpdatedata.canvass_detail" v-if="Update==true">
                  <td>{{canvassData.Article}}</td>
                  <td>{{canvassData.Unit}}</td>
                <td>
                  <vue-numeric v-bind:minus="false" v-bind:precision="2" min="0" currency="₱" v-model="UpdatePrice[loop]=canvassData.Price" placeholder="price"></vue-numeric>
                </td>
                </tr>
            </table>
          </div>
          <div class="modal-canvass-buttons">
            <button type="button"id="cancel-canvass" @click.prevent="IsActive= !IsActive">Cancel</button>
            <button v-if="Update==false" type="submit" class="done-canvass" @click="saveSupplier()" v-on:click="IsActive= !IsActive">Done</button>
            <button v-if="Update==true" type="submit" class="done-canvass" v-on:click="IsActive= !IsActive,saveUpdate()">Update</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Longpress from 'vue-longpress';
import VueNumeric from 'vue-numeric';
Vue.use(VueNumeric);
  export default {
    data () {
      return {
        Update:false,
        Suppliers:[],
        RVdata:[],
        SupplierChoice:[],
        IsActive:false,
        formSupplier:'',
        formAddress:'',
        formTelephone:'',
        UpdateformSupplier:'',
        UpdateformAddress:'',
        UpdateformTelephone:'',
        ItemCode:[],
        AccountCode:[],
        UpdatePrice:[],
        PriceNew:[],
        Integ:[],
        Particulars:[],
        Unit:[],
        Qty:[],
        laravelerrors:[],
        ownerrors:'',
        successAlerts:'',
        selectedValue:'',
        fetchUpdatedata:[],
        HideSubmitBtn:false,
      }
    },
    props:['rvno'],
    created:function()
    {
      this.getSuppliers();
    },
     methods: {
         getSuppliers()
         {
           var vm=this;
           axios.get(`/canvass-suppliers/`+this.rvno.RVNo).then(function(response)
           {
             console.log(response)
             Vue.set(vm.$data,'Suppliers',response.data.supplierdata);
             Vue.set(vm.$data,'RVdata',response.data.rvdata);
             Vue.set(vm.$data,'Integ',response.data.integ);
           },function(error)
           {
            console.log(error);
           });
          },
          formatPrice(value) {
                let val = (value/1).toFixed(2).replace('.', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
          },
         saveSupplier()
         {
           var vm=this;
           if (confirm("Confirm this new supplier data?"))
           {
             axios.post(`/supplier-save-canvass`,{
               RVNo:this.RVdata[0].RVNo,
               AccountCode:this.AccountCode,
               ItemCode:this.ItemCode,
               Supplier:this.formSupplier,
               Address:this.formAddress,
               Telephone:this.formTelephone,
               Particulars:this.Particulars,
               Price:this.PriceNew,
               Qty:this.Qty,
               Unit:this.Unit,
             }).then(function(response)
             {
              console.log(response);
              Vue.set(vm.$data,'successAlerts','Saved successfully');
              Vue.set(vm.$data,'ownerrors','');
              Vue.set(vm.$data,'laravelerrors','');
              Vue.set(vm.$data,'formSupplier','');
              Vue.set(vm.$data,'formAddress','');
              Vue.set(vm.$data,'PriceNew',[]);
              Vue.set(vm.$data,'formTelephone','');
              vm.getSuppliers();
            },function(error)
            {
              console.log(error);
              Vue.set(vm.$data,'ownerrors','');
              Vue.set(vm.$data,'successAlerts','');
              Vue.set(vm.$data,'laravelerrors',error.response.data);
            });
           }
         },
        generatePO()
        {
          this.HideSubmitBtn=true;
          var vm=this;
          axios.post(`/generate-po`,{
            RVNo:this.RVdata[0].RVNo,
            SupplierChoice:this.SupplierChoice,
          }).then(function(response)
          {
            console.log(response);
             if (response.data.error==null)
             {
               window.location=response.data.redirect;
             }else
             {
               Vue.set(vm.$data,'ownerrors',response.data.error);
               Vue.set(vm.$data,'laravelerrors','');
               Vue.set(vm.$data,'successAlerts','');
               Vue.set(vm.$data,'HideSubmitBtn',false);
             }
          },function(error)
          {
            Vue.set(vm.$data,'laravelerrors',error.response.data);
            Vue.set(vm.$data,'successAlerts','');
            Vue.set(vm.$data,'ownerrors','');
            Vue.set(vm.$data,'HideSubmitBtn',false);
          });
        },
        changeValue(count,newValue) {
              this.SupplierChoice[count] = newValue;
          },
        fetchSupplierUpdate(id)
        {
          var url = window.location.href;
          var RVid= url.split('/')[4];
          var vm=this;
          axios.get(`/search-supplier/`+RVid,{
            params:
            {
              canvassID:id,
            }
          }).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'fetchUpdatedata',response.data[0]);
            Vue.set(vm.$data,'UpdateformSupplier',response.data[0].Supplier);
            Vue.set(vm.$data,'UpdateformAddress',response.data[0].Address);
            Vue.set(vm.$data,'UpdateformTelephone',response.data[0].Telephone);
          },function(error)
          {
            Vue.set(vm.$data,'laravelerrors',error.response.data)
          });
        },

        saveUpdate()
        {
          var vm=this;
          if (confirm("Confirm save changes?")==true)
          {
            var id=this.fetchUpdatedata.id;
            axios.put(`/update-canvass/`+id,{
                Prices:this.UpdatePrice,
                Supplier:this.UpdateformSupplier,
                Address:this.UpdateformAddress,
                Telephone:this.UpdateformTelephone,
            }).then(function(response)
            {
              console.log(response);
              Vue.set(vm.$data,'laravelerrors','');
              Vue.set(vm.$data,'successAlerts','Successfully updated !');

            },function(error){
              console.log(error);
              Vue.set(vm.$data,'laravelerrors',error.response.data);
              Vue.set(vm.$data,'successAlerts','');
            });
            this.getSuppliers();
          }
        },
        deleteCanvass(id)
        {
          var vm=this;
          if (confirm("Are you sure to delete this permanently?")==true)
          {
            axios.delete(`/deleteCanvassRecord/`+id,{}).then(function(response){
              console.log(response);
              vm.getSuppliers()
            },function(error)
            {
              console.log(error);
            });
          }
        },
       },
       components: {
          Longpress
        },

  }
</script>
