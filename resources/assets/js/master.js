require('./bootstrap');
window.Vue = require('vue');
   Vue.component('mynotification', require('./components/NotificationModal.vue'));
   Vue.component('globalnotification', require('./components/GlobalNotif.vue'));
new Vue({
    el:'#master'
});
