<template>
  <div class="row mx-auto">
     <!-- update responsive  -->
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
     <!-- update responsive  -->
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

      /* get cache data from localstorage */
      let cacheHistory = localStorage.getItem("cache_history_object");
      cacheHistory = JSON.parse(cacheHistory);

      /* check condition */
      if (!cacheHistory) {
        await defaultCache();
      } else if (cacheHistory.some((item) =>item.text === textInput.value && item.type === typeInput.value)) {
        await checkCacheResult();
      } else {
         await updateCache();
      }

      clearCacheHistory();
      loadingStatus.value = false;
    }

     /* use to call frist time to make localstorage*/ 
     async function defaultCache(){
        let cacheHistory = localStorage.getItem("cache_history_object");
        cacheHistory = JSON.parse(cacheHistory);
        const resData = await goolgeMapSearch();
        cacheHistory = [{ text: textInput.value,type: typeInput.value,dataPlace: resData.dataPlace, dataRestaturant: resData.restaurantData,cDate: new Date(),}];
        let temp_his_string = JSON.stringify(cacheHistory);
        localStorage.setItem("cache_history_object", temp_his_string);

     }

     /*check cache data with text and type , if have history not call google api */
     async function checkCacheResult(){
        let cacheHistory = localStorage.getItem("cache_history_object"); 
        cacheHistory = JSON.parse(cacheHistory);
        const resultCache = cacheHistory.find((item) =>item.text === textInput.value && item.type === typeInput.value) || null;

        if (resultCache) {
          centerLocation.value.lat = resultCache.dataPlace.data.results[0].geometry.location.lat;
          centerLocation.value.lng = resultCache.dataPlace.data.results[0].geometry.location.lng;
          for (let i = 0; i < resultCache.dataRestaturant.length; i++) {
            RestaurantList.value.push({
              restaurant_name: resultCache.dataRestaturant[i].name,
              lat: resultCache.dataRestaturant[i].geometry.location.lat,
              lng: resultCache.dataRestaturant[i].geometry.location.lng,
            });

            markerRestaurant.value.push({
              position: {
                lat: resultCache.dataRestaturant[i].geometry.location.lat,
                lng: resultCache.dataRestaturant[i].geometry.location.lng,
              },
            });
          }
        }

     }
    
     /* update cache data  push new response to localstorage */
     async function updateCache(){
        const resData = await goolgeMapSearch();
        let tempHistory = localStorage.getItem("cache_history_object");

        tempHistory = JSON.parse(tempHistory);

        tempHistory.push({
          text: textInput.value,
          type: typeInput.value,
          dataPlace: resData.dataPlace,
          dataRestaturant: resData.restaurantData,
          cDate: new Date(),
        });

        tempHistory = JSON.stringify(tempHistory);
        localStorage.setItem("cache_history_object", tempHistory);
     }


    /* clear cache  data  with time , can set  5 minus , 6 hours , 1 day */
    async function clearCacheHistory() {
          let cacheHistory = localStorage.getItem("cache_history_object");
          cacheHistory = JSON.parse(cacheHistory);

          const currentDate = new Date();
          //const timeToCheck = 1 * 60 * 1000; // 5 minus 
          //const timeToCheck = 6 * 60 * 60 * 1000; // 6 hours
          const timeToCheck = 24 * 60 * 60 * 1000; // 1 day 

          cacheHistory = cacheHistory.filter((item) => {
            const itemDate = new Date(item.cDate);
            const timeDifference = currentDate - itemDate;
            return timeDifference <= timeToCheck;
          });

          cacheHistory =  JSON.stringify(cacheHistory)
          localStorage.setItem('cache_history_object',cacheHistory)
    }


    /* use to call google map service */
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

      /* change logo */
      changeLogoMarker();
      return { restaurantData, dataPlace };
    }
     
    /* change logo marker */
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
      defaultCache,
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
