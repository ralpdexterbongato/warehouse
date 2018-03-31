<template lang="html">
  <div class="sidebar-online">
    <button type="button" id="opener-icon"  v-on:click="IsActive=!IsActive" class="fixed-userstatus-opener" :class="[IsActive==true?'active':'']" name="button">
      <p v-if="IsActive==false">
        Online
        <i class="material-icons">navigate_next</i>
      </p>
      <p v-else><i class="material-icons">navigate_before</i></p>
    </button>
    <div class="list-wrapper">
      <ul :class="[IsActive==true?'open':'']">
        <div class="online-list-header"><p>CHECK STATUS</p></div>
        <div class="search-user-container">
          <i class="material-icons">search</i>
          <input type="text" v-model="NameSearch" v-on:keyup="fetchOrSearchEmployee()" placeholder="Search">
          <div class="divider">
          </div>
        </div>
        <div class="online-scroll-container">
          <li v-for="employee in Employees">
            <p class="employee-list-name">{{employee.FullName}}</p>
            <p v-if="employee.last_activity==0" class="online-dot active"></p>
            <p v-else class="last-online-time">{{employee.last_activity}}</p>
          </li>
        </div>
      </ul>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  export default {
    data () { return {
        NameSearch:'',
        Employees:[],
        IsActive:false,
      }
    },
    // props: [],
    mounted()
    {
      this.fetchOrSearchEmployee();
    },
    methods: {
      fetchOrSearchEmployee()
      {
        var vm=this;
        axios.get(`/all-users-status?search=`+this.NameSearch).then(function(response)
        {
          vm.Employees = response.data.data;
        }).catch(function(error)
        {
        });
      }
    },
  }
</script>
