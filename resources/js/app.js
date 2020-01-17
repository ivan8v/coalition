import Index from "./components/tasks/Index";
import Form from "./components/tasks/Form";

require('./bootstrap');

window.Vue = require('vue');

import VuePromiseBtn from 'vue-promise-btn'
import Draggable from 'vuedraggable';

Vue.component('draggable', Draggable);
Vue.use(VuePromiseBtn);

Vue.component(Index.name, Index);
Vue.component(Form.name, Form);


const app = new Vue({
    el: '#app'
});

