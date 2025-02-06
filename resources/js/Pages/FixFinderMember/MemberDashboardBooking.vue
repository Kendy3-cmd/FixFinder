<template>
  <Head title="FixFinder| Booking" />
  <div class="container-m-d">
    <NavBarMember />

    <!-- SIDEBAR MEMBER DASHBOARD -->
    <div class="sidebar-m-d">
      <div class="sidebar-m-d-list-choose">
        <Link :href="route('MemberDashboardBooking')">
          <div>
            <span class="material-symbols-outlined">bookmark</span>
          </div>
          <p>BOOKING</p>
        </Link>
      </div>
    </div>

    <main class="main-m-d-b">
      <div class="main-m-b-left">
        <h3>BOOKING LIST</h3>
        <div class="main-m-booking-list-left">
          <table>
            <tr>
              <th>SERVICE</th>
              <th>NAME</th>
              <th>ACTION</th>
            </tr>
            <tr v-if="props.employees && props.employees.length === 0">
              <td colspan="3">No employees available</td>
            </tr>
            <tr v-for="employee in props.employees" :key="employee.id">
              <td>
                {{
                  (props.services && Array.isArray(props.services))
                    ? props.services.find(service => service.service_ID === employee.service_ID)?.serviceName 
                    : 'No skill selected yet'
                }}
              </td>
              <td>{{ employee?.firstName }} {{ employee?.lastName }}</td>
              <td>
                <button
                  @click="toggleFormVisibility(employee.employee_ID)"
                  :disabled="employee.bookingStatus === 'PENDING' || isBookingPending"
                >
                  {{ employee.bookingStatus || bookingStatus }}
                </button>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Search form and booking form only visible when selectedEmployeeId is null -->
      <div class="main-m-b-right" v-if="isSearchViewVisible">
        <h3 style="text-align: center;">SEARCH SERVICES</h3>
        <div class="main-m-profile-right-view">
          <div class="view-head">
            <form @submit="handleFormSubmit" class="search-view">
              <input v-model="formData.selectedService" type="text" name="services" placeholder="Search Services">
              <button type="submit" :disabled="isBookingPending">SEARCH</button>
            </form>
          </div>
          <div class="view-body">
            <div class="Welcome">HELLO</div>
            <div class="Welcome-User">{{ currentUser.lastName }}</div>
            <div class="Welcome" style="font-size: 30px; margin-top: 10px;">BOOK NOW</div>
          </div>
        </div>
      </div>

      <!-- Add key here to force re-render when employee selection changes -->
      <MemberDashboardViewProfile class="main-m-b-right"
        v-if="selectedEmployeeId !== null && Array.isArray(props.employees)"
        :key="selectedEmployeeId"
        :employee="props.employees.find(employee => employee.employee_ID === selectedEmployeeId) || {}"
        :services="props.services"
        @update-booking-status="updateBookingStatus"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MemberDashboardViewProfile from '../Components/MemberDashboardViewProfile.vue';
import NavBarMember from '../Components/NavBarMember.vue';

const props = defineProps({
  members: Array,
  employees: {
    type: Array,
    default: () => []  // Ensure it defaults to an empty array if not passed
  },
  services: Array,
  auth: Object
});

// Initialize profileVisibility
const profileVisibility = reactive(new Map());
props.employees.forEach(employee => {
  profileVisibility.set(employee.employee_ID, true); // Default to true
});

const selectedEmployeeId = ref(null);
const isSearchViewVisible = computed(() => selectedEmployeeId.value === null);

const bookingStatus = ref('BOOK NOW');
const isBookingPending = ref(false);

// Initialize formData to store form data
const formData = reactive({
  selectedService: '',  // For search service form
  bookingDate: '',      // For the booking date, if needed
  employeeId: null      // Employee ID to whom the booking is related
});

const toggleFormVisibility = (employeeId) => {
  selectedEmployeeId.value = selectedEmployeeId.value === employeeId ? null : employeeId;
};

const handleFormSubmit = (e) => {
  e.preventDefault();

  // Change the booking status to "PENDING"
  bookingStatus.value = 'PENDING';
  isBookingPending.value = true; // Disable the button

  // Simulate form submission or API call
  setTimeout(() => {
    console.log('Booking processed:', formData);
    // After processing, reset the form status
    bookingStatus.value = 'BOOK NOW';
    isBookingPending.value = false;
  }, 2000);

  localStorage.setItem(`bookingStatus_${props.employees.employee_ID}`, 'PENDING');
};

const updateBookingStatus = (employeeId, status) => {
  const employee = props.employees.find(emp => emp.employee_ID === employeeId);
  if (employee) {
    employee.bookingStatus = status; // Update only the specific employee's status
  }
};

props.employees.forEach(employee => {
  const savedStatus = localStorage.getItem(`bookingStatus_${employee.employee_ID}`);
  if (savedStatus) {
    employee.bookingStatus = savedStatus;
  }
});

const currentUser = computed(() => usePage().props.auth.user || {});
</script>
