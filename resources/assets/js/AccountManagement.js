window.Vue = require('vue');
Vue.component('accountsettings', require('./components/AccountManagementGM.vue'));
Vue.component('history', require('./components/MyHistory.vue'));
Vue.component('loginpage', require('./components/loginpage.vue'));
Vue.component('managers', require('./components/ManagerTakePlacer.vue'));
new Vue({
    el:'#accounts',
});