require('./bootstrap');

import Vue from 'vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import axios from 'axios';

import App from './App.vue';
import DisplayPayments from './components/DisplayPayments.vue';

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
Vue.use(VueInputMask);

const router = new VueRouter({
    mode: 'history',
    linkActiveClass: 'open active',
    routes: routes
});

new Vue(Vue.util.extend({
    router
}, App)).$mount('#app');
