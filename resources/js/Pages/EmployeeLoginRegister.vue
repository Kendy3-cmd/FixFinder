<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "./Components/TextInput.vue";
import { ref } from 'vue';

  const isloginRegisterMove = ref(true);
  const isloginRegisterMoveRight = ref(false);

  const loginRegisterClick = () => {
    if (isloginRegisterMove.value) {
      isloginRegisterMove.value = false;
      isloginRegisterMoveRight.value = true;
    }
  };
  const loginRegisterClickLeft = () => {
    if (isloginRegisterMoveRight.value) {
      isloginRegisterMove.value = true;
      isloginRegisterMoveRight.value = false;
    }
  };

const form = useForm({
  firstName: null,
  lastName: null,
  email: null,
  password: null,
  password_confirmation: null,
});

const formLogin = useForm({
  email: null,
  password: null,
});

const submitLogin =() => {
  formLogin.post(route("/EmployeeLogin"), {
    onError: () => formLogin.reset("password"),
  });
};

const submitRegister = () => {
  form.post(route('/EmployeeRegister'), {
    onError: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <Head title="Employee Register & Login" />
  <body>
    <div class="container-mls">
      <div class="logo" v-show="isloginRegisterMove">
        <h3>WELCOME BACK!</h3>
        <div class="biglogo"></div>
      </div>
      <div class="logoRight" v-show="isloginRegisterMoveRight">
        <h3>NICE TO MEET YOU!</h3>
        <div class="biglogo"></div>
      </div>

      <div class="register-mls">
        <Link :href="route('homepage')" class="back-homepage">
          BACK
        </Link>
        <div class="registerlogin-mls-head">
          <h3>CREATE AN ACCOUNT</h3>
        </div>
        <div class="registerlogin-mls-body">
          <!-- REGISTER -->
          <form @submit.prevent="submitRegister" class="registerlogin-mls-form">
            <TextInput name="First Name:" type="text" v-model="form.firstName" placeholder="Enter First Name" :message="form.errors.firstName" />
            <TextInput name="Last Name:" type="text" v-model="form.lastName" placeholder="Enter Last Name" :message="form.errors.lastName" />
            <TextInput name="Email:" type="email" v-model="form.email" placeholder="Enter Email" :message="form.errors.email" />
            <TextInput name="Password:" type="password" v-model="form.password" placeholder="Enter Password" :message="form.errors.password" />
            <TextInput name="Confirmed Password:" type="password" v-model="form.password_confirmation" placeholder="Confirm Password" />
            <button class="registerLogin-btn" :disabled="form.processing">REGISTER NOW</button>
          </form>
        </div>
        <div class="registerlogin-mls-footer" v-on:click="loginRegisterClickLeft">
          <a>Already have an account? login here!</a>
        </div>
      </div>

      <div class="login-mls">
        <Link :href="route('homepage')" class="back-homepage">
          BACK
        </Link>
        <div class="registerlogin-mls-head">
          <h3>EMPLOYEE LOGIN</h3>
        </div>
        <div class="registerlogin-mls-body">
          <!-- LOGIN -->
          <form @submit.prevent="submitLogin" class="registerlogin-mls-form">
            <TextInput name="Email:" type="email" v-model="formLogin.email" placeholder="Email" :message="formLogin.errors.email" />
            <TextInput name="Password:" type="password" v-model="formLogin.password" placeholder="Password" :message="formLogin.errors.password" />
            <button class="registerLogin-btn" :disabled="formLogin.processing">LOGIN</button>
          </form>
        </div>
        <div class="registerlogin-mls-footer">
          <a v-on:click="loginRegisterClick">New here? create a new account!</a>
        </div>
      </div>

    </div>
  </body>
</template>