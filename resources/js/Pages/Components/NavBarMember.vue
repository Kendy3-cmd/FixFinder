<template>
    <nav class="nav-m-d">
      <div class="nav-left-m-d">
        <div class="logo-nav"></div>
      </div>
      <div class="nav-right-m-d">
        <h5 style="margin-right: 10px; font-size: 20px;">{{ currentUser.firstName || 'Guest' }} {{ currentUser.lastName }}</h5>
        <div class="menu-list" v-show="isMenuListVisible">
          <Link :href="route('logout')" method="POST" as="button">LOG OUT</Link>
          <Link :href="route('MemberDashboardProfile')">PROFILE</Link>
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
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
// MENU btn
const isMenuListVisible = ref(false);

const menuButton = () => {
  if (!isMenuListVisible.value) {
    isMenuListVisible.value = true;
  } else if(isMenuListVisible.value){
    isMenuListVisible.value = false;
  }
}

const currentUser = computed(() => usePage().props.auth.user || {});
</script>