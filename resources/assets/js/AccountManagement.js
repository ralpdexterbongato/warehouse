window.Vue = require('vue');
Vue.component('generalmanagers', require('./components/AccountManagementGM.vue'));
Vue.component('managers', require('./components/AccountManagementManagers.vue'));
Vue.component('admin', require('./components/AccountManagementAdmin.vue'));
Vue.component('other', require('./components/AccountManagementOther.vue'));
new Vue({
    el:'#accounts',
});
