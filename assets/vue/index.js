import Vue from "vue";
import axios from 'axios'
import VueAxios from 'vue-axios'
import moment from 'moment';

 
import DataTable from "./DataTable";
import App from "./App";

import '../styles/app.css';

Vue.prototype.$eventBus = new Vue(); 
Vue.use(VueAxios, axios);
Vue.component('DataTable', DataTable);

Vue.filter('asDate', function(value) {
    if (value) {
        return moment(String(value)).format('DD.MM.YYYY')
    }
});

new Vue({
  components: { 
    App,
    DataTable
  },
  template: "<App/>",
}).$mount("#app");

