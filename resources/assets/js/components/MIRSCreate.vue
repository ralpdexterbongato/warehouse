<template lang="html">
<div class="MIRS-create-wrap">
  <div class="Search-item-container">
    <div class="Added-Items">
      <div class="modal-find-button" >
          <button type="button" name="button" v-on:click="isActive = !isActive"><i class="material-icons">add</i>item</button>
      </div>
      <div class="added-table-wrapper">
        <table>
          <tr>
            <th>Item Code</th>
            <th>Particular</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Remarks</th>
            <th>Cancel</th>
          </tr>
            <tr v-for="sessionitem in SessionItems">
              <td>{{sessionitem.ItemCode}}</td>
              <td>{{sessionitem.Particulars}}</td>
              <td>{{sessionitem.Quantity}}</td>
              <td>{{sessionitem.Unit}}</td>
              <td>{{sessionitem.Remarks}}</td>
              <td class="delete-trash"><i class="material-icons" v-on:click="deleteSession(sessionitem.ItemCode)">close</i></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="MIRSform-container">
    <div class="form-wrap-mirs">
      <ul>
        <li><input type="text" autocomplete="off" name="Purpose" v-model="purpose" placeholder="Purpose"></li>
        <li v-if="manager[0]!=null">
          <h3 class="mymanagerName">{{manager[0].FullName}}</h3>
          <p>Recommended by</p>
        </li>
        <li v-if="gm[0]!=null">
          <h3 class="gm-name">{{gm[0].FullName}}</h3>
          <p>General Manager</p>
        </li>
        <longpress id="go-btn"  class="submitMCT-btn" :class="{'hide':HideButton}" duration="3" :on-confirm="submitWholePage" pressing-text="Submit confirmed in {$rcounter}" action-text="Please wait...">
          Submit
        </longpress>
      </ul>
    </div>
  </div>
  <div class="modal-search-item":class="{'active':isActive}" v-on:click="isActive=!isActive">
    <div class="middle-modal-search">
      <h1>MIRS</h1>
        <div class="table-mirs-modalcontain">
          <div class="search-item-bar">
              <div class="search-mirs-modal">
                <input type="text" autocomplete="off" v-model="ItemSearch" name="Description" @keyup="searchItem()" placeholder="Search Name/Code">
              </div>
          </div>
          <div class="modal-search-results">
              <table>
                <tr>
                  <th>Item code</th>
                  <th>Particular</th>
                  <th>Quantity</th>
                  <th>Unit</th>
                  <th>Remarks</th>
                  <th>Action</th>
                </tr>

                <tr v-for="(itemcoderesult,count) in SearchResults">
                  <td>{{itemcoderesult.ItemCode}}</td>
                  <td>{{itemcoderesult.Description}}</td>
                  <td><input type="number" min="1" v-model="Quantity[count]"></td>
                  <td>{{itemcoderesult.Unit}}</td>
                  <td><input type="text" autocomplete="off" min="1"  name="Remarks[]" v-model="Remarks[count]"></td>
                  <td><button type="button" v-on:click="submitTosession(itemcoderesult,count)"><i class="material-icons">add</i></button></td>
                </tr>
              </table>
              <div class="pagination-container">
                <ul class="pagination" >
                  <li v-if="Pagination.current_page > 1">
                    <a href="#" @click.prevent="changePage(Pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <li v-for="page in pagesNumber" v-bind:class="[ page == PageActive ? 'active':'']">
                    <a href="#" @click.prevent="changePage(page)">{{page}}</a>
                  </li>
                  <li v-if="Pagination.current_page < Pagination.last_page">
                    <a href="#" @click.prevent="changePage(Pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
              </div>
          </div>
        </div>
      </div>
  </div>
</div>
</template>
<script>
import Longpress from 'vue-longpress';
import axios from 'axios';
import moment from 'moment';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
  export default {
     data () {
       return{
         isActive:false,
         purpose:'',
         ItemSearch:'',
         SearchResults:[],
         Pagination:[],
         Quantity:[],
         Remarks:[],
         SessionItems:[],
         offset:4,
         HideButton:false,
       }
     },
     props: ['manager','gm'],

     methods: {
      searchItem(page)
      {
        this.$loading('Loading');
        var vm=this;
        axios.get(`/item-search?search=`+this.ItemSearch+`&page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'SearchResults',response.data.data);
          Vue.set(vm.$data,'Pagination',response.data);
          vm.Quantity=[];
          vm.Remarks=[];
          vm.$loading.close();
        });
      },
      submitTosession(datas,count)
      {
        this.$loading('adding...');
        var vm=this;
        axios.post(`/sessionMIRSitem`,{
        ItemCode:datas.ItemCode,
        Particulars:datas.Description,
        Unit:datas.Unit,
        Remarks:this.Remarks[count],
        Quantity:this.Quantity[count],
        }).then(function(response)
      {
          console.log(response);
          if (response.data.error==null)
          {
            vm.fetchAddedSession();
            vm.$toast.top('Added successfully');
            vm.Remarks=[];
            vm.Quantity=[];
            vm.$loading.close();
          }else
          {
            vm.$toast.top(response.data.error);
            vm.Remarks=[];
            vm.Quantity=[];
            vm.$loading.close();
          }
      },function(error)
      {
          vm.$toast.top(error.response.data.Quantity[0]);
          vm.Remarks=[];
          vm.Quantity=[];
          vm.$loading.close();
      });
      },
      deleteSession(code)
      {
        this.$loading('removing...')
        var vm=this;
        axios.delete(`/removeSessions/`+code,{}).then(function(response)
        {
          console.log(response);
          vm.fetchAddedSession();
          vm.$toast.top('Successfully removed');
          vm.$loading.close();
        },function(error)
        {
          console.log(error);
          vm.$loading.close();
        });
      },
        fetchAddedSession()
        {
          var vm=this;
          axios.get(`/fetchSessionMIRS`).then(function(response)
          {
            console.log(response)
            Vue.set(vm.$data,'SessionItems',response.data);
          });
        },
         submitWholePage()
        {
          this.HideButton=true;
          this.$loading('Please wait...');
          var vm=this;
          axios.post(`/mirs-storedata`,{
            Purpose:this.purpose,
            Approvedby:this.gm[0].id,
          }).then(function(response)
          {
            if (response.data.error==null)
            {
              window.location=response.data.redirect;
            }else
            {
              vm.$toast.top(response.data.error);
              vm.HideButton=false;
              vm.$loading.close();
            }
          },function(error)
          {
            vm.$loading.close();
            vm.HideButton=false;
            vm.$toast.top(error.response.data.Purpose[0]);
          });
        },
       changePage(page){
        this.Pagination.current_page = page;
        this.searchItem(page);
        this.Quantity=[];
        this.Remarks=[];
      },
     },
     created:function(){
       this.fetchAddedSession();
       this.searchItem();
     },
     computed: {
       PageActive:function(){
         return this.Pagination.current_page;
       },
       pagesNumber:function(){
         if (!this.Pagination.to) {
                       return [];
                   }
                   var from = this.Pagination.current_page - this.offset;
                   if (from < 1) {
                       from = 1;
                   }
                   var to = from + (this.offset * 2);
                   if (to >= this.Pagination.last_page) {
                       to = this.Pagination.last_page;
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
