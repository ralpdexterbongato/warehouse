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
            <i class="material-icons" :class="{'active':usernameForm}">person</i>
            <input type="text" autofocus :class="{'active':usernameForm}"v-on:keyup.enter="submitCredentials()" v-model="Username" @focus="usernameForm=true" @blur="[Username!=''?usernameForm=true:usernameForm=false]">
          </div>
        </div>
        <div class="login-placeholder-container">
          <h5 :class="{'active':passForm}">Password</h5>
          <div class="icon-and-input">
            <i class="material-icons" :class="{'active':passForm}">vpn_key</i>
            <input type="password" :class="{'active':passForm}" v-on:keyup.enter="submitCredentials()" v-model="Password" @focus="passForm=true" @blur="[Password!=''?passForm=true:passForm=false]">
          </div>
        </div>
        <div class="submit-btn-login-container">
          <p>Forgot password?<br>-please contact the administrator.</p>
          <button type="button" class="waves-effect waves-light" :class="{'pulse':formValid}" name="button" v-on:click="submitCredentials"> <i class="material-icons">send</i> Login</button>
        </div>
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
   data ()
   {
      return {
        Username:'',
        Password:'',
        usernameForm:false,
        passForm:false
      }
    },
   methods:
   {
     submitCredentials()
     {
      this.$loading('loading...');
       var vm=this;
       axios.post(`/login-submit`,{
         Username:this.Username,
         Password:this.Password,
       }).then(function(response)
       {
         if (response.data.message!=null)
         {
           vm.$toast.bottom(response.data.message);
           vm.$loading.close();
         }else
         {
           vm.$loading.close();
           vm.$toast.top('Welcome');
           window.location=response.data.redirect;
         }
       });
     }
   },
   computed:
   {
     formValid:function()
     {
       if (((this.Username!='')&&(this.Password!='')))
       {
         return true;
       }
     }
   }
}
</script>
