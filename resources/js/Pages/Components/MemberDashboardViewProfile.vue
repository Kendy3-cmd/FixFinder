<script setup>
import { reactive, ref, computed ,onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';


// Props definition
const props = defineProps({
  employee: Object,
  services: {
    type: Array,
    default: () => []
  }
});

// Button state based on ongoing booking
const hasOngoingBooking = computed(() => {
  return bookings.value.some(
    (booking) =>
      booking.member_ID === bookingForm.member_ID &&
      booking.employee_ID === bookingForm.employee_ID &&
      booking.statusBooking !== 'completed' &&
      booking.statusBooking !== 'cancelled'
  );
});

// Button Action: "Book Now" or "Cancel"
function handleButtonClick() {
  if (hasOngoingBooking.value) {
    cancelBooking(); // Trigger booking cancellation
  } else {
    submitBooking(); // Submit new booking
  }
}

// Function to cancel the booking
async function cancelBooking() {
  try {
    const activeBooking = bookings.value.find(
      (booking) =>
        booking.member_ID === bookingForm.member_ID &&
        booking.employee_ID === bookingForm.employee_ID &&
        booking.statusBooking !== 'completed' &&
        booking.statusBooking !== 'cancelled'
    );

    if (activeBooking) {
      const response = await axios.patch(
        `/bookings/${activeBooking.booking_ID}/status`,
        { statusBooking: 'cancelled' }
      );
      alert('Booking cancelled successfully!');
      fetchBookingStatus(); // Refresh booking data
    }
  } catch (error) {
    console.error('Error cancelling booking:', error);
    alert('Failed to cancel the booking.');
  }
}

// Form and user data
const user = usePage().props.auth?.user || null;

const bookingForm = reactive({
  member_ID: user?.member_ID || '',
  employee_ID: props.employee?.employee_ID || '',
  location: '',
  message: '',
  bookingDate: '',
  bookingTime: '',
  statusBooking: '', // Backend expects this exact name
  processing: false,
});

function convertTo12HourFormat(timeString) {
  if (!timeString) return ''; // Return an empty string if the time is null or undefined

  let time = timeString.split(':');
  let hours = parseInt(time[0], 10);
  let minutes = time[1];
  let period = hours >= 12 ? 'PM' : 'AM';
  
  if (hours > 12) hours -= 12;
  if (hours === 0) hours = 12;

  return `${hours}:${minutes} ${period}`;
}


const weeklyAvailability = ref({});

const fetchAvailability = async () => {
  try {
    const response = await axios.get(route('employee.getAvailability', props.employee.employee_ID));
    console.log('Raw Availability Data:', response.data); // Log the full API response

    const availability = response.data;

    // If availability is an object (not an array)
    if (typeof availability === 'object' && availability !== null) {
      // Iterate over the object keys (days of the week)
      Object.entries(availability).forEach(([day, times]) => {
        weeklyAvailability.value[day] = {
          start: times.start ? convertTo12HourFormat(times.start) : 'Closed', // If start time exists
          end: times.end ? convertTo12HourFormat(times.end) : '', // If end time exists
        };
      });
    } else {
      console.error('Expected an object of availability data, but got:', availability);
    }

  } catch (error) {
    console.error('Error fetching availability:', error);
  }
};


// Helper function to format dates
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString();
}

// Manage booking form visibility
const isBodyBookVisible = ref(localStorage.getItem('isBodyBookVisible') === 'true');
const isAvailableVisible = ref(false); // Track the visibility of the availability section
const ShowButton = ref(true);

// Toggle visibility of the booking form
function toggleBookingForm() {
  isBodyBookVisible.value = !isBodyBookVisible.value;
  localStorage.setItem('isBodyBookVisible', isBodyBookVisible.value.toString());
}

const buttonText = ref('Show Availability');  // Define buttonText

// Toggle availability section visibility
function toggleAvailability() {
  isAvailableVisible.value = !isAvailableVisible.value; // Toggle the visibility
  if (isAvailableVisible.value) {
    buttonText.value = 'Back to Profile'; // Change button text when availability is shown
  } else {
    buttonText.value = 'Show Availability'; // Change it back when hiding availability
  }
}

const bookings = ref([]); // Initialize bookings as an empty array

// Function to fetch bookings
const fetchBookingStatus = async () => {
  try {
    const response = await axios.get('/bookings', {
      headers: {
        'Authorization': `Bearer ${user?.api_token}` // Assuming `api_token` is the token for the user
      }
    });
    const bookingsData = response.data.data;
    console.log('Fetched Bookings:', bookingsData);

    bookings.value = Array.isArray(bookingsData) ? bookingsData : [];

    // Ensure we are using the correct employee ID and member ID for comparison
    const currentBooking = bookings.value.find(booking => 
      booking.member_ID === bookingForm.member_ID && booking.employee_ID === props.employee.employee_ID
    );

    if (currentBooking) {
      bookingForm.statusBooking = currentBooking.statusBooking;
      console.log('Booking Status for Employee:', props.employee.firstName, currentBooking.statusBooking);
    } else {
      // Default message if no booking is found for this employee
      bookingForm.statusBooking = 'No booking found';
      console.log('No matching booking found for employee:', props.employee.firstName);
    }
  } catch (error) {
    console.error('Error fetching bookings:', error);
  }
};

