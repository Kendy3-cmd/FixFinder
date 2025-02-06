<template>
  <body>
    <div class="container-m-d" id="main">
      <!-- NAV EMPLOYEE DASHBOARD -->
      <nav class="nav-m-d">
        <div class="nav-left-m-d">
          <div class="logo-nav"></div>
        </div>
        <div class="nav-right-m-d">
          <div class="menu-list" v-show="isMenuListVisible">
            <Link :href="route('EmployeeDashboardManage')">BACK</Link>
          </div>
          <button class="nav-menu-m-d" @click="menuButton">
            <div>
              <span class="material-symbols-outlined">menu</span>
            </div>
          </button>
        </div>
      </nav>

      <!-- MAIN PROFILE DASHBOARD -->
      <main class="member-profile-container">
        <div class="profile-content">
          <div class="m-profile-sidebar">
            <div class="m-profile-sidebar-list">
              <button @click="profileBtnClick">PROFILE</button>
            </div>
            <div class="m-profile-sidebar-list">
              <button @click="securityBtnClick">SECURITY</button>
            </div>
            <div class="m-profile-sidebar-list">
              <button @click="availabilityBtnClick">AVAILABILITY</button>
            </div>
          </div>
          <div class="m-main-profile">
            <!-- PROFILE FORM -->
            <div v-show="isProfileEditVisible">
              <h3>PROFILE</h3>
              <form @submit.prevent="updateEmployeeProfile" class="e-profile-form">
                <div class="e-profile-form-left">
                  <label for="firstName">FIRST NAME</label>
                  <input type="text" name="firstName" placeholder="First Name" v-model="form.firstName">
                  <label for="lastName">LAST NAME</label>
                  <input type="text" name="lastName" placeholder="Last Name" v-model="form.lastName">
                  <label for="address">ADDRESS</label>
                  <input type="text" name="address" placeholder="Address" v-model="form.address">
                  <label for="phone">CONTACT NUMBER</label>
                  <input type="text" name="phone" placeholder="Contact Number" v-model="form.phone">
                  <label for="dateOfBirth">DATE OF BIRTH</label>
                  <input type="date" name="dateOfBirth" placeholder="Date Of Birth" v-model="form.dateOfBirth">
                  <label for="coverageArea">COVERAGE AREA</label>
                  <input type="text" name="coverageArea" placeholder="Coverage Area" v-model="form.coverageArea">
                </div>
                <div class="e-profile-form-right">
                  <label for="description">DESCRIPTION</label>
                  <textarea name="description" v-model="form.description"></textarea>

                  <label for="serviceName">WHAT SKILL DO YOU HAVE?</label>
                  <select name="serviceName" size="5" v-model="form.service_ID">
                    <option disabled selected>Select a skill</option>
                    <option v-for="(item, index) in services" :key="index" :value="item.service_ID">
                      {{ item.serviceName }}
                    </option>
                  </select>
                </div>
                <div class="e-profile-form-save-btn">
                  <button :disabled="form.processing">SAVE</button>
                </div>
              </form>
            </div>

            <!-- SECURITY FORM -->
            <div v-show="isSecurityEditVisible">
              <h3>SECURITY</h3>
              <form @submit.prevent="updatePassword" class="m-profile-form">
                <label for="current_password">CURRENT PASSWORD</label>
                <input type="password" name="current_password" placeholder="Current Password" v-model="passwordForm.current_password">
                <label for="new_password">NEW PASSWORD</label>
                <input type="password" name="new_password" placeholder="New Password" v-model="passwordForm.new_password">
                <label for="new_password_confirmation">CONFIRM NEW PASSWORD</label>
                <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" v-model="passwordForm.new_password_confirmation">
                <button type="submit" :disabled="passwordForm.processing">CHANGE PASSWORD</button>
              </form>
            </div>

            <!-- AVAILABILITY FORM -->
            <div v-show="isAvailabilityEditVisible">
              <h3>AVAILABILITY</h3>
              <form @submit.prevent="updateAvailability" class="availability-form">
                <table>
                  <thead>
                    <tr>
                      <th>Day</th>
                      <th>Start Time</th>
                      <th>End Time</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(times, day) in availabilityForm.availability" :key="day">
                      <td>{{ day }}</td>
                      <td>
                        <input type="time" v-model="times.start" :disabled="times.start === ''">
                      </td>
                      <td>
                        <input type="time" v-model="times.end" :disabled="times.start === ''">
                      </td>
                      <td>
                        <button type="button" @click="toggleAvailability(day)">
                          {{ availabilityForm.availability[day].start === '' ? 'Mark as Open' : 'Mark as Closed' }}
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <button type="submit" :disabled="availabilityForm.processing">SAVE AVAILABILITY</button>
              </form>
            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, ref } from 'vue';
