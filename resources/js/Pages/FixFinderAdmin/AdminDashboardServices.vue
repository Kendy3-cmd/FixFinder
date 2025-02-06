<script setup>
import { useForm } from "@inertiajs/vue3";
import NavBarAdmin from "../Components/NavBarAdmin.vue";
import { ref } from "vue";

defineProps({
  services: Array,
});

const form = useForm({
  serviceName: null,
});

const submit =() => {
  form.post(route("serviceAdd"), {
    onError: () => form.reset("serviceName"),
  });
};

const isaddServiceVisible = ref(false);

const addServicebtn = () => {
  isaddServiceVisible.value = !isaddServiceVisible.value;
};

// Delete service method
const deleteService = (service_ID) => {
  if (confirm("Are you sure you want to delete this service?")) {
    form.delete(route("services.destroy", service_ID), {
      preserveScroll: true, // To prevent the page from scrolling to the top
      onSuccess: () => {
        alert("Service deleted successfully.");
      },
    });
  }
};

</script>

<template>
  <body>
    <div class="container-m-d" id="main">
      <!-- NAV ADMIN DASHBOARD -->
      <NavBarAdmin />

      <!-- SIDEBAR ADMIN DASHBOARD -->
      <div class="sidebar-m-d">
        <div class="sidebar-m-d-list-choose">
          <Link :href="route('AdminDashboardServices')">
            <div>
              <span class="material-symbols-outlined">design_services</span>
            </div>
            <p>SERVICES</p>
          </Link>
        </div>
        <div class="sidebar-m-d-list">
          <Link :href="route('AdminDashboardManageUsers')">
            <div>
              <span class="material-symbols-outlined">manage_accounts</span>
            </div>
            <p>MANAGE USERS</p>
          </Link>
        </div>
      </div>

      <!-- MAIN ADMIN DASHBOARD -->
      <main class="main-m-d">
        <div class="main-m-d-head">
          <h3>SERVICES</h3>
          <div class="main-m-d-searchbar">
            <span class="material-symbols-outlined">search</span>
            <form action="" class="main-m-d-searchform">
              <input type="text" name="searchServices" placeholder="Search">
            </form>
            <span class="material-symbols-outlined">tune</span>
          </div>
          <div class="add-service">
            <button @click="addServicebtn">
              ADD SERVICE
            </button>
          </div>
          <div class="addService" v-if="isaddServiceVisible">
            <form @submit.prevent="submit">
              <input type="text" name="serviceName" v-model="form.serviceName" placeholder="ADD SERVICE">
              <button>Submit</button>
            </form>
          </div>
        </div>

        <div class="main-m-d-body" id="tableGoRemove">
          <table>
            <thead>
              <tr>
                <th>SERVICES</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in services" :key="index">
                <td>{{ item.serviceName }}</td>
                <td>
                  <button class="activate-btn" @click="deleteService(item.service_ID)">REMOVE</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </body>
</template>

<style scoped>
.activate-btn{
  margin-top: 10px;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
</style>