<template lang="html">
<div class="MRTCreate-vue">
  <div class="MRT-container">
    <div class="List-items-mrt">
      <div class="pick-from-items">
        <h1>Create Materials Returned Ticket</h1>
        <button type="button" class="waves-effect waves-light" v-on:click="ModalIsActive=!ModalIsActive"><i class="material-icons">add</i> Item</button>
      </div>
      <div class="items-form">
          <div class="items-from-mct">
            <table>
              <tr>
                <th>Item Code</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Summary</th>
                <th>Remove</th>
              </tr>
                <tr v-for="session in SessionSelected">
                  <td>{{session.ItemCode}}</td>
                  <td>{{session.Description}}</td>
                  <td>{{session.Unit}}</td>
                  <td>{{session.Summary}}</td>
                  <td><i v-on:click="deleteSession(session.ItemCode)" class="material-icons">close</i></td>
                </tr>
            </table>
          </div>
      </div>
    </div>
    <div class="MRT-middle-form">
      <div class="mrt-form">
        <div class="returner-form">
          Returned by:<br> <h3>{{mctdata[0].receiver_m_c_t[0].FullName}}</h3>
          <p>{{mctdata[0].receiver_m_c_t[0].Position}}</p>
        </div>
        <input autocomplete="off" type="text" v-model="remarks" placeholder="Remarks">
        <longpress duration="3" class="mrt-gobtn" :class="{'hide':HideSubmitBtn}"  :on-confirm="SubmitMRT" pressing-text="Confirm in {$rcounter}" action-text="Please wait">
        Submit
        </longpress>
      </div>
    </div>
  </div>
  <div class="mrt-items-modal" :class="{'active':ModalIsActive}" v-on:click="ModalIsActive=!ModalIsActive">
    <div class="mrt-items">
      <table>
        <tr>
          <th class="left-part">Item Code</th>
          <th>Description</th>
          <th>Unit</th>
          <th>Summary</th>
          <th class="right-part">Add</th>
        </tr>
        <tr v-for="(mtdata,count) in MTDetails">
          <td>{{mtdata.ItemCode}}</td>
          <td>{{mtdata.Description}}</td>
          <td>{{mtdata.Unit}}</td>
          <td><input type="number" v-model="Summary[count]" autocomplete="off" min="1"></td>
          <td><button type="submit" v-on:click="AddItemToSession(mtdata,count)" class="bttn-unite bttn-xs bttn-primary"><i class="material-icons">add</i></button></td>
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
        MTDetails:[],
        pagination:[],
        Summary:[],
        offset:4,
        SessionSelected:[],
        remarks:'',
        ModalIsActive:false,
        HideSubmitBtn:false,
      }
    },
    props: ['mctdata','mctno'],
    methods: {
      FetchMCTOfMRTData(page)
      {
        var vm=this;
        axios.get(`/MRT-create-fetch-mct-of-mrt/`+this.mctno.MCTNo+`?page=`+page).then(function(response)
        {
          console.log(response);
          vm.MTDetails=response.data.MTDetails.data
          vm.pagination=response.data.MTDetails;
        });
      },
      AddItemToSession(data,count)
      {
        this.$loading('please wait');
        var vm=this;
        axios.post(`/MRT-session`,{
          ItemCode:data.ItemCode,
          Description:data.Description,
          Unit:data.Unit,
          Summary:this.Summary[count],
          Limit:data.Quantity,
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error==null)
          {
            vm.fetchSelectedSession();
            vm.$toast.top('Added successfully');
            vm.$loading.close();
          }else
          {
            vm.$toast.top(response.data.error);
            vm.$loading.close();
          }
        },function(error)
        {
          vm.$toast.top(error.response.data.Summary[0]);
          vm.$loading.close();
        });

      },
      deleteSession(ItemCode)
      {
          this.$loading('Removing');
          var vm=this;
          axios.delete(`/MRT-delete/`+ItemCode).then(function(response)
          {
            console.log(response);
            vm.fetchSelectedSession();
            vm.$toast.top('Removed successfully.');
            vm.$loading.close();
          });
      },
      changepage(next){
        this.pagination.current_page = next;
        this.FetchMCTOfMRTData(next);
      },
      fetchSelectedSession()
      {
        var vm=this;
        axios.get(`/display-session-mrt`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'SessionSelected',response.data.SelectedSession);
        });
      },
      SubmitMRT()
      {
        this.HideSubmitBtn=true;
        this.$loading('Submitting');
        var vm=this;
        axios.post(`/MRT-store/`+this.mctno.MCTNo,{
          Particulars:this.mctdata[0].Particulars,
          AddressTo:this.mctdata[0].AddressTo,
          Remarks:this.remarks,
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error!=null)
          {
            vm.$loading.close();
            vm.$toast.top(response.data.error);
            Vue.set(vm.$data,'HideSubmitBtn',false);
          }else
          {
            window.location=response.data.redirect;
          }
        });
      }
    },
    created () {
      this.FetchMCTOfMRTData(1);
      this.fetchSelectedSession();
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
