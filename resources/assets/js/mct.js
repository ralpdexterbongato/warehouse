window.Vue = require('vue');
  Vue.component('mctpreview', require('./components/MCTPreview.vue'));
  Vue.component('mctindex', require('./components/MCTindex.vue'));
new Vue({
    el:'#mct',
});
