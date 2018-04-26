window.Vue = require('vue');
   Vue.component('additemtolist', require('./components/Item/AddItemToList.vue'));
   Vue.component('itemhistorytable', require('./components/Item/Welcome.vue'));
   Vue.component('sidestats', require('./components/Item/SideStats.vue'));
   Vue.component('recentfiles', require('./components/Item/RecentFiles.vue'));
new Vue({
    el:'#items',
});
