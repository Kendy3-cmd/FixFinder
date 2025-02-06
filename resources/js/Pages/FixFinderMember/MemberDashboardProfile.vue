<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const isMenuListVisible = ref(false);
const isProfileEditVisible = ref(true);
const isSecurityEditVisible = ref(false);

const profileBtnClick = () => {
  if (isProfileEditVisible.value = true) {
    isSecurityEditVisible.value = false;
  }
};
const securityBtnClick = () => {
  if (isSecurityEditVisible.value = true) {
    isProfileEditVisible.value = false;
  }
};
const menuButton = () => {
  if (!isMenuListVisible.value) {
    isMenuListVisible.value = true;
  } else if(isMenuListVisible.value){
    isMenuListVisible.value = false;
  }
}

const props = defineProps({
  members: {
    type: Object,
    default: () => ({}), // Default empty object if no employee is passed
  },
});

const form = useForm({
    firstName: props.members.firstName || '',
    lastName: props.members.lastName || '',
    address: props.members.address || '',
    phone: props.members.phone || '',
    dateOfBirth: props.members.dateOfBirth || '',
});

const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const updateMemberProfile = () => {
  form.put(route('member.update', props.members.member_ID), {
    onSuccess: () => {
      alert('Profile updated successfully!');
    },
    onError: (errors) => {
      console.error(errors);
    },
  });
};

const updatePassword = () => {
    passwordForm.put(route('member.password.update', props.members.member_ID), {
        onSuccess: () => {
            alert('Password updated successfully!');
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};

</script>

<template>
  <body>
    <div class="container-m-d">
      <nav class="nav-m-d">
        <div class="nav-left-m-d">
            <div class="logo-nav"></div>
        </div>
          <div class="nav-right-m-d">
        <div class="menu-list" v-show="isMenuListVisible">
          <Link :href="route('MemberDashboardBooking')">BACK</Link>
        </div>
          <button class="nav-menu-m-d" v-on:click="menuButton">
            <div>
              <span class="material-symbols-outlined">menu</span>
            </div>
          </button>
        </div>
      </nav>

      <main class="member-profile-container">
        <div class="profile-content">
          <div class="m-profile-sidebar">
            <div class="m-profile-sidebar-list">
              <button v-on:click="profileBtnClick">PROFILE</button>
            </div>
            <div class="m-profile-sidebar-list">
              <button v-on:click="securityBtnClick">SECURITY</button>
            </div>
          </div>
          <div class="m-main-profile">
            <div v-show="isProfileEditVisible">
            <h3>PROFILE</h3>
              <form @submit.prevent="updateMemberProfile" class="m-profile-form">
                <label for="firstName">FIRST NAME</label>
                <input type="text" name="firstName" placeholder="First Name" v-model="form.firstName">
                <label for="lastName">LAST NAME</label>
                <input type="text" name="lastName" placeholder="Last Name" v-model="form.lastName">
                <label for="address">ADDRESS</label>
                <input type="text" name="address" placeholder="Address" v-model="form.address">
                <label for="phone">CONTACT NUMBER</label>
                <input type="text" name="phone" placeholder="Contact Number" v-model="form.phone">
                <label for="dataOfBirth">DATE OF BIRTH</label>
                <input type="date" name="dateOfBirth" placeholder="Date Of Birth" v-model="form.dateOfBirth"> 
                <button :disabled="form.processing">SAVE</button>
              </form>
            </div>

            <div v-show="isSecurityEditVisible">
              <h3>SECURITY</h3>
              <form @submit.prevent="updatePassword" class="m-profile-form-security">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" placeholder="Current Password" v-model="passwordForm.current_password" />
                
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" placeholder="New Password" v-model="passwordForm.new_password" />
                
                <label for="new_password_confirmation">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" v-model="passwordForm.new_password_confirmation" />

                <button type="submit" :disabled="passwordForm.processing">Update Password</button>
              </form>
            </div>
          </div>
        </div>
      </main>
      
    </div>
  </body>

<div v-if="$page.props.flash.success" class="alert alert-success">
    {{ $page.props.flash.success }}
</div>
<div v-if="$page.props.errors" class="alert alert-error">
    <ul>
        <li v-for="(error, index) in $page.props.errors" :key="index">{{ error }}</li>
    </ul>
</div>

</template>