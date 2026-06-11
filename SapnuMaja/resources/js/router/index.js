import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Home.vue';
import Catalog from '../components/Catalog.vue';
import PropertyDetail from '../components/PropertyDetail.vue';
import Favorites from '../components/Favorites.vue'

const routes = [
    { path: '/', component: Home },
    { path: '/catalog', component: Catalog },
    { path: '/property/:id', component: PropertyDetail},
    {path: '/favorites', component: Favorites}
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;