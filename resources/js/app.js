import Vue from 'vue'

import ListingGpxPage from './gpx/pages/ListingGpxPage';
import {Icon} from 'leaflet'
import 'leaflet/dist/leaflet.css'
import vuetify from './plugins/vuetify';

// this part resolve an issue where the markers would not appear
delete Icon.Default.prototype._getIconUrl;

Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

new Vue({
    vuetify,
    components: {
        'listing-gpx': ListingGpxPage
    },
    template: "<listing-gpx/>"
}).$mount("#app");
