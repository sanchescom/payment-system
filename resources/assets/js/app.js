require('./bootstrap');

import Vue from 'vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import axios from 'axios';
import BootstrapVue from 'bootstrap-vue';

import App from './App.vue';
import DisplayPayments from './components/DisplayPayments.vue';

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

export const app_url = 'http://payment-system.d:8092/api/';

const VueInputMask = require('vue-inputmask').default;

const routes = [
    {
        name: 'DisplayPayments',
        path: '/',
        component: DisplayPayments
    },
];

Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(BootstrapVue);
Vue.use(VueInputMask);

const router = new VueRouter({
    mode: 'history',
    linkActiveClass: 'open active',
    routes: routes
});

new Vue(Vue.util.extend({
    router
}, App)).$mount('#app');
