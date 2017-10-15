window.Vue = require('vue');
 Vue.component('mirscreate', require('./components/MIRSCreate.vue'));
 Vue.component('mirspreview', require('./components/MIRSPreview.vue'));
 Vue.component('mirsindex', require('./components/MIRSindex.vue'));
new Vue({
    el:'#mirs',
});
