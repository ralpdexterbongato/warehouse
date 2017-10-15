window.Vue = require('vue');
  Vue.component('mctpreview', require('./components/MCTPreview.vue'));
  Vue.component('mctindex', require('./components/MCTindex.vue'));
  Vue.component('createmct', require('./components/CreateMCT.vue'));
new Vue({
    el:'#mct',
});
