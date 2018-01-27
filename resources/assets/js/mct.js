window.Vue = require('vue');
  Vue.component('mctpreview', require('./components/MCT/MCTPreview.vue'));
  Vue.component('mctindex', require('./components/MCT/MCTindex.vue'));
  Vue.component('createmct', require('./components/MCT/CreateMCT.vue'));
new Vue({
    el:'#mct',
});
