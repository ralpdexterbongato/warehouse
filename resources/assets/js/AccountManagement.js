window.Vue = require('vue');
Vue.component('accountsettings', require('./components/AccountManagement.vue'));
Vue.component('history', require('./components/MyHistory.vue'));
Vue.component('loginpage', require('./components/loginpage.vue'));
Vue.component('managers', require('./components/ManagerTakePlacer.vue'));
Vue.component('myaccount', require('./components/MyAccountSettings.vue'));
new Vue({
    el:'#accounts',
});
