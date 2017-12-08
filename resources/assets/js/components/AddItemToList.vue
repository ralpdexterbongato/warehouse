<template lang="html">
  <span>
    <div class="message-container">
    </div>
    <div class="Adding-items-form">
      <div class="left-item-adding-form">
        <h2>Recently added</h2>
        <span class="search-added-master">
          <input type="text"placeholder="Search" v-model="ItemCodeSearch" v-on:keyup.enter="RecentAddedAndSearch()">
          <button type="button" v-on:click="RecentAddedAndSearch()">
            <i class="material-icons">search</i>
          </button>
        </span>
        <table>
          <tr>
            <th>Account Code</th>
            <th>ItemCode</th>
            <th>Name & Description</th>
            <th>Unit</th>
            <th>CurrentQuantity</th>
            <th>CurrentCost</th>
            <th>Minimum Qty</th>
            <th>Edit</th>
          </tr>
          <tr v-for="details in RecentDataResults">
            <td>{{details.AccountCode}}</td>
            <td>{{details.ItemCode}}</td>
            <td>{{details.master_items.Description}}</td>
            <td>{{details.master_items.Unit}}</td>
            <td>{{details.CurrentQuantity}}</td>
            <td>{{formatPrice(details.CurrentCost)}}</td>
            <td>{{details.master_items.AlertIfBelow}}</td>
            <td><i class="material-icons" v-on:click="ToBeEditId=details.id,fetchDataToBeEdit(details.id)">border_color</i></td>
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
      <div class="right-item-adding-form">
        <div class="form-item-right">
          <div class="form-item-right-inputs">
              <div class="updateinput-label">
                <h3 :class="[AccountCode!=''?'active color-white':'']">Account Code</h3>
                <input type="text"  v-model="AccountCode">
              </div>
              <div class="updateinput-label">
                <h3 :class="[ItemCode!=''?'active color-white':'']">Item Code</h3>
                <input type="text"  v-model="ItemCode">
              </div>
              <div class="updateinput-label">
                <h3 :class="[Description!=''?'active color-white':'']">Name & Description</h3>
                  <input type="text"  v-model="Description">
              </div>
              <div class="updateinput-label">
                <h3 :class="[Unit!=''?'active color-white':'']">Unit</h3>
                <div class="small-unit-form" :class="[UnitIsActive==true?'flex':'hide']">
                  <h2><i class="material-icons">add</i> New unit</h2>
                  <input type="text" v-model="UnitNew">
                  <h1> <button type="button" v-on:click="UnitIsActive=false">Cancel</button><button type="button" v-on:click="addUnitRow()">Save</button></h1>
                </div>
                <div class="mini-unit-menu" :class="[minimenu==true?'flex':'hide']">
                  <i class="material-icons" v-on:click="UnitIsActive=true,minimenu=false">add</i> <i class="material-icons" v-on:click="deleteunitIsActive=true,minimenu=false">close</i>
                </div>
                <div class="delete-unit-form" :class="[deleteunitIsActive==true?'flex':'hide']">
                <h2><i class="material-icons">close</i> Delete unit</h2>
                <select v-model="unitdelete">
                  <option :value="ulist.id" v-for="ulist in UnitList">{{ulist.UnitName}}</option>
                </select>
                <div class="delete-unit-btns">
                    <button type="button" name="button" v-on:click="deleteunitIsActive=false">Cancel</button>
                    <button type="button" name="button" v-if="unitdelete!=null" v-on:click="deleteUnit()">Delete</button>
                </div>
                </div>
                <i class="material-icons New-Unit" v-on:click="minimenu=!minimenu">settings</i>
                  <select v-model="Unit">
                    <option value=""></option>
                    <option v-for="ulist in UnitList">{{ulist.UnitName}}</option>
                  </select>
              </div>
              <div class="updateinput-label">
                <h3 :class="[CurrentQuantity!=''?'active color-white':'']">Current Qty</h3>
                  <input type="text"  v-model="CurrentQuantity">
              </div>
            <div class="updateinput-label">
              <h3 :class="[CurrentCost!=''?'active color-white':'center-input']">Current cost</h3>
                <vue-numeric  v-bind:minus="false" v-bind:precision="2" min="0" currency="₱" v-model="CurrentCost"></vue-numeric>
            </div>
            <div class="updateinput-label">
              <h3 :class="[AlertBelow!=''?'active color-white':'']">Alert if qty. is below</h3>
                <input type="text" v-model="AlertBelow">
            </div>
            <button type="button" v-on:click="saveItem()"v-if="ToBeEditId==null" >Save</button>
            <span v-if="ToBeEditId!=null" class="update-item-btns">
              <button type="button" v-on:click="ToBeEditId=null,clearfields()" ><i class="material-icons">close</i> Cancel</button>
              <button type="button" v-on:click="updateChangesItem()" ><i class="material-icons">update</i> Update</button>
            </span>
          </div>
        </div>
      </div>
    </div>
  </span>
