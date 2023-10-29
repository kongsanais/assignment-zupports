<template>
  <div>
    <div class="row">
      <div class="col-3">

        <!-- Search Card -->
        <div class="card" style="width: 100%; margin: 15px">
          <div class="card-body">
            <!-- search box -->
            <div class="text-center">
              <div class="input-group input-margin">
              
                <input
                  type="text"
                  class="form-control"
                  placeholder="Location"
                  aria-describedby="basic-addon1"
                  v-model="textInput"
                  @keydown.enter="findLocation()"
                />
              </div>
            </div>

            <!-- Select type -->
            <div class="input-group input-margin">
              <select
                class="form-select"
                aria-label="Type"
                v-model="typeInput"
                @change="findLocation()"
              >
                <option value="restaurant" selected>Restaurant</option>
                <option value="hospital">Hospital</option>
                <option value="pharmacy">Pharmacy</option>
              </select>
            </div>

            <!-- Search button -->
            <div class="text-center">
              <button
                type="button"
                class="btn btn-primary"
                style="margin: 3px"
                @click="findLocation()"
              >
                <b>Search</b>
              </button>
            </div>
          </div>
        </div>

        <!-- table card -->
        <div class="card" style="width: 100%; height: 375px; margin: 15px">
          <div style="overflow-y: auto; height: 330px" v-if="!loadingStatus">
            <div class="card-title text-center text-capitalize fw-bold">
              {{ typeInput }} List ({{ RestaurantList.length }})
            </div>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <td>No.</td>
                  <td>Name</td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in RestaurantList" :key="item.id">
                  <td>{{ index + 1 }}</td>
                  <td class="text-left">{{ item.restaurant_name }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center">
            <div class="spinner-border" style="margin: 30px">
              <span class="sr-only"></span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-9">
            <!-- map card to display google map  -->
        <div class="card" style="width: 98%; height: 560px; margin: 15px">
          <div class="card-body">
            <GMapMap
              v-if="!loadingStatus"
              :center="centerLocation"
              :zoom="14"
              map-type-id="terrain"
              :zoomOnClick="true"
              style="width: 100%; height: 500px"
            >
              <GMapMarker
                :position="markerDetails.position"
                :clickable="true"
                :draggable="false"
              >
              </GMapMarker>

              <GMapMarker
                :key="index"
                v-for="(m, index) in markerRestaurant"
                :position="m.position"
                :clickable="true"
                :icon="markerIcon"
              />
            </GMapMap>
            <div v-else class="text-center">
              <div class="spinner-border" style="margin: 30px">
                <span class="sr-only"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from "vue";
import apiGoogleMap from "../services/apiGoogleMap.js";
export default {
  setup() {
    let textInput = ref("Bang Sue");
    let typeInput = ref("restaurant");
    let loadingStatus = ref(false);
    let markerIcon = ref({
      url: "../../public/makerLogo/cutlery.png",
      scaledSize: { width: 30, height: 30 },
      labelOrigin: { x: 16, y: -10 },
    });

    const centerLocation = ref({ lat: 0, lng: 0 });
    const markerDetails = ref({
      position: centerLocation.value,
    });

    const markerRestaurant = ref([]);

    const RestaurantList = ref([]);

    async function findLocation() {

      loadingStatus.value = true;
      RestaurantList.value = [];
      markerRestaurant.value = [];

      const data = await apiGoogleMap.getMapBytext(textInput.value);
      
      if(data.data.status == 'ZERO_RESULTS'){
        alert('Not Found Locaiton')
        location.reload(); 
      }


      centerLocation.value.lat = data.data.results[0].geometry.location.lat;
      centerLocation.value.lng = data.data.results[0].geometry.location.lng;

      /* find restaurant  with lat lng */
      let restaurantData = await apiGoogleMap.getLocationByLatLngAndType(
        centerLocation.value.lat,
        centerLocation.value.lng,
        typeInput.value
      );

      restaurantData = restaurantData.data.results;

      /* change data format */
      for (let i = 0; i < restaurantData.length; i++) {
        RestaurantList.value.push({
          restaurant_name: restaurantData[i].name,
          lat: restaurantData[i].geometry.location.lat,
          lng: restaurantData[i].geometry.location.lng,
        });
      }

      /*set data to marker templetes */
      for (let i = 0; i < RestaurantList.value.length; i++) {
        markerRestaurant.value.push({
          position: {
            lat: RestaurantList.value[i].lat,
            lng: RestaurantList.value[i].lng,
          },
        });
      }

      /*change logo*/
      changeLogoMarker()

      loadingStatus.value = false;
    }

    async function changeLogoMarker(){
        /*change marker icon*/
      if (typeInput.value == "restaurant") {
        markerIcon.value = {
          url: "../../public/makerLogo/cutlery.png",
          scaledSize: { width: 30, height: 30 },
          labelOrigin: { x: 16, y: -10 },
        };
      } else if (typeInput.value == "hospital") {
        markerIcon.value = {
          url: "../../public/makerLogo/hospital.png",
          scaledSize: { width: 30, height: 30 },
          labelOrigin: { x: 16, y: -10 },
        };
      } else if (typeInput.value == "pharmacy") {
        markerIcon.value = {
          url: "../../public/makerLogo/capsules.png",
          scaledSize: { width: 30, height: 30 },
          labelOrigin: { x: 16, y: -10 },
        };
    }
    }

    onMounted(async () => {
      /* load default */
      await findLocation();
    });

    return {
      changeLogoMarker,
      findLocation,
      markerIcon,
      loadingStatus,
      markerRestaurant,
      RestaurantList,
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
