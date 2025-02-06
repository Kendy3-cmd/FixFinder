<script setup>
import NavBarAdmin from '../Components/NavBarAdmin.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, computed } from 'vue';

const isMenuListVisible = ref(false);
const isProfileEditVisible = ref(false);  // Initially set to false, will open when editing
const isSecurityEditVisible = ref(false);

const menuButton = () => {
  isMenuListVisible.value = !isMenuListVisible.value;
};

// Props for either member or employee
const props = defineProps({
  member: {
    type: Array,
    default: () => []
  },
  employee: {
    type: Array,
    default: () => []
  },
});

// Combine members and employees into a single array of users
const users = computed(() => {
  return [...props.member, ...props.employee];
});

// Ensure that the user exists before proceeding
const user = computed(() => {
  return props.member.length > 0 ? props.member[0] : (props.employee.length > 0 ? props.employee[0] : null);
});

const form = useForm({
  firstName: user.value?.firstName || '',
  lastName: user.value?.lastName || '',
  email: user.value?.email || '',
  password: '',
  password_confirmation: '',
});

// Edit user function: Open the modal and set the form values
const editUser = (user) => {
  form.id = user.member_ID || user.employee_ID; // Correct ID assignment
  form.userType = user.member_ID ? 'member' : 'employee'; // Track if the user is a member or employee
  form.firstName = user.firstName;
  form.lastName = user.lastName;
  form.email = user.email;
  form.password = ''; // Reset password field
  form.password_confirmation = ''; // Reset password confirmation
  isProfileEditVisible.value = true; // Open the edit modal
};


// Close modal
const closeModal = () => {
  isProfileEditVisible.value = false;
};

// Edit profile function
const updateProfile = () => {
  if (!form.id || !form.userType) {
    console.error("No valid user ID or user type found.");
    return;
  }

  form.put(route(`user.update`, { id: form.id, userType: form.userType }), {
    onSuccess: () => {
      alert('Profile updated successfully!');
      closeModal(); // Close the modal after success
    },
    onError: (errors) => {
      console.error('Error updating profile:', errors);
    },
  });
};


// Delete function
const deleteUser = (user) => {
  if (confirm('Are you sure you want to delete this user?')) {
    axios.delete(route('user.delete', { id: user.member_ID || user.employee_ID, userType: user.member_ID ? 'member' : 'employee' }))
      .then(() => {
        alert(`${getRole(user)} deleted successfully!`);
        window.location.reload(); // Refresh the page to reflect changes
      })
      .catch((error) => {
        console.error('Error deleting user:', error);
      });
  }
};


// Method to get the role as "Member" or "Employee"
const getRole = (user) => {
  if (user.member_ID) {
    return 'Member';
  } else if (user.employee_ID) {
    return 'Employee';
  } else {
    return 'Unknown'; // Fallback for cases where the role is not defined
  }
};

</script>

<template>
  <body>
    <div class="container-m-d" id="main">
      <!-- NAV DASHBOARD -->
      <NavBarAdmin />

      <!-- SIDEBAR DASHBOARD -->
      <div class="sidebar-m-d">
        <div class="sidebar-m-d-list">
          <Link :href="route('AdminDashboardServices')">
            <div>
              <span class="material-symbols-outlined">design_services</span>
            </div>
            <p>SERVICES</p>
          </Link>
        </div>
        <div class="sidebar-m-d-list-choose">
          <Link :href="route('AdminDashboardManageUsers')">
            <div>
              <span class="material-symbols-outlined">manage_accounts</span>
            </div>
            <p>MANAGE USERS</p>
          </Link>
        </div>
      </div>

      <!-- MAIN DASHBOARD -->
      <main class="main-m-d">
        <div class="main-m-d-head">
          <h3>SEARCH</h3>
          <div class="main-m-d-searchbar">
            <span class="material-symbols-outlined">search</span>
            <form action="" class="main-m-d-searchform">
              <input type="text" name="searchServices" placeholder="Search">
            </form>
            <span class="material-symbols-outlined">tune</span>
          </div>
        </div>

        <div class="main-m-d-body">
          <table class="admin-manage-table">
            <tr>
              <th>NAME</th>
              <th>EMAIL</th>
              <th>ROLE</th>
              <th>REGISTRATION DATE</th>
              <th>STATUS</th>
            </tr>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.firstName }} {{ user.lastName }}</td>
              <td>{{ user.email }}</td>
              <td>{{ getRole(user) }}</td> <!-- Use the method to get role -->
              <td>{{ user.created_at }}</td>
              <td>{{ user.status }}</td>
              <td>
                <button class="admin-manage-edit" @click="editUser(user)">
                  EDIT
                </button>
                <button class="activate-btn" @click="deleteUser(user)">
                  DELETE
                </button>
              </td>
            </tr>
          </table>
        </div>
      </main>

      <!-- EDIT PROFILE MODAL -->
      <div v-if="isProfileEditVisible" class="edit-modal">
        <div class="modal-overlay" @click="closeModal"></div> <!-- Overlay to close modal -->
        <div class="modal-content">
          <form @submit.prevent="updateProfile">
            <h3>Edit Profile</h3>
            <input type="hidden" v-model="form.userType" />

            <label for="firstName">First Name</label>
            <input type="text" v-model="form.firstName" required>
            <p v-if="form.errors.firstName" class="error-message">{{ form.errors.firstName }}</p>

            <label for="lastName">Last Name</label>
            <input type="text" v-model="form.lastName" required>
            <p v-if="form.errors.lastName" class="error-message">{{ form.errors.lastName }}</p>

            <label for="email">Email</label>
            <input type="email" v-model="form.email" required>
            <p v-if="form.errors.email" class="error-message">{{ form.errors.email }}</p>

            <label for="password">New Password</label>
            <input type="password" v-model="form.password" required>
            <p v-if="form.errors.password" class="error-message">{{ form.errors.password }}</p>

            <label for="password_confirmation">Confirm Password</label>
            <input type="password" v-model="form.password_confirmation" required>
            <p v-if="form.errors.password_confirmation" class="error-message">{{ form.errors.password_confirmation }}</p>

            <button type="submit" :disabled="form.processing">Save Changes</button>
            <button type="button" @click="closeModal">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </body>
</template>

<style scoped>
.error-message {
  color: red;
  font-size: 0.9em;
  margin-top: 5px;
}

.edit-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.1); /* Slightly darker background to highlight modal */
  z-index: 1000;
}

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.1); /* Semi-transparent dark background */
  z-index: 999; /* Ensures the overlay is below the modal content */
}

.modal-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  width: 400px;
  max-width: 100%;
  position: relative;
  z-index: 1001; /* Ensures the content stays above the overlay */
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Adds a shadow for better visibility */
}
.modal-content > form{
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.admin-manage-edit {
  margin-top: 10px;
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.activate-btn{
  margin-top: 10px;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

</style>
