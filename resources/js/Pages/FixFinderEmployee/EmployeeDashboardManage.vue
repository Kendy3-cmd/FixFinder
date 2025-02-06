<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import NavBarEmployee from '../Components/NavBarEmployee.vue';

// Payment modal state
const paymentModal = ref(false); // Show/hide payment modal
const paymentAmount = ref(null); // Track payment input amount

// Booking-related states
const bookings = ref([]);
const selectedBooking = ref(null);

// Fetch bookings from the API
const fetchBookings = async () => {
  try {
    const response = await axios.get('/bookings');
    bookings.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch bookings:', error);
  }
};

// Update booking status
const updateBookingStatus = async (booking_ID, status) => {
  try {
    const response = await axios.patch(`/bookings/${booking_ID}/status`, { statusBooking: status });
    const updatedBooking = bookings.value.find(b => b.booking_ID === booking_ID);

    if (updatedBooking) {
      updatedBooking.statusBooking = status;

      // Show payment modal if status is "completed"
      if (status === 'completed') {
        paymentModal.value = true;
        selectedBooking.value = updatedBooking;
      }

      // If status is "cancelled," delete the booking
      if (status === 'cancelled') {
        await deleteBooking(booking_ID);
      }
    }

    alert(`Booking status updated to ${status.toUpperCase()}`);
  } catch (error) {
    console.error('Error updating booking status:', error);
    alert('Failed to update booking status.');
  }
};

// Delete booking
const deleteBooking = async (booking_ID) => {
  try {
    await axios.delete(`/bookings/${booking_ID}`);
    bookings.value = bookings.value.filter(b => b.booking_ID !== booking_ID);
    alert('Booking deleted successfully.');
  } catch (error) {
    console.error('Error deleting booking:', error);
    alert('Failed to delete booking.');
  }
};

// Complete payment
const completePayment = async () => {
  if (!paymentAmount.value || paymentAmount.value <= 0) {
    alert('Please enter a valid payment amount.');
    return;
  }

  try {
    console.log(`Payment of ₱${paymentAmount.value} for booking ID: ${selectedBooking.value.booking_ID}`);
    // Optionally, save the payment in your database here.
    // Delete booking after successful payment completion
    alert(`Payment of ₱${paymentAmount.value} completed successfully!`);
    closePaymentModal();
  } catch (error) {
    console.error('Error processing payment:', error);
    alert('Failed to process payment.');
  }
};

// Close payment modal
const closePaymentModal = () => {
  paymentModal.value = false;
  selectedBooking.value = null;
  paymentAmount.value = null; // Reset payment amount
};

// Show/hide modal for detailed view
const showModal = ref(false);
const openModal = (booking) => {
  selectedBooking.value = booking;
  showModal.value = true;
};
const closeModal = () => {
  showModal.value = false;
  selectedBooking.value = null;
};

// Fetch bookings on mount
onMounted(async () => {
  await fetchBookings();
});
</script>


