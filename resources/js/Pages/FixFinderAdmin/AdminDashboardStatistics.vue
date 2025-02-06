<template>
  <div class="container-m-d" id="main">
    <NavBarAdmin />

    <!-- Main Content -->
    <main class="main-admin-d-b">
      <div class="head-stats">
        <h2>STATISTICS OVERVIEW</h2>
      </div>
      <div class="top-stats">
        <table>
          <tr>
            <th>TOTAL MEMBERS</th>
            <th>TOTAL EMPLOYEES</th>
            <th>TOTAL SERVICES</th>
            <th>TOTAL BOOKINGS</th>
          </tr>
          <tr>
            <td>{{ totalMembers }}</td>
            <td>{{ totalEmployees }}</td>
            <td>{{ totalServices }}</td>
            <td>{{ totalBookings }}</td>
          </tr>
        </table>
      </div>
    </main>
  </div>
</template>

<script setup>
import NavBarAdmin from "../Components/NavBarAdmin.vue";

// Default to empty arrays if null or undefined
defineProps({
  bookings: { type: Array, default: () => [] },
  members: { type: Array, default: () => [] },
  employees: { type: Array, default: () => [] },
  services: { type: Array, default: () => [] },
});

// Calculate counts safely
const totalBookings = bookings?.length || 0;
const totalMembers = members?.length || 0;
const totalEmployees = employees?.length || 0;
const totalServices = services?.length || 0;

console.log("Bookings:", totalBookings);
console.log("Members:", totalMembers);
console.log("Employees:", totalEmployees);
console.log("Services:", totalServices);
</script>
