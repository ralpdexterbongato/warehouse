<template lang="html">
<div class="welcome.vue">
  <div class="new-search-container">
    <div class="search-box" :class="[latestFound.MTNo!=null?'SearchItemSuccess':'z-depth-4']">
      <div class="text-left">
        <p v-if="latestFound.MTNo==null">
          <span class="big"><i class="material-icons">dashboard</i> Search</span> & <span class="big">check</span> item's latest & previous data here.
        </p>
        <h1 v-else-if="NotFoundSearch==''">
          <i class="fa fa-th-large"></i> Item # {{latestFound.ItemCode}} data
        </h1>
      </div>
      <div class="Search-item-box">
        <input id="search-code-input" autocomplete="off" type="text" v-on:keyup.enter="SearchItemHistory(1)" v-on:keyup="SearchForSuggestions()" v-model="ItemSearchInput" placeholder="Search" required>
        <button id="search-go" type="submit" v-on:click="SearchItemHistory(1)"><i class="material-icons">search</i></button>
        <div class="own-autocomplete z-depth-4">
          <p v-for="suggestion in autocomplete" v-on:click="SelectSuggestion(suggestion.Description)" class="autocomplete-row">{{suggestion.Description}}</p>
        </div>
      </div>
    </div>
  </div>
  <div class="report-damage-button" v-if="((latestFound.MTNo!=null) && (NotFoundSearch=='') && (user.Role==3 || user.Role==4))">
    <button v-on:click="dmgModalActive=true" type="button" class="z-depth-4" name="button"><i class="material-icons">broken_image</i></button>
    <button  type="button" v-on:click="deleteDmgShow=!deleteDmgShow" class="z-depth-4" name="button">
      <i v-if="deleteDmgShow==false" class="material-icons">delete</i>
      <i v-else class="material-icons">visibility_off</i>
    </button>
  </div>
  <div class="data-results-container" v-if="NotFoundSearch==''">
    <div v-if="latestFound.MTNo!=null" class="animated bounceInUp result-wrapper">
      <div class="search-welcome-title">
        <div class="Current-title">
          <h1><i class="material-icons">whatshot</i> Latest info</h1>
        </div>
      </div>
      <div class="latest-data">
        <table>
          <tr>
            <th>MT type</th>
            <th>MT No.</th>
            <th>Account code</th>
            <th>Item code</th>
            <th>Description</th>
            <th>Unit cost</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Amount</th>
            <th>Current cost</th>
            <th>Current quantity</th>
            <th>Current amount</th>
            <th>Date</th>
          </tr>
          <tr>
            <td>
              <button v-on:click="DeleteRecordDamage(latestFound.id,latestFound.ItemCode)" v-if="latestFound.MTType=='DMG' && deleteDmgShow==true" type="button" class="remove-damage-item-btn" name="button">
                <i class="material-icons">delete</i>
              </button>
              <span v-else>{{latestFound.MTType}}</span>
            </td>
            <td>{{latestFound.MTNo}}</td>
            <td>{{latestFound.AccountCode}}</td>
            <td>{{latestFound.ItemCode}}</td>
            <td>{{latestFound.master_items.Description}}</td>
            <td>{{formatPrice(latestFound.UnitCost)}}</td>
            <td>{{latestFound.Quantity}}</td>
            <td>{{latestFound.master_items.Unit}}</td>
            <td>{{formatPrice(latestFound.Amount)}}</td>
            <td>{{formatPrice(latestFound.CurrentCost)}}</td>
            <td>{{latestFound.CurrentQuantity}}</td>
            <td>{{formatPrice(latestFound.CurrentAmount)}}</td>
            <td>{{latestFound.MTDate}}</td>
          </tr>
        </table>
      </div>
      <div class="history-found">
        <h1><i class="material-icons">history</i> Previous info</h1>
        <table>
          <tr>
            <th>MT type</th>
            <th>MT No.</th>
            <th>Unit cost</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Amount</th>
            <th>Current cost</th>
            <th>Current quantity</th>
            <th>Current amount</th>
            <th>Date</th>
          </tr>
          <tr v-for="history in historiesfound" v-if="history.id!=latestFound.id">
            <td>
              <button v-on:click="DeleteRecordDamage(history.id,history.ItemCode)" v-if="history.MTType=='DMG' && deleteDmgShow==true" type="button" class="remove-damage-item-btn" name="button">
                <i class="material-icons">delete</i>
              </button>
              <span v-else>{{history.MTType}}</span>
            </td>
            <td>{{history.MTNo}}</td>
            <td>{{formatPrice(history.UnitCost)}}</td>
            <td>{{history.Quantity}}</td>
            <td>{{latestFound.master_items.Unit}}</td>
            <td>{{formatPrice(history.Amount)}}</td>
            <td>{{formatPrice(history.CurrentCost)}}</td>
            <td>{{history.CurrentQuantity}}</td>
            <td>{{formatPrice(history.CurrentAmount)}}</td>
            <td>{{history.MTDate}}</td>
          </tr>
        </table>
        <div class="paginate-container">
          <ul class="pagination">
            <li v-if="pagination.current_page > 1">
              <a href="#" @click.prevent="changepage(pagination.current_page - 1)"><i class="fa fa-angle-left"></i></a>
            </li>
            <li v-for="page in pagesNumber" v-bind:class="[ page == isActive ? 'active':'']">
              <a href="#" @click.prevent="changepage(page)">{{page}}</a>
            </li>
            <li v-if="pagination.current_page < pagination.last_page">
              <a href="#" @click.prevent="changepage(pagination.current_page+1)"><i class="fa fa-angle-right"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </div><!--  end of v-if result is not empty -->
    <div class="background-pic" v-else-if="((user.Role!=1)&&(user.Role!=3)&&(user.Role!=4))">
      <img src="/DesignIMG/truck.jpg" alt="img">
    </div>
    <div v-else class="dash-container">
      <div class="dash-home">
        <div class="dashbox dash-high z-depth-4">
          <div class="left-dash">
            <h1 class="circle-dash-icon">
              <i class="material-icons">filter_hdr</i>
            </h1>
            <h2 class="dash-labels">High</h2>
          </div>
          <div class="right-dash">
            <span>
              <h1 class="dash-totals">
                <animate-number
                    from="0"
                    :to="DashGood"
                    duration="1000"
                    easing="easeOutQuad" v-if="DashGood>0">
                </animate-number>
                <span v-else>
                  0
                </span>
              </h1>
              <p v-if="DashGood>1">Items</p>
              <p v-else>Item</p>
            </span>
            <h2><i class="material-icons">equalizer</i></h2>
          </div>
        </div>
        <div class="dashbox dash-low z-depth-4">
          <div class="left-dash">
            <h1 class="circle-dash-icon">
              <i class="material-icons">trending_down</i>
            </h1>
            <h2 class="dash-labels">Low</h2>
          </div>
          <div class="right-dash">
            <span>
              <h1 class="dash-totals">
                <animate-number
                    from="0"
                    :to="DashWarn"
                    duration="1500"
                    easing="easeOutQuad" v-if="DashWarn>0">
                </animate-number>
                <span v-else>
                  0
                </span>
              </h1>
              <p v-if="DashWarn>1">Items</p>
              <p v-else>Item</p>
            </span>
            <h2><i class="material-icons">equalizer</i></h2>
          </div>
        </div>
        <div class="dashbox dash-empty z-depth-4">
          <div class="left-dash">
            <h1 class="circle-dash-icon">
              <i class="material-icons">shopping_basket</i>
            </h1>
            <h2 class="dash-labels">Empty</h2>
          </div>
          <div class="right-dash">
            <span>
              <h1 class="dash-totals">
                <animate-number
                    from="0"
                    :to="DashEmpty"
                    duration="2000"
                    easing="easeOutQuad"
                    v-if="DashEmpty>0"
                    >
                </animate-number>
                <span v-else>
                  0
                </span>
              </h1>
              <p v-if="DashEmpty>1">Items</p>
              <p v-else>Item</p>
            </span>
            <h2><i class="material-icons">equalizer</i></h2>
          </div>
        </div>
      </div>
      <div class="charts-container">
        <div class="graphbox-left">
          <canvas id="myBarChart"></canvas>
        </div>
        <div class="graphbox-right">
          <canvas id="myLineChart"></canvas>
          <canvas id="myDoughnutChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="not-found-msg" v-if="NotFoundSearch!=''">
    <h2><i class="material-icons">search</i> {{NotFoundSearch}}</h2>
  </div>
  <div class="Report-Damage-Modal" v-on:click="dmgModalActive= !dmgModalActive" :class="[dmgModalActive==true?'active':'']">
    <div class="damage-item-modal-form z-depth-5" v-on:click="dmgModalActive= !dmgModalActive">
      <h1>Record damages</h1>
      <div class="input-container-material">
        <input type="text" v-model="dmgQty" id="Qty" :class="[dmgQty!=''?'active':'']">
        <label for="Qty">Quantity</label>
      </div>
      <div class="input-container-material">
        <input type="text" v-model="dmgQtyConfirm" id="Qtyconfirm"  :class="[dmgQtyConfirm!=''?'active':'']">
        <label for="Qtyconfirm">Retype quantity</label>
      </div>
      <p class="qty-error">{{QtyError}}</p>
      <div class="report-dmg-actions">
        <p v-on:click="dmgModalActive=false">CANCEL</p> <p v-on:click="recordDamageSubmit()" class="active">RECORD</p>
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
import VueAnimateNumber from 'vue-animate-number';
import Vue from 'vue';
Vue.use(VueAnimateNumber);
  export default {
    data () {
      return {
        deleteDmgShow:false,
        QtyError:'',
        dmgQty:'',
        dmgQtyConfirm:'',
        dmgModalActive:false,
        autocomplete:[],
        ItemSearchInput: '',
        pagination: [],
        offset: 4,
        latestFound: [],
        historiesfound: [],
        NotFoundSearch: '',
        DashGood:0,
        DashWarn:0,
        DashEmpty:0,
        LinechartData: {
        labels: [],
        datasets: [
                {
                    label:'MIRS',
                    data: [],
                    borderColor: "#fff",
                    backgroundColor: "#fdd835",
                    borderCapStyle: 'butt',
                    lineTension: 0.1,
                    pointRadius: 5,
                    pointHitRadius: 10,

                }
            ]
        },
        DoughnutchartData: {
        labels: ['MCT', 'MRT', 'RR'],
        datasets: [
          {
              data: [0,0,0],
              backgroundColor: ['#f44336', '#fdd835', '#3367D6'],
          },
        ]
        },
        barData : {
            labels: [],
            datasets: [
                {
                    label:'MCT',
                    data: [],
                    borderColor: "#fff",
                    lineTension: 0.1,
                    pointRadius: 5,
                    pointHitRadius: 10,
                    backgroundColor: "#f44336",

                },
                {
                    label:'MRT',
                    data: [],
                    borderColor: "#fff",
                    lineTension: 0.1,
                    pointRadius: 5,
                    pointHitRadius: 10,
                    backgroundColor: "#fdd835",
                },
                {
                    label:'RR',
                    data: [],
                    borderColor: "#fff",
                    lineTension: 0.1,
                    pointRadius: 5,
                    pointHitRadius: 10,
                    backgroundColor: "#3367D6",

                },
            ]
        },
        chartOption : {
      	showLines: true,
        responsive: true,
        showTooltips: true,
        },
        barOption : {
      	showLines: true,
        responsive: true,
        showTooltips: true,
        scales: {
                    xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                            }
                        }],
                    yAxes: [{
                            display: true,
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                },
        },
      }
    },
    props: ['user'],
    mounted() {
      if (this.user.Role==1||this.user.Role==3||this.user.Role==4)
      {
        this.DashData();
        this.fetchBarChart();
        this.fetchLineChart();
        this.fetchDoughnut();
      }
    },
    methods: {
      recordDamageSubmit()
      {
        if (this.dmgQty!=this.dmgQtyConfirm)
        {
          this.QtyError = 'Quantities did not match';
          return false;
        }else if (this.dmgQty=='' || this.dmgQtyConfirm=='')
        {
          this.QtyError = 'Please fill-up the fields';
          return false;
        }
        this.QtyError='';
        var vm=this;
        axios.post(`/damage-item-store/`+vm.latestFound.ItemCode,
        {
          'quantity':this.dmgQtyConfirm
        }).then(function(response)
        {
          console.log(response);
          vm.dmgQty ='';
          vm.dmgQtyConfirm = '';
          vm.dmgModalActive=false;
          vm.$toast.top('Recorded successfully');
          vm.SearchItemHistory(1);
        }).catch(function(error)
        {
          console.log(error);
          vm.QtyError = error.response.data.quantity[0];
        })
      },
      DeleteRecordDamage(id,itemcode)
      {
        if (confirm('Are you sure? you wont be able to revert this!'))
        {
          var vm=this;
          axios.delete(`/damage-item-delete/`+id+`/`+itemcode).then(function(response)
          {
            console.log(response);
            vm.$toast.top('removed successfully');
            vm.SearchItemHistory(1);
          }).catch(function(error)
          {
            console.log(error);
          });
        }
      },
      SearchForSuggestions()
      {
        if (this.ItemSearchInput.length > 3)
        {
          var vm=this;
          axios.get(`/search-item-autocomplete?typed=`+vm.ItemSearchInput).then(function(response)
          {
            console.log(response);
            vm.autocomplete = response.data;
          }).catch(function(error)
          {
            console.log(error);
          });
        }else
        {
          this.autocomplete = [];
        }
      },
      SelectSuggestion(Description)
      {
        this.ItemSearchInput = Description;
        this.autocomplete=[];
        this.SearchItemHistory(1);
      },
      DashData()
      {
        var vm=this;
        axios.get(`/show-data`).then(function(response)
        {
          console.log(response);
          vm.DashGood=response.data.good;
          vm.DashWarn=response.data.warn;
          vm.DashEmpty=response.data.empty;
        });
      },
      SearchItemHistory(page)
      {
        this.$loading('Please wait');
        var vm=this;
        axios.get(`/search-item-data?SearchInput=`+this.ItemSearchInput+`&page=`+page).then(function(response)
        {
          console.log(response);
          if (response.data.latestFound[0]==null) {
            vm.$loading.close();
            Vue.set(vm.$data,'NotFoundSearch','No results found.');
          }else
          {
            vm.$loading.close();
            Vue.set(vm.$data,'latestFound',response.data.latestFound[0]);
            Vue.set(vm.$data,'historiesfound',response.data.historiesfound.data);
            Vue.set(vm.$data,'pagination',response.data.historiesfound);
            Vue.set(vm.$data,'NotFoundSearch','');
          }

        });
      },
      fetchLineChart()
      {
        var canvas = document.getElementById('myLineChart');
        var myLineChart = Chart.Line(canvas,{
          data:this.LinechartData,
          options:this.chartOption
        });
        var vm=this;
        axios.get(`/line-chart-data`).then(function(response)
        {
          console.log(response);
          for (var i = 0; i < response.data.length ; i++)
          {
            vm.LinechartData.datasets[0].data.push(response.data[i].total);
            vm.LinechartData.labels.push(response.data[i].month);
            myLineChart.update();
          }
        }).catch(function(error)
        {
          console.log(error);
        })
      },
      fetchBarChart()
      {
        var canvas = document.getElementById('myBarChart');
        var myBarChart = Chart.Bar(canvas,{
          data:this.barData,
          options:this.barOption
        });
        var vm=this;
        axios.get(`/bar-chart-data`).then(function(response)
        {
          console.log(response.data);
          vm.barData.datasets[0].data=response.data.mct;
          vm.barData.datasets[1].data=response.data.mrt;
          vm.barData.datasets[2].data=response.data.rr;
          vm.barData.labels=response.data.months[0];
          myBarChart.update();
        }).catch(function(error)
        {
          console.log(error);
        });
      },
      fetchDoughnut()
      {
        var canvas = document.getElementById('myDoughnutChart');
        var myDoughnutChart = Chart.Doughnut(canvas,{
          data:this.DoughnutchartData,
          options:this.chartOption
        });
        var vm=this;
        axios.get(`/doughnut-chart-data`).then(function(response)
        {
          console.log(response);
          vm.DoughnutchartData.datasets[0].data[0]=response.data.mct;
          vm.DoughnutchartData.datasets[0].data[1]=response.data.mrt;
          vm.DoughnutchartData.datasets[0].data[2]=response.data.rr;
          myDoughnutChart.update();
        }).then(function(error)
        {
          console.log(error);
        })
      },
      formatPrice(value) {
            let val = (value/1).toFixed(2).replace('.', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      },
      changepage(next){
        this.pagination.current_page = next;
        this.SearchItemHistory(next);
      },
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
      },
    },

  }
</script>
