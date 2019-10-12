import Vue from 'vue'
import Vuetify from 'vuetify'

import 'vuetify/dist/vuetify.min.css' // Ensure you are using css-loader

import ListingGpxPage from './gpx/pages/ListingGpxPage';
import { Icon }  from 'leaflet'
import 'leaflet/dist/leaflet.css'


// this part resolve an issue where the markers would not appear
delete Icon.Default.prototype._getIconUrl;

Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

Vue.use(Vuetify);

new Vue({
    el: '#app',
    components: {
        'listing-gpx': ListingGpxPage
    },
    template: "<v-app><listing-gpx></listing-gpx></v-app>"
});
