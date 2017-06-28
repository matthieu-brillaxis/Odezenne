// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import moment from 'moment';
import axios from 'axios';
import VueAnalytics from 'vue-analytics';

import App from './App';
import router from './router';
import store from './store';
import config from './config';

Vue.config.productionTip = false;

// Set moment locale
moment.locale('fr');

// Setting Googe Analytics
axios.get(`${config.apiEndpoint}/tools/analytics`).then((response) => {
  Vue.use(VueAnalytics, {
    id: response.data, // Google Analytics Key
    router, // Using the router
  });
});

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App },
});
