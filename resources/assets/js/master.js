require('./bootstrap');
window.Vue = require('vue');
   Vue.component('mynotification', require('./components/Master/NotificationModal.vue'));
new Vue({
    el:'#master'
});
