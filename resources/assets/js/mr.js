window.Vue = require('vue');
Vue.component('mrcreate', require('./components/MR/CreateMRViews.vue'));
Vue.component('mrpreview', require('./components/MR/MRPreview.vue'));
Vue.component('mrindex', require('./components/MR/MRindex.vue'));
new Vue({
    el:'#mr',
});
