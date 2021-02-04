<template>
  <div class="register">
    <b-container fluid>
      <b-row class="mt-4">
        <b-col lg="12" class="text-center">
          <h3>Register</h3>
        </b-col>
      </b-row>
      <b-alert variant="danger" :show="errorMsg" dismissible fade>{{ errorMessage }}</b-alert>
      <b-alert variant="success" :show="successMsg" dismissible fade>{{ successMessage }}</b-alert>
      <b-row class="mt-4 mb-5">
        <b-col lg="3" class="mx-auto">
          <b-form @submit.prevent="addUser">
            <b-form-group label="Username" label-for="username-input">
              <b-form-input id="username-input" v-model="newUser.username" type="text" required></b-form-input>
            </b-form-group>
            <b-form-group label="Email address" label-for="email-input">
              <b-form-input id="email-input" v-model="newUser.email" type="email" required></b-form-input>
            </b-form-group>
            <b-form-group label="Password" label-for="password-input">
              <b-form-input id="password-input" v-model="newUser.password" type="password" required></b-form-input>
            </b-form-group>
            <b-form-group label="Confirm Password" label-for="password-input-2">
              <b-form-input
                id="password-input-2"
                v-model="newUser.password2"
                type="password"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group label="Role" label-for="role-input" invalid-feedback="Role is required">
              <b-form-select id="role-input" v-model="newUser.role" :options="roleOptions" required></b-form-select>
            </b-form-group>
            <b-button type="submit" variant="primary">Submit</b-button>
          </b-form>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import API_BASE from "../api_base";
export default {
  name: "Register",
  data() {
    return {
      errorMsg: false,
      successMsg: false,
      errorMessage: "",
      successMessage: "",
      newUser: {
        username: "",
        email: "",
        password: "",
        password2: "",
        role: null
      },
      roleOptions: [
        { value: null, text: "Please select role" },
        { value: "Admin", text: "Admin" },
        { value: "User", text: "User" }
      ]
    };
  },
  methods: {
    addUser() {
      this.clearMsg();
      var formData = this.toFormData(this.newUser);
      axios
        .post(`${API_BASE}/register.php?action=create`, formData)
        .then(response => {
          if (response.data.error) {
            if (response.data.message === "Confirm password does not match") {
              document.getElementById("password-input-2").focus();
            }
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.newUser = {
              username: "",
              email: "",
              password: "",
              password2: "",
              role: null
            };
            this.successMsg = true;
            this.successMessage = response.data.message;
          }
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