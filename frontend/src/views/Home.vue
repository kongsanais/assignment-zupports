<template>
  <div>
    <div class="row">
      <div class="col-2">
        <div class="card" style="width: 100%; margin: 5px">
          <div class="card-body">
            <h5 class="card-title text-center">Google Map</h5>
            <hr />
            <div class="text-center">
              <div class="input-group input-margin">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Location"
                  aria-label="Username"
                  aria-describedby="basic-addon1"
                  v-model="textInput"
                />
              </div>
            </div>

            <div class="input-group input-margin">
              <select class="form-select" aria-label="Type" v-model="typeInput">
                <option selected></option>
                <option value="restaurant">Restaurant</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="col-10">
        <div class="card" style="width: 100%;height:560px;margin: 5px">
          <h5 class="card-title text-center">Map</h5>

          <GMapMap :center="centerLocation" :zoom="15" map-type-id="terrain" style="width: 80vw; height: 50vh">
                <GMapMarker
                :key="markerDetails.id"
                :position="markerDetails.position"
                :clickable="true"
                :draggable="false"
              >
              </GMapMarker>
         </GMapMap>


        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted,ref } from "vue";

export default {
  setup() {

    let textInput = ref("Bang Sue");
    let typeInput = ref("restaurant");

    const centerLocation = ref({ lat: 13.828253, lng: 100.5284507 });
    const markerDetails = ref({
      position: centerLocation.value
    })



    function findLocation (){
      
    }

   onMounted(() => {
      

   })

    return {
      findLocation,
      markerDetails,
      centerLocation,
      textInput,
      typeInput,
    };
  },
};
</script>

<style scoped>
.input-margin {
  margin: 5px;
}
</style>
