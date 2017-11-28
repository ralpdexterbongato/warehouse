<template lang="html">
  <div class="setting-accounts-table">
    <div class="title-account-manager">
      <h1>
        <i class="material-icons">people</i><span> List of accounts</span>
      </h1>
    </div>
    <div class="top-right-menu-accounts">
      <div class="search-name-box">
        <input type="text" placeholder="Firstname Lastname" v-on:keyup="getSelectedAndSearch" v-model="FullNameSearch">
      </div>
      <ul>
        <select class="SortByRole" v-model="SelectedRole" v-on:change="getSelectedAndSearch(1)">
          <option value=''>All</option>
          <option value="0">Managers</option>
          <option value="1">Admins</option>
          <option value="2">General managers</option>
          <option value="3">Warehouse assistants</option>
          <option value="4">Warehouse heads</option>
          <option value="5">Auditors</option>
          <option value="6">Clerks</option>
          <option value="7">Budget officers</option>
          <option value="8">Basic-roles</option>
        </select>
        <li>
          <button type="button" v-on:click="createAccMenu=!createAccMenu">
            <i class="material-icons" v-if="createAccMenu==false">person_add</i>
            <i class="material-icons"v-else>close</i>
          </button>
          <div class="create-acc-minimenu" :class="[createAccMenu==true?'active':'']">
            <h1><i class="material-icons">person_add</i> Create Account</h1>
            <h2>
              <input type="button" v-on:click="ManagerCreateModal=true" value="manager">
              <input type="button" v-on:click="newusermodal=true,[ManagerChoices[0]==null?fetchManagerChoices():'']" value="user">
            </h2>
          </div>
        </li>
      </ul>
    </div>
    <ul class="error-tab" v-if="laravelerrors!=''">
      <span v-for="errors in laravelerrors">
        <li v-for="error in errors">{{error}}</li>
      </span>
    </ul>
    <ul class="error-tab" v-if="ownerrors!=''">
      <h5>{{ownerrors}}</h5>
    </ul>
    <div class="successAlertRRsession" v-if="successAlerts!=''">
      <p>{{successAlerts}}</p>
    </div>
    <table>
      <tr>
        <th>FullName</th>
        <th>Username</th>
        <th>Mobile #</th>
        <th>Signature</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <tr v-for="account in AccountResults">
        <td><h2><p>{{account.FullName}}</p></h2></td>
        <td>{{account.Username}}</td>
        <td v-if="account.Mobile!=null">{{account.Mobile}}</td>
        <td v-else>N/A</td>
        <td><h1><img :src="'/storage/signatures/'+account.Signature" alt="signature"></h1></td>
        <td class="userstatus">
          <i v-if="account.IsActive!=null" class="material-icons active">person</i>
          <i v-else class="material-icons">person</i>
        </td>
        <td class="settingActions">
          <i class="material-icons" v-on:click="modalUpdate=!modalUpdate,fetchselecteduser(account.id),fetchManagerChoices()">edit</i>
          <i class="material-icons" v-on:click="deleteAccount(account.id)">delete_forever</i>
        </td>
      </tr>
    </table>
    <div class="paginate-container">
      <ul class="pagination">
        <li v-if="pagination.current_page > 1">
          <a href="#" @click.prevent="changepage(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
        </li>
        <li v-for="page in pagesNumber" v-bind:class="[ page == Activepage ? 'active':'']">
          <a href="#" @click.prevent="changepage(page)">{{page}}</a>
        </li>
        <li v-if="pagination.current_page < pagination.last_page">
          <a href="#" @click.prevent="changepage(pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
        </li>
      </ul>
    </div>
    <div class="update-account-modal" :class="{'active animated fadeIn':modalUpdate}" v-on:click="modalUpdate=!modalUpdate">
      <div class="update-modal-center" v-on:click="modalUpdate=!modalUpdate">
        <h1>Update account</h1>
        <div class="triangle-top-right-update">
        </div>
        <div class="update-inputs">
          <div class="updateinput-label short-width">
            <h3 :class="[fullname!=''?'active':'']">Full Name</h3>
            <input type="text" name="FullName" v-model="fullname=userFetched.FullName">
          </div>
          <span class="doubleform">
            <div class="updateinput-label" v-if="userFetched.Role==0">
              <h3 class="active">Position</h3>
              <input type="text" v-model="position=userFetched.Position">
            </div>
            <div class="updateinput-label">
              <h3 class="active">Role</h3>
              <select v-model="role=userFetched.Role">
                <option value="0">Manager</option>
                <option value="1">Admin</option>
                <option value="2">General Manager</option>
                <option value="3">Warehouse Assistant</option>
                <option value="4">WarehouseHead</option>
                <option value="5">Auditor</option>
                <option value="6">Clerk</option>
                <option value="7">Budget Officer</option>
                <option value="8">Normal User</option>
              </select>
            </div>
          </span>
          <div class="updateinput-label short-width">
            <h3 class="active">Account status</h3>
            <select v-model="activeUser=userFetched.IsActive">
              <option :value="'0'">Active</option>
              <option :value="null">Inactive</option>
            </select>
          </div>
          <div class="updateinput-label short-width" v-if="((userFetched.Role!=0)&&(userFetched.Role!=2))">
            <h3 :class="[mymanager!=null?'active':'']">Manager</h3>
            <select v-model="mymanager=userFetched.Manager">
            <option :value="manager.id" v-for="manager in ManagerChoices">{{manager.FullName}}</option>
            </select>
          </div>
          <div class="updateinput-label short-width">
            <h3 :class="[username!=''?'active':'']">Username</h3>
            <input type="text" name="Username" autocomplete="off" v-model="username=userFetched.Username">
          </div>
          <div class="updateinput-label short-width">
            <h3 :class="[MobileUpdate==''?'':'active']">Mobile #</h3>
            <input type="text" name="Username" autocomplete="off" v-model="MobileUpdate=userFetched.Mobile">
          </div>
          <div class="updateinput-label short-width" >
            <h3 :class="[Password!=''?'active':'']">Change password</h3>
            <input type="password" name="Password" autocomplete="off" v-model="Password">
          </div>
          <div class="updateinput-label short-width">
            <h3 :class="[Password_confirmation!=''?'active':'']">Confirm-password</h3>
            <input type="password" name="PasswordConfirmation" v-model="Password_confirmation" autocomplete="off" >
          </div>
          <input type="file"  name="Signature" @change="onFileChange" autocomplete="off" id="inputSignature" accept="image/PNG">
          <div class="image-signature-wrap">
            <img id="signatureUpdate" v-if="image==''&&userFetched!=''" :src="'/storage/signatures/'+userFetched.Signature" alt="your signature" />
            <img id="signatureUpdate" v-else :src="image" alt="your signature" />
          </div>
          <button type="button" v-on:click="submitUpdate(userFetched.id),modalUpdate=!modalUpdate">Update</button>
        </div>
      </div>
    </div>
    <div class="CreateManagerAccount-Modal" :class="{'active':ManagerCreateModal}" v-on:click="ManagerCreateModal=!ManagerCreateModal">
      <div class="center-manager-create-form" v-on:click="ManagerCreateModal=!ManagerCreateModal">
        <h1>
          <i class="material-icons">person_add</i> Manager Account
        </h1>
        <div class="manager-form-inputs">
          <div class="doubleform">
            <input type="text" v-model="ManagerRegisterFullName" placeholder="Full Name" autocomplete="off">
          </div>
          <div class="doubleform">
            <input type="text" v-model="ManagerRegisterUsername" placeholder="Username" autocomplete="off">
            <input type="text" v-model="ManagerRegisterPosition" placeholder="Position" autocomplete="off">
          </div>
          <div class="doubleform">
            <input type="password" v-model="ManagerRegisterPassword" placeholder="Password" autocomplete="off">
            <input type="password" v-model="ManagerPwordConfirm" placeholder="Confirm-password" autocomplete="off">
          </div>
          <input type="text" v-model="ManagerRegisterMobile" placeholder="Mobile #" autocomplete="off">
          <input type="file" @change="onFileChange2" accept="image/PNG">
          <h3 class="signature-preview"><img :src="image2" alt="your signature"></h3>
          <button type="button" class="save-btn-manager" v-on:click="saveManagerAccount()"><i class="fa fa-user"></i> Save Account</button>
        </div>
      </div>
    </div>
    <div class="CreateNEwUserModal" :class="{'active':newusermodal}" v-on:click="newusermodal=!newusermodal">
      <div class="center-form-newuser" v-on:click="newusermodal=!newusermodal">
        <h1><i class="material-icons">person_add</i> New account</h1>
        <div class="newuserinputs">
          <input type="text" placeholder="FullName" v-model="RegisterFullName" autocomplete="off">
          <div class="doubleform">
            <input type="text" placeholder="Username" v-model="RegisterUsername" autocomplete="off">
            <input type="text" placeholder="Mobile #" v-model="RegisterMobile" autocomplete="off">
          </div>
          <input type="password" placeholder="Password" v-model="RegisterPassword" autocomplete="off">
          <input type="password" v-model="RegisterPwordConfirm" placeholder="Password-confirmation" autocomplete="off">
          <select name="Role" v-model="RegisterRole">
            <option value="">Choose role</option>
            <option value="1">Admin</option>
            <option value="2">General Manager</option>
            <option value="3">Warehouse Assistant</option>
            <option value="4">WarehouseHead</option>
            <option value="5">Auditor</option>
            <option value="6">Clerk</option>
            <option value="7">Budget Officer</option>
            <option value="8">Requisitioner</option>
          </select>
          <select  v-model="ChoosenManager">
            <option :value="null">His/Her manager</option>
            <option :value="manager.id" v-for="manager in ManagerChoices">{{manager.FullName}}</option>
          </select>
          <input type="file" @change="onFileChange3" accept="image/PNG">
          <h3 class="signature-preview"><img :src="image3" alt="your signature"></h3>
          <button type="button" class="save-btn-user" v-on:click="SubmitNewUser()"> <i class="fa fa-user"></i> Save account</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
  export default {
     data () {
       return {
       AccountResults:[],
       pagination:[],
       offset:4,
       modalUpdate:false,
       userFetched:[],
       fullname:'',
       position:'',
       role:null,
       activeUser:null,
       mymanager:null,
       username:'',
       MobileUpdate:'',
       Password:'',
       Password_confirmation:'',
       image:'',
       laravelerrors:[],
       ownerrors:'',
       successAlerts:'',
       createAccMenu:false,
       ManagerCreateModal:false,
       //managerregistration
       ManagerRegisterFullName:'',
       ManagerRegisterUsername:'',
       ManagerRegisterPosition:'',
       ManagerRegisterPassword:null,
       ManagerPwordConfirm:null,
       ManagerRegisterMobile:null,
       image2:'',
       //NewUserCreate
       ManagerChoices:[],
       newusermodal:false,
       image3:'',
       RegisterFullName:'',
       RegisterUsername:'',
       RegisterRole:'',
       ChoosenManager:null,
       RegisterPassword:null,
       RegisterPwordConfirm:null,
       RegisterMobile:null,
       //render by Role
       SelectedRole:'',
       FullNameSearch:''
       }
     },
      methods: {
        getSelectedAndSearch(page)
        {
          var vm=this;
          axios.get(`/sort-by-role-and-search?Role=`+this.SelectedRole+`&FullName=`+this.FullNameSearch+`&page=`+page).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'AccountResults',response.data.data);
            Vue.set(vm.$data,'pagination',response.data);
          },function(error)
          {
            console.log(error)
          });
        },
        changepage(next)
        {
          this.pagination.current_page = next;
          this.getSelectedAndSearch(next);
        },
        fetchselecteduser(id)
        {
          var vm=this;
          axios.get(`/fetchDataofSelectedUser/`+id).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'userFetched',response.data[0]);
            Vue.set(vm.$data,'Password','');
            Vue.set(vm.$data,'Password_confirmation','');
          });
        },
        onFileChange(e) {
          var files = e.target.files || e.dataTransfer.files;
          if (!files.length)
            return;
          this.createImage(files[0]);
        },
        createImage(file) {
          var image = new Image();
          var reader = new FileReader();
          var vm = this;

          reader.onload = (e) => {
            vm.image = e.target.result;
          };
          reader.readAsDataURL(file);
        },
        onFileChange2(e) {
          var files = e.target.files || e.dataTransfer.files;
          if (!files.length)
            return;
          this.createImage2(files[0]);
        },
        createImage2(file) {
          var image2 = new Image();
          var reader = new FileReader();
          var vm = this;

          reader.onload = (e) => {
            vm.image2 = e.target.result;
          };
          reader.readAsDataURL(file);
        },
        onFileChange3(e) {
          var files = e.target.files || e.dataTransfer.files;
          if (!files.length)
            return;
          this.createImage3(files[0]);
        },
        createImage3(file) {
          var image3 = new Image();
          var reader = new FileReader();
          var vm = this;

          reader.onload = (e) => {
            vm.image3 = e.target.result;
          };
          reader.readAsDataURL(file);
        },
        submitUpdate(id)
        {
            var vm=this;
          if (confirm("Save Changes?")==true)
          {
            axios.put(`/update-user-data/`+id,
            {
              emulateJSON: true,
              FullName:this.fullname,
              Role:this.role,
              Position:this.position,
              Manager:this.mymanager,
              Username:this.username,
              Mobile:this.MobileUpdate,
              Password:this.Password,
              Password_confirmation:this.Password_confirmation,
              Signature:this.image,
              IsActive:this.activeUser
            }).then(function(response)
            {
              console.log(response);
              if (response.data.error!=null)
              {
                Vue.set(vm.$data,'ownerrors',response.data.error);
                Vue.set(vm.$data,'successAlerts','');
                Vue.set(vm.$data,'laravelerrors','');
              }else
              {
                Vue.set(vm.$data,'successAlerts','Updated Successfully');
                Vue.set(vm.$data,'laravelerrors','');
                Vue.set(vm.$data,'ownerrors','');
                vm.getSelectedAndSearch(vm.Activepage);
                vm.image='';
              }
            },function(error)
            {
              Vue.set(vm.$data,'laravelerrors',error.response.data);
              Vue.set(vm.$data,'successAlerts','');
              Vue.set(vm.$data,'ownerrors','');
            });
          }
        },
        deleteAccount(id)
        {
          var vm=this;
          if (confirm("Are you sure to delete this account?")==true)
          {
            axios.delete(`/deleteAccount/`+id).then(function(response)
            {
              console.log(response);
              vm.getSelectedAndSearch(vm.Activepage);
              Vue.set(vm.$data,'successAlerts','Account removed successfully');
            },function(error)
            {
              console.log(error);
            });
          }
        },
        saveManagerAccount()
        {
          var vm=this;
          if (confirm("Save this manager account?")==true)
          {
            axios.post(`/saving-account-manager`,{
              FullName:this.ManagerRegisterFullName,
              Username:this.ManagerRegisterUsername,
              Mobile:this.ManagerRegisterMobile,
              Position:this.ManagerRegisterPosition,
              Password:this.ManagerRegisterPassword,
              Password_confirmation:this.ManagerPwordConfirm,
              Signature:this.image2,
            }).then(function(response)
            {
              console.log(response);
              vm.getSelectedAndSearch(vm.Activepage);
              Vue.set(vm.$data,'successAlerts','Success');
              Vue.set(vm.$data,'laravelerrors','');
              Vue.set(vm.$data,'ManagerRegisterFullName','');
              Vue.set(vm.$data,'ManagerRegisterUsername','');
              Vue.set(vm.$data,'ManagerRegisterPosition','');
              Vue.set(vm.$data,'ManagerRegisterPassword',null);
              Vue.set(vm.$data,'ManagerPwordConfirm',null);
              Vue.set(vm.$data,'ManagerRegisterMobile',null);
              Vue.set(vm.$data,'image2',null);
            },function(error)
            {
              Vue.set(vm.$data,'laravelerrors',error.response.data);
              Vue.set(vm.$data,'successAlerts','');
            });
          }
        },
        SubmitNewUser()
        {
          var vm=this;
          if (confirm("Save this new account?")==true)
          {
            axios.post(`/save-account-user`,{
              FullName:this.RegisterFullName,
              Username:this.RegisterUsername,
              Mobile:this.RegisterMobile,
              Role:this.RegisterRole,
              Manager:this.ChoosenManager,
              Password:this.RegisterPassword,
              Password_confirmation:this.RegisterPwordConfirm,
              Signature:this.image3,
            }).then(function(response)
            {
              console.log(response);
              if (response.data.error!=null)
              {
                Vue.set(vm.$data,'ownerrors',response.data.error);
                Vue.set(vm.$data,'laravelerrors','');
                Vue.set(vm.$data,'successAlerts','');
              }else
              {
                vm.successAlerts='Success';
                vm.ownerrors='';
                vm.laravelerrors='';
                vm.RegisterFullName='';
                vm.RegisterUsername='';
                vm.RegisterRole='';
                vm.ChoosenManager=null;
                vm.RegisterPassword=null;
                vm.RegisterPwordConfirm=null;
                vm.image3=null;
                vm.RegisterMobile=null;
                vm.getSelectedAndSearch(vm.Activepage)
              }
            },function(error)
            {
              Vue.set(vm.$data,'laravelerrors',error.response.data);
              Vue.set(vm.$data,'successAlerts','');
              Vue.set(vm.$data,'ownerrors','');
            });
          }
        },
        fetchManagerChoices()
        {
          var vm=this;
          axios.get(`/get-all-managers`).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'ManagerChoices',response.data);
          });
        },
      },
      computed:{
        Activepage:function(){
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
      mounted () {
        this.getSelectedAndSearch(this.pagination.current_page);
      },
  }
</script>
