import { createRouter, createWebHistory } from "vue-router";
import App from "../App.vue"; // or use a Home.vue if you have it

const routes = [
  { path: "/", name: "Home", component: App },
  // add more routes if needed
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