</template>

<script>
import axios from 'axios';
import VueNumeric from 'vue-numeric';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
Vue.use(VueNumeric)
  export default {
     data () {
        return {
          AccountCode:'',
          ItemCode:'',
          CurrentQuantity:'',
          Unit:'',
          CurrentCost:0,
          Description:'',
          AlertBelow:'',
          ItemCodeSearch:'',
          pagination:[],
          RecentDataResults:[],
          offset:4,
          ToBeEditId:null,
          UnitIsActive:false,
          UnitNew:'',
          UnitList:[],
          unitdelete:null,
          minimenu:false,
          deleteunitIsActive:false,
        }
      },
     methods: {
       saveItem()
       {
         this.$loading('Saving');
         var vm=this;
         axios.post(`/save-New-Item-warehouse`,{
           AccountCode:this.AccountCode,
           ItemCode:this.ItemCode,
           CurrentQuantity:this.CurrentQuantity,
           Unit:this.Unit,
           CurrentCost:this.CurrentCost,
           Description:this.Description,
           AlertIfBelow:this.AlertBelow,
         }).then(function(response)
         {
            console.log(response);
            if (response.data.error==null)
            {
              vm.RecentAddedAndSearch();
              Vue.set(vm.$data,'AccountCode','');
              Vue.set(vm.$data,'ItemCode','');
              Vue.set(vm.$data,'CurrentQuantity','');
              Vue.set(vm.$data,'Unit','');
              Vue.set(vm.$data,'CurrentCost','');
              Vue.set(vm.$data,'Description','');
              Vue.set(vm.$data,'AlertBelow','');
              vm.$toast.top('Successfully added');
            }else
            {
            vm.$toast.top(response.data.error);
            }
            vm.$loading.close();
          },function(error)
          {

            if (error.response.data.AccountCode!=null)
            {
              vm.$toast.top(error.response.data.AccountCode[0]);
            }else if(error.response.data.ItemCode!=null)
            {
              vm.$toast.top(error.response.data.ItemCode[0]);
            }else if(error.response.data.Description!=null)
            {
              vm.$toast.top(error.response.data.Description[0]);
            }else if(error.response.data.Unit!=null)
            {
              vm.$toast.top(error.response.data.Unit[0]);
            }else if(error.response.data.CurrentQuantity!=null)
            {
              vm.$toast.top(error.response.data.CurrentQuantity[0]);
            }else if(error.response.data.AlertIfBelow!=null)
            {
              vm.$toast.top(error.response.data.AlertIfBelow[0]);
            }
            vm.$loading.close();
          });
       },
       RecentAddedAndSearch(page)
       {
         var vm=this;
         axios.get(`/search-by-description-and-recently-inits?Search=`+this.ItemCodeSearch+`&page=`+page).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'RecentDataResults',response.data.pagination.data);
            Vue.set(vm.$data,'pagination',response.data.pagination);
         });
       },
       fetchDataToBeEdit(id)
       {
         var vm=this;
         axios.get(`/fetchData-of-item-to-be-edited/`+id).then(function(response)
         {
           console.log(response);
           Vue.set(vm.$data,'AccountCode',response.data.response[0].AccountCode)
           Vue.set(vm.$data,'ItemCode',response.data.response[0].ItemCode)
           Vue.set(vm.$data,'CurrentQuantity',response.data.response[0].CurrentQuantity)
           Vue.set(vm.$data,'CurrentCost',response.data.response[0].CurrentCost)
           Vue.set(vm.$data,'Unit',response.data.response[0].master_items.Unit)
           Vue.set(vm.$data,'Description',response.data.response[0].master_items.Description)
           Vue.set(vm.$data,'AlertBelow',response.data.response[0].master_items.AlertIfBelow)
         })
       },
       changepage(next){
       this.pagination.current_page = next;
       this.RecentAddedAndSearch(next);
       },
       clearfields()
       {
         this.AccountCode='';
         this.ItemCode='';
         this.CurrentQuantity='';
         this.Unit='';
         this.CurrentCost='';
         this.Description='';
         this.AlertBelow='';
       },
       formatPrice(value) {
             let val = (value/1).toFixed(2).replace('.', '.')
             return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
       },
       updateChangesItem()
       {
         this.$loading('Updating');
         var vm=this;
         axios.put(`/update-changes-item/`+this.ToBeEditId,{
           AccountCode:this.AccountCode,
           ItemCode:this.ItemCode,
           CurrentQuantity:this.CurrentQuantity,
           Unit:this.Unit,
           CurrentCost:this.CurrentCost,
           Description:this.Description,
           AlertIfBelow:this.AlertBelow,
         }).then(function(response)
         {
           if (response.data.error==null)
           {
             console.log(response);
             vm.RecentAddedAndSearch();
             Vue.set(vm.$data,'AccountCode','');
             Vue.set(vm.$data,'ItemCode','');
             Vue.set(vm.$data,'CurrentQuantity','');
             Vue.set(vm.$data,'CurrentCost',0);
             Vue.set(vm.$data,'Unit','');
             Vue.set(vm.$data,'Description','');
             Vue.set(vm.$data,'AlertBelow','');
             Vue.set(vm.$data,'ToBeEditId',null);
             vm.$toast.top('Updated');
           }else
           {
             vm.$toast.top(response.data.error);
           }
           vm.$loading.close();
         },function(error)
          {
            if (error.response.data.AccountCode!=null)
            {
              vm.$toast.top(error.response.data.AccountCode[0]);
            }else if(error.response.data.ItemCode!=null)
            {
              vm.$toast.top(error.response.data.ItemCode[0]);
            }else if(error.response.data.Description!=null)
            {
              vm.$toast.top(error.response.data.Description[0]);
            }else if(error.response.data.Unit!=null)
            {
              vm.$toast.top(error.response.data.Unit[0]);
            }else if(error.response.data.CurrentQuantity!=null)
            {
              vm.$toast.top(error.response.data.CurrentQuantity[0]);
            }else if(error.response.data.AlertIfBelow!=null)
            {
              vm.$toast.top(error.response.data.AlertIfBelow[0]);
            }
            vm.$loading.close();
          });
       },
       addUnitRow()
       {
         this.$loading('Adding');
         var vm=this;
         axios.post(`/add-new-unit-to-dropdown`,{
           NewUnit:this.UnitNew,
         }).then(function(response)
        {
          console.log(response);
          vm.getUnitlist();
          Vue.set(vm.$data,'UnitIsActive',false);
          vm.$toast.top('Successfully added');
          vm.$loading.close();
        },function(error)
        {
          console.log(error);
          vm.$toast.top(error.response.data.NewUnit[0]);
          vm.$loading.close();
        });
      },
      getUnitlist()
      {
        var vm=this;
        axios.get(`/get-all-units`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'UnitList',response.data);
        })
      },
      deleteUnit()
      {
        this.$loading('Removing unit');
        var vm=this;
        axios.delete(`/delete-unit/`+this.unitdelete).then(function(response)
        {
            vm.getUnitlist();
            Vue.set(vm.$data,'deleteunitIsActive',false);
            vm.$toast.top('Unit removed.');
            vm.$loading.close();
        });
      }
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
     mounted () {
       this.RecentAddedAndSearch();
       this.getUnitlist();
     },
  }
</script>
