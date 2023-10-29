import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/Home',
            name: 'Home',
            component: Home,
        },
        {
            path: '/',
            component: Home,
        },
    ]
});

export default router;
