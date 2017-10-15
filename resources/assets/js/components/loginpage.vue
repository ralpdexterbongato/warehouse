<template lang="html">
    <div class="middle-box-login">
      <div class="error-tab login-errors" v-if="failmsg!=''" v-on:click="failmsg=''">
        {{failmsg}}
      </div>
      <div class="box-form-login log-box animated" :class="[failmsg!=''?'headShake':'']">
        <h1 v-if="loadingMsg==''">Login <i class="fa fa-sign-in"></i></h1>
        <h1 v-if="loadingMsg!=''">{{loadingMsg}}</h1>
        <h1 class="login-loading" v-if="loadingMsg!=''"><i class="fa fa-spinner fa-spin fa-pulse"></i></h1>
        <div class="login-form" v-if="loadingMsg==''">
          <div class="login-input-container">
            <p><i class="fa fa-user"></i></p><input type="text" v-model="Username" @change="failmsg=''" autocomplete="off" name="Username" placeholder="Username">
          </div>
          <div class="login-input-container">
            <p><i class="fa fa-key"></i></p><input type="password" @change="failmsg=''" v-model="Password" v-on:keyup.enter="submitCredentials()" name="Password" placeholder="Password">
          </div>
          <button type="button" v-on:click="submitCredentials()">Login <i class="fa fa-sign-in"></i></button>
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
        loadingMsg:'',
        laravelerror:[],
      }
    },
   methods:
   {
     submitCredentials()
     {
      this.loadingMsg='Loading';
       var vm=this;
       axios.post(`/login-submit`,{
         Username:this.Username,
         Password:this.Password,
       }).then(function(response)
       {
         if (response.data.message!=null)
         {
           Vue.set(vm.$data,'failmsg',response.data.message);
           Vue.set(vm.$data,'loadingMsg','');
         }else
         {
           window.location=response.data.redirect;
         }
         console.log('response');
       },function(error)
       {
         console.log(error);
         Vue.set(vm.$data,'failmsg','Fields are required');
         Vue.set(vm.$data,'loadingMsg','');
       });
     }
   },
}
</script>
