window.Vue = require('vue');
 Vue.component('mirscreate', require('./components/MIRS/MIRSCreate.vue'));
 Vue.component('mirspreview', require('./components/MIRS/MIRSPreview.vue'));
 Vue.component('mirsindex', require('./components/MIRS/MIRSindex.vue'));
new Vue({
    el:'#mirs',
});
