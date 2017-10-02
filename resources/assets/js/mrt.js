window.Vue = require('vue');
  Vue.component('mrtpreview', require('./components/MRTPreview.vue'));
  Vue.component('mrtindex', require('./components/MRTindex.vue'));
  Vue.component('mrtrequesttable', require('./components/myMRTSignatureRequest.vue'));
new Vue({
    el:'#mrt',
});
