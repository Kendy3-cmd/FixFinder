<script setup>
import LoginRegister from './Components/LoginRegister.vue';

</script>

<template>
  <Head title=" | Home" />
  <div class="container" id="app">
    <nav>
      <div class="left-nav">
        <div class="logo-nav"></div>
      </div>
      <div class="right-nav">
        <div class="nav-list">
          CONTACT US
        </div>
        <div class="nav-list">
          ABOUT US
        </div>
      </div>
    </nav>
      <section class="signuplogin-main">
        <div class="signuplogin">
          <h1>FIND LOCAL SERVICES NEAR YOU</h1>
          <div class="login-signup">
            <LoginRegister />
          </div>
        </div>
      </section>
      <section class="aboutus-main">
        <div class="aboutus">
          <h1>ABOUT US</h1>
          <p>
            FIXFINDER is a Local Service Finder website designed to connect users with local professionals and service providers such as plumbers, electricians, cleaners, landscapers, or Any type of services. The platform would act as a digital marketplace, offering a convenient way for users to quickly find and book trusted service providers in their area.
          </p>
        </div>
      </section>
  </div>
</template>