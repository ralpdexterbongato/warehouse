<template lang="html">
  <span>
    <div class="create-mr-bigcontainer">
      <div class="title-mr">
        <h3>Create Memorandum Receipt</h3>
      </div>
      <div class="create-mr-container">
        <div class="selected-mr-session">
          <div class="addfromrr-btn">
            <button type="button"  v-on:click="isActive = !isActive"> Select item</button>
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
          <table>
            <tr>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Name & Description</th>
              <th>Property No.</th>
              <th>Unit Value</th>
              <th>Total Value</th>
              <th>Remarks</th>
              <th>Delete</th>
            </tr>
            <tr v-for="(session, count) in sessions">
              <td>{{session.Quantity}}</td>
              <td>{{session.Unit}}</td>
              <td>{{session.Description}}</td>
              <td>{{session.ItemCode}}</td>
              <td>{{formatPrice(session.UnitCost)}}</td>
              <td>{{formatPrice(session.Amount)}}</td>
              <td>{{session.Remarks}}</td>
              <td><a @click.prevent="deleteSession(count)"><i class="fa fa-trash"></i></a></td>
            </tr>
          </table>
        </div>
        <div class="form-left-mr">
          <div class="form-left-box-mr">
            <select v-model="Receivedby">
              <option value="null">Received by</option>
              <option v-bind:value="activeuser.id" v-for="activeuser in allactive">{{activeuser.FullName}}</option>
            </select>
            <select name="ManagerID" v-model="ManagerID" autocomplete="off">
              <option :value="null">Recommended by</option>
              <option v-for="manager in allmanager" v-bind:value="manager.id">{{manager.FullName}}</option>
            </select>
            <textarea name="Note" v-model="Note" placeholder="Note" autocomplete="none"></textarea>
            <longpress id="submitMRbtn" :class="{'hide':HideSubmitBtn}" duration="3" :on-confirm="submitMR"  pressing-text="Submitting in {$rcounter}" action-text="Loading . . .">
              <i class="fa fa-check-square"></i> Submit
            </longpress>
            <div id="loading-submit" :class="HideSubmitBtn==true?'show':'hide'">
              <i class="fa fa-spinner fa-spin fa-pulse"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="items-table-from-RR" :class="{ 'active': isActive }"  v-on:click="isActive=!isActive">
      <div class="center-white-fromrr" v-on:click="isActive=!isActive">
        <h1>Select Items from RR <i class="fa fa-times" v-on:click="isActive=!isActive"></i></h1>
        <div class="table-container-mr-form">
          <table>
            <tr>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Name & Description</th>
              <th>Property No.</th>
              <th>Unit Value</th>
              <th>Remarks</th>
              <th>Add</th>
            </tr>
              <tr v-for="rritem in rritems">
                  <td><input type="number" name="Quantity" v-model="Quantity[rritem.ItemCode]" min="1" autocomplete="off" required ></td>
                  <td>{{rritem.Unit}}</td>
                  <td>{{rritem.Description}}</td>
                  <td>{{rritem.ItemCode}}</td>
                  <td>{{formatPrice(rritem.UnitCost)}}</td>
                  <td><input type="text" name="Remarks" placeholder="remarks" v-model="Remarks[rritem.ItemCode]" autocomplete="off"></td>
                  <td><button @click.prevent="addtoSession(rritem)" v-on:click="isActive=!isActive"><i class="fa fa-plus-circle"></i></button></td>
              </tr>
          </table>
        </div>
      </div>
    </div>
  </span>
</template>
<script>
import axios from 'axios'
import Longpress from 'vue-longpress'
export default {
  props:['rritems','allmanager','allactive'],
  data(){
    return{
      Quantity:[],
      Remarks:[],
      sessions:[],
      isActive:false,
      laravelerrors:[],
      successAlerts:[],
      ownerrors:[],
      Receivedby:null,
      ManagerID:null,
      Note:'',
      HideSubmitBtn:false,
    }
  },

  created:function()
  {
    this.fetchSessionDatas();
  },
  methods: {

    addtoSession(datas)
    {
     var vm=this
     axios.post(`/addSession-MR`,{
       ItemCode:datas.ItemCode,
       Quantity:this.Quantity[datas.ItemCode],
       Unit:datas.Unit,
       Description:datas.Description,
       UnitCost:datas.UnitCost,
       Remarks:this.Remarks[datas.ItemCode],
       RRNo:this.rritems[0].RRNo,
     }).then(function(response){
       console.log(response);
       Vue.set(vm.$data,'laravelerrors','');
       Vue.set(vm.$data,'successAlerts','');
       Vue.set(vm.$data,'ownerrors','');
       if (response.data.error) {
         Vue.set(vm.$data,'ownerrors',response.data.error);
       }else
       {
         vm.fetchSessionDatas();
         vm.Quantity=[];
         vm.Remarks=[];
         Vue.set(vm.$data,'successAlerts','Successfully added !');
       }
     },function(error){
       Vue.set(vm.$data,'successAlerts','');
       Vue.set(vm.$data,'laravelerrors',error.response.data);
     });
   },
   fetchSessionDatas()
   {
     var vm=this
     axios.get(`/displaySessionMR`).then(function(response)
     {
       Vue.set(vm.$data,'sessions',response.data.sessions);
     });
  },

  deleteSession(count)
  {
    var vm=this
    axios.delete(`/deletemrSession/`+count).then(function(response)
  {
    vm.fetchSessionDatas();
    Vue.set(vm.$data,'ownerrors','')
    Vue.set(vm.$data,'laravelerrors','');
    Vue.set(vm.$data,'successAlerts','Removed successfully !')
  },function(error)
    {
      console.log(error);
    });
  },
  formatPrice(value) {
        let val = (value/1).toFixed(2).replace('.', '.')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    },
  submitMR()
  {
    this.HideSubmitBtn=true;
    var vm=this
    axios.post(`/save-mr`,{
      Note:this.Note,
      ManagerID:this.ManagerID,
      Receivedby:this.Receivedby,
      RRNo:this.rritems[0].RRNo,
    }).then(function(response)
    {
      console.log(response);
      if (response.data.error!=null){
        Vue.set(vm.$data,'ownerrors',response.data.error)
        Vue.set(vm.$data,'successAlerts','');
        Vue.set(vm.$data,'laravelerrors','');
        Vue.set(vm.$data,'HideSubmitBtn',false);
      }else
      {
        window.location= response.data.redirect;
      }
    },function(error){
      Vue.set(vm.$data,'ownerrors','')
      Vue.set(vm.$data,'successAlerts','');
      Vue.set(vm.$data,'laravelerrors',error.response.data);
      Vue.set(vm.$data,'HideSubmitBtn',false);
    });
  }
},
components: {
   Longpress
 },

}
</script>
