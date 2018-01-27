window.Vue = require('vue');
Vue.component('createrrnopo', require('./components/RR/CreateRRNoPO.vue'));
Vue.component('createrrwithpo', require('./components/RR/CreateRRWithPO.vue'));
Vue.component('rrindex', require('./components/RR/RRindex.vue'));
Vue.component('rrpreview', require('./components/RR/RRfullpreview.vue'));
new Vue({
    el:'#rr',
});