<template>
  <body>
    <div class="container-m-d" id="main">
      <!-- NAV EMPLOYEE DASHBOARD -->
      <NavBarEmployee />

      <!-- SIDEBAR DASHBOARD -->
      <div class="sidebar-m-d">
        <div class="sidebar-m-d-list-choose">
          <Link :href="route('EmployeeDashboardManage')">
            <div>
              <span class="material-symbols-outlined">bookmark</span>
            </div>
            <p style="font-size: 20px; margin: 0; padding: 17px 0;">MANAGE BOOKING</p>
          </Link>
        </div>
      </div>

      <!-- MAIN DASHBOARD -->
      <main class="main-e-d">
        <h3>BOOKING OVERVIEW</h3>
        <div class="main-book-tables">
          <!-- UPCOMING BOOKINGS -->
          <div class="upcoming-book-e-d">
            <h5>ONGOING BOOKINGS</h5>
            <div class="upcoming-book-table">
              <table>
                <tr>
                  <th>DATE & TIME</th>
                  <th>MEMBER NAME</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
                </tr>
                <tr v-for="booking in bookings.filter(b => b.statusBooking === 'confirmed')" :key="booking.booking_ID">
                  <td>{{ booking.bookingDate }}, {{ booking.bookingTime }}</td>
                  <td>{{ booking.member ? `${booking.member.firstName} ${booking.member.lastName}` : 'No member info' }}</td>
                  <td>{{ booking.statusBooking }}</td>
                  <td class="book-manage-btn">
                    <button @click="openModal(booking)" style="background-color: blue;">VIEW</button>
                    <button style="background-color: #1B253D;" @click="updateBookingStatus(booking.booking_ID, 'completed')">
                      COMPLETE
                    </button>
                    <button style="background-color: red;" @click="updateBookingStatus(booking.booking_ID, 'cancelled')">CANCEL</button>
                  </td>
                </tr>
              </table>
            </div>
          </div>

          <!-- PENDING REQUESTS -->
          <div class="pending-book-e-d">
            <h5>PENDING REQUESTS</h5>
            <div class="pending-book-table">
              <table>
                <tr>
                  <th>REQUEST DATE</th>
                  <th>MEMBER NAME</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
                </tr>
                <tr v-for="booking in bookings.filter(b => b.statusBooking === 'pending')" :key="booking.booking_ID">
                  <td>{{ booking.bookingDate }}, {{ booking.bookingTime }}</td>
                  <td>{{ booking.member ? `${booking.member.firstName} ${booking.member.lastName}` : 'No member info' }}</td>
                  <td>{{ booking.statusBooking }}</td>
                  <td class="book-manage-btn">
                    <button @click="openModal(booking)" style="background-color: blue;">VIEW</button>
                    <button style="background-color: #1B253D;" @click="updateBookingStatus(booking.booking_ID, 'confirmed')">ACCEPT</button>
                    <button style="background-color: red;" @click="updateBookingStatus(booking.booking_ID, 'cancelled')">DECLINE</button>
                  </td>
                </tr>
              </table>
            </div>
          </div>

          <!-- COMPLETED BOOKINGS -->
          <div class="completed-book-e-d">
            <h5>COMPLETED BOOKINGS</h5>
            <div class="completed-book-table">
              <table>
                <tr>
                  <th>DATE</th>
                  <th>MEMBER NAME</th>
                </tr>
                <tr v-for="booking in bookings.filter(b => b.statusBooking === 'completed')" :key="booking.booking_ID">
                  <td>{{ booking.bookingDate }}</td>
                  <td>{{ booking.member ? `${booking.member.firstName} ${booking.member.lastName}` : 'No member info' }}</td>
                </tr>
              </table>
            </div>
          </div>

           <!-- Modal -->
        <div v-if="showModal" class="modal-overlay">
          <div class="modal-content">
            <h3>Booking Details</h3>
            <p><strong>Location:</strong> {{ selectedBooking?.location }}</p>
            <p><strong>Message:</strong> {{ selectedBooking?.message }}</p>
            <button @click="closeModal">CLOSE</button>
          </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="paymentModal" class="modal-overlay">
          <div class="modal-content">
            <h3>Payment for Booking</h3>
            <p><strong>Member Name:</strong> {{ selectedBooking?.member.firstName }} {{ selectedBooking?.member.lastName }}</p>
            <p><strong>Date:</strong> {{ selectedBooking?.bookingDate }}</p>

            <!-- Input Total Payment -->
            <div style="margin: 15px 0;">
              <label for="paymentAmount">Enter Total Payment:</label>
              <input 
                v-model="paymentAmount" 
                id="paymentAmount" 
                type="number" 
                min="0" 
                placeholder="Enter payment amount"
                style="padding: 8px; width: 90%; margin-top: 5px;"
              />
            </div>

            <!-- Payment Action -->
            <div class="payment-actions">
              <button @click="completePayment" style="background-color: green;">COMPLETE</button>
            </div>
          </div>
        </div>

        </div>
      </main>
    </div>
  </body>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.payment-actions button {
  margin: 10px;
  padding: 10px 20px;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

</style>