// Inside your submitBooking function
function submitBooking() {
  bookingForm.statusBooking = 'pending'; // Set initial status as 'pending'
  bookingForm.processing = true;

  axios.post('/bookings', bookingForm)
    .then((response) => {
      bookingForm.processing = false;
      fetchBookingStatus(); // Refresh booking status
      alert('Booking created successfully!');
    })
    .catch((error) => {
      bookingForm.processing = false;
      console.error('Failed to submit booking:', error);
      alert('Already Booking.');
    });
}


// Clear the booking form
function clearForm() {
  bookingForm.location = '';
  bookingForm.message = '';
  bookingForm.bookingDate = '';
  bookingForm.bookingTime = '';
}

// Initialize form visibility from localStorage
onMounted(() => {
  const savedVisibility = localStorage.getItem('isBodyBookVisible');
  if (savedVisibility !== null) {
    isBodyBookVisible.value = savedVisibility === 'true';
  }
});

onMounted(() => {
  console.log('Employee:', props.employee);
  console.log('Services:', props.services);
  console.log('Initial Booking Status:', bookingForm.statusBooking);
  fetchBookingStatus();
  fetchAvailability();
});

console.log('Booking Status:', bookingForm.statusBooking);
</script>

<template>
  <div>
    <h3>PROFILE</h3>
    <div class="main-m-profile-right">
      <!-- Profile Header -->
      <div class="profile-head">
        <div class="profile-head-left">
          <h2>{{ props.employee.firstName }} {{ props.employee.lastName }}</h2>
          <h4>
            {{
              (props.services && props.services.length > 0) 
              ? props.services.find(
                  (service) => service.service_ID === props.employee?.service_ID
                )?.serviceName 
              : 'No skills available'
            }}
          </h4>
        </div>
        <div class="profile-head-right">
          <button class="profile-available-btn" @click="toggleAvailability" v-if="ShowButton">{{ buttonText }}</button> <!-- Show availability when clicked -->
        </div>
      </div>

      <!-- Availability Section -->
      <div v-show="isAvailableVisible && Object.keys(weeklyAvailability).length > 0" class="availability">
        <h4>AVAILABLE</h4>
        <table class="availability-table">
          <thead>
            <tr>
              <th>Day</th>
              <th>Available Time</th>
            </tr>
          </thead>
          <tbody>
            <tr class="tr-available-table" v-for="(time, day) in weeklyAvailability" :key="day">
              <td>{{ day }}</td>
              <td v-if="time.start === 'Closed'">Closed</td>
              <td v-else>{{ time.start }} - {{ time.end }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Profile Body -->
      <div v-show="!isAvailableVisible" class="profile-body">
        <div class="profile-description">
          <h4>DESCRIPTION</h4>
          <p>{{ props.employee.description }}</p>
        </div>
        <div class="profile-identity">
          <div class="profile-identity-u">
            <h4>IDENTITY</h4>
            <h4>Address: {{ props.employee.address }}</h4>
            <h4>Phone: {{ props.employee.phone }}</h4>
            <h4>Coverage Area: {{ props.employee.coverageArea }}</h4>
            <h4>Date Of Birth: {{ props.employee.dateOfBirth }}</h4>
          </div>
          <div class="profile-identity-d">
            <h4>FixFinder Employee Since:</h4>
            <h5>{{ formatDate(props.employee.created_at) }}</h5>
          </div>
        </div>
      </div>

      <!-- Booking Form -->
      <div class="profile-booking">
        <form @submit.prevent="submitBooking" class="profile-booking-form">
          <div class="form-container-book">
            <div class="group1">
              <label>LOCATION</label>
              <textarea v-model="bookingForm.location" placeholder="Enter location" spellcheck="false" required></textarea>
            </div>
            <div class="group2">
              <label>LEAVE A MESSAGE</label>
              <textarea v-model="bookingForm.message" placeholder="Enter Message" spellcheck="false"></textarea>
            </div>
            <div class="group3">
              <label>TIME AND DATE</label>
              <input type="date" v-model="bookingForm.bookingDate" required />
              <input type="time" v-model="bookingForm.bookingTime" required />
            </div>
          </div>
          <!-- Booking Button -->
          <button
            type="button"
            v-if="!hasOngoingBooking"
            @click="submitBooking"
            :disabled="bookingForm.processing"
            class="book-booking-btn"
          >
            Book Now
          </button>

          <!-- Cancel Button -->
          <button
            type="button"
            v-if="hasOngoingBooking"
            @click="cancelBooking"
            :disabled="bookingForm.processing"
            class="cancel-booking-btn"
          >
            Cancel Booking
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>

.book-booking-btn{
  background-image: linear-gradient(to right, #1B253D, #375089);
  border: none;
  margin: auto;
  color: white;
  font-family: "Agdasima", sans-serif;
  padding: 5px 250px;
  border-radius: 100px;
  font-size: 15px;
  cursor: pointer;
}
.book-booking-btn > .book-booking-btn:hover{
  background-image: linear-gradient(to right, #375089, rgb(170, 170, 170));
}

.cancel-booking-btn {
  background-color: red; /* Red background */
  border: none;
  margin: auto;
  color: white;
  font-family: "Agdasima", sans-serif;
  padding: 5px 250px;
  border-radius: 100px;
  font-size: 15px;
  cursor: pointer;
}

.cancel-booking-btn:hover {
  background-color: darkred; /* Darker red on hover */
}
</style>