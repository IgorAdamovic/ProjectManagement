<template>
  <div class="login">
    <b-container fluid>
      <b-row class="mt-4">
        <b-col lg="12" class="text-center">
          <h3>Login</h3>
        </b-col>
      </b-row>
      <b-alert variant="danger" :show="errorMsg" dismissible fade>{{ errorMessage }}</b-alert>
      <b-alert variant="success" :show="successMsg" dismissible fade>{{ successMessage }}</b-alert>
      <b-row class="mt-4 mb-4">
        <b-col lg="3" class="mx-auto">
          <b-form @submit.prevent="loginUser">
            <b-form-group label="Username / Email" label-for="email-input">
              <b-form-input id="email-input" v-model="user.mailuid" type="text" required></b-form-input>
            </b-form-group>
            <b-form-group label="Password" label-for="password-input">
              <b-form-input id="password-input" v-model="user.password" type="password" required></b-form-input>
            </b-form-group>
            <b-button type="submit" variant="info">Submit</b-button>
          </b-form>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import API_BASE from "../api_base";
export default {
  name: "Login",
  data() {
    return {
      errorMsg: false,
      successMsg: false,
      errorMessage: "",
      successMessage: "",
      user: {
        mailuid: "",
        password: "",
        role: ""
      }
    };
  },
  methods: {
    loginUser() {
      this.clearMsg();
      var formData = this.toFormData(this.user);
      axios.post(`${API_BASE}/login.php`, formData).then(response => {
        this.user = {
          mailuid: "",
          password: ""
        };
        if (response.data.error) {
          this.errorMsg = true;
          this.errorMessage = response.data.message;
        } else {
          this.onSignin(
            response.data.idUsers,
            response.data.uidUsers,
            response.data.pwdUsers,
            response.data.role
          );
          this.$router.push("/");
        }
      });
    },
    onSignin(idUsers, uidUsers, pwdUsers, role) {
      this.$store.dispatch("signUserIn", {
        idUsers: idUsers,
        uidUsers: uidUsers,
        pwdUsers: pwdUsers,
        role: role
      });
    },
    toFormData(obj) {
      var formData = new FormData();
      for (var i in obj) {
        formData.append(i, obj[i]);
      }
      return formData;
    },
    clearMsg() {
      this.errorMsg = false;
      this.successMsg = false;
      this.errorMessage = "";
      this.successMessage = "";
    }
  }
};
</script>