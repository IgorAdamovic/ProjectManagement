<template>
  <div id="app">
    <b-navbar toggleable="lg" type="light" variant="light">
      <b-navbar-brand href="#">Recognite</b-navbar-brand>
      <span id="userName" v-if="adminIsAuthenticated">
        <i class="fas fa-user-shield"></i>
        {{ username }}
      </span>
      <span
        id="userName"
        v-if="userIsAuthenticated && !adminIsAuthenticated"
        class="text-secondary"
      >
        <i class="fas fa-user"></i>
        {{ username }}
      </span>
      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <div id="nav">
            <router-link to="/" v-if="userIsAuthenticated">Projects</router-link>
            <router-link to="/updates" v-if="userIsAuthenticated">Updates</router-link>
            <router-link to="/hardware" v-if="userIsAuthenticated">Hardware</router-link>
            <router-link to="/clients" v-if="userIsAuthenticated">Clients</router-link>
          </div>
        </b-navbar-nav>

        <!-- Right aligned nav items -->
        <b-navbar-nav class="ml-auto">
          <div id="login">
            <router-link to="/register" v-if="adminIsAuthenticated">Register</router-link>
            <router-link to="/login" v-if="!userIsAuthenticated">Login</router-link>
            <a @click="onLogout" v-else>Logout</a>
          </div>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
    <router-view />
  </div>
</template>

<script>
export default {
  computed: {
    username() {
      return this.$store.getters.accessToken.uidUsers;
    },
    adminIsAuthenticated() {
      return (
        this.$store.getters.accessToken !== null &&
        this.$store.getters.accessToken !== undefined &&
        this.$store.getters.accessToken.role === "Admin"
      );
    },
    userIsAuthenticated() {
      return (
        this.$store.getters.accessToken !== null &&
        this.$store.getters.accessToken !== undefined
      );
    }
  },
  methods: {
    onLogout() {
      this.$router.push("/login");
      this.$store.dispatch("logout");
    }
  }
};
</script>

<style lang="scss">
// Import custom SASS variable overrides, or alternatively
// define your variable overrides here instead
@import "assets/custom-vars.scss";

// Import Bootstrap and BootstrapVue source SCSS files
@import "~bootstrap/scss/bootstrap.scss";
@import "~bootstrap-vue/src/index.scss";

// General style overrides and custom classes

</style>
<style>
body {
  background-color: #f8f9fa !important;
  overflow-x: hidden;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  background: #f8f9fa;
  color: #000000;
}

#nav {
  padding-top: 0;
  margin-top: 0;
  margin-left: 20px;
}

#nav a {
  font-weight: bold;
  color: #000000;
  margin: 2px 15px 0;
  float: left;
  padding: 5px 10px;
}

#login a {
  font-weight: bold;
  color: #000000;
  margin: 2px 0 0 35px;
  padding: 8px 10px;
}

#nav a:hover,
#login a:hover {
  cursor: pointer;
  text-decoration: none;
  opacity: 0.5;
  border-bottom: 2px solid #17a2b8;
}

#nav a.router-link-exact-active,
#login a.router-link-exact-active {
  text-decoration: none;
  border-bottom: 2px solid #17a2b8;
}

span#userName {
  font-weight: bold;
  position: absolute;
  top: 16px;
  right: 250px;
  color: #000000;
}

.dropdown-menu .custom-checkbox {
  margin-left: 10px !important;
}

.dropdown-btn {
  width: 65px;
}

.b-dropdown-text {
  padding: 0.2rem 0.25rem 0 !important;
}

.square {
  float: left;
  width: 1rem;
  height: 1rem;
  margin: 0 5px;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 0.25rem;
}

.table {
  border-collapse: separate;
  border-spacing: 0 2px;
}

.table.b-table.b-table-selectable:not(.b-table-selectable-no-click)
  > tbody
  > tr {
  outline: none !important;
  outline-width: 0 !important;
  box-shadow: none;
  -moz-box-shadow: none;
  -webkit-box-shadow: none;
}

.hidden_outline th:focus {
  outline: none;
}

th {
  vertical-align: middle !important;
}

th.column-width, td.column-width {
  max-width: 400px;
}

.b-table-sticky-header {
  max-height: 360px !important;
}

.table-border-primary th,
.table-border-primary > td {
  color: #212529;
  border-top: 2px solid #007bff !important;
  border-bottom: 2px solid #007bff !important;
}

.table-border-primary:hover {
  background-color: #b8daff !important;
}

.table-border-primary th,
.table-border-primary > td:first-child {
  border-left: 2px solid #007bff !important;
}

.table-border-primary th,
.table-border-primary > td:last-child {
  border-right: 2px solid #007bff !important;
}

