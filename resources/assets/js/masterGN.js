require('./bootstrap');
window.Vue = require('vue');
   Vue.component('globalnotification', require('./components/Master/GlobalNotif.vue'));
new Vue({
    el:'#masterGN'
});
