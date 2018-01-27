
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
window.Vue = require('vue');
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component('rvtable', require('./components/RV/RVindex.vue'));
Vue.component('rvpreview', require('./components/RV/FullRVpreview.vue'));
Vue.component('rvcreate', require('./components/RV/RVCreate.vue'));
new Vue({
    el:'#rv',
});
