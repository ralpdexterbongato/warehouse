require('./bootstrap');
window.Vue = require('vue');
   Vue.component('mynotification', require('./components/Master/NotificationModal.vue'));
   Vue.component('globalnotification', require('./components/Master/GlobalNotif.vue'));
new Vue({
    el:'#master'
});
