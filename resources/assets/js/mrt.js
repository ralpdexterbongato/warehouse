window.Vue = require('vue');
  Vue.component('mrtpreview', require('./components/MRT/MRTPreview.vue'));
  Vue.component('mrtindex', require('./components/MRT/MRTindex.vue'));
  Vue.component('mrtcreate', require('./components/MRT/MRTcreate.vue'));
  Vue.component('mrtrequesttable', require('./components/MRT/myMRTSignatureRequest.vue'));
new Vue({
    el:'#mrt',
});
