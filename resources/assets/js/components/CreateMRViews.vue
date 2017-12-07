<template lang="html">
  <span>
    <div class="create-mr-bigcontainer">
      <div class="title-mr">
        <h3>Create Memorandum Receipt</h3>
      </div>
      <div class="create-mr-container">
        <div class="selected-mr-session">
          <div class="addfromrr-btn">
            <button type="button"  v-on:click="isActive = !isActive"> <i class="material-icons">add</i> item</button>
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
              <td v-if="session.ItemCode!=null">{{session.ItemCode}}</td>
              <td v-else>N/A</td>
              <td>{{formatPrice(session.UnitCost)}}</td>
              <td>{{formatPrice(session.Amount)}}</td>
              <td v-if="session.Remarks!=null">{{session.Remarks}}</td>
              <td v-else>N/A</td>
              <td><a @click.prevent="deleteSession(count)"><i class="material-icons">close</i></a></td>
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
              Submit
            </longpress>
          </div>
        </div>
      </div>
    </div>
    <div class="items-table-from-RR" :class="{ 'active': isActive }"  v-on:click="isActive=!isActive">
      <div class="center-white-fromrr" v-on:click="isActive=!isActive">
        <h1>Select Items from RR</h1>
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
                  <td v-if="rritem.ItemCode!=null">{{rritem.ItemCode}}</td>
                  <td v-else>N/A</td>
                  <td>{{formatPrice(rritem.UnitCost)}}</td>
                  <td><input type="text" name="Remarks" placeholder="remarks" v-model="Remarks[rritem.ItemCode]" autocomplete="off"></td>
                  <td><button @click.prevent="addtoSession(rritem)"><i class="material-icons">add</i></button></td>
              </tr>
          </table>
        </div>
      </div>
    </div>
  </span>
</template>
<script>
import axios from 'axios';
import Longpress from 'vue-longpress';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
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
      this.$loading('Adding');
     var vm=this
     axios.post(`/addSession-MR`,{
       id:datas.id,
       ItemCode:datas.ItemCode,
       Quantity:this.Quantity[datas.ItemCode],
       Unit:datas.Unit,
       Description:datas.Description,
       UnitCost:datas.UnitCost,
       Remarks:this.Remarks[datas.ItemCode],
       RRNo:this.rritems[0].RRNo,
     }).then(function(response){
       console.log(response);
       if (response.data.error) {
         vm.$toast.top(response.data.error);
       }else
       {
         vm.fetchSessionDatas();
         vm.Quantity=[];
         vm.Remarks=[];
         vm.$toast.top('Successfully added');
       }
       vm.$loading.close();
     },function(error){
       vm.$toast.top(error.response.data.Quantity[0]);
       vm.$loading.close();
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
    this.$loading('Removing');
    var vm=this
    axios.delete(`/deletemrSession/`+count).then(function(response)
  {
    vm.fetchSessionDatas();
    vm.$toast.top('Removed');
    vm.$loading.close();
  });
  },
  formatPrice(value) {
        let val = (value/1).toFixed(2).replace('.', '.')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    },
  submitMR()
  {
    this.$loading('Submitting');
    this.HideSubmitBtn=true;
    var vm=this
    axios.post(`/save-mr`,{
      Note:this.Note,
      RecommendedBy:this.ManagerID,
      ReceivedBy:this.Receivedby,
      RRNo:this.rritems[0].RRNo,
    }).then(function(response)
    {
      console.log(response);
      if (response.data.error!=null){
        vm.$toast.top(response.data.error);
        Vue.set(vm.$data,'HideSubmitBtn',false);
      }else
      {
        window.location= response.data.redirect;
      }
      vm.$loading.close();
    },function(error){
      if (error.response.data.ReceivedBy!=null)
      {
        vm.$toast.top(error.response.data.ReceivedBy[0]);
      }else if (error.response.data.RecommendedBy!=null)
      {
        vm.$toast.top(error.response.data.RecommendedBy[0]);
      }
      Vue.set(vm.$data,'HideSubmitBtn',false);
      vm.$loading.close();
    });
  }
},
components: {
   Longpress
 },

}
</script>
