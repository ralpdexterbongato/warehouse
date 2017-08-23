<template lang="html">
  <div class="setting-accounts-table">
    <h1>Admins</h1>
    <table>
      <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        <th>Signature</th>
        <th>Active</th>
        <th>Action</th>
      </tr>
      <tr v-for="admin in Admins">
        <td><h2><p>{{admin.Fname}}</p></h2></td>
        <td>{{admin.Lname}}</td>
        <td>{{admin.Username}}</td>
        <td><h1><img :src="'/storage/signatures/'+admin.Signature" alt="signature"></h1></td>
        <td class="userstatus">
          <i v-if="admin.IsActive!=null" class="fa fa-circle active"></i>
          <i v-else class="fa fa-circle"></i>
        </td>
        <td class="settingActions"><i class="fa fa-edit"></i> <i class="fa fa-trash"></i></td>
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
  </div>
</template>

<script>
import axios from 'axios';
  export default {
     data () {
       return {
       Admins:[],
       pagination:[],
       offset:4,
       }
     },
      methods: {
        getAllAdmins(page)
        {
          var vm=this;
          axios.get(`/getallAdmin?page=`+page).then(function(response)
        {
          console.log(response);
          Vue.set(vm.$data,'Admins',response.data.data);
          Vue.set(vm.$data,'pagination',response.data);
        },function(error)
        {
          console.log(error)
        });
        },
        changepage(next)
        {
          this.pagination.current_page = next;
          this.getAllAdmins(next);
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
        this.getAllAdmins(this.pagination.current_page);
      },
}
</script>
