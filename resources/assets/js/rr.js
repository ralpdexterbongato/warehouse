window.Vue = require('vue');
Vue.component('createrrnopo', require('./components/CreateRRNoPO.vue'));
Vue.component('createrrwithpo', require('./components/CreateRRWithPO.vue'));
Vue.component('rrindex', require('./components/RRindex.vue'));
Vue.component('rrpreview', require('./components/RRfullpreview.vue'));
new Vue({
    el:'#rr',
});
