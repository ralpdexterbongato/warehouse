window.Vue = require('vue');
Vue.component('accountsettings', require('./components/Account/AccountManagement.vue'));
Vue.component('history', require('./components/Account/MyHistory.vue'));
Vue.component('loginpage', require('./components/Account/loginpage.vue'));
Vue.component('managers', require('./components/Account/ManagerTakePlacer.vue'));
Vue.component('myaccount', require('./components/Account/MyAccountSettings.vue'));
new Vue({
    el:'#accounts',
});
