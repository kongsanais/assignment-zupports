<template>
  <div class="row mx-auto">
    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-3">
      <!-- Search Card -->
      <div class="card" style="width: 100%; margin-top: 5px">
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
      <div class="card" style="width: 100%; height: 500px; margin-top: 5px">
        <div style="overflow-y: auto; height: 500px" v-if="!loadingStatus">
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

    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-9">
      <!-- Map card to display google map  -->
      <div
        class="card"
        style="width: 100%; height: 560px; margin-top: 5px; height: 100%"
      >
        <div class="card-body">
          <GMapMap
            v-if="!loadingStatus"
            :center="centerLocation"
            :zoom="14"
            map-type-id="terrain"
            :zoomOnClick="true"
            style="width: 100%; height: 650px"
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

      /* save text,type and res to database */
      let his_obj = localStorage.getItem("cache_history_object");
      his_obj = JSON.parse(his_obj);

      /* first time search */
      if (his_obj == null) {

        const resData = await goolgeMapSearch();
        his_obj = [
          {
            text: textInput.value,
            type: typeInput.value,
            dataPlace: resData.dataPlace,
            dataRestaturant: resData.restaurantData,
            cDate: new Date(),
          },
        ];
        let temp_his_string = JSON.stringify(his_obj);
        localStorage.setItem("cache_history_object", temp_his_string);

      } 
      else if (his_obj.some((item) =>item.text === textInput.value && item.type === typeInput.value)) {
        checkCacheResult()
      } else 
      {
         updateCache()
      }


      clearCacheHistory();
      loadingStatus.value = false;
    }




     async function updateCache(){
        const resData = await goolgeMapSearch();
        /*get local storage */
        let temp_his_string = localStorage.getItem("cache_history_object");
        temp_his_string = JSON.parse(temp_his_string);

        temp_his_string.push({
          text: textInput.value,
          type: typeInput.value,
          dataPlace: resData.dataPlace,
          dataRestaturant: resData.restaurantData,
          cDate: new Date(),
        });

        temp_his_string = JSON.stringify(temp_his_string);
        localStorage.setItem("cache_history_object", temp_his_string);
     }

     async function checkCacheResult(){

        let cacheHistory = localStorage.getItem("cache_history_object"); 
        cacheHistory = JSON.parse(cacheHistory);
        console.log(cacheHistory)
        const resultCache = cacheHistory.find((item) =>item.text === textInput.value && item.type === typeInput.value) || null;

        if (resultCache) {
          
          console.log(cacheHistory.dataPlace)
          centerLocation.value.lat = cacheHistory.dataPlace.data.results[0].geometry.location.lat;
          centerLocation.value.lng = cacheHistory.dataPlace.data.results[0].geometry.location.lng;

          for (let i = 0; i < cacheHistory.dataRestaturant.length; i++) {

            RestaurantList.value.push({
              restaurant_name: cacheHistory.dataRestaturant[i].name,
              lat: cacheHistory.dataRestaturant[i].geometry.location.lat,
              lng: cacheHistory.dataRestaturant[i].geometry.location.lng,
            });

            markerRestaurant.value.push({
              position: {
                lat: cacheHistory.dataRestaturant[i].geometry.location.lat,
                lng: cacheHistory.dataRestaturant[i].geometry.location.lng,
              },
            });
          
          }

        }

     }

      async function clearCacheHistory() {
        let his_obj = localStorage.getItem("cache_history_object");
        his_obj = JSON.parse(his_obj);

        const currentDate = new Date();
        const timeToCheck = 5 * 60 * 1000;

        his_obj = his_obj.filter((item) => {
          const itemDate = new Date(item.cDate);
          const timeDifference = currentDate - itemDate;
          return timeDifference <= timeToCheck;
        });

        console.log(his_obj);
      }



    async function goolgeMapSearch() {
    
      const dataPlace = await apiGoogleMap.getMapBytext(textInput.value);
      centerLocation.value.lat =
        dataPlace.data.results[0].geometry.location.lat;
      centerLocation.value.lng =
        dataPlace.data.results[0].geometry.location.lng;
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
        markerRestaurant.value.push({
          position: {
            lat: restaurantData[i].geometry.location.lat,
            lng: restaurantData[i].geometry.location.lng,
          },
        });
      }
      /*change logo*/
      changeLogoMarker();
      return { restaurantData, dataPlace };
    }

    async function changeLogoMarker() {
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
      updateCache,
      checkCacheResult,
      goolgeMapSearch,
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
