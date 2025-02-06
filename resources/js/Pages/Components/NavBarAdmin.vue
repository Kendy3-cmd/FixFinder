<template>
    <nav class="nav-m-d">
      <div class="nav-left-m-d">
        <div class="logo-nav"></div>
      </div>
      <div class="nav-right-m-d">
        <div class="menu-list" v-show="isMenuListVisible">
          <Link :href="route('homepage')">LOG OUT</Link>
          <!-- <Link :href="route('admindashboardProfile')">PROFILE</Link> -->
        </div>
        <button class="nav-menu-m-d" v-on:click="menuButton">
          <div>
            <span class="material-symbols-outlined">menu</span>
          </div>
        </button>
      </div>
    </nav>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
// MENU btn
const isMenuListVisible = ref(false);

const menuButton = () => {
  if (!isMenuListVisible.value) {
    isMenuListVisible.value = true;
  } else if(isMenuListVisible.value){
    isMenuListVisible.value = false;
  }
}

</script>