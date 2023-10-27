<template>
<div>
  <Navbar/>
  <Content/>
</div>
</template>


<script>

import { ref } from "vue";
import Navbar from  '../src/components/core/Navbar.vue';
import Content from '../src/components/core/Content.vue' 

export default {
  components : {
    Navbar,
    Content,
  },
  setup(){
    return{
    }
  },
}
</script>
<style lang="">
  
</style>