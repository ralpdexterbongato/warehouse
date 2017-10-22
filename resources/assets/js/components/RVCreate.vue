<template lang="html">
  <span>
    <div class="title-RV">
      <h3>Create Requisition Voucher</h3>
      <h3 class="empty-right"></h3>
    </div>
    <div class="RV-wrapper">
      <div class="added-items-table">
          <ul class="error-tab" v-if="laravelerrors!=''" v-on:click="laravelerrors=[]">
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
        <div class="add-item-RV">
          <button type="button" v-if="user.Role==3||user.Role==4" name="button" id="forstock-ItemRV" v-on:click="forstock=!forstock"><i class="fa fa-cubes" v-if="user.Role==3||user.Role==4"></i> For stocks</button>
          <button type="button" class="bttn-unite bttn-sm bttn-primary" id="none-existing-itemRV" v-on:click="notforstock=!notforstock"><i class="fa fa-ban"></i> Not in warehouse</button>
        </div>
        <table>
          <tr>
            <th>Articles</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Remarks</th>
            <th>Action</th>
          </tr>
            <tr v-for="(sessionrv,key) in SessionStored">
              <td>{{sessionrv.Description}}</td>
              <td>{{sessionrv.Unit}}</td>
              <td>{{sessionrv.Quantity}}</td>
              <td>{{sessionrv.Remarks}}</td>
              <td><i class="fa fa-trash" v-on:click="deleteSession(key)"></i></td>
            </tr>
        </table>
      </div>
      <div class="RVMaster-form">
        <div class="RV-form">
          <input type="text" autocomplete="off" name="Purpose" v-model="purpose" placeholder="Purpose"  required>
          <input type="text" v-if="user.Role==7" v-model="budgetavailable" placeholder="Budget Available Remarks">
          <div class="autoselectedRV">
              <h4 v-if="mymanager!=null">{{mymanager.Fname}} {{mymanager.Lname}}</h4>
              <h4 v-else>No Manager found, please ask the administrator</h4>
            <p>Recommended by</p>
          </div>
          <div class="autoselectedRV">
              <h4 v-if="budgetofficer[0]!=null">{{budgetofficer[0].Fname}} {{budgetofficer[0].Lname}}</h4>
              <h4 v-else>No Account yet</h4>
            <p>Budget Officer</p>
          </div>
          <div class="autoselectedRV space-bottom">
              <h4 v-if="gm!=null">{{gm[0].Fname}} {{gm[0].Lname}}</h4>
              <h4 v-else>No Account yet</h4>
            <p>General Manager</p>
          </div>
          <longpress class="submit-button-RV" duration="3" :class="{'hide':HideBtn}" :on-confirm="SubmitWholeRV" pressing-text="Submitting in {$rcounter}" action-text="Loading . . .">
            Submit
          </longpress>
          <div id="loading-submit" :class="[HideBtn==true?'show':'hide']">
            <i class="fa fa-spinner fa-spin fa-pulse"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="add-RV-item-modal" :class="{'active':notforstock}" v-on:click="notforstock=!notforstock">
      <div class="rv-modal-centered" v-on:click="notforstock=!notforstock">
        <h1>Request Item</h1>
        <div class="itemRV-form">
          <textarea name="Description" v-model="Description" placeholder="Articles (max:50 characters)" required></textarea>
          <select name="Unit" v-model="Unit" required>
            <option value="">Select Unit</option>
            <option v-for="unitdb in UnitsFromDB">{{unitdb.UnitName}}</option>
          </select>
          <input type="number" v-model="Quantity" autocomplete="off" name="Quantity" placeholder="Quantity" min="1" required>
          <input type="text" autocomplete="off" v-model="Remarks" name="Remarks" placeholder="Remarks">
          <div class="buttons-RV-Item">
            <button type="button" id="closemodalRV" v-on:click="notforstock=!notforstock">Cancel</button>
            <button type="submit" id="addtolistRV" v-on:click="notforstock=!notforstock,addToSession()">Add to list</button>
          </div>
        </div>
      </div>
    </div>
    <span v-if="user.Role==3||user.Role==4">
      <div class="for-stock-Modal" :class="{'active':forstock}" v-on:click="forstock=!forstock">
        <div class="middle-forStock-div" v-on:click="forstock=!forstock">
          <h1>Request for warehouse stock items</h1>
          <div class="searchboxes-forstock">
            <div class="low-qty-items">
              <button type="button" v-on:click="[lowqtyactive==false?FetchLowQtyItems():searchDescription()],lowqtyactive=!lowqtyactive" :class="{'lowqtyactive':lowqtyactive}"><i class="fa fa-arrow-down"></i> Qty items</button>
            </div>
              <span><input type="text" autocomplete="off" name="Description" placeholder="Article/Description" v-model="findDescription" v-on:keyup.enter="searchDescription(),lowqtyactive=false"><button type="submit"v-on:click="searchDescription(),lowqtyactive=false"><i class="fa fa-search"></i></button></span>
          </div>
          <div class="searchResults-forstock">
            <table>
              <tr>
                <th>Item Code</th>
                <th>Article</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Current Balance</th>
                <th>Minimum balance</th>
                <th>Remarks</th>
                <th>Action</th>
              </tr>
              <tr v-for="(result,count) in findResults">
                  <td>{{result.ItemCode}}</td>
                  <td>{{result.Description}}</td>
                  <td>{{result.Unit}}</td>
                  <td><input type="number" autocomplete="off" v-model="QuantityForWHouse[count]" name="Quantity" min="1" required></td>
                  <td class="bold" :class="{'color-red':lowqtyactive}">{{result.CurrentQuantity}}</td>
                  <td class="bold">{{result.AlertIfBelow}}</td>
                  <td><input type="text" autocomplete="off" v-model="RemarksForWHouse[count]" name="Remarks"></td>
                  <td><button type="submit" v-on:click="AddtoSessionForWarehouse(result,count),forstock=!forstock"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
            </table>
            <div class="paginate-container">
              <ul class="pagination">
                <li v-if="pagination.current_page > 1">
                  <a href="#" @click.prevent="changepage(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
                </li>
                <li v-for="page in pagesNumber" v-bind:class="[ page == isActive ? 'active':'']">
                  <a href="#" @click.prevent="changepage(page)">{{page}}</a>
                </li>
                <li v-if="pagination.current_page < pagination.last_page">
                  <a href="#" @click.prevent="changepage(pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </span>
  </span>
