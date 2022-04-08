import './assets/css/custom.css'
import './bootstrap'
import Vue from 'vue'
import App from './App.vue'

// VUEX - https://vuex.vuejs.org/
import store from './store'

// VUE-ROUTER - https://router.vuejs.org/
import router from './router'

// PLUGINS
import vuetify from './plugins/vuetify'
import i18n from './plugins/vue-i18n'
import './plugins/vue-google-maps'
import './plugins/vue-shortkey'
import './plugins/vue-head'
import './plugins/vue-gtag'
import './plugins/apexcharts'
import './plugins/echarts'
import './plugins/animate'
import './plugins/clipboard'
import './plugins/moment'
import './plugins/lodash'

// FILTERS
import './filters/capitalize'
import './filters/lowercase'
import './filters/uppercase'
import './filters/formatCurrency'
import './filters/formatDate'

// STYLES
// Main Theme SCSS
import '../sass/theme.scss'

// Animation library - https://animate.style/
import 'animate.css/animate.min.css'

// Set this to false to prevent the production tip on Vue startup.
Vue.config.productionTip = false

import {ColorPicker, ColorPanel} from 'one-colorpicker';
import CKEditor from '@ckeditor/ckeditor5-vue2';
Vue.use(ColorPanel)
Vue.use(ColorPicker)
Vue.use(CKEditor)

/*
|---------------------------------------------------------------------
| Main Vue Instance
|---------------------------------------------------------------------
|
| Render the vue application on the <div id="app"></div> in index.html
|
| https://vuejs.org/v2/guide/instance.html
|
*/

Vue.prototype.hasRole = (r) => {
    return localStorage.authUserRoles && localStorage.authUserRoles.includes(r);
};
Vue.prototype.userCan = (p) => {
    return typeof p == 'undefined' || !p
        || (localStorage.authUserPermissions && localStorage.authUserPermissions.split(',').some(r => p.split('|').indexOf(r) >= 0));
};
Vue.prototype.reloadPage = () => router.go(0);

axios.interceptors.response.use(
    response => response,
    error => {
        const {status} = error.response;
        if (status === 401) {
            store.state.app.isLoggedIn = false;
            router.push({ name: 'auth-signin' });
        }
        return Promise.reject(error);
    });

export default new Vue({
    i18n,
    vuetify,
    router,
    store,
    render: (h) => h(App)
}).$mount('#app')
