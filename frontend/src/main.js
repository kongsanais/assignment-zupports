import { createApp } from 'vue';
import App from './App.vue';
import router from './router'; 
import VueGoogleMaps from '@fawmi/vue-google-maps';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.js';



const app = createApp(App);


app.use(VueGoogleMaps, {
    load: {
      key: '',
      libraries: "places"
    },
})


app.use(router);
app.mount('#app');
