<template lang="html">
<div class="managerbox-container">
  <div class="manager-box">
    <h1><i class="fa fa-info-circle color-blue"></i> This manager will be able to signature any request that belongs to you, whenever you are not available.</h1>
    <div class="manager-assigned-name">
      <h1 v-if="currentAssigned!=null">{{currentAssigned.FullName}}</h1>
      <h1 v-else>No one was assigned</h1>
    </div>
    <div class="Manager-List">
      <p>Allow
      <select v-model="AssignedManagerID" v-if="editActive==true">
        <option :value="null">No one</option>
        <option v-for="manager in Managers" :value="manager.id">{{manager.FullName}}</option>
      </select>
      <span class="underline" v-if="editActive==false&&currentAssigned!=null">{{currentAssigned.FullName}}</span>
      <span class="underline" v-else-if="editActive==false&&currentAssigned==null">no one</span>
       to take place whenever im not available</p>
       <i class="fa fa-edit darker-blue"v-if="editActive==false" v-on:click="editActive=true"></i>
       <span v-if="editActive==true">
         <i class="fa fa-check color-blue" :disable="[AssignedManagerID==null?'true':'false']" v-on:click="UpdateManagerToTakePlace()"></i>
         <i class="fa fa-times decliner" v-on:click="editActive=false"></i>
       </span>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios';
  export default {
     data () {
        return {
          editActive:false,
          Managers:[],
          currentAssigned:[],
          AssignedManagerID:null,
        }
      },
     methods: {
       getCurrentAssigned()
       {
         var vm=this;
         axios.get(`/get-current-assigned-bygm`).then(function(response)
        {
            console.log(response);
            Vue.set(vm.$data,'currentAssigned',response.data[0]);
        })
       },
       fetchAllManager()
       {
         var vm=this;
         axios.get(`/get-all-active-manager`).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'Managers',response.data);
        })
      },
      UpdateManagerToTakePlace()
      {
        var vm=this;
        axios.put(`/update-manager-to-take-place`,{ManagerID:this.AssignedManagerID}).then(function(response)
        {
          console.log(response);
        });
        this.getCurrentAssigned();
        this.editActive=false;
      }
     },
     created()
     {
      this.fetchAllManager();
      this.getCurrentAssigned();
     }
  }
</script>
