
window.Vue = require('vue');
   Vue.component('example', require('./components/Example.vue'));
   Vue.component('pofullpreview', require('./components/PO/POFullpreview.vue'));
   Vue.component('poindex', require('./components/PO/POindex.vue'));
new Vue({
    el:'#po',
});
