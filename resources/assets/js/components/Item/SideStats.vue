<template lang="html">
  <div class="side-user-stats">
    <div class="user-stat-box">
      <div class="user-stat-box-top">
        <h3>{{totalpending}}</h3>
        <p>Pending transactions</p>
      </div>
      <div class="user-stat-box-bot">
        <i class="material-icons">alarm</i>
      </div>
    </div>
    <div class="user-stat-box">
      <div class="user-stat-box-top">
        <h3>{{totalvalid}}</h3>
        <p>Valid transactions</p>
      </div>
      <div class="user-stat-box-bot">
        <i class="material-icons">done_all</i>
      </div>
    </div>
    <div class="user-stat-box">
      <div class="user-stat-box-top">
        <h3>{{totalinvalid}}</h3>
        <p>Invalid transactions</p>
      </div>
      <div class="user-stat-box-bot">
        <i class="material-icons">layers_clear</i>
      </div>
    </div>
    <div class="user-stat-box">
      <div class="user-stat-box-top">
        <h3>{{alltotal}}</h3>
        <p>Total transactions</p>
      </div>
      <div class="user-stat-box-bot">
        <i class="material-icons">description</i>
      </div>
    </div>
  </div>
</template>

<script >
  import axios from 'axios';
  export default {
    data () { return {
      totalvalid:'',
      totalinvalid:'',
      totalpending:'',
      alltotal:'',
      }
    },
    props: ['user'],
    mounted(){
      if (this.user.Role!=1 && this.user.Role!=3 && this.user.Role != 4)
      {
        this.countRelatedTransactions();
      }
    },
    computed: {

    },
    methods: {
      countRelatedTransactions()
      {
        var vm=this;
        axios.get(`/transactions-count`).then(function(response)
        {
          vm.totalvalid = response.data.validtotal;
          vm.totalinvalid = response.data.invalidtotal;
          vm.totalpending = response.data.pendingtotal;
          vm.alltotal = response.data.overall;
        }).catch(function(error)
        {

        });
      }
    },
  }
</script>
