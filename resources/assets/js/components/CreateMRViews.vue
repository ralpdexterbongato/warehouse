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
          <ul class="rr-error-tab" v-if="laravelerrors!=''">
            <span v-for="errors in laravelerrors">
              <li v-for="error in errors">{{error}}</li>
            </span>
          </ul>
          <ul class="rr-error-tab" v-if="ownerrors!=''">
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
              <th>Action</th>
            </tr>
            <tr v-for="session in sessions">
              <td>{{session.Quantity}}</td>
              <td>{{session.Unit}}</td>
              <td>{{session.Description}}</td>
              <td>{{session.ItemCode}}</td>
              <td>{{formatPrice(session.UnitCost)}}</td>
              <td>{{formatPrice(session.Amount)}}</td>
              <td>{{session.Remarks}}</td>
              <td><a @click.prevent="deleteSession(session.ItemCode)"><i class="fa fa-trash"></i></a></td>
            </tr>
          </table>
        </div>
        <div class="form-left-mr">
          <div class="form-left-box-mr">
            <input type="text" name="Receivedby" v-model="Receivedby" placeholder="Recieved by" autocomplete="off">
            <input type="text" name="ReceivedbyPosition" v-model="ReceivedbyPosition" placeholder="Receiver's position" autocomplete="off">
            <select name="ManagerID" v-model="ManagerID" autocomplete="off">
              <option :value="null">Recommended by</option>
              <option v-for="manager in allmanager" v-bind:value="manager.id">{{manager.Fname}}</option>
            </select>
            <textarea name="Note" v-model="Note" placeholder="Note" autocomplete="none"></textarea>
            <button type="button" v-on:click="submitMR()"><i class="fa fa-check-square"></i> Submit</button>
          </div>
        </div>
      </div>
    </div>
    <div class="items-table-from-RR" :class="{ 'active': isActive }">
      <div class="center-white-fromrr">
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
              <th>Action</th>
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
import axios from 'axios';
export default {
  props:['rritems','allmanager'],
  data(){
    return{
      Quantity:[],
      Remarks:[],
      sessions:[],
      isActive:false,
      laravelerrors:[],
      successAlerts:[],
      ownerrors:[],
      Receivedby:'',
      ReceivedbyPosition:'',
      ManagerID:null,
      Note:'',
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
       QuantityValidator:datas.QuantityAccepted,
       Remarks:this.Remarks[datas.ItemCode],
     }).then(function(response){
       Vue.set(vm.$data,'laravelerrors','');
       Vue.set(vm.$data,'successAlerts','');
       if (response.data.error) {
         Vue.set(vm.$data,'ownerrors',response.data.error);
       }else
       {
       Vue.set(vm.$data,'successAlerts','Successfully added !');
       }
     },function(error){
       Vue.set(vm.$data,'successAlerts','');
       Vue.set(vm.$data,'laravelerrors',error.response.data);
     });
     this.Quantity=[];
     this.Remarks=[];
     this.fetchSessionDatas();
   },
   fetchSessionDatas()
   {
     var vm=this
     axios.get(`/displaySessionMR`).then(function(response)
   {
     Vue.set(vm.$data,'sessions',response.data.sessions);
   });
  },

  deleteSession(code)
  {
    var vm=this
    axios.delete(`/deletemrSession/`+code).then(function(response)
  {
    Vue.set(vm.$data,'ownerrors','')
    Vue.set(vm.$data,'laravelerrors','');
    Vue.set(vm.$data,'successAlerts','Removed successfully !')
  })
  this.fetchSessionDatas();
  },
  formatPrice(value) {
        let val = (value/1).toFixed(2).replace('.', '.')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    },
  submitMR()
  {
    var vm=this
    axios.post(`/save-mr`,{
      Note:this.Note,
      ManagerID:this.ManagerID,
      Receivedby:this.Receivedby,
      ReceivedbyPosition:this.ReceivedbyPosition,
      RRNo:this.rritems[0].RRNo,
    }).then(function(response)
    {
      console.log(response);
      window.location= response.data.redirect;
    },function(error){
      Vue.set(vm.$data,'ownerrors','')
      Vue.set(vm.$data,'successAlerts','');
      Vue.set(vm.$data,'laravelerrors',error.response.data);
    });
  }
},

}
</script>
