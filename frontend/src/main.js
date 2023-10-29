import { createApp } from 'vue';
import App from './App.vue';
import router from './router'; 
import VueGoogleMaps from '@fawmi/vue-google-maps';
/*import bootstrap css*/
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.js';

const app = createApp(App);

/* import router */
app.use(router);

/* use vue gogole maps */
app.use(VueGoogleMaps, {
  load: {
    key: import.meta.env.VITE_APP_GOOGLE_MAP_API_KEY, // Access the environment variable
    libraries: "places"
  },
});

app.mount('#app');
