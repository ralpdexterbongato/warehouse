<template lang="html">
<div class="MIRS-create-wrap">
  <div class="Search-item-container">
    <div class="Added-Items">
      <div class="modal-find-button" >
          <button type="button" name="button" v-on:click="isActive = !isActive"><i class="fa fa-plus-circle"></i> Add item</button>
      </div>
      <div class="added-table-wrapper">
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
            <th>Item Code</th>
            <th>Particular</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Remarks</th>
            <th>Cancel</th>
          </tr>
            <tr v-for="sessionitem in SessionItems">
              <td>{{sessionitem.ItemCode_id}}</td>
              <td>{{sessionitem.Particulars}}</td>
              <td>{{sessionitem.Quantity}}</td>
              <td>{{sessionitem.Unit}}</td>
              <td>{{sessionitem.Remarks}}</td>
              <td class="delete-trash"><i class="fa fa-trash" v-on:click="deleteSession(sessionitem.ItemCode_id)"></i></td>
            <!-- <form class="delete-submit{{$selected->ItemCode_id}}"  action="{{route('delete.session',[$selected->ItemCode_id])}}" method="post" style="display:none">
            </form> -->
          </tr>
        </table>
      </div>
    </div>
  </div>
  <div class="MIRSform-container">
    <!-- <form action="{{route('mirs.store')}}" method="post"> -->
      <div class="form-wrap-mirs">
        <ul>
          <li><input type="text" autocomplete="off" name="Purpose" v-model="purpose" placeholder="Purpose"></li>
          <li><select name="Recommendedby" v-model="Recommendedby">
            <option :value="null">Recommended by</option>
            <option v-for="manager in managers" v-bind:value="manager.id">{{manager.Fname}} {{manager.Lname}}</option>
          </select></li>
            <li v-if="gm[0]!=null">
              <p class="gm-label">To be Approved by the General Manager</p><br><h3 class="gm-name">{{gm[0].Fname}} {{gm[0].Lname}}</h3>
            </li>
            <button id="go-btn" class="submitMCT-btn" v-on:click="submitWholePage()" type="submit">Submit</button>
        </ul>
      </div>
    <!-- </form> -->
  </div>
  <div class="modal-search-item":class="{'active':isActive}" v-on:click="isActive=!isActive">
    <div class="middle-modal-search" v-on:click="isActive=!isActive">
      <h1>MIRS</h1>
        <div class="table-mirs-modalcontain">
          <div class="search-item-bar">
              <div class="search-mirs-modal">
                <input type="text" autocomplete="off" v-model="SearchDescription" name="Description" @keyup="searchbyDescriptionMIRS()" placeholder="Search by description">
              </div>
              <div class="search-mirs-modal">
                <input type="text" autocomplete="off" name="ItemCode" @keyup="searchbyItemCode()" v-model="ItemCodeSearch" placeholder="Search by item code">
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

                <tr v-for="itemcoderesult in SearchResults">
                  <td>{{itemcoderesult.ItemCode_id}}</td>
                  <td>{{itemcoderesult.Description}}</td>
                  <td><input type="number" min="1" name="Quantity[]" v-model="Quantity[itemcoderesult.id]"></td>
                  <td>{{itemcoderesult.Unit}}</td>
                  <td><input type="text" autocomplete="off" min="1"  name="Remarks[]" v-model="Remarks[itemcoderesult.id]"></td>
                  <td><button type="button" v-on:click="submitTosession(itemcoderesult),isActive=!isActive"><i class="fa fa-plus"></i>Add</button></td>
                </tr>
              </table>
              <div class="pagination-container">
                <ul class="pagination" v-if="ItemCodeSearch!=''">
                  <li v-if="Pagination.current_page > 1">
                    <a href="#" @click.prevent="changePageCode(Pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
                  </li>
                  <li v-for="page in pagesNumber" v-bind:class="[ page == PageActive ? 'active':'']">
                    <a href="#" @click.prevent="changePageCode(page)">{{page}}</a>
                  </li>
                  <li v-if="Pagination.current_page < Pagination.last_page">
                    <a href="#" @click.prevent="changePageCode(Pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
                  </li>
                </ul>
                <ul class="pagination" v-else="SearchDescription!=''">
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
import axios from 'axios';
  export default {

     data () {
       return{
         isActive:false,
         Recommendedby:null,
         purpose:'',
         ItemCodeSearch:'',
         SearchDescription:'',
         DescriptionSearch:'',
         SearchResults:[],
         Pagination:[],
         Pagination:[],
         Quantity:[],
         Remarks:[],
         laravelerrors:[],
         successAlerts:[],
         ownerrors:[],
         SessionItems:[],
         offset:4,
       }
     },
     props: ['managers','gm'],

     methods: {
       searchbyItemCode(page)
       {
         this.SearchDescription='';
         var vm=this;
         axios.get(`/findMasterItem?ItemCode=`+this.ItemCodeSearch+`&page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'SearchResults',response.data.data);
          Vue.set(vm.$data,'Pagination',response.data);
        });
      },
      searchbyDescriptionMIRS(page)
      {
        this.ItemCodeSearch='';
        var vm=this;
        axios.get(`/Items-ByDescription?search=`+this.SearchDescription+`&page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'SearchResults',response.data.data);
          Vue.set(vm.$data,'Pagination',response.data);
        });
      },
      submitTosession(datas)
      {
        var vm=this;
        axios.post(`/sessionMIRSitem`,{
        ItemCode_id:datas.ItemCode_id,
        Particulars:datas.Description,
        Unit:datas.Unit,
        Remarks:this.Remarks[datas.id],
        Quantity:this.Quantity[datas.id],
        }).then(function(response)
      {
          console.log(response);
          if (response.data.error==null)
          {
            Vue.set(vm.$data,'successAlerts','Successfully added !');
            Vue.set(vm.$data,'ownerrors','');
            Vue.set(vm.$data,'laravelerrors','');
          }else
          {
            Vue.set(vm.$data,'ownerrors',response.data.error);
            Vue.set(vm.$data,'successAlerts','');
            Vue.set(vm.$data,'laravelerrors','');
          }
      },function(error)
      {
          console.log(error);
          Vue.set(vm.$data,'laravelerrors',error.response.data);
      });
        this.fetchAddedSession();
      },
      deleteSession(code)
      {
        var vm=this;
        axios.delete(`/removeSessions/`+code,{}).then(function(response)
        {
          console.log(response);
        },function(error)
        {
          console.log(error);
        });
        this.fetchAddedSession();
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
          var vm=this;
          axios.post(`/mirs-storedata`,{
            Purpose:this.purpose,
            Recommendedby:this.Recommendedby,
            Approvedby:this.gm[0].id,
          }).then(function(response)
          {
            console.log(response);
            window.location=response.data.redirect;
          },function(error)
          {
            console.log(error);
              Vue.set(vm.$data,'laravelerrors',error.response.data);
          });
        },
        changePageCode(page){
         this.Pagination.current_page = page;
         this.searchbyItemCode(page);
       },
       changePage(page){
        this.Pagination.current_page = page;
        this.searchbyDescriptionMIRS(page);
      },
     },
     created:function(){
       this.searchbyItemCode();
       this.fetchAddedSession();
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

  }
</script>
