<template>
<div>
  <Navbar navTextTitle="Google Map Api"/>
  <Content/>
  <Footer/>
</div>
</template>


<script>

import { ref } from "vue";
import Navbar from  '../src/components/core/Navbar.vue';
import Content from '../src/components/core/Content.vue'; 
import Footer from '../src/components/core/Footer.vue';

export default {
  components : {
    Navbar,
    Content,
    Footer
  },
  setup(){
    return{
    }
  },
}
</script>
<style lang="">
  
</style>