</template>

<script>
import axios from 'axios';
import Longpress from 'vue-longpress';
  export default {
     data () {
        return {
          forstock:false,
          notforstock:false,
          findDescription:'',
          findResults:[],
          pagination:[],
          offset:4,
          Description:'',
          Unit:'',
          Quantity:'',
          Remarks:'',
          SessionStored:[],
          budgetavailable:'',
          purpose:'',
          laravelerrors:[],
          ownerrors:'',
          successAlerts:'',
          QuantityForWHouse:[],
          RemarksForWHouse:[],
          lowqtyactive:false,
          UnitsFromDB:[],
          HideBtn:false,
        }
      },
    props: ['budgetofficer','gm','user','mymanager'],
     methods: {
       searchDescription(page)
       {
         var vm=this;
         axios.get(`/search-rv-forstock?Description=`+this.findDescription+`&page=`+page).then(function(response)
         {
            console.log(response);
            Vue.set(vm.$data,'findResults',response.data.MasterResults.data);
            Vue.set(vm.$data,'pagination',response.data.MasterResults);
         });
       },
       changepage(next){
       this.pagination.current_page = next;
       if (this.lowqtyactive)
       {
         this.FetchLowQtyItems(next);
       }else
       {
         this.searchDescription(next);
       }

      },
      addToSession()
      {
        var vm=this;
        axios.post(`/SessionSave`,{
          Description:this.Description,
          Unit:this.Unit,
          Quantity:this.Quantity,
          Remarks:this.Remarks
        }).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'laravelerrors','');
          Vue.set(vm.$data,'ownerrors','');
          Vue.set(vm.$data,'successAlerts','Added successfully');
          Vue.set(vm.$data,'Description','');
          Vue.set(vm.$data,'Unit','');
          Vue.set(vm.$data,'Quantity','');
          Vue.set(vm.$data,'Remarks','');
        },function(error)
        {
          Vue.set(vm.$data,'laravelerrors',error.response.data);
          console.log(error);
        });
        this.FetchSessionStored();
      },
      AddtoSessionForWarehouse(data,count)
      {
        var vm=this;
        axios.post(`/addtoStockSession`,{
          AccountCode:data.AccountCode,
          ItemCode:data.ItemCode,
          Description:data.Description,
          Unit:data.Unit,
          Quantity:this.QuantityForWHouse[count],
          Remarks:this.RemarksForWHouse[count],
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
            Vue.set(vm.$data,'successAlerts','Successfully added.');
            Vue.set(vm.$data,'laravelerrors','');
            Vue.set(vm.$data,'ownerrors','');
          }
        },function(error)
        {
          Vue.set(vm.$data,'laravelerrors',error.response.data);
          Vue.set(vm.$data,'ownerrors','');
          Vue.set(vm.$data,'successAlerts','');
        });
        this.FetchSessionStored();
      },
      FetchSessionStored()
      {
        var vm=this;
        axios.get(`/fetch-rv-session`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'SessionStored',response.data);
        });
      },
      FetchLowQtyItems(page)
      {
        var vm=this;
        axios.get(`/get-low-qty-items?page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'findResults',response.data.data)
          Vue.set(vm.$data,'pagination',response.data)
        })
      },
      deleteSession(key)
      {
        var vm=this;
        axios.delete(`DeleteSession/`+key).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'successAlerts','Removed successfully.');
          Vue.set(vm.$data,'laravelerrors','');
          Vue.set(vm.$data,'ownerrors','');
        });
        this.FetchSessionStored();
      },
      fetchallUnit()
      {
        var vm=this;
        axios.get(`/get-all-units`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'UnitsFromDB',response.data);
        })
      },
      SubmitWholeRV()
      {
        this.HideBtn=true;
        var vm=this;
        axios.post(`/SavetoDBRV`,{
          Purpose:this.purpose,
          BudgetAvailable:this.budgetavailable,
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error!=null) {
            Vue.set(vm.$data,'ownerrors',response.data.error);
            Vue.set(vm.$data,'laravelerrors','');
            Vue.set(vm.$data,'successAlerts','');
            Vue.set(vm.$data,'HideBtn',false);
          }else
          {
            window.location=response.data.redirect;
          }
        },function(error){
          Vue.set(vm.$data,'laravelerrors',error.response.data);
          Vue.set(vm.$data,'successAlerts','');
          Vue.set(vm.$data,'ownerrors','');
          Vue.set(vm.$data,'HideBtn',false);
        });
      },
     },
     computed:{
       isActive:function(){
         return this.pagination.current_page;
       },
       pagesNumber:function(){
         if (!this.pagination.to) {
                       return [];
                   }
                   var from = this.pagination.current_page - this.offset;
                   if (from < 1) {
                       from = 1;
                   }
                   var to = from + (this.offset * 2);
                   if (to >= this.pagination.last_page) {
                       to = this.pagination.last_page;
                   }
                   var pagesArray = [];
                   while (from <= to) {
                       pagesArray.push(from);
                       from++;
                   }
                   return pagesArray;
       }
     },
     mounted()
     {
       this.FetchSessionStored();
       if (this.user.Role==3||this.user.Role==4) {
         this.searchDescription();
       }
       this.fetchallUnit();
     },
     components: {
    Longpress
      },

  }
</script>
