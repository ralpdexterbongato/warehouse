<template lang="html">
  <div class="my-account-settings-vue">
    <div class="my-account-page-left">
    </div>
    <div class="my-account-page-middle">
      <h1 class="myacctitle"><i class="material-icons">settings</i> Account Settings</h1>
      <div class="myaccount-settings-table-container">
        <table>
          <tr>
            <th>Fullname</th><td>{{MyData.FullName}}</td><td class="align-right"><i class="material-icons">security</i></td>
          </tr>
          <tr>
            <th>Position</th><td>{{MyData.Position}}</td><td class="align-right"><i class="material-icons">security</i></td>
          </tr>
          <tr>
            <th>Signature</th><td><img v-if="MyData.Signature!=null" :src="'storage/signatures/'+MyData.Signature"></td><td class="align-right"><i class="material-icons">security</i></td>
          </tr>
          <tr>
            <th>Contact</th>
            <td v-if="contactEdit==false">{{MyData.Mobile}}</td>
            <td v-else>
              <input type="text" autofocus v-model="NewContact = MyData.Mobile">
            </td>
            <td class="align-right">
              <button v-if="contactEdit==false" v-on:click="contactEdit=true">Edit</button>
              <div class="acc-small-modal-setting" v-else>
                <button type="button" v-on:click="contactEdit=false,updateNewMobile()">
                  <i class="material-icons">check</i>
                </button>
                <button type="button" v-on:click="contactEdit=false,getMyAccountData()">
                  <i class="material-icons">close</i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <th>Username</th>
            <td v-if="usernameEdit==false">{{MyData.Username}}</td>
            <td v-else>
              <input type="text" autofocus v-model="NewUsername = MyData.Username">
            </td>
            <td class="align-right">
              <button v-if="usernameEdit==false" v-on:click="usernameEdit=true">Edit</button>
              <div class="acc-small-modal-setting" v-else>
                <button type="button" v-on:click="usernameEdit=false,updateNewUserName()">
                  <i class="material-icons">check</i>
                </button>
                <button type="button" v-on:click="usernameEdit=false,getMyAccountData()">
                  <i class="material-icons">close</i>
                </button>
              </div>
            </td>
          </tr>
          <tr class="password-settings">
            <th>Change-password</th>
            <td>
              <div class="change-pass-form">
                <input type="password" v-model="currentPass" placeholder="Current password" autofocus>
                <input type="password" v-model="newPass" placeholder="New password">
                <input type="password" v-model="newPassConfirm" placeholder="Confirm-password">
                <div class="save-new-pass-btn" v-if="passwordValidation==true" v-on:click="changeMyPass()">Save</div>
                <p v-else>* {{passwordValidation}}</p>
              </div>
            </td>
            <td class="align-right">
              <button><i class="material-icons">verified_user</i></button>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="my-account-page-right">
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
        usernameEdit:false,
        contactEdit:false,
        passwordEdit:false,
        MyData:[],
        currentPass:'',
        newPass:'',
        newPassConfirm:'',
        NewContact:'',
        NewUsername:''
      }
    },
    // computed: {},
    methods: {
      getMyAccountData()
      {
        var vm=this;
        axios.get(`/my-own-account-fetch-data`).then(function(response)
        {
          console.log(response.data);
          vm.MyData=response.data;
        });
      },
      updateNewMobile()
      {
        if (this.NewContact=='')
        {
          this.$toast.top('Contact number is required');
          this.contactEdit=true;
        }else if(this.NewContact.length!=11)
        {
          this.$toast.top('Number must be 11 digits');
          this.contactEdit=true;
        }else
        {
          var vm=this;
          axios.put('/update-account-contact',
          {
            NewMobile:this.NewContact
          }).then(function(response)
          {
            console.log(response);
            vm.$toast.top('Contact updated');
          });
        }
      },
      updateNewUserName()
      {
        if (this.NewUsername=='')
        {
          this.$toast.top('Username is required');
          this.usernameEdit=true;
        }else if(this.NewContact.length>30)
        {
          this.$toast.top('username is too long');
          this.usernameEdit=true;
        }else
        {
          var vm=this;
          axios.put('/update-account-username',
          {
            NewUserName:this.NewUsername
          }).then(function(response)
          {
            console.log(response);
            if (response.data.error==null)
            {
              vm.$toast.top('Username updated');
              vm.getMyAccountData();
            }else
            {
              vm.$toast.top(response.data.error);
              vm.getMyAccountData();
            }
          });
        }
      },
      changeMyPass()
      {
        var vm=this;
        axios.put(`/update-account-password`,
        {
          currentPass:this.currentPass,
          Password:this.newPass,
          Password_confirmation:this.newPassConfirm
        }).then(function(response)
        {
          console.log(response);
          if (response.data.error!=null)
          {
            vm.$toast.top(response.data.error);
          }else
          {
            vm.$toast.top('Password changed');
            vm.currentPass='';
            vm.newPass='';
            vm.newPassConfirm='';
          }
        });
      }
    },
    created()
    {
      this.getMyAccountData();
    },
    computed:{
      passwordValidation:function()
      {

        if((this.currentPass=='')&&((this.newPass!='')||(this.newPassConfirm!='')))
        {
          return 'Please input your current password';
        }else if((this.currentPass=='')&&(this.newPass=='')&&(this.newPassConfirm==''))
        {
          return '';
        }else if((this.newPass.length < 10)&&(this.newPass!=''))
        {
          return 'Your new password must be atleast 10 characters';
        }else if (this.newPassConfirm!=this.newPass)
        {
          return 'Your new password must match';
        }else if(this.newPass.length > 50)
        {
          return 'Your new password is too long';
        }else if ((this.currentPass!='')&&(this.newPassConfirm!='')&&(this.newPass!='')&&(this.newPassConfirm==this.newPass))
        {
          return true;
        }
      }
    }
  }
</script>
