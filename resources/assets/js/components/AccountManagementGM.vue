<template lang="html">
  <div class="setting-accounts-table">
    <div class="title-account-manager">
      <h1>
        <span v-if="gmbtn==true">List of General Managers</span>
        <span v-if="managerbtn==true">List of Managers</span>
        <span v-if="adminbtn==true">List of Admins</span>
        <span v-if="otherbtn==true">List of Other accounts</span>
        <i class="fa fa-group color-blue"></i>
      </h1>
    </div>
    <div class="top-right-menu-accounts">
      <ul>
        <li><button v-on:click="gmbtn=true,managerbtn=false,adminbtn=false,otherbtn=false,getSelected()" type="button" :class="[gmbtn==true?'active':'']">General Managers</button></li>
        <li><button type="button" v-on:click="gmbtn=false,managerbtn=true,adminbtn=false,otherbtn=false,getSelected()" :class="[managerbtn==true?'active':'']">Managers</button></li>
        <li><button type="button" v-on:click="gmbtn=false,managerbtn=false,adminbtn=true,otherbtn=false,getSelected()" :class="[adminbtn==true?'active':'']">Admins</button></li>
        <li><button type="button" v-on:click="gmbtn=false,managerbtn=false,adminbtn=false,otherbtn=true,getSelected()" :class="[otherbtn==true?'active':'']">Other</button></li>
        <li>
          <button type="button" v-on:click="createAccMenu=!createAccMenu"><i class="fa fa-user-plus"></i></button>
          <div class="create-acc-minimenu" :class="[createAccMenu==true?'active':'']">
            <h1><i class="fa fa-user-plus"></i> Create Account</h1>
            <h2>
              <input type="button" v-on:click="ManagerCreateModal=true" value="manager">
              <input type="button" v-on:click="newusermodal=true,[ManagerChoices[0]==null?fetchManagerChoices():'']" value="user">
            </h2>
          </div>
        </li>
      </ul>
    </div>
    <h1 v-if="gmbtn==true">General Managers</h1>
    <h1 v-if="managerbtn==true">Managers</h1>
    <h1 v-if="adminbtn==true">Admins</h1>
    <h1 v-if="otherbtn==true">Other accounts</h1>
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
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        <th>Signature</th>
        <th>Active</th>
        <th>Action</th>
      </tr>
      <tr v-for="account in AccountResults">
        <td><h2><p>{{account.Fname}}</p></h2></td>
        <td>{{account.Lname}}</td>
        <td>{{account.Username}}</td>
        <td><h1><img :src="'/storage/signatures/'+account.Signature" alt="signature"></h1></td>
        <td class="userstatus">
          <i v-if="account.IsActive!=null" class="fa fa-circle active"></i>
          <i v-else class="fa fa-circle"></i>
        </td>
        <td class="settingActions"><i class="fa fa-edit" v-on:click="modalUpdate=!modalUpdate,fetchselecteduser(account.id),fetchManagerChoices()"></i> <i class="fa fa-trash" v-on:click="deleteAccount(account.id)"></i></td>
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
          <span class="doubleform">
            <div class="updateinput-label">
              <h3 :class="[fname!=''?'active':'']">Firstname</h3>
              <input type="text" name="Fname" v-model="fname=userFetched.Fname">
            </div>
            <div class="updateinput-label">
              <h3 :class="[lname!=''?'active':'']">Lastname</h3>
              <input type="text" name="Lname" v-model="lname=userFetched.Lname">
            </div>
          </span>
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
            <option :value="manager.id" v-for="manager in ManagerChoices">{{manager.Fname}} {{manager.Lname}}</option>
            </select>
          </div>
          <div class="updateinput-label short-width">
            <h3 :class="[username!=''?'active':'']">Username</h3>
            <input type="text" name="Username" autocomplete="off" v-model="username=userFetched.Username">
          </div>
            <div class="updateinput-label short-width" >
              <h3 :class="[Password!=''?'active':'']">Change password</h3>
              <input type="password" name="Password" autocomplete="off" v-model="Password">
            </div>
            <div class="updateinput-label short-width">
              <h3 :class="[Password_confirmation!=''?'active':'']">Confirm new password</h3>
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
          <i class="fa fa-user-plus"></i> Manager Account
        </h1>
        <div class="manager-form-inputs">
          <div class="doubleform">
            <input type="text" v-model="ManagerRegisterFname" placeholder="Firstname">
            <input type="text" v-model="ManagerRegisterLname" placeholder="Lastname">
          </div>
          <div class="doubleform">
            <input type="text" v-model="ManagerRegisterUsername" placeholder="Username">
            <input type="text" v-model="ManagerRegisterPosition" placeholder="Position">
          </div>
          <div class="doubleform">
            <input type="password" v-model="ManagerRegisterPassword" placeholder="Password">
            <input type="password" v-model="ManagerPwordConfirm" placeholder="Confirm-password">
          </div>
          <input type="file" @change="onFileChange2" accept="image/PNG">
          <h3 class="signature-preview"><img :src="image2" alt="your signature"></h3>
          <button type="button" class="save-btn-manager" v-on:click="saveManagerAccount()"><i class="fa fa-user"></i> Save Account</button>
        </div>
      </div>
    </div>
    <div class="CreateNEwUserModal" :class="{'active':newusermodal}" v-on:click="newusermodal=!newusermodal">
      <div class="center-form-newuser" v-on:click="newusermodal=!newusermodal">
        <h1><i class="fa fa-user-plus"></i> New account</h1>
        <div class="newuserinputs">
          <div class="doubleform">
            <input type="text" placeholder="Firstname" v-model="RegisterFname">
            <input type="text" placeholder="Lastname" v-model="RegisterLname">
          </div>
          <div class="doubleform">
            <input type="text" placeholder="Username" v-model="RegisterUsername">
            <input type="password" placeholder="Password" v-model="RegisterPassword">
          </div>
          <input type="password" v-model="RegisterPwordConfirm" placeholder="Password-confirmation">
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
            <option :value="manager.id" v-for="manager in ManagerChoices">{{manager.Fname}} {{manager.Lname}}</option>
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
       gmbtn:true,
       managerbtn:false,
       adminbtn:false,
       otherbtn:false,
       modalUpdate:false,
       userFetched:[],
       fname:'',
       lname:'',
       position:'',
       role:null,
       activeUser:null,
       mymanager:null,
       username:'',
       Password:'',
       Password_confirmation:'',
       image:'',
       laravelerrors:[],
       ownerrors:'',
       successAlerts:'',
       createAccMenu:false,
       ManagerCreateModal:false,
       //managerregistration
       ManagerRegisterFname:'',
       ManagerRegisterLname:'',
       ManagerRegisterUsername:'',
       ManagerRegisterPosition:'',
       ManagerRegisterPassword:null,
       ManagerPwordConfirm:null,
       image2:'',
       //NewUserCreate
       ManagerChoices:[],
       newusermodal:false,
       image3:'',
       RegisterFname:'',
       RegisterLname:'',
       RegisterUsername:'',
       RegisterRole:'',
       ChoosenManager:null,
       RegisterPassword:null,
       RegisterPwordConfirm:null,
       }
     },
      methods: {
        getSelected(page)
        {
          if (this.gmbtn==true)
          {
            var url='/get-general-managers';
          }else if(this.managerbtn==true)
          {
            var url='/get-all-managers';
          }else if(this.adminbtn==true)
          {
            var url='/getallAdmin';
          }else if(this.otherbtn==true)
          {
            var url='/get-other-accounts';
          }
          var vm=this;
          axios.get(url+`?page=`+page).then(function(response)
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
          this.getSelected(next);
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
          axios.put(`/update-user-data/`+id,
          {
            emulateJSON: true,
            Fname:this.fname,
            Lname:this.lname,
            Role:this.role,
            Position:this.position,
            Manager:this.mymanager,
            Username:this.username,
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
            }
          },function(error)
          {
            Vue.set(vm.$data,'laravelerrors',error.response.data);
            Vue.set(vm.$data,'successAlerts','');
            Vue.set(vm.$data,'ownerrors','');
          });
          this.getSelected();
          this.image='';
        },
        deleteAccount(id)
        {
          var vm=this;
          axios.delete(`/deleteAccount/`+id).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'successAlerts','Account removed successfully');
          },function(error)
          {
            console.log(error);
          });
          this.getSelected();
        },
        saveManagerAccount()
        {
          var vm=this;
          axios.post(`/saving-account-manager`,{
            Fname:this.ManagerRegisterFname,
            Lname:this.ManagerRegisterLname,
            Username:this.ManagerRegisterUsername,
            Position:this.ManagerRegisterPosition,
            Password:this.ManagerRegisterPassword,
            Password_confirmation:this.ManagerPwordConfirm,
            Signature:this.image2,
          }).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'successAlerts','Success');
            Vue.set(vm.$data,'laravelerrors','');
            Vue.set(vm.$data,'ManagerRegisterFname','');
            Vue.set(vm.$data,'ManagerRegisterLname','');
            Vue.set(vm.$data,'ManagerRegisterUsername','');
            Vue.set(vm.$data,'ManagerRegisterPosition','');
            Vue.set(vm.$data,'ManagerRegisterPassword',null);
            Vue.set(vm.$data,'ManagerPwordConfirm',null);
            Vue.set(vm.$data,'image2',null);
          },function(error)
          {
            Vue.set(vm.$data,'laravelerrors',error.response.data);
            Vue.set(vm.$data,'successAlerts','');
          });
          if (this.managerbtn)
          {
            this.getSelected();
          }
        },
        SubmitNewUser()
        {
          var vm=this;
          axios.post(`/save-account-user`,{
            Fname:this.RegisterFname,
            Lname:this.RegisterLname,
            Username:this.RegisterUsername,
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
            }else
            {
              Vue.set(vm.$data,'successAlerts','Success');
              Vue.set(vm.$data,'ownerrors','');
              Vue.set(vm.$data,'laravelerrors','');
              Vue.set(vm.$data,'RegisterFname','');
              Vue.set(vm.$data,'RegisterLname','');
              Vue.set(vm.$data,'RegisterUsername','');
              Vue.set(vm.$data,'RegisterRole','');
              Vue.set(vm.$data,'ChoosenManager',null);
              Vue.set(vm.$data,'RegisterPassword',null);
              Vue.set(vm.$data,'RegisterPwordConfirm',null);
              Vue.set(vm.$data,'image3',null);
            }
          },function(error)
          {
            Vue.set(vm.$data,'laravelerrors',error.response.data);
            Vue.set(vm.$data,'successAlerts','');
            Vue.set(vm.$data,'ownerrors','');
          });
          this.getSelected();
        },
        fetchManagerChoices()
        {
          var vm=this;
          axios.get(`/get-all-managers`).then(function(response)
          {
            console.log(response);
            Vue.set(vm.$data,'ManagerChoices',response.data.data);
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
        this.getSelected(this.pagination.current_page);
      },
  }
</script>
