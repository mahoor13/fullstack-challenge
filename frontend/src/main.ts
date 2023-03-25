import { createApp } from "vue";
import { createPinia } from "pinia";
import { Quasar, Notify } from 'quasar'

import '@quasar/extras/roboto-font/roboto-font.css'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'

import App from "./App.vue";
import router from "./router";

import "./assets/main.css";

const app = createApp(App);

app.use(Quasar, {
    plugins: {
        Notify
    },
})

app.use(createPinia());
app.use(router);

app.mount("#app");
