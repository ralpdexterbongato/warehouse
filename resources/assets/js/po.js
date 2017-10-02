
window.Vue = require('vue');
   Vue.component('example', require('./components/Example.vue'));
   Vue.component('pofullpreview', require('./components/POFullpreview.vue'));
   Vue.component('poindex', require('./components/POindex.vue'));
new Vue({
    el:'#po',
});
