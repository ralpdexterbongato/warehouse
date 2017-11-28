<template lang="html">
  <div class="login-vue">
    <div class="left-login">
      <h1>Warehouse</h1>
    </div>
    <div class="right-login">
      <div class="form-login-container">
        <div class="login-placeholder-container">
          <h5 :class="{'active':usernameForm}">Username</h5>
          <div class="icon-and-input">
            <i class="material-icons">person</i>
            <input type="text" v-model="Username" @focus="usernameForm=true" @blur="[Username!=''?usernameForm=true:usernameForm=false]">
          </div>
        </div>
        <div class="login-placeholder-container">
          <h5 :class="{'active':passForm}">Password</h5>
          <div class="icon-and-input">
            <i class="material-icons">vpn_key</i>
            <input type="password" v-on:keyup.enter="submitCredentials" v-model="Password" @focus="passForm=true" @blur="[Password!=''?passForm=true:passForm=false]">
          </div>
        </div>
        <div class="submit-btn-login-container">
          <p>Forgot password?<br>-please contact the administrator.</p>
          <button type="button" name="button" v-on:click="submitCredentials" v-if="stillLoading==false"><i class="material-icons">send</i> Login</button>
          <div v-else class="loader-container">
            <i class="loading-login material-icons fa-spin">toys</i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
   data ()
   {
      return {
        Username:'',
        Password:'',
        failmsg:'',
        stillLoading:false,
        laravelerror:[],
        usernameForm:false,
        passForm:false
      }
    },
   methods:
   {
     submitCredentials()
     {
      this.stillLoading=true;
       var vm=this;
       axios.post(`/login-submit`,{
         Username:this.Username,
         Password:this.Password,
       }).then(function(response)
       {
         if (response.data.message!=null)
         {
           Vue.set(vm.$data,'failmsg',response.data.message);
           vm.stillLoading=false;
         }else
         {
           window.location=response.data.redirect;
         }
         console.log('response');
       },function(error)
       {
         console.log(error);
         Vue.set(vm.$data,'failmsg','Fields are required');
         vm.stillLoading=false;
       });
     }
   },
}
</script>
