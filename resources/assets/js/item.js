window.Vue = require('vue');
   Vue.component('additemtolist', require('./components/AddItemToList.vue'));
   Vue.component('itemhistorytable', require('./components/Welcome.vue'));
new Vue({
    el:'#items',
});
