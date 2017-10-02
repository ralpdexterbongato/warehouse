require('./bootstrap');
window.Vue = require('vue');
   Vue.component('mynotification', require('./components/NotificationModal.vue'));
new Vue({
    el:'#master'
});
