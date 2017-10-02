window.Vue = require('vue');
Vue.component('mrcreate', require('./components/CreateMRViews.vue'));
Vue.component('mrpreview', require('./components/MRPreview.vue'));
Vue.component('mrindex', require('./components/MRindex.vue'));
new Vue({
    el:'#mr',
});
