import Vue from "vue";
import axios from 'axios'
import VueAxios from 'vue-axios'
 
import DataTable from "./DataTable";
import App from "./App";

import '../styles/app.css';

Vue.prototype.$eventBus = new Vue(); 
Vue.use(VueAxios, axios);
Vue.component('DataTable', DataTable);

new Vue({
  components: { 
    App,
    DataTable
  },
  template: "<App/>"
}).$mount("#app");

