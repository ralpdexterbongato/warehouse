<template lang="html">
<div class="create-mct-vue">
  <div class="RecordingMCT-Container">
    <h1 class="title-create-mct">Materials charge ticket recording</h1>
    <h2 class="mirs-num">Materials from MIRS No. <span class="color-blue">{{mirsno.MIRSNo}}</span></h2>
    <div class="button-find-item-container">
      <button type="button" v-on:click="ModalActive=!ModalActive"><i class="material-icons">add</i> items</button>
    </div>
    <div class="session-and-formContainer">
      <div class="selected-items-session-mct">
        <table>
          <tr>
            <th>Item Code</th>
            <th>Particulars</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Remarks</th>
            <th>Remove</th>
          </tr>
          <tr v-for="list in SessionList">
            <td>{{list.ItemCode}}</td>
            <td>{{list.Particulars}}</td>
            <td>{{list.Unit}}</td>
            <td>{{list.Quantity}}</td>
            <td v-if="list.Remarks!=null">{{list.Remarks}}</td>
            <td v-else>No remarks</td>
            <td>
              <button class="deleteMCT-session-button" v-on:click="deleteSession(list.ItemCode)"><i class="material-icons">close</i></button>
            </td>
          </tr>
        </table>
      </div>
      <div class="smallform-mct-container">
        <div class="address-where-form">
          <input type="text" placeholder="Address to where?" v-model="AddressTo" autocomplete="off" required>
          <longpress :class="{'hide':HideBtn}" duration="3" class="SubmitMCTButton" :on-confirm="SavingMCT" pressing-text="Submitting in {$rcounter} seconds" action-text="Please wait">
            Submit
          </longpress>
        </div>
      </div>
    </div>
  </div>
  <div class="mct-modal-ofItems" :class="{'active':ModalActive}" v-on:click="ModalActive=!ModalActive">
    <div class="mct-modal-center" v-on:click="ModalActive=!ModalActive">
      <h1>Pick Items from MIRS No. {{mirsno.MIRSNo}}</h1>
      <div class="table-mct-itemchoices">
        <table>
          <tr>
            <th>Item Code</th>
            <th>Particulars</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Not claimed</th>
            <th>Remarks</th>
            <th>Select</th>
          </tr>

          <tr v-for="(validator,count) in FromMIRSDetail">
              <td>{{validator.ItemCode}}</td>
              <td>{{validator.Particulars}}</td>
              <td>{{validator.Unit}}</td>
              <td><input type="number" v-model="InputQty[count]" name="Quantity" min="1" autocomplete="off"></td>
              <td>{{validator.QuantityValidator}}</td>
              <td>{{validator.Remarks}}</td>
              <td><button v-on:click="SaveToSession(validator,count),ModalActive=false"><i class="material-icons">add</i></button></td>
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
</div>
</template>

<script>
import axios from 'axios';
import Longpress from 'vue-longpress';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
  export default {
    data () {
      return {
        FromMIRSDetail:[],
        pagination:[],
        offset:4,
        InputQty:[],
        SessionList:[],
        ModalActive:false,
        AddressTo:'',
        HideBtn:false,
    }
  },
    props: ['mirsno','purpose'],
    methods: {
      FetchTheValidator(page)
      {
        var vm=this;
        axios.get(`/fetch-mct-validator/`+this.mirsno.MIRSNo+`?page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'FromMIRSDetail',response.data.FromMIRSDetail.data);
          Vue.set(vm.$data,'pagination',response.data.FromMIRSDetail);
        });
      },
      changepage(next){
        this.pagination.current_page = next;
        this.FetchTheValidator(next);
      },
      SaveToSession(validator,count)
      {
        this.$loading('Loading');
        var vm=this;
        axios.post(`/mct-session-saving`,{
          ItemCode:validator.ItemCode,
          Quantity:this.InputQty[count],
          MIRSNo:validator.MIRSNo,
          Particulars:validator.Particulars,
          Unit:validator.Unit,
          Remarks:validator.Remarks,
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error==null)
          {
            vm.fetchSessionData();
            vm.$toast.top('Item added successfully');
            vm.$loading.close();
          }else
          {
            vm.$toast.top(response.data.error);
            vm.$loading.close();
          }
        });
      },
      fetchSessionData()
      {
        var vm=this;
        axios.get(`/mct-create-fetch-session-data`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'SessionList',response.data.SessionData)
        })
      },
      deleteSession(ItemCode)
      {
        this.$loading('removing');
        var vm=this;
        axios.delete(`/delete-session-mct/`+ItemCode).then(function(response)
        {
          console.log(response);
          vm.fetchSessionData();
          vm.$toast.top('1 item is removed.');
          vm.$loading.close();
        });
      },
      SavingMCT()
      {
        this.$loading('Submitting');
        this.HideBtn=true;
        var vm=this;
        axios.post(`/MCTstore`,{
          MIRSNo:this.mirsno.MIRSNo,
          Particulars:this.purpose.Purpose,
          AddressTo:this.AddressTo,
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error!=null)
          {
            vm.$toast.top(response.data.error);
            Vue.set(vm.$data,'HideBtn',false);
            vm.$loading.close();
          }else
          {
            window.location=response.data.redirect;
          }
        });
      }
    },
    created () {
      this.FetchTheValidator();
      this.fetchSessionData();
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
    components: {
   Longpress
     },

  }
</script>
