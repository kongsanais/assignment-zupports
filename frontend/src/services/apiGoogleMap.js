import httpClient from "../services/httpClient";
import { googleMap } from "../services/constants";

/* 
  author : Hatsanai
  create date : 2023-10-28
  description : service 
  route ex : http://127.0.0.1:8000/api/v1/googleMapApi/placeSearch/BangSue
*/ 
export const getMapBytext = (textsearch) => {
  return httpClient.get(googleMap.GOOGLE_MAP_URL + `/placeSearch/${textsearch}` );
};

/* 
  author : Hatsanai
  create date : 2023-10-28
  description : service 
  route ex : http://127.0.0.1:8000/api/v1/googleMapApi/nearbySearch/13.828253/100.5284507/restaurant
*/ 
export const getLocationByLatLngAndType = (lat,lng,type) => {
  return httpClient.get(googleMap.GOOGLE_MAP_URL + `/nearbySearch/${lat}/${lng}/${type}` );
};


 export default {
  getMapBytext,
  getLocationByLatLngAndType 
};
