window.Vue = require('vue');
   Vue.component('additemtolist', require('./components/Item/AddItemToList.vue'));
   Vue.component('itemhistorytable', require('./components/Item/Welcome.vue'));
new Vue({
    el:'#items',
});
