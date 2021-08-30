import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios';
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
import Toastr from 'vue-toastr';

Vue.router = router

Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons); //Иконки https://bootstrap-vue.org/docs/icons

Vue.use(Toastr, {
  defaultTimeout: 15000,
  defaultProgressBar: false,
  defaultProgressBarValue: 0,
  defaultPosition : "toast-bottom-full-width",
  defaultCloseOnHover: false,
  defaultClassNames: ["animated", "zoomInUp"]
});

Vue.config.productionTip = false

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = process.env.VUE_APP_API_URL;
Vue.prototype.$http = axios;

new Vue({
  router,
  store,
  created() {
    axios.interceptors.response.use(
      response => response,
      error => {
        return Promise.reject(error);
      })
  },
  render: h => h(App)
}).$mount('#app')
