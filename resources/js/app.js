import "./bootstrap";
import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";
import axios from "axios";

// Layouts
import AdminLayout from "./layouts/AdminLayout.vue";
import ClientLayout from "./layouts/ClientLayout.vue";
import LessorLayout from "./layouts/LessorLayout.vue";

// Admin pages
import AdminLogin from "./pages/admin/auth/Login.vue";
import AdminDashboard from "./pages/admin/Dashboard.vue";
import AdminCompanies from "./pages/admin/Companies.vue";
import AdminCustomers from "./pages/admin/Customers.vue";
import AdminBookings from "./pages/admin/Bookings.vue";
import AdminRentals from "./pages/admin/Rentals.vue";

// Client pages
import ClientLogin from "./pages/client/Login.vue";
import ClientRegister from "./pages/client/Register.vue";
import BrowseRentals from "./pages/client/BrowseRentals.vue";
import MyBookings from "./pages/client/MyBookings.vue";

// Lessor pages
import LessorLogin from "./pages/lessor/auth/Login.vue";
import LessorDashboard from "./pages/lessor/Dashboard.vue";
import LessorProperties from "./pages/lessor/MyProperties.vue";
import LessorBookings from "./pages/lessor/Bookings.vue";
import LessorReports from "./pages/lessor/Reports.vue";
import ClientSettings from "./pages/client/Settings.vue";
import AdminSettings from "./pages/admin/Settings.vue";
import LessorSettings from "./pages/lessor/Settings.vue";
import ClientProfile from "./pages/client/Profile.vue";
import AdminProfile from "./pages/admin/Profile.vue";
import LessorProfile from "./pages/lessor/Profile.vue";
const adminRoutes = [
  { path: "/login", component: AdminLogin },
  {
    path: "/",
    component: AdminLayout,
    children: [
      { path: "", redirect: "/dashboard" },
      { path: "dashboard", component: AdminDashboard },
      { path: "companies", component: AdminCompanies },
      { path: "customers", component: AdminCustomers },
      { path: "bookings", component: AdminBookings },
      { path: "rentals", component: AdminRentals },
      { path: "settings", component: AdminSettings },
      { path: "profile", component: AdminProfile },
    ],
  },
];

const lessorRoutes = [
  { path: "/login", component: LessorLogin },
  {
    path: "/",
    component: LessorLayout,
    children: [
      { path: "", redirect: "/dashboard" },
      { path: "dashboard", component: LessorDashboard },
      { path: "my-properties", component: LessorProperties },
      { path: "bookings", component: LessorBookings },
      { path: "reports", component: LessorReports },
      { path: "settings", component: LessorSettings },
      { path: "profile", component: LessorProfile },
    ],
  },
];

const clientRoutes = [
  { path: "/login", component: ClientLogin },
  { path: "/register", component: ClientRegister },
  {
    path: "/",
    component: ClientLayout,
    children: [
      { path: "", redirect: "/browse-rentals" },
      { path: "browse-rentals", component: BrowseRentals, meta: { requiresAuth: true } },
      { path: "my-bookings", component: MyBookings, meta: { requiresAuth: true } },
      { path: "settings", component: ClientSettings, meta: { requiresAuth: true } },
      { path: "profile", component: ClientProfile, meta: { requiresAuth: true } },
    ],
  },
];

const path = window.location.pathname;

const isAdmin = path.startsWith("/admin");
const isLessor = path.startsWith("/lessor");

const base = isAdmin ? "/admin" : isLessor ? "/lessor" : "/";
const routes = isAdmin ? adminRoutes : isLessor ? lessorRoutes : clientRoutes;

const router = createRouter({
  history: createWebHistory(base),
  routes,
});


// ✅ AUTH GUARD (THIS IS THE IMPORTANT PART)
router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAuth) {
    try {
      await axios.get("/api/me");
      next();
    } catch {
      next("/login");
    }
  } else {
    next();
  }
});

createApp(App).use(router).mount("#app");