import { onMounted } from 'vue';

  const isMenuListVisible = ref(false);
  const isProfileEditVisible = ref(true);
  const isSecurityEditVisible = ref(false);
  const isAvailabilityEditVisible = ref(false);

  const profileBtnClick = () => {
    isProfileEditVisible.value = true;
    isSecurityEditVisible.value = false;
    isAvailabilityEditVisible.value = false;
  };

  const securityBtnClick = () => {
    isProfileEditVisible.value = false;
    isSecurityEditVisible.value = true;
    isAvailabilityEditVisible.value = false;
  };

  const availabilityBtnClick = () => {
    isProfileEditVisible.value = false;
    isSecurityEditVisible.value = false;
    isAvailabilityEditVisible.value = true;
    console.log(availabilityForm.availability);
  };

  const menuButton = () => {
    isMenuListVisible.value = !isMenuListVisible.value;
  };

  const props = defineProps({
    services: {
      type: Array,
      default: () => [], // Default empty array if no services are passed
    },
    employees: {
      type: Object,
      default: () => ({}), // Default empty object if no employee is passed
    },
    availability: {
    type: Object,
      default: () => ({
        Monday: { start: '09:00', end: '17:00' },
        Tuesday: { start: '09:00', end: '17:00' },
        Wednesday: { start: '09:00', end: '17:00' },
        Thursday: { start: '09:00', end: '17:00' },
        Friday: { start: '09:00', end: '17:00' },
        Saturday: { start: '', end: '' },
        Sunday: { start: '', end: '' },
      }),
    },
  });

  const form = useForm({
      firstName: props.employees.firstName || '',
      lastName: props.employees.lastName || '',
      address: props.employees.address || '',
      phone: props.employees.phone || '',
      dateOfBirth: props.employees.dateOfBirth || '',
      coverageArea: props.employees.coverageArea || '',
      description: props.employees.description || '',
      service_ID: props.employees.service_ID || null, // Use service_ID
  });

  const passwordForm = useForm({
      current_password: '',
      new_password: '',
      new_password_confirmation: '',
  });

  const availabilityForm = reactive({
    availability: props.availability || {
      Monday: { start: '09:00', end: '17:00' },
      Tuesday: { start: '09:00', end: '17:00' },
      Wednesday: { start: '09:00', end: '17:00' },
      Thursday: { start: '09:00', end: '17:00' },
      Friday: { start: '09:00', end: '17:00' },
      Saturday: { start: '', end: '' },
      Sunday: { start: '', end: '' },
    },
    processing: false,
  });

  const toggleAvailability = (day) => {
    if (availabilityForm.availability[day].start === '') {
      // If currently closed, mark as open with default times
      availabilityForm.availability[day].start = '09:00'; // Default start time
      availabilityForm.availability[day].end = '17:00'; // Default end time
    } else {
      // If currently open, mark as closed
      availabilityForm.availability[day].start = '';
      availabilityForm.availability[day].end = '';
    }
  };

  onMounted(() => {
  console.log('Employee data:', props.employees); // Verify employee data

  // Check if employee_ID is passed correctly
  if (props.employees && props.employees.employee_ID) {
    axios.get(route('employee.getAvailability', { employee_ID: props.employees.employee_ID }))
  .then(response => {
    console.log('Response data:', response.data);  // Log the structure to confirm it’s the correct format
    
    // Directly assign the availability data
    if (typeof response.data === 'object' && response.data !== null) {
      availabilityForm.availability = response.data; // Assign it directly without needing reduce
      console.log('Updated availabilityForm:', availabilityForm.availability); // Log for verification
    } else {
      console.error("Unexpected data format:", response.data);
    }
  })
  .catch(error => {
    console.error("Error fetching availability:", error);
  });
  } else {
    console.error("Employee ID is missing");
  }
});



  // Modify the updateAvailability function to convert start and end times
  const updateAvailability = () => {
  availabilityForm.processing = true;

  const formattedAvailability = {};
  Object.keys(availabilityForm.availability).forEach((day) => {
    const times = availabilityForm.availability[day];
    formattedAvailability[day] = {
      start: times.start || null,
      end: times.end || null,
    };
  });

  router.post(
    route('employee.updateAvailability', props.employees.employee_ID),
    { availability: formattedAvailability },
    {
      onSuccess: () => {
        alert('Availability updated successfully!');
        
        // After successful update, fetch availability again
        axios.get(route('employee.getAvailability', { employee_ID: props.employees.employee_ID }))
          .then(response => {
            console.log('Availability fetched:', response.data); // Verify structure

            // Remove the incorrect use of .reduce() and directly assign the response.data
            if (typeof response.data === 'object' && response.data !== null) {
              availabilityForm.availability = response.data; // Directly assign to availabilityForm
              console.log('Updated availabilityForm:', availabilityForm.availability); // Log for verification
            } else {
              console.error("Unexpected data format:", response.data);
            }
          })
          .catch(error => {
            console.error("Error fetching availability:", error);
          });
      },
      onError: (errors) => {
        console.error(errors);
      },
      onFinish: () => {
        availabilityForm.processing = false;
      },
    }
  );
};


const updateEmployeeProfile = () => {
  form.put(route('employee.update', props.employees.employee_ID), {
    onSuccess: () => {
      alert('Profile updated successfully!');
    },
    onError: (errors) => {
      console.error(errors);
    },
  });
};

const updatePassword = () => {
    passwordForm.put(route('employee.password.update', props.employees.employee_ID), {
        onSuccess: () => {
            alert('Password updated successfully!');
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};
</script>