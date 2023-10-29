<template>
  <nav class="navbar" style="background-color: #0064CF;" data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img
          src="../../../public/map.png"
          style="height: 30px; width: 30px"
        />
        <b style="margin:15px">{{ navTextTitle }}</b>
        </a
      >
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";
defineProps({
  navTextTitle: String,
});
</script>

<style scoped></style>