.table-border-info th,
.table-border-info > td {
  color: #212529;
  border-top: 2px solid #20c997 !important;
  border-bottom: 2px solid #20c997 !important;  
}

.table-border-info:hover {
  background-color: #bee5eb !important;
}

.table-border-info th,
.table-border-info > td:first-child {
  border-left: 2px solid #20c997 !important;
}

.table-border-info th,
.table-border-info > td:last-child {
  border-right: 2px solid #20c997 !important;
}

.table-border-success th,
.table-border-success > td {
  color: #212529;
  border-top: 2px solid #28a745 !important;
  border-bottom: 2px solid #28a745 !important; 
}

.table-border-success:hover {
  background-color: #b1dfbb !important;
}

.table-border-success th,
.table-border-success > td:first-child {
  border-left: 2px solid #28a745 !important;
}

.table-border-success th,
.table-border-success > td:last-child {
  border-right: 2px solid #28a745 !important;
}

.table-border-warning th,
.table-border-warning > td {
  color: #212529;
  border-top: 2px solid #ffc107 !important;
  border-bottom: 2px solid #ffc107 !important; 
}

.table-border-warning:hover {
  background-color: #ffff66 !important;
}

.table-border-warning th,
.table-border-warning > td:first-child {
  border-left: 2px solid #ffc107 !important;
}

.table-border-warning th,
.table-border-warning > td:last-child {
  border-right: 2px solid #ffc107 !important;
}

.table-border-orange th,
.table-border-orange > td {
  color: #212529;
  border-top: 2px solid #fd7e14 !important;
  border-bottom: 2px solid #fd7e14 !important; 
}

.table-border-orange:hover {
  background-color: #ffd966 !important;
}

.table-border-orange th,
.table-border-orange > td:first-child {
  border-left: 2px solid #fd7e14 !important;
}

.table-border-orange th,
.table-border-orange > td:last-child {
  border-right: 2px solid #fd7e14 !important;
}

.table-border-danger th,
.table-border-danger > td {
  color: #212529;
  border-top: 2px solid #dc3545 !important;
  border-bottom: 2px solid #dc3545 !important; 
}

.table-border-danger:hover {
  background-color: #f5c6cb !important;
}

.table-border-danger th,
.table-border-danger > td:first-child {
  border-left: 2px solid #dc3545 !important;
}

.table-border-danger th,
.table-border-danger > td:last-child {
  border-right: 2px solid #dc3545 !important;
}

.table-dark th,
.table-dark > td {
  color: #212529;
  border-top: 2px solid #62656a !important;
  border-bottom: 2px solid #62656a !important;
}

.table-dark th,
.table-dark > td:first-child {
  border-left: 2px solid #62656a !important;
}

.table-dark th,
.table-dark > td:last-child {
  border-right: 2px solid #62656a !important;
}

.modal-title span {
  font-size: 14px;
}

textarea {
  resize: none !important;
}

img.color-palette {
  position: relative;
  bottom: 2px;
  margin-right: 5px;
}

.subtitle,
.time {
  color: #17a2b8;
  position: relative;
  bottom: 1px;
  margin-left: 5px !important;
}

@media screen and (min-width: 1367px) {
  .search {
    max-width: 300px !important;
  }
}

@media screen and (max-width: 823px) {
  .navbar-toggler:hover,
  .navbar-toggler:focus {
    outline: none;
  }

  #nav {
    margin-top: 20px;
    margin-left: 0;
  }
  #nav a {
    width: 100%;
    text-align: center;
    margin: 10px 0;
    padding: 5px;
    float: left;
    display: block;
    clear: both;
  }
  #nav a.router-link-exact-active,
  #login a.router-link-exact-active {
    text-decoration: none;
  }
  #login a {
    width: 100%;
    text-align: center;
    margin: 10px 0;
    padding: 5px;
    float: left;
    display: block;
    clear: both;
  }
  span#userName {
    position: absolute;
    top: 18px;
    right: 100px;
  }
  .colours-legend {
    display: none;
  }
}

@media screen and (max-width: 414px) {
  .navbar-toggler:hover,
  .navbar-toggler:focus {
    outline: none;
  }
  #nav {
    margin-top: 20px;
    margin-left: 0;
  }
  #nav a {
    margin: 10px 0;
    float: left;
    display: block;
    clear: both;
  }
  #login a {
    margin: 10px 0;
    float: left;
    display: block;
    clear: both;
  }
  span#userName {
    position: absolute;
    top: 18px;
    right: 100px;
  }
}

@media screen and (max-width: 280px) {
  span#userName {
    right: 80px;
  }
}
</style>
