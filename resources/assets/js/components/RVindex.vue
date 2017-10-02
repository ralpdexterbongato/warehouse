
<template lang="html">
<div class="table-rv-container">
  <div class="top-RV-index">
    <div class="rv-index-title">
      <h1><i class="fa fa-th-large"></i> Requisition Voucher index</h1>
    </div>
    <div class="searchbox-RV">
      <input @keyup="fetchdataRV()" type="text" name="search" v-model="search" placeholder="Search RV Number">
    </div>
  </div>
  <table>
    <tr>
      <th>RV No.</th>
      <th>Purpose</th>
      <th>Requisitioner</th>
      <th>Recommended by</th>
      <th>Budget Officer</th>
      <th>General Manager</th>
      <th>RV Date</th>
      <th>Status</th>
      <th>Show</th>
    </tr>
    <tr v-for="models in model.data">
      <td>{{models.RVNo}}</td>
      <td>{{models.Purpose}}</td>
      <td>{{models.Requisitioner}}<br><i v-if="models.RequisitionerSignature!=null" class="fa fa-check"></i><i v-else-if="models.Requisitioner==models.IfDeclined" class="fa fa-times decliner"></i></td>
      <td>{{models.Recommendedby}}<br><i v-if="(models.RecommendedbySignature!=null)||(models.ManagerReplacerSignature!=null)" class="fa fa-check"></i><i v-else-if="models.Recommendedby==models.IfDeclined" class="fa fa-times decliner"></i></td>
      <td>{{models.BudgetOfficer}}<br><i v-if="models.BudgetOfficerSignature!=null" class="fa fa-check"></i><i v-else-if="models.BudgetOfficer==models.IfDeclined" class="fa fa-times decliner"></i></td>
      <td>{{models.GeneralManager}}<br><i v-if="models.GeneralManagerSignature!=null||models.ApprovalReplacerSignature!=null" class="fa fa-check"></i><i v-else-if="models.GeneralManager==models.IfDeclined" class="fa fa-times decliner"></i></td>
      <td>{{models.RVDate}}</td>
      <td v-if="(((models.RecommendedbySignature!=null)||(models.ManagerReplacerSignature!=null))&&(models.BudgetOfficerSignature!=null)&&((models.ApprovalReplacerSignature!=null)||(models.GeneralManagerSignature!=null)))"><i class="fa fa-thumbs-up"></i></td>
      <td v-else-if="models.IfDeclined==null"><i class="fa fa-clock-o"></i></td>
      <td v-else><i class="fa fa-times decliner"></i></td>
      <td><a v-bind:href="'RVfullview/'+models.RVNo"><i class="fa fa-eye"></i></a></td>
    </tr>
  </table>
  <div class="paginate-container">
    <ul class="pagination">
      <li v-if="pagination.current_page > 1">
        <a href="#" @click.prevent="changepageRV(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
      </li>
      <li v-for="page in pagesNumber" v-bind:class="[ page == isActive ? 'active':'']">
        <a href="#" @click.prevent="changepageRV(page)">{{page}}</a>
      </li>
      <li v-if="pagination.current_page < pagination.last_page">
        <a href="#" @click.prevent="changepageRV(pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
      </li>
    </ul>
  </div>

</div>
</template>
<script>
import axios from 'axios'
export default {
  data(){
    return {
      model:[],
      search:'',
      url:'indexRVVUE',
      pagination:{
        total:0,
        per_page:10,
        from:1,
        to:1,
        current_page:1
      },
      offset:4
    }
  },
  computed:{
    isActive:function(){
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


  created:function(){
    this.fetchdataRV(this.pagination.current_page);
  },
  methods: {
    fetchdataRV(page){
      var vm= this
      axios.get(`${this.url}?search=${this.search}&page=`+page)
      .then(function (response){
        Vue.set(vm.$data,'model',response.data.model);
        Vue.set(vm.$data,'pagination',response.data.pagination);
      })
    },
    changepageRV(next){
    this.pagination.current_page = next;
    this.fetchdataRV(next);
    },
  }
}
</script>
