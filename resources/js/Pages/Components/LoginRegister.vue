<script setup>

</script>

<template>
  <div class="container-Login-Register">
    <Link :href="route('EmployeeLoginRegister')" as="button" class="logRegEmployee">EMPLOYEE</Link>
    <Link :href="route('MemberLoginRegister')" as="button" class="logRegMember">MEMBERS</Link>
  </div>
</template>