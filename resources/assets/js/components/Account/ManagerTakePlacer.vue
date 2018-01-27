<template lang="html">
<div class="managerbox-container">
  <div class="manager-box">
    <h1 class="flex"><i class="material-icons color-blue">info</i><p>This manager will be able to signature any request created that needs your approval, whenever you are not available</p></h1>
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
       <i class="material-icons darker-blue"v-if="editActive==false" v-on:click="editActive=true">border_color</i>
       <span v-if="editActive==true" class="update-manager-btns">
         <i class="material-icons color-blue" :disable="[AssignedManagerID==null?'true':'false']" v-on:click="UpdateManagerToTakePlace()">check</i>
         <i class="material-icons decliner" v-on:click="editActive=false">close</i>
       </span>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios';
import 'vue2-toast/lib/toast.css';
import Toast from 'vue2-toast';
Vue.use(Toast);
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
        this.$loading('Updating');
        var vm=this;
        axios.put(`/update-manager-to-take-place`,{ManagerID:this.AssignedManagerID}).then(function(response)
        {
          console.log(response);
          vm.getCurrentAssigned();
          vm.editActive=false;
          vm.$toast.top('Updated successfully');
          vm.$loading.close();
        });
      }
     },
     created()
     {
      this.fetchAllManager();
      this.getCurrentAssigned();
     }
  }
</script